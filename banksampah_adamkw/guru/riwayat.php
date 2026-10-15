
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("guru");

$id_user = (int) $_SESSION['id_user'];


// =========================================
// AMBIL RIWAYAT SETOR GURU
// =========================================

$query = mysqli_query(
    $conn,
    "SELECT
        t.id_transaksi,
        t.tanggal,
        t.berat_kg,
        t.total_rp,
        s.jenis_sampah,
        s.harga_per_kg
     FROM transaksi t
     LEFT JOIN sampah s
        ON s.id_sampah = t.id_sampah
     WHERE t.id_user = '$id_user'
     AND t.tipe = 'setor'
     ORDER BY t.id_transaksi DESC"
);


// =========================================
// TOTAL SETORAN
// =========================================

$query_total = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS jumlah,
        COALESCE(SUM(berat_kg), 0) AS berat,
        COALESCE(SUM(total_rp), 0) AS total
     FROM transaksi
     WHERE id_user = '$id_user'
     AND tipe = 'setor'"
);

$total = mysqli_fetch_assoc($query_total);

$jumlah = (int) ($total['jumlah'] ?? 0);
$berat = (float) ($total['berat'] ?? 0);
$total_rp = (float) ($total['total'] ?? 0);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Setor Guru - EcoBank</title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

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

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f5f5;

            color: #111;

        }


        /* ======================================
           SIDEBAR
        ====================================== */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 250px;

            height: 100vh;

            background: #111;

            color: white;

            padding: 25px 18px;

            z-index: 1000;

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 10px 12px 30px;

            border-bottom: 1px solid #333;

            margin-bottom: 25px;

        }


        .brand-icon {

            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            background: white;

            color: #111;

            border-radius: 12px;

            font-size: 22px;

        }


        .brand-text {

            font-size: 20px;

            font-weight: 700;

        }


        .brand-text small {

            display: block;

            color: #aaa;

            font-size: 11px;

            font-weight: normal;

            margin-top: 2px;

        }


        .menu-title {

            color: #777;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding: 0 12px;

            margin-bottom: 10px;

        }


        .sidebar a {

            display: flex;

            align-items: center;

            gap: 12px;

            text-decoration: none;

            color: #bbb;

            padding: 13px 14px;

            border-radius: 10px;

            margin-bottom: 6px;

            transition: .2s;

        }


        .sidebar a:hover {

            background: #222;

            color: white;

        }


        .sidebar a.active {

            background: white;

            color: #111;

            font-weight: 600;

        }


        .sidebar a.logout {

            color: #ff8b8b;

            margin-top: 25px;

        }


        .sidebar a.logout:hover {

            background: #2a1515;

            color: #ffb0b0;

        }


        /* ======================================
           MAIN
        ====================================== */

        .main {

            margin-left: 250px;

            min-height: 100vh;

            padding: 35px;

        }


        /* ======================================
           HEADER
        ====================================== */

        .topbar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .page-title h2 {

            font-size: 28px;

            font-weight: 700;

            margin: 0;

        }


        .page-title p {

            margin: 5px 0 0;

            color: #777;

        }


        .user-badge {

            display: flex;

            align-items: center;

            gap: 10px;

            background: white;

            padding: 9px 15px;

            border-radius: 12px;

            box-shadow: 0 5px 20px rgba(0,0,0,.05);

        }


        .user-icon {

            width: 38px;
            height: 38px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #111;

            color: white;

        }


        /* ======================================
           STATISTIC CARD
        ====================================== */

        .stat-card {

            background: white;

            border: 1px solid #e8e8e8;

            border-radius: 16px;

            padding: 22px;

            height: 100%;

            transition: .2s;

        }


        .stat-card:hover {

            transform: translateY(-3px);

            box-shadow: 0 12px 30px rgba(0,0,0,.07);

        }


        .stat-icon {

            width: 45px;
            height: 45px;

            border-radius: 12px;

            background: #111;

            color: white;

            display: flex;

            align-items: center;
            justify-content: center;

            margin-bottom: 15px;

            font-size: 19px;

        }


        .stat-label {

            color: #777;

            font-size: 13px;

            margin-bottom: 5px;

        }


        .stat-value {

            font-size: 25px;

            font-weight: 700;

            margin: 0;

        }


        /* ======================================
           TABLE CARD
        ====================================== */

        .table-card {

            background: white;

            border: 1px solid #e8e8e8;

            border-radius: 16px;

            overflow: hidden;

            margin-top: 25px;

        }


        .table-header {

            padding: 20px 22px;

            border-bottom: 1px solid #eee;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .table-header h5 {

            margin: 0;

            font-weight: 700;

        }


        .table-header small {

            color: #888;

        }


        .table {

            margin: 0;

        }


        .table thead th {

            background: #111;

            color: white;

            border: none;

            padding: 14px;

            font-size: 13px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 14px;

            vertical-align: middle;

            border-color: #eee;

            font-size: 14px;

        }


        .table tbody tr:hover {

            background: #f8f8f8;

        }


        .badge-status {

            background: #111;

            color: white;

            padding: 7px 12px;

            border-radius: 20px;

            font-size: 11px;

        }


        /* ======================================
           BUTTON
        ====================================== */

        .btn-black {

            background: #111;

            color: white;

            border: none;

            border-radius: 9px;

            padding: 10px 16px;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            gap: 7px;

            font-weight: 600;

            transition: .2s;

        }


        .btn-black:hover {

            background: #333;

            color: white;

        }


        .btn-outline-black {

            background: white;

            color: #111;

            border: 1px solid #111;

            border-radius: 9px;

            padding: 9px 15px;

            text-decoration: none;

            display: inline-flex;

            align-items: center;

            gap: 7px;

            font-weight: 600;

            transition: .2s;

        }


        .btn-outline-black:hover {

            background: #111;

            color: white;

        }


        /* ======================================
           EMPTY STATE
        ====================================== */

        .empty-state {

            padding: 50px 20px;

            text-align: center;

            color: #777;

        }


        .empty-icon {

            width: 60px;
            height: 60px;

            margin: auto auto 15px;

            border-radius: 16px;

            background: #f1f1f1;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 25px;

            color: #555;

        }


        /* ======================================
           FOOTER
        ====================================== */

        .footer {

            margin-top: 30px;

            padding-top: 20px;

            border-top: 1px solid #ddd;

            color: #888;

            font-size: 12px;

        }


        /* ======================================
           RESPONSIVE
        ====================================== */

        @media (max-width: 768px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }


            .sidebar a {

                display: inline-flex;

                margin-right: 5px;

            }


            .main {

                margin-left: 0;

                padding: 20px;

            }


            .topbar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }

        }

    </style>

