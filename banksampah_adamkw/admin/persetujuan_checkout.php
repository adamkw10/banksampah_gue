<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("admin");

// ==================================================
// PROSES PERSETUJUAN CHECKOUT
// ==================================================

if (isset($_GET['aksi']) && isset($_GET['id'])) {

    $aksi = $_GET['aksi'];
    $id   = (int) $_GET['id'];

    // ==================================================
    // SETUJUI CHECKOUT
    // ==================================================

    if ($aksi === 'setujui') {

        mysqli_begin_transaction($conn);

        try {

            // --------------------------------------------------
            // AMBIL DATA CHECKOUT
            // --------------------------------------------------

            $query = mysqli_query(
                $conn,
                "SELECT
                    p.id_pembelian,
                    p.id_user,
                    p.id_sampah,
                    p.berat_kg,
                    p.harga_per_kg,
                    p.total_rp,
                    p.tanggal,
                    p.status,

                    s.jenis_sampah,
                    s.stok_kg,

                    u.nama,
                    u.username,
                    u.saldo

                 FROM pembelian p

                 INNER JOIN sampah s
                    ON s.id_sampah = p.id_sampah

                 INNER JOIN users u
                    ON u.id_user = p.id_user

                 WHERE p.id_pembelian = $id
                 AND p.status = 'pending'

                 FOR UPDATE"
            );

            if (!$query) {

                throw new Exception(
                    "Gagal mengambil data checkout: "
                    . mysqli_error($conn)
                );

            }

            $data = mysqli_fetch_assoc($query);

            // --------------------------------------------------
            // CEK DATA
            // --------------------------------------------------

            if (!$data) {

                throw new Exception(
                    "Checkout tidak ditemukan atau sudah diproses."
                );

            }

            // --------------------------------------------------
            // CEK BERAT
            // --------------------------------------------------

            $berat = (float) $data['berat_kg'];

            if ($berat <= 0) {

                throw new Exception(
                    "Berat checkout tidak valid."
                );

            }

            // --------------------------------------------------
            // CEK STOK
            // --------------------------------------------------

            $stok = (float) $data['stok_kg'];

            if ($berat > $stok) {

                throw new Exception(
                    "Stok sampah tidak mencukupi. Stok tersedia: "
                    . number_format($stok, 2, ',', '.')
                    . " Kg."
                );

            }

            // --------------------------------------------------
            // CEK SALDO
            // --------------------------------------------------

            $total_rp = (float) $data['total_rp'];
            $saldo    = (float) $data['saldo'];

            if ($total_rp > $saldo) {

                throw new Exception(
                    "Saldo pengepul tidak mencukupi. Saldo tersedia: Rp "
                    . number_format($saldo, 0, ',', '.')
                );

            }

            $id_sampah = (int) $data['id_sampah'];
            $id_user   = (int) $data['id_user'];

            // ==================================================
            // KURANGI STOK SAMPAH
            // ==================================================

            $update_stok = mysqli_query(
                $conn,
                "UPDATE sampah

                 SET stok_kg = stok_kg - $berat

                 WHERE id_sampah = $id_sampah
                 AND stok_kg >= $berat"
            );

            if (!$update_stok) {

                throw new Exception(
                    "Gagal mengurangi stok sampah: "
                    . mysqli_error($conn)
                );

            }

            if (mysqli_affected_rows($conn) !== 1) {

                throw new Exception(
                    "Stok sampah tidak mencukupi atau sudah berubah."
                );

            }

            // ==================================================
            // KURANGI SALDO PENGEPUL
            // ==================================================

            $update_saldo = mysqli_query(
                $conn,
                "UPDATE users

                 SET saldo = saldo - $total_rp

                 WHERE id_user = $id_user
                 AND saldo >= $total_rp"
            );

            if (!$update_saldo) {

                throw new Exception(
                    "Gagal mengurangi saldo pengepul: "
                    . mysqli_error($conn)
                );

            }

            if (mysqli_affected_rows($conn) !== 1) {

                throw new Exception(
                    "Saldo pengepul tidak mencukupi atau sudah berubah."
                );

            }

            // ==================================================
            // CATAT TRANSAKSI
            // ==================================================

            $insert_transaksi = mysqli_query(
                $conn,
                "INSERT INTO transaksi
                (
                    id_user,
                    id_sampah,
                    tanggal,
                    tipe,
                    berat_kg,
                    total_rp
                )

                VALUES
                (
                    $id_user,
                    $id_sampah,
                    CURDATE(),
                    'jual',
                    $berat,
                    $total_rp
                )"
            );

            if (!$insert_transaksi) {

                throw new Exception(
                    "Gagal mencatat transaksi: "
                    . mysqli_error($conn)
                );

            }

            // ==================================================
            // UBAH STATUS CHECKOUT
            // ==================================================

            $update_pembelian = mysqli_query(
                $conn,
                "UPDATE pembelian

                 SET status = 'disetujui'

                 WHERE id_pembelian = $id
                 AND status = 'pending'"
            );

            if (!$update_pembelian) {

                throw new Exception(
                    "Gagal mengubah status checkout: "
                    . mysqli_error($conn)
                );

            }

            if (mysqli_affected_rows($conn) !== 1) {

                throw new Exception(
                    "Checkout sudah diproses atau tidak ditemukan."
                );

            }

            // ==================================================
            // SEMUA BERHASIL
            // ==================================================

            mysqli_commit($conn);

            header(
                "Location: persetujuan_checkout.php?pesan=disetujui"
            );

            exit;

        } catch (Exception $e) {

            mysqli_rollback($conn);

            header(
                "Location: persetujuan_checkout.php?error="
                . urlencode($e->getMessage())
            );

            exit;
        }
    }

    // ==================================================
    // TOLAK CHECKOUT
    // ==================================================

    if ($aksi === 'tolak') {

        $update = mysqli_query(
            $conn,
            "UPDATE pembelian

             SET status = 'ditolak'

             WHERE id_pembelian = $id
             AND status = 'pending'"
        );

        if ($update && mysqli_affected_rows($conn) === 1) {

            header(
                "Location: persetujuan_checkout.php?pesan=ditolak"
            );

            exit;
        }

        header(
            "Location: persetujuan_checkout.php?error="
            . urlencode(
                "Checkout tidak ditemukan atau sudah diproses."
            )
        );

        exit;
    }
}

