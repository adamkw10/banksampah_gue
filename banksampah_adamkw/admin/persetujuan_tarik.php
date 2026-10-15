<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("admin");


// ==================================================
// PROSES TERIMA
// ==================================================

if (isset($_GET['terima'])) {

    $id_tarik = (int) $_GET['terima'];

    mysqli_begin_transaction($conn);

    try {

        // Ambil data tarik dan kunci baris
        $query = mysqli_query(
            $conn,
            "SELECT *
             FROM tarik_saldo
             WHERE id_tarik = '$id_tarik'
             FOR UPDATE"
        );

        $tarik = mysqli_fetch_assoc($query);

        if (!$tarik) {
            throw new Exception(
                "Pengajuan tarik saldo tidak ditemukan."
            );
        }

        // Pastikan masih pending
        if ($tarik['status'] != 'pending') {
            throw new Exception(
                "Pengajuan ini sudah diproses."
            );
        }

        // Ambil saldo user
        $id_user = (int) $tarik['id_user'];

        $query_user = mysqli_query(
            $conn,
            "SELECT saldo
             FROM users
             WHERE id_user = '$id_user'
             FOR UPDATE"
        );

        $user = mysqli_fetch_assoc($query_user);

        if (!$user) {
            throw new Exception(
                "User tidak ditemukan."
            );
        }

        $saldo = (int) $user['saldo'];
        $nominal = (int) $tarik['nominal'];

        // Pastikan saldo cukup
        if ($saldo < $nominal) {
            throw new Exception(
                "Saldo user tidak mencukupi."
            );
        }

        // Kurangi saldo
        $update_saldo = mysqli_query(
            $conn,
            "UPDATE users
             SET saldo = saldo - '$nominal'
             WHERE id_user = '$id_user'"
        );

        if (!$update_saldo) {
            throw new Exception(
                "Gagal mengurangi saldo user."
            );
        }

        // Ubah status
        $update_status = mysqli_query(
            $conn,
            "UPDATE tarik_saldo
             SET status = 'diterima'
             WHERE id_tarik = '$id_tarik'"
        );

        if (!$update_status) {
            throw new Exception(
                "Gagal mengubah status pengajuan."
            );
        }

        mysqli_commit($conn);

        header(
            "Location: persetujuan_tarik.php?pesan=diterima"
        );
        exit;

    } catch (Exception $e) {

        mysqli_rollback($conn);

        header(
            "Location: persetujuan_tarik.php?pesan=" .
            urlencode($e->getMessage())
        );
        exit;
    }
}


// ==================================================
// PROSES TOLAK
// ==================================================

if (isset($_GET['tolak'])) {

    $id_tarik = (int) $_GET['tolak'];

    $query = mysqli_query(
        $conn,
        "SELECT *
         FROM tarik_saldo
         WHERE id_tarik = '$id_tarik'"
    );

    $tarik = mysqli_fetch_assoc($query);

    if (!$tarik) {

        header(
            "Location: persetujuan_tarik.php?pesan=tidakada"
        );
        exit;
    }

    if ($tarik['status'] != 'pending') {

        header(
            "Location: persetujuan_tarik.php?pesan=sudahdiproses"
        );
        exit;
    }

    $update = mysqli_query(
        $conn,
        "UPDATE tarik_saldo
         SET status = 'ditolak'
         WHERE id_tarik = '$id_tarik'"
    );

    if ($update) {

        header(
            "Location: persetujuan_tarik.php?pesan=ditolak"
        );
        exit;

    } else {

        header(
            "Location: persetujuan_tarik.php?pesan=gagal"
        );
        exit;
    }
}


// ==================================================
// AMBIL DATA TARIK SALDO
// ==================================================

