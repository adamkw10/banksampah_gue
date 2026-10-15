
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("admin");


// ==================================================
// PROSES TERIMA SETORAN
// ==================================================

if (isset($_GET['terima'])) {

    $id_pengajuan = (int) $_GET['terima'];

    if ($id_pengajuan <= 0) {
        header("Location: persetujuan_setor.php?pesan=tidakada");
        exit;
    }

    mysqli_begin_transaction($conn);

    try {

        // 1. Ambil dan kunci pengajuan
        $stmt = mysqli_prepare(
            $conn,
            "SELECT *
             FROM pengajuan_setor
             WHERE id_pengajuan = ?
             FOR UPDATE"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $pengajuan = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$pengajuan) {
            throw new Exception("tidakada");
        }

        if ($pengajuan['status'] !== 'pending') {
            throw new Exception("sudahdiproses");
        }

        $id_user = (int) $pengajuan['id_user'];
        $id_sampah = (int) $pengajuan['id_sampah'];
        $total_rp = (int) $pengajuan['total_rp'];
        $berat_kg = (float) $pengajuan['berat_kg'];

        if ($total_rp <= 0 || $berat_kg <= 0) {
            throw new Exception("nominaltidakvalid");
        }


        // 2. Pastikan penyetor adalah siswa atau guru
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id_user
             FROM users
             WHERE id_user = ?
               AND role IN ('siswa', 'guru')
             FOR UPDATE"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_user);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $penyetor = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$penyetor) {
            throw new Exception("penyetortidakvalid");
        }


        // 3. Ambil akun admin dan kunci saldonya
        $result_admin = mysqli_query(
            $conn,
            "SELECT id_user, saldo
             FROM users
             WHERE role = 'admin'
             ORDER BY id_user ASC
             LIMIT 1
             FOR UPDATE"
        );

        if (!$result_admin) {
            throw new Exception("Gagal mengambil data admin.");
        }

        $admin = mysqli_fetch_assoc($result_admin);

        if (!$admin) {
            throw new Exception("admin tidak ditemukan");
        }

        $id_admin = (int) $admin['id_user'];
        $saldo_admin = (int) $admin['saldo'];

        if ($id_admin === $id_user) {
            throw new Exception("penyetortidakvalid");
        }


        // 4. Periksa saldo admin
        if ($saldo_admin < $total_rp) {

            error_log(
                "Saldo admin kurang. ID admin: " .
                $id_admin .
                ", saldo: " .
                $saldo_admin .
                ", total setoran: " .
                $total_rp
            );

            throw new Exception("saldoadminkurang");
        }


        // 5. Kurangi saldo admin
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET saldo = saldo - ?
             WHERE id_user = ?
               AND saldo >= ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iii",
            $total_rp,
            $id_admin,
            $total_rp
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Gagal mengurangi saldo admin.");
        }

        $berhasil = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($berhasil !== 1) {
            throw new Exception("saldoadminkurang");
        }


        // 6. Tambahkan saldo siswa/guru
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE users
             SET saldo = saldo + ?
             WHERE id_user = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ii",
            $total_rp,
            $id_user
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Gagal menambahkan saldo penyetor.");
        }

        if (mysqli_stmt_affected_rows($stmt) !== 1) {
            mysqli_stmt_close($stmt);
            throw new Exception("Penyetor tidak ditemukan.");
        }

        mysqli_stmt_close($stmt);


        // 7. Tambahkan stok sampah
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE sampah
             SET stok_kg = stok_kg + ?
             WHERE id_sampah = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "di",
            $berat_kg,
            $id_sampah
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Gagal memperbarui stok sampah.");
        }

        if (mysqli_stmt_affected_rows($stmt) !== 1) {
            mysqli_stmt_close($stmt);
            throw new Exception("Data sampah tidak ditemukan.");
        }

        mysqli_stmt_close($stmt);


        // 8. Catat transaksi setoran
        $tanggal = $pengajuan['tanggal'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO transaksi
                (id_user, id_sampah, tanggal, tipe, berat_kg, total_rp)
             VALUES (?, ?, ?, 'setor', ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iisdi",
            $id_user,
            $id_sampah,
            $tanggal,
            $berat_kg,
            $total_rp
        );

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Gagal mencatat transaksi.");
        }

        mysqli_stmt_close($stmt);


        // 9. Ubah status menjadi diterima
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pengajuan_setor
             SET status = 'diterima'
             WHERE id_pengajuan = ?
               AND status = 'pending'"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);

        if (!mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            throw new Exception("Gagal mengubah status pengajuan.");
        }

        if (mysqli_stmt_affected_rows($stmt) !== 1) {
            mysqli_stmt_close($stmt);
            throw new Exception("sudahdiproses");
        }

        mysqli_stmt_close($stmt);

        // 10. Simpan seluruh perubahan
        mysqli_commit($conn);

        header("Location: persetujuan_setor.php?pesan=diterima");
        exit;

    } catch (Throwable $e) {

        mysqli_rollback($conn);

        $error = $e->getMessage();

        if ($error === "tidakada") {
            header("Location: persetujuan_setor.php?pesan=tidakada");
            exit;
        }

        if ($error === "sudahdiproses") {
            header("Location: persetujuan_setor.php?pesan=sudahdiproses");
            exit;
        }

        if ($error === "saldoadminkurang") {
            header("Location: persetujuan_setor.php?pesan=saldokurang");
            exit;
        }

        error_log("Persetujuan setoran gagal: " . $error);

        header("Location: persetujuan_setor.php?pesan=gagal");
        exit;
    }
}