// ==================================================
// AMBIL CHECKOUT PENDING
// ==================================================

$query = mysqli_query(
    $conn,
    "SELECT
        p.id_pembelian,
        p.id_user,
        p.id_sampah,
        p.berat_kg,
        p.harga_per_kg,
        p.total_rp,
        p.tanggal,
        p.status,

        u.nama,
        u.username,

        s.jenis_sampah,
        s.stok_kg

     FROM pembelian p

     INNER JOIN users u
        ON u.id_user = p.id_user

     INNER JOIN sampah s
        ON s.id_sampah = p.id_sampah

     WHERE p.status = 'pending'

     ORDER BY p.id_pembelian DESC"
);

if (!$query) {

    die(
        "Gagal mengambil data checkout: "
        . mysqli_error($conn)
    );
}

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0"

>

<title>Persetujuan Checkout - EcoBank</title>

<!-- BOOTSTRAP -->

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<!-- BOOTSTRAP ICON -->

<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    background: #f5f6f8;

    color: #111;

    font-family:
        Arial,
        Helvetica,
        sans-serif;
}

.page-wrapper {

    max-width: 1450px;

    margin: auto;
}

/* ==================================================
   HEADER
================================================== */

.topbar {

    background: #111;

    color: white;

    border-radius: 18px;

    padding: 25px 30px;

    margin-bottom: 25px;

    box-shadow:
        0 10px 30px
        rgba(0,0,0,.08);
}

.topbar h2 {

    margin: 0;

    font-weight: 700;
}

.topbar p {

    margin: 7px 0 0;

    color: #cfcfcf;
}