$query_tarik = mysqli_query(
    $conn,
    "SELECT
        tarik_saldo.*,
        users.nama,
        users.username,
        users.saldo

     FROM tarik_saldo

     LEFT JOIN users
        ON tarik_saldo.id_user = users.id_user

     ORDER BY tarik_saldo.id_tarik DESC"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Persetujuan Tarik Saldo - EcoBank
</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>

    body {
        background: #f5f5f5;
        color: #111;
        font-family: Arial, sans-serif;
    }

    .page-wrapper {
        max-width: 1450px;
        margin: auto;
    }

    /* ===============================
       HEADER
    =============================== */

    .topbar {
        background: #111;
        color: white;
        border-radius: 18px;
        padding: 25px 30px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
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
    }

    .btn-dashboard:hover {
        background: #e9e9e9;
        color: #000;
    }


    /* ===============================
       CARD
    =============================== */

    .main-card {
        background: white;
        border: none;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .card-header-custom {
        background: white;
        padding: 22px 25px;
        border-bottom: 1px solid #e8e8e8;
    }

    .card-header-custom h5 {
        margin: 0;
        font-weight: 700;
    }


    /* ===============================
       TABLE
    =============================== */

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #111;
        color: white;
        border: none;
        padding: 15px;
        white-space: nowrap;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #ededed;
    }

    .table tbody tr:hover {
        background: #fafafa;
    }


    /* ===============================
       USER
    =============================== */

    .user-name {
        font-weight: 600;
    }

    .username {
        color: #888;
        font-size: 12px;
    }


    /* ===============================
       STATUS
    =============================== */

    .badge-status {
        display: inline-block;
        padding: 7px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending {
        background: #eeeeee;
        color: #111;
    }

    .status-diterima {
        background: #111;
        color: white;
    }

    .status-ditolak {
        background: #dc3545;
        color: white;
    }


    /* ===============================
       BUTTON
    =============================== */

    .btn-action {
        border-radius: 9px;
        font-weight: 600;
        padding: 7px 13px;
        margin-right: 4px;
    }

    .btn-terima {
        background: #111;
        color: white;
        border: none;
    }

    .btn-terima:hover {
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


    /* ===============================
       ALERT
    =============================== */

    .alert {
        border: none;
        border-radius: 12px;
    }


    /* ===============================
       EMPTY
    =============================== */

    .empty-state {
        padding: 55px !important;
        color: #777;
    }


    /* ===============================
       RESPONSIVE
    =============================== */

    @media (max-width: 768px) {

        .topbar {
            padding: 20px;
        }

        .topbar .header-content {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }

        .table {
            font-size: 13px;
        }

    }

</style>
```

</head>

<body>

<div class="container-fluid py-4">

<div class="page-wrapper">

```
<!-- =========================================
     HEADER
========================================== -->

<div class="topbar">

    <div
        class="header-content d-flex justify-content-between align-items-center"
    >

        <div>

            <h2>
                Persetujuan Tarik Saldo 💸
            </h2>

            <p>
                Kelola pengajuan penarikan saldo siswa dan guru
            </p>

        </div>


        <a
            href="dashboard.php"
            class="btn btn-dashboard"
        >
            ← Dashboard
        </a>

    </div>

</div>


<!-- =========================================
     PESAN
========================================== -->

<?php if (isset($_GET['pesan'])) : ?>

    <?php if ($_GET['pesan'] == 'diterima') : ?>

        <div class="alert alert-dark">

            ✓ Pengajuan tarik saldo berhasil diterima.
            Saldo user telah dikurangi.

        </div>

    <?php elseif ($_GET['pesan'] == 'ditolak') : ?>

        <div class="alert alert-secondary">

            Pengajuan tarik saldo berhasil ditolak.
            Saldo user tidak berubah.

        </div>

    <?php elseif ($_GET['pesan'] == 'sudahdiproses') : ?>

        <div class="alert alert-danger">

            Pengajuan tersebut sudah diproses sebelumnya.

        </div>

    <?php elseif ($_GET['pesan'] == 'tidakada') : ?>

        <div class="alert alert-danger">

            Pengajuan tarik saldo tidak ditemukan.

        </div>

    <?php elseif ($_GET['pesan'] == 'gagal') : ?>

        <div class="alert alert-danger">

            Terjadi kesalahan saat memproses pengajuan.

        </div>

    <?php else : ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_GET['pesan']); ?>

        </div>

    <?php endif; ?>

<?php endif; ?>


<!-- =========================================
     TABLE CARD
========================================== -->

<div class="main-card">

    <div class="card-header-custom">

        <div
            class="d-flex justify-content-between align-items-center"
        >

            <div>

                <h5>
                    Daftar Pengajuan Tarik Saldo
                </h5>

                <small class="text-secondary">

                    Periksa dan proses permintaan penarikan saldo

                </small>

            </div>


            <span class="badge bg-dark rounded-pill px-3 py-2">

                <?= mysqli_num_rows($query_tarik); ?>

                Pengajuan

            </span>

        </div>

    </div>


    <div class="table-responsive">

        <table class="table align-middle">

            <thead>

                <tr>

                    <th>
                        Tanggal
                    </th>

                    <th>
                        User
                    </th>

                    <th>
                        Saldo Saat Ini
                    </th>

                    <th>
                        Nominal Tarik
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if (mysqli_num_rows($query_tarik) > 0) : ?>


                <?php while ($row = mysqli_fetch_assoc($query_tarik)) : ?>


                    <tr>


                        <!-- TANGGAL -->

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $row['tanggal']
                                ); ?>
                            </strong>

                        </td>


                        <!-- USER -->

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


                        <!-- SALDO -->

                        <td>

                            <strong>

                                <?= rupiah(
                                    $row['saldo']
                                ); ?>

                            </strong>

                        </td>


                        <!-- NOMINAL -->

                        <td>

                            <strong>

                                <?= rupiah(
                                    $row['nominal']
                                ); ?>

                            </strong>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php if ($row['status'] == 'pending') : ?>

                                <span class="badge-status status-pending">

                                    Pending

                                </span>

                            <?php elseif ($row['status'] == 'diterima') : ?>

                                <span class="badge-status status-diterima">

                                    Diterima

                                </span>

                            <?php else : ?>

                                <span class="badge-status status-ditolak">

                                    Ditolak

                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- AKSI -->

                        <td>

                            <?php if ($row['status'] == 'pending') : ?>


                                <a
                                    href="persetujuan_tarik.php?terima=<?= $row['id_tarik']; ?>"
                                    class="btn btn-action btn-terima"
                                    onclick="return confirm('Terima penarikan ini? Saldo user akan dikurangi.')"
                                >

                                    ✓ Terima

                                </a>


                                <a
                                    href="persetujuan_tarik.php?tolak=<?= $row['id_tarik']; ?>"
                                    class="btn btn-action btn-tolak"
                                    onclick="return confirm('Tolak pengajuan tarik saldo ini?')"
                                >

                                    ✕ Tolak

                                </a>


                            <?php else : ?>


                                <span class="text-secondary small">

                                    Sudah diproses

                                </span>


                            <?php endif; ?>

                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else : ?>


                <tr>

                    <td
                        colspan="6"
                        class="text-center empty-state"
                    >

                        <div style="font-size:42px;">
                            💸
                        </div>

                        <strong>
                            Belum ada pengajuan tarik saldo
                        </strong>

                        <br>

                        <small>
                            Pengajuan penarikan dari siswa atau guru
                            akan muncul di sini.
                        </small>

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>

</div>


<!-- FOOTER -->

<div class="text-center mt-4 text-secondary">

    <small>
        EcoBank • Sistem Bank Sampah
    </small>

</div>
```

</div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