</head>


<body>


<!-- ==========================================
     SIDEBAR
=========================================== -->

<div class="sidebar">


    <div class="brand">

        <div class="brand-icon">

            <i class="bi bi-recycle"></i>

        </div>

        <div class="brand-text">

            EcoBank

            <small>
                Sistem Bank Sampah
            </small>

        </div>

    </div>


    <div class="menu-title">
        Menu Utama
    </div>


    <a href="dashboard.php">

        <i class="bi bi-grid"></i>

        Dashboard

    </a>


    <a href="../setor/index.php">

        <i class="bi bi-recycle"></i>

        Setor Sampah

    </a>


    <a href="../tarik/index.php">

        <i class="bi bi-wallet2"></i>

        Tarik Saldo

    </a>


    <div class="menu-title mt-4">
        Riwayat
    </div>


    <a
        href="riwayat.php"
        class="active"
    >

        <i class="bi bi-clock-history"></i>

        Riwayat Setor

    </a>


    <a href="riwayat_tarik.php">

        <i class="bi bi-receipt"></i>

        Riwayat Tarik

    </a>


    <a
        href="../logout.php"
        class="logout"
    >

        <i class="bi bi-box-arrow-right"></i>

        Logout

    </a>


</div>



<!-- ==========================================
     MAIN
=========================================== -->