.btn-dashboard {

    background: white;

    color: #111;

    border: none;

    border-radius: 10px;

    padding: 10px 18px;

    font-weight: 600;

    text-decoration: none;

    display: inline-block;
}

.btn-dashboard:hover {

    background: #e9e9e9;

    color: #000;
}

/* ==================================================
   MAIN CARD
================================================== */

.main-card {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 18px;

    box-shadow:
        0 10px 30px
        rgba(0,0,0,.06);

    overflow: hidden;
}

.card-header-custom {

    background: white;

    padding: 22px 25px;

    border-bottom:
        1px solid #e8e8e8;
}

.card-header-custom h5 {

    margin: 0;

    font-weight: 700;
}

/* ==================================================
   TABLE
================================================== */

.table {

    margin-bottom: 0;
}

.table thead th {

    background: #111;

    color: white;

    border: none;

    padding: 15px;

    white-space: nowrap;

    font-size: 12px;
}

.table tbody td {

    padding: 15px;

    vertical-align: middle;

    border-color: #ededed;

    font-size: 13px;
}

.table tbody tr:hover {

    background: #fafafa;
}

/* ==================================================
   USER
================================================== */

.user-name {

    font-weight: 600;
}

.username {

    color: #888;

    font-size: 12px;
}

/* ==================================================
   TOTAL
================================================== */

.total-price {

    font-weight: 700;

    color: #111;
}

/* ==================================================
   BUTTON
================================================== */

.btn-action {

    border-radius: 9px;

    font-weight: 600;

    padding: 7px 13px;

    margin-right: 4px;

    font-size: 12px;

    text-decoration: none;

    display: inline-block;
}

.btn-setujui {

    background: #111;

    color: white;

    border: none;
}

.btn-setujui:hover {

    background: #333;

    color: white;
}

.btn-tolak {

    background: white;

    color: #111;

    border: 1px solid #111;
}

.btn-tolak:hover {

    background: #111;

    color: white;
}

/* ==================================================
   ALERT
================================================== */

.alert {

    border: none;

    border-radius: 12px;
}

/* ==================================================
   EMPTY
================================================== */

.empty-state {

    padding: 60px !important;

    color: #777;
}

/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 768px) {

    .topbar {

        padding: 20px;
    }

    .header-content {

        flex-direction: column;

        align-items: flex-start !important;

        gap: 15px;
    }

    .table {

        font-size: 13px;
    }

}

</style>

</head>

<body>

<div class="container-fluid py-4">

<div class="page-wrapper">

<!-- ==================================================
     HEADER
================================================== -->

<div class="topbar">

```
<div class="header-content d-flex justify-content-between align-items-center">

    <div>

        <h2>

            <i class="bi bi-cart-check me-2"></i>

            Persetujuan Checkout

        </h2>

        <p>

            Kelola pembelian sampah oleh pengepul

        </p>

    </div>

    <a
        href="dashboard.php"
        class="btn-dashboard"
    >

        <i class="bi bi-arrow-left me-1"></i>

        Dashboard

    </a>

</div>
```

</div>

<!-- ==================================================
     PESAN SUKSES
================================================== -->

<?php if (isset($_GET['pesan'])): ?>

```
<?php if ($_GET['pesan'] === 'disetujui'): ?>

    <div class="alert alert-dark">

        <i class="bi bi-check-circle me-2"></i>

        <strong>Checkout berhasil disetujui.</strong>

        Stok sampah dan saldo pengepul telah diperbarui.

    </div>

<?php elseif ($_GET['pesan'] === 'ditolak'): ?>

    <div class="alert alert-secondary">

        <i class="bi bi-x-circle me-2"></i>

        <strong>Checkout berhasil ditolak.</strong>

        Stok dan saldo tidak berubah.

    </div>

<?php endif; ?>
```

<?php endif; ?>

<!-- ==================================================
     ERROR
================================================== -->

<?php if (isset($_GET['error'])): ?>

```
<div class="alert alert-danger">

    <i class="bi bi-exclamation-triangle me-2"></i>

    <?= htmlspecialchars($_GET['error']); ?>

</div>
```

<?php endif; ?>