// ==================================================
// PROSES TOLAK SETORAN
// ==================================================

if (isset($_GET['tolak'])) {

    $id_pengajuan = (int) $_GET['tolak'];

    if ($id_pengajuan <= 0) {
        header("Location: persetujuan_setor.php?pesan=tidakada");
        exit;
    }

    mysqli_begin_transaction($conn);

    try {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE pengajuan_setor
             SET status = 'ditolak'
             WHERE id_pengajuan = ?
               AND status = 'pending'"
        );

        mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
        mysqli_stmt_execute($stmt);

        $berhasil = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($berhasil !== 1) {

            $stmt = mysqli_prepare(
                $conn,
                "SELECT id_pengajuan
                 FROM pengajuan_setor
                 WHERE id_pengajuan = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $id_pengajuan);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $ada = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$ada) {
                throw new Exception("tidakada");
            }

            throw new Exception("sudahdiproses");
        }

        mysqli_commit($conn);

        header("Location: persetujuan_setor.php?pesan=ditolak");
        exit;

    } catch (Throwable $e) {

        mysqli_rollback($conn);

        if ($e->getMessage() === "tidakada") {
            header("Location: persetujuan_setor.php?pesan=tidakada");
            exit;
        }

        if ($e->getMessage() === "sudahdiproses") {
            header("Location: persetujuan_setor.php?pesan=sudahdiproses");
            exit;
        }

        error_log("Penolakan setoran gagal: " . $e->getMessage());

        header("Location: persetujuan_setor.php?pesan=gagal");
        exit;
    }
}


// ==================================================
// AMBIL DATA PENGAJUAN
// ==================================================

$query_pengajuan = mysqli_query(
    $conn,
    "SELECT
        pengajuan_setor.*,
        users.nama,
        users.username,
        sampah.jenis_sampah,
        sampah.harga_per_kg
     FROM pengajuan_setor
     LEFT JOIN users
        ON pengajuan_setor.id_user = users.id_user
     LEFT JOIN sampah
        ON pengajuan_setor.id_sampah = sampah.id_sampah
     ORDER BY pengajuan_setor.id_pengajuan DESC"
);