<div class="main">


    <!-- HEADER -->

    <div class="topbar">


        <div class="page-title">

            <h2>
                Riwayat Setor
            </h2>

            <p>
                Lihat seluruh aktivitas setoran sampah kamu.
            </p>

        </div>


        <div class="user-badge">

            <div class="user-icon">

                <i class="bi bi-person"></i>

            </div>

            <div>

                <strong>
                    <?= htmlspecialchars($_SESSION['nama']); ?>
                </strong>

                <small class="d-block text-secondary">
                    Guru
                </small>

            </div>

        </div>


    </div>



    <!-- ======================================
         STATISTIK
    ======================================= -->

    <div class="row g-4">


        <!-- JUMLAH -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-recycle"></i>

                </div>


                <div class="stat-label">
                    Total Setoran
                </div>


                <div class="stat-value">

                    <?= number_format($jumlah); ?>

                </div>

            </div>

        </div>



        <!-- BERAT -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-box-seam"></i>

                </div>


                <div class="stat-label">
                    Total Berat
                </div>


                <div class="stat-value">

                    <?= number_format(
                        $berat,
                        2,
                        ',',
                        '.'
                    ); ?>

                    <span class="fs-6 text-secondary">
                        Kg
                    </span>

                </div>

            </div>

        </div>



        <!-- PENDAPATAN -->

        <div class="col-md-4">

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-cash-stack"></i>

                </div>


                <div class="stat-label">
                    Total Pendapatan
                </div>


                <div class="stat-value">

                    <?= rupiah($total_rp); ?>

                </div>

            </div>

        </div>


    </div>



    <!-- ======================================
         TABLE
    ======================================= -->

    <div class="table-card">


        <div class="table-header">


            <div>

                <h5>
                    Daftar Setoran
                </h5>

                <small>
                    Semua transaksi setor yang telah diterima.
                </small>

            </div>


            <a
                href="../setor/index.php"
                class="btn-black"
            >

                <i class="bi bi-plus-lg"></i>

                Setor Sampah

            </a>


        </div>



        <div class="table-responsive">


            <table class="table align-middle">


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Jenis Sampah
                        </th>

                        <th>
                            Harga / Kg
                        </th>

                        <th>
                            Berat
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php if (
                    $query &&
                    mysqli_num_rows($query) > 0
                ): ?>


                    <?php

                    $no = 1;

                    while (
                        $row = mysqli_fetch_assoc($query)
                    ):

                    ?>


                        <tr>


                            <td>

                                <?= $no++; ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $row['tanggal']
                                ); ?>

                            </td>


                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['jenis_sampah'] ?? '-'
                                    ); ?>

                                </strong>

                            </td>


                            <td>

                                <?= rupiah(
                                    $row['harga_per_kg'] ?? 0
                                ); ?>

                            </td>


                            <td>

                                <?= number_format(
                                    $row['berat_kg'],
                                    2,
                                    ',',
                                    '.'
                                ); ?>

                                Kg

                            </td>


                            <td>

                                <strong>

                                    <?= rupiah(
                                        $row['total_rp']
                                    ); ?>

                                </strong>

                            </td>


                            <td>

                                <span class="badge-status">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Diterima

                                </span>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td colspan="7">


                            <div class="empty-state">


                                <div class="empty-icon">

                                    <i class="bi bi-inbox"></i>

                                </div>


                                <h5>
                                    Belum ada riwayat setor
                                </h5>


                                <p>
                                    Kamu belum memiliki transaksi
                                    setoran yang diterima.
                                </p>


                                <a
                                    href="../setor/index.php"
                                    class="btn-black"
                                >

                                    <i class="bi bi-recycle"></i>

                                    Mulai Setor

                                </a>


                            </div>


                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>


    </div>



    <!-- ======================================
         ACTION
    ======================================= -->

    <div class="d-flex flex-wrap gap-2 mt-4">


        <a
            href="../tarik/index.php"
            class="btn-outline-black"
        >

            <i class="bi bi-wallet2"></i>

            Tarik Saldo

        </a>


        <a
            href="riwayat_tarik.php"
            class="btn-outline-black"
        >

            <i class="bi bi-receipt"></i>

            Riwayat Tarik

        </a>


        <a
            href="dashboard.php"
            class="btn-outline-black"
        >

            <i class="bi bi-grid"></i>

            Dashboard

        </a>


    </div>



    <!-- FOOTER -->

    <div class="footer">

        EcoBank Sampah &copy; <?= date('Y'); ?>

        <span class="float-end">
            Riwayat Setor Guru
        </span>

    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