<!-- ==================================================
     TABLE
================================================== -->

<div class="main-card">

```
<div class="card-header-custom">

    <div class="d-flex justify-content-between align-items-center">

        <div>

            <h5>

                Checkout Menunggu Persetujuan

            </h5>

            <small class="text-secondary">

                Periksa pembelian sebelum diproses.

            </small>

        </div>

        <span class="badge bg-dark rounded-pill px-3 py-2">

            <?= number_format(mysqli_num_rows($query)); ?>

            Pending

        </span>

    </div>

</div>

<div class="table-responsive">

    <table class="table align-middle">

        <thead>

            <tr>

                <th>Pengepul</th>

                <th>Jenis Sampah</th>

                <th>Berat</th>

                <th>Harga / Kg</th>

                <th>Total</th>

                <th>Tanggal</th>

                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

        <?php if (mysqli_num_rows($query) > 0): ?>

            <?php while ($row = mysqli_fetch_assoc($query)): ?>

                <tr>

                    <!-- PENGEPUL -->

                    <td>

                        <div class="user-name">

                            <?= htmlspecialchars(
                                $row['nama']
                            ); ?>

                        </div>

                        <div class="username">

                            @<?= htmlspecialchars(
                                $row['username']
                            ); ?>

                        </div>

                    </td>

                    <!-- JENIS SAMPAH -->

                    <td>

                        <strong>

                            <?= htmlspecialchars(
                                $row['jenis_sampah']
                            ); ?>

                        </strong>

                        <div class="text-secondary small">

                            Stok:

                            <?= number_format(
                                $row['stok_kg'],
                                2,
                                ',',
                                '.'
                            ); ?>

                            Kg

                        </div>

                    </td>

                    <!-- BERAT -->

                    <td>

                        <?= number_format(
                            $row['berat_kg'],
                            2,
                            ',',
                            '.'
                        ); ?>

                        Kg

                    </td>

                    <!-- HARGA -->

                    <td>

                        Rp

                        <?= number_format(
                            $row['harga_per_kg'],
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>

                    <!-- TOTAL -->

                    <td>

                        <span class="total-price">

                            Rp

                            <?= number_format(
                                $row['total_rp'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </span>

                    </td>

                    <!-- TANGGAL -->

                    <td>

                        <?= htmlspecialchars(
                            $row['tanggal']
                        ); ?>

                    </td>

                    <!-- AKSI -->

                    <td>

                        <!--
                            PENTING:
                            href HARUS SATU BARIS.
                            Jangan diberi spasi/newline.
                        -->

                        <a
                            href="persetujuan_checkout.php?aksi=setujui&id=<?= (int) $row['id_pembelian']; ?>"
                            class="btn btn-action btn-setujui"
                            onclick="return confirm('Setujui checkout ini?\n\nSaldo pengepul akan dikurangi dan stok sampah akan diperbarui.');"
                        >

                            <i class="bi bi-check-lg"></i>

                            Setujui

                        </a>

                        <a
                            href="persetujuan_checkout.php?aksi=tolak&id=<?= (int) $row['id_pembelian']; ?>"
                            class="btn btn-action btn-tolak"
                            onclick="return confirm('Tolak checkout ini?');"
                        >

                            <i class="bi bi-x-lg"></i>

                            Tolak

                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>

                <td
                    colspan="7"
                    class="text-center empty-state"
                >

                    <div
                        style="
                            font-size:50px;
                            margin-bottom:10px;
                        "
                    >

                        <i class="bi bi-cart-x"></i>

                    </div>

                    <strong>

                        Tidak ada checkout pending

                    </strong>

                    <br>

                    <small>

                        Checkout dari pengepul yang
                        menunggu persetujuan admin
                        akan muncul di sini.

                    </small>

                </td>

            </tr>

        <?php endif; ?>

        </tbody>

    </table>

</div>
```

</div>

<!-- ==================================================
     FOOTER
================================================== -->

<div class="text-center mt-4 text-secondary">

```
<small>

    EcoBank • Sistem Bank Sampah

</small>
```

</div>

</div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