if (!$query_pengajuan) {
    die("Gagal mengambil data pengajuan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Persetujuan Setor - EcoBank</title>

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
            margin: 6px 0 0;
            color: #cfcfcf;
        }

        .btn-dashboard {
            background: white;
            color: #111;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 18px;
        }

        .btn-dashboard:hover {
            background: #e9e9e9;
            color: #000;
        }

        .main-card {
            background: white;
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,.06);
            overflow: hidden;
        }

        .card-header-custom {
            padding: 22px 25px;
            border-bottom: 1px solid #e8e8e8;
            background: white;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 700;
        }

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

        .user-name {
            font-weight: 600;
        }

        .username {
            color: #888;
            font-size: 12px;
        }

        .badge-status {
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

        .btn-action {
            border-radius: 9px;
            font-weight: 600;
            padding: 7px 13px;
            margin-right: 4px;
            margin-bottom: 4px;
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

        .empty-state {
            padding: 50px !important;
            color: #777;
        }

        .alert {
            border: none;
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .topbar {
                padding: 20px;
            }

            .topbar .d-flex {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start !important;
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

        <div class="topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Persetujuan Setor ♻️</h2>
                    <p>Kelola pengajuan setor siswa dan guru</p>
                </div>

                <a href="dashboard.php" class="btn btn-dashboard">
                    ← Dashboard
                </a>
            </div>
        </div>


        <!-- PESAN -->

        <?php if (isset($_GET['pesan'])) : ?>

            <?php if ($_GET['pesan'] == 'diterima') : ?>

                <div class="alert alert-success">
                    ✓ Pengajuan berhasil diterima. Saldo admin berkurang,
                    saldo penyetor bertambah, dan stok telah diperbarui.
                </div>

            <?php elseif ($_GET['pesan'] == 'ditolak') : ?>

                <div class="alert alert-secondary">
                    Pengajuan berhasil ditolak. Saldo tidak berubah.
                </div>

            <?php elseif ($_GET['pesan'] == 'sudahdiproses') : ?>

                <div class="alert alert-danger">
                    Pengajuan sudah diproses sebelumnya.
                </div>

            <?php elseif ($_GET['pesan'] == 'tidakada') : ?>

                <div class="alert alert-danger">
                    Data pengajuan tidak ditemukan.
                </div>

            <?php elseif ($_GET['pesan'] == 'saldokurang') : ?>

                <div class="alert alert-danger">
                    Saldo admin tidak mencukupi untuk membayar setoran.
                    Saldo admin dan saldo penyetor tidak diubah.
                </div>

            <?php elseif ($_GET['pesan'] == 'gagal') : ?>

                <div class="alert alert-danger">
                    Setoran gagal diproses. Periksa log aplikasi dan
                    struktur database.
                </div>

            <?php endif; ?>

        <?php endif; ?>


        <!-- TABEL PENGAJUAN -->

        <div class="main-card">

            <div class="card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5>Daftar Pengajuan Setor</h5>
                        <small class="text-secondary">
                            Periksa dan proses pengajuan yang masuk
                        </small>
                    </div>

                    <span class="badge bg-dark rounded-pill px-3 py-2">
                        <?= mysqli_num_rows($query_pengajuan); ?>
                        Pengajuan
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Sampah</th>
                            <th>Berat</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (mysqli_num_rows($query_pengajuan) > 0) : ?>

                        <?php while ($row = mysqli_fetch_assoc($query_pengajuan)) : ?>

                            <tr>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['tanggal'] ?? ''); ?>
                                    </strong>
                                </td>

                                <td>
                                    <div class="user-name">
                                        <?= htmlspecialchars($row['nama'] ?? '-'); ?>
                                    </div>
                                    <div class="username">
                                        @<?= htmlspecialchars($row['username'] ?? '-'); ?>
                                    </div>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['jenis_sampah'] ?? '-'); ?>
                                    </strong>
                                    <br>
                                    <small class="text-secondary">
                                        <?= rupiah($row['harga_per_kg'] ?? 0); ?>/Kg
                                    </small>
                                </td>

                                <td>
                                    <?= number_format(
                                        (float) $row['berat_kg'],
                                        2,
                                        ',',
                                        '.'
                                    ); ?>
                                    Kg
                                </td>

                                <td>
                                    <strong>
                                        <?= rupiah($row['total_rp']); ?>
                                    </strong>
                                </td>

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

                                <td>
                                    <?php if ($row['status'] == 'pending') : ?>

                                        <a
                                            href="persetujuan_setor.php?terima=<?= (int) $row['id_pengajuan']; ?>"
                                            class="btn btn-action btn-terima"
                                            onclick="return confirm('Terima setoran ini? Saldo admin akan berkurang sesuai total setoran.')"
                                        >
                                            ✓ Terima
                                        </a>

                                        <a
                                            href="persetujuan_setor.php?tolak=<?= (int) $row['id_pengajuan']; ?>"
                                            class="btn btn-action btn-tolak"
                                            onclick="return confirm('Tolak pengajuan setor ini?')"
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
                            <td colspan="7" class="text-center empty-state">
                                <div style="font-size:40px;">📭</div>
                                <strong>Belum ada pengajuan setor</strong>
                                <br>
                                <small>
                                    Pengajuan dari siswa atau guru akan muncul di sini.
                                </small>
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-4 text-secondary">
            <small>EcoBank • Sistem Bank Sampah</small>
        </div>

    </div>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>
</html>
