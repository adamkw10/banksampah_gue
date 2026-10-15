
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("admin");


// =====================================================
// STATISTIK USER
// =====================================================

$query_user = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role IN ('siswa', 'guru', 'pengepul')"
);

$total_user = 0;

if ($query_user) {
    $data_user = mysqli_fetch_assoc($query_user);
    $total_user = $data_user['total'] ?? 0;
}


// =====================================================
// TOTAL SETORAN
// =====================================================

$query_setor = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM transaksi
     WHERE tipe = 'setor'"
);

$total_setor = 0;

if ($query_setor) {
    $data_setor = mysqli_fetch_assoc($query_setor);
    $total_setor = $data_setor['total'] ?? 0;
}


// =====================================================
// NILAI SETORAN
// =====================================================

$query_nilai = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(total_rp), 0) AS total
     FROM transaksi
     WHERE tipe = 'setor'"
);

$total_nilai = 0;

if ($query_nilai) {
    $data_nilai = mysqli_fetch_assoc($query_nilai);
    $total_nilai = $data_nilai['total'] ?? 0;
}


// =====================================================
// TOTAL PENARIKAN
// =====================================================

$query_tarik = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(nominal), 0) AS total
     FROM tarik_saldo
     WHERE status = 'diterima'"
);

$total_tarik = 0;

if ($query_tarik) {
    $data_tarik = mysqli_fetch_assoc($query_tarik);
    $total_tarik = $data_tarik['total'] ?? 0;
}


// =====================================================
// SALDO ADMIN
// Mengambil saldo terbaru langsung dari tabel users
// =====================================================

$query_saldo_admin = mysqli_query(
    $conn,
    "SELECT COALESCE(saldo, 0) AS total
     FROM users
     WHERE role = 'admin'
     ORDER BY id_user ASC
     LIMIT 1"
);

$saldo_admin = 0;

if ($query_saldo_admin) {
    $data_saldo_admin = mysqli_fetch_assoc($query_saldo_admin);
    $saldo_admin = (float) ($data_saldo_admin['total'] ?? 0);
}


// =====================================================
// PENDING SETOR
// =====================================================

$query_pending_setor = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM transaksi
     WHERE tipe = 'setor'
     AND status = 'pending'"
);

$pending_setor = 0;

if ($query_pending_setor) {

    $data_pending_setor =
        mysqli_fetch_assoc($query_pending_setor);

    $pending_setor =
        $data_pending_setor['total'] ?? 0;
}


// =====================================================
// PENDING TARIK
// =====================================================

$query_pending_tarik = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM tarik_saldo
     WHERE status = 'pending'"
);

$pending_tarik = 0;

if ($query_pending_tarik) {

    $data_pending_tarik =
        mysqli_fetch_assoc($query_pending_tarik);

    $pending_tarik =
        $data_pending_tarik['total'] ?? 0;
}


// =====================================================
// PENDING CHECKOUT
// =====================================================

$pending_checkout = 0;

$query_pending_checkout = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM checkout
     WHERE status = 'pending'"
);

if ($query_pending_checkout) {

    $data_pending_checkout =
        mysqli_fetch_assoc($query_pending_checkout);

    $pending_checkout =
        $data_pending_checkout['total'] ?? 0;
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

    <title>
        Dashboard Admin - EcoBank
    </title>


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

            background: #f6f7f9;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            color: #111827;

        }


        /* =====================================================
           SIDEBAR
        ===================================================== */

        .sidebar {

            position: fixed;

            left: 0;
            top: 0;

            width: 260px;

            height: 100vh;

            background: #111111;

            color: white;

            padding: 25px 18px;

            display: flex;

            flex-direction: column;

            z-index: 1000;

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                5px
                10px
                30px;

            border-bottom:
                1px solid
                rgba(255,255,255,.12);

        }


        .brand-icon {

            width: 44px;
            height: 44px;

            border-radius: 13px;

            background: white;

            color: #111;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 25px;

        }


        .brand-name {

            font-size: 21px;

            font-weight: 700;

            letter-spacing: -.5px;

        }


        .brand-subtitle {

            display: block;

            font-size: 10px;

            color: #999;

            margin-top: 2px;

        }


        .menu-title {

            font-size: 11px;

            color: #777;

            letter-spacing: 1px;

            margin:
                28px
                10px
                10px;

            text-transform: uppercase;

        }


        .nav-link-custom {

            display: flex;

            align-items: center;

            gap: 13px;

            color: #cfcfcf;

            text-decoration: none;

            padding:
                12px
                14px;

            margin-bottom: 5px;

            border-radius: 10px;

            font-size: 14px;

            transition: .2s;

        }


        .nav-link-custom:hover {

            background: #242424;

            color: white;

        }


        .nav-link-custom.active {

            background: #303030;

            color: white;

        }


        .nav-icon {

            width: 22px;

            text-align: center;

            font-size: 17px;

        }


        .badge-menu {

            margin-left: auto;

            background: #f5c542;

            color: #111;

            font-size: 10px;

            padding:
                3px
                7px;

            border-radius: 20px;

            font-weight: bold;

        }


        .sidebar-bottom {

            margin-top: auto;

            border-top:
                1px solid
                rgba(255,255,255,.12);

            padding-top: 18px;

        }


        /* =====================================================
           MAIN
        ===================================================== */

        .main {

            margin-left: 260px;

            min-height: 100vh;

        }


        /* =====================================================
           TOPBAR
        ===================================================== */

        .topbar {

            height: 72px;

            background: white;

            border-bottom:
                1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0
                35px;

        }


        .search-box {

            width: 390px;

            background: #f4f5f7;

            border-radius: 30px;

            padding:
                11px
                18px;

            color: #9ca3af;

            font-size: 13px;

        }


        .admin-info {

            display: flex;

            align-items: center;

            gap: 12px;

        }


        .admin-avatar {

            width: 40px;
            height: 40px;

            border-radius: 50%;

            background: #111;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: bold;

        }


        /* =====================================================
           CONTENT
        ===================================================== */

        .content {

            padding: 32px;

        }


        /* =====================================================
           HERO
        ===================================================== */

        .hero {

            background: #111;

            color: white;

            border-radius: 15px;

            padding:
                30px
                28px;

            margin-bottom: 25px;

            position: relative;

            overflow: hidden;

        }


        .hero small {

            color: #aaa;

            letter-spacing: 1px;

            text-transform: uppercase;

        }


        .hero h1 {

            font-size: 32px;

            margin:
                8px
                0
                8px;

            font-weight: 700;

        }


        .hero p {

            color: #cfcfcf;

            margin: 0;

        }


        .hero-icon {

            position: absolute;

            right: 40px;

            top: 15px;

            font-size: 100px;

            opacity: .12;

        }


        /* =====================================================
           STAT CARD
        ===================================================== */

        .stat-card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 14px;

            padding: 20px;

            height: 100%;

            transition: .2s;

        }


        .stat-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.07);

        }


        .stat-icon {

            width: 45px;
            height: 45px;

            border-radius: 50%;

            background: #f1f2f4;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            margin-bottom: 15px;

        }


        .stat-title {

            font-size: 13px;

            color: #6b7280;

        }


        .stat-number {

            font-size: 27px;

            font-weight: 700;

            margin-top: 3px;

        }


        .stat-description {

            color: #9ca3af;

            font-size: 11px;

            margin-top: 3px;

        }


        /* =====================================================
           SECTION CARD
        ===================================================== */

        .section-card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 14px;

            padding: 22px;

            height: 100%;

        }


        .section-title {

            font-size: 17px;

            font-weight: 700;

            margin-bottom: 3px;

        }


        .section-subtitle {

            color: #9ca3af;

            font-size: 12px;

        }


        /* =====================================================
           APPROVAL
        ===================================================== */

        .approval-card {

            border:
                1px solid #e5e7eb;

            border-radius: 12px;

            padding: 18px;

            background: #fafafa;

        }


        .approval-icon {

            width: 42px;
            height: 42px;

            background: #111;

            color: white;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 12px;

        }


        .approval-number {

            font-size: 25px;

            font-weight: 700;

        }


        .btn-black {

            background: #111;

            border: none;

            color: white;

            border-radius: 8px;

            padding:
                8px
                14px;

            font-size: 12px;

        }


        .btn-black:hover {

            background: #333;

            color: white;

        }


        /* =====================================================
           QUICK MENU
        ===================================================== */

        .quick-btn {

            text-decoration: none;

            color: #111;

            border:
                1px solid #e5e7eb;

            border-radius: 10px;

            padding:
                18px
                10px;

            text-align: center;

            background: white;

            transition: .2s;

        }


        .quick-btn:hover {

            background: #111;

            color: white;

            transform:
                translateY(-2px);

        }


        .quick-btn i {

            font-size: 25px;

            display: block;

            margin-bottom: 8px;

        }


        .quick-btn span {

            font-size: 12px;

            font-weight: 600;

        }


        /* =====================================================
           TABLE
        ===================================================== */

        .table-card {

            background: white;

            border:
                1px solid #e5e7eb;

            border-radius: 14px;

            overflow: hidden;

        }


        .table-card .table {

            margin: 0;

        }


        .table-card th {

            background: #f8f9fa;

            font-size: 11px;

            color: #6b7280;

            text-transform: uppercase;

            border-bottom:
                1px solid #e5e7eb;

        }


        .table-card td {

            font-size: 13px;

            vertical-align: middle;

        }


        /* =====================================================
           STATUS
        ===================================================== */

        .status {

            display: inline-block;

            padding:
                5px
                10px;

            border-radius: 20px;

            font-size: 10px;

            font-weight: 600;

        }


        .status-pending {

            background: #fff1c7;

            color: #8a6500;

        }


        .status-success {

            background: #d9f7e7;

            color: #167346;

        }


        /* =====================================================
           SALDO ADMIN
        ===================================================== */

        .saldo-admin-card {

            background: #111;

            color: white;

            border-radius: 14px;

            padding: 20px;

            height: 100%;

            position: relative;

            overflow: hidden;

            transition: .2s;

        }


        .saldo-admin-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.15);

        }


        .saldo-admin-card .stat-icon {

            background: rgba(255,255,255,.12);

            color: white;

        }


        .saldo-admin-card .stat-title {

            color: #cfcfcf;

        }


        .saldo-admin-card .stat-description {

            color: #aaa;

        }


        .saldo-admin-icon {

            position: absolute;

            right: 18px;

            bottom: -10px;

            font-size: 85px;

            opacity: .07;

        }


        /* =====================================================
           5 KOLOM
        ===================================================== */

        .col-xl-20 {

            flex:
                0 0 auto;

            width: 20%;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media(max-width: 1199px) {

            .col-xl-20 {

                width: 33.333333%;

            }

        }


        @media(max-width: 991px) {

            .sidebar {

                width: 220px;

            }


            .main {

                margin-left: 220px;

            }


            .search-box {

                width: 250px;

            }


            .col-xl-20 {

                width: 50%;

            }

        }


        @media(max-width: 768px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }


            .main {

                margin-left: 0;

            }


            .topbar {

                padding:
                    0
                    20px;

            }


            .search-box {

                display: none;

            }


            .content {

                padding: 20px;

            }


            .hero h1 {

                font-size: 25px;

            }


            .col-xl-20 {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="sidebar">


    <div class="brand">

        <div class="brand-icon">
            ♻
        </div>

        <div>

            <div class="brand-name">
                EcoBank
            </div>

            <span class="brand-subtitle">
                BANK SAMPAH
            </span>

        </div>

    </div>


    <div class="menu-title">
        Menu Admin
    </div>


    <a
        href="dashboard.php"
        class="nav-link-custom active"
    >

        <i class="bi bi-grid nav-icon"></i>

        Dashboard

    </a>


    <a
        href="sampah.php"
        class="nav-link-custom"
    >

        <i class="bi bi-recycle nav-icon"></i>

        Data Sampah

    </a>


    <a
        href="persetujuan_setor.php"
        class="nav-link-custom"
    >

        <i class="bi bi-box-seam nav-icon"></i>

        Persetujuan Setor

        <?php if ($pending_setor > 0): ?>

            <span class="badge-menu">
                <?= $pending_setor; ?>
            </span>

        <?php endif; ?>

    </a>


    <a
        href="persetujuan_tarik.php"
        class="nav-link-custom"
    >

        <i class="bi bi-cash-coin nav-icon"></i>

        Persetujuan Tarik

        <?php if ($pending_tarik > 0): ?>

            <span class="badge-menu">
                <?= $pending_tarik; ?>
            </span>

        <?php endif; ?>

    </a>


    <a
        href="persetujuan_checkout.php"
        class="nav-link-custom"
    >

        <i class="bi bi-cart3 nav-icon"></i>

        Persetujuan Checkout

        <?php if ($pending_checkout > 0): ?>

            <span class="badge-menu">
                <?= $pending_checkout; ?>
            </span>

        <?php endif; ?>

    </a>


    <a
        href="riwayat_transaksi.php"
        class="nav-link-custom"
    >

        <i class="bi bi-receipt nav-icon"></i>

        Riwayat Transaksi

    </a>


    <div class="sidebar-bottom">

        <a
            href="../logout.php"
            class="nav-link-custom"
        >

            <i class="bi bi-box-arrow-right nav-icon"></i>

            Logout

        </a>

    </div>


</aside>



<!-- =====================================================
     MAIN
===================================================== -->

<main class="main">


    <!-- =================================================
         TOPBAR
    ================================================= -->

    <header class="topbar">


        <div class="search-box">

            <i class="bi bi-search me-2"></i>

            Cari fitur, data, atau transaksi...

        </div>


        <div class="admin-info">


            <i
                class="bi bi-bell"
                style="font-size:20px;"
            ></i>


            <div class="admin-avatar">

                <?= strtoupper(
                    substr(
                        $_SESSION['nama'] ?? 'A',
                        0,
                        1
                    )
                ); ?>

            </div>


            <div>

                <strong style="font-size:13px;">

                    <?= htmlspecialchars(
                        $_SESSION['nama'] ?? 'Administrator'
                    ); ?>

                </strong>

                <div
                    style="
                    font-size:10px;
                    color:#999;
                    "
                >

                    Pengelola EcoBank

                </div>

            </div>


        </div>


    </header>



    <!-- =================================================
         CONTENT
    ================================================= -->

    <div class="content">


        <!-- =================================================
             HERO
        ================================================= -->

        <div class="hero">

            <small>
                ECOBANK ADMIN
            </small>

            <h1>
                Kelola Bank Sampah
            </h1>

            <p>
                Pantau setoran, saldo, penarikan dan checkout.
            </p>

            <div class="hero-icon">
                ♻
            </div>

        </div>



        <!-- =================================================
             STATISTIK
        ================================================= -->

        <div class="row g-3 mb-4">


            <!-- TOTAL USER -->

            <div class="col-xl-20 col-md-6">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-people"></i>

                    </div>

                    <div class="stat-title">
                        Total User
                    </div>

                    <div class="stat-number">

                        <?= number_format($total_user); ?>

                    </div>

                    <div class="stat-description">
                        Siswa, Guru & Pengepul
                    </div>

                </div>

            </div>


            <!-- TOTAL SETORAN -->

            <div class="col-xl-20 col-md-6">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-recycle"></i>

                    </div>

                    <div class="stat-title">
                        Total Setoran
                    </div>

                    <div class="stat-number">

                        <?= number_format($total_setor); ?>

                    </div>

                    <div class="stat-description">
                        Transaksi setor
                    </div>

                </div>

            </div>


            <!-- NILAI SETORAN -->

            <div class="col-xl-20 col-md-6">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-wallet2"></i>

                    </div>

                    <div class="stat-title">
                        Nilai Setoran
                    </div>

                    <div class="stat-number">

                        <?= rupiah($total_nilai); ?>

                    </div>

                    <div class="stat-description">
                        Total nilai setoran
                    </div>

                </div>

            </div>


            <!-- PENARIKAN -->

            <div class="col-xl-20 col-md-6">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-cash-stack"></i>

                    </div>

                    <div class="stat-title">
                        Penarikan
                    </div>

                    <div class="stat-number">

                        <?= rupiah($total_tarik); ?>

                    </div>

                    <div class="stat-description">
                        Penarikan diterima
                    </div>

                </div>

            </div>


            <!-- SALDO ADMIN -->

            <div class="col-xl-20 col-md-6">

                <div class="saldo-admin-card">

                    <div class="stat-icon">

                        <i class="bi bi-bank2"></i>

                    </div>

                    <div class="stat-title">
                        Saldo Admin
                    </div>

                    <div
                        class="stat-number"
                        style="font-size:24px;"
                    >

                        <?= rupiah($saldo_admin); ?>

                    </div>

                    <div class="stat-description">
                        Hasil checkout pengepul
                    </div>

                    <div class="saldo-admin-icon">

                        <i class="bi bi-bank2"></i>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             APPROVAL
        ================================================= -->

        <div class="row g-3 mb-4">


            <!-- MENUNGGU PERSETUJUAN -->

            <div class="col-lg-8">

                <div class="section-card">


                    <div class="d-flex justify-content-between mb-3">

                        <div>

                            <div class="section-title">
                                Menunggu Persetujuan
                            </div>

                            <div class="section-subtitle">
                                Kelola pengajuan yang masuk
                            </div>

                        </div>

                    </div>


                    <div class="row g-3">


                        <!-- SETOR -->

                        <div class="col-md-4">

                            <div class="approval-card">


                                <div class="approval-icon">

                                    <i class="bi bi-box-seam"></i>

                                </div>


                                <div class="section-subtitle">
                                    Setoran Pending
                                </div>


                                <div class="approval-number">

                                    <?= number_format(
                                        $pending_setor
                                    ); ?>

                                </div>


                                <a
                                    href="persetujuan_setor.php"
                                    class="btn btn-black mt-2"
                                >

                                    Periksa Setoran

                                    <i
                                        class="bi bi-arrow-right"
                                    ></i>

                                </a>


                            </div>

                        </div>



                        <!-- TARIK -->

                        <div class="col-md-4">

                            <div class="approval-card">


                                <div class="approval-icon">

                                    <i class="bi bi-cash-coin"></i>

                                </div>


                                <div class="section-subtitle">
                                    Tarik Saldo Pending
                                </div>


                                <div class="approval-number">

                                    <?= number_format(
                                        $pending_tarik
                                    ); ?>

                                </div>


                                <a
                                    href="persetujuan_tarik.php"
                                    class="btn btn-black mt-2"
                                >

                                    Periksa Penarikan

                                    <i
                                        class="bi bi-arrow-right"
                                    ></i>

                                </a>


                            </div>

                        </div>



                        <!-- CHECKOUT -->

                        <div class="col-md-4">

                            <div class="approval-card">


                                <div class="approval-icon">

                                    <i class="bi bi-cart3"></i>

                                </div>


                                <div class="section-subtitle">
                                    Checkout Pending
                                </div>


                                <div class="approval-number">

                                    <?= number_format(
                                        $pending_checkout
                                    ); ?>

                                </div>


                                <a
                                    href="persetujuan_checkout.php"
                                    class="btn btn-black mt-2"
                                >

                                    Periksa Checkout

                                    <i
                                        class="bi bi-arrow-right"
                                    ></i>

                                </a>


                            </div>

                        </div>


                    </div>


                </div>

            </div>



            <!-- AKTIVITAS -->

            <div class="col-lg-4">

                <div class="section-card">


                    <div class="section-title">
                        Aktivitas Terbaru
                    </div>


                    <div class="section-subtitle mb-3">
                        Status sistem EcoBank
                    </div>



                    <!-- SETORAN -->

                    <div class="d-flex gap-3 mb-3">


                        <div class="stat-icon mb-0">

                            <i class="bi bi-recycle"></i>

                        </div>


                        <div>

                            <strong style="font-size:12px;">
                                Setoran
                            </strong>


                            <div class="section-subtitle">

                                <?= number_format(
                                    $pending_setor
                                ); ?>

                                pengajuan pending

                            </div>

                        </div>


                    </div>



                    <!-- TARIK -->

                    <div class="d-flex gap-3 mb-3">


                        <div class="stat-icon mb-0">

                            <i class="bi bi-cash"></i>

                        </div>


                        <div>

                            <strong style="font-size:12px;">
                                Tarik Saldo
                            </strong>


                            <div class="section-subtitle">

                                <?= number_format(
                                    $pending_tarik
                                ); ?>

                                pengajuan pending

                            </div>

                        </div>


                    </div>



                    <!-- CHECKOUT -->

                    <div class="d-flex gap-3 mb-3">


                        <div class="stat-icon mb-0">

                            <i class="bi bi-cart"></i>

                        </div>


                        <div>

                            <strong style="font-size:12px;">
                                Checkout
                            </strong>


                            <div class="section-subtitle">

                                <?= number_format(
                                    $pending_checkout
                                ); ?>

                                pengajuan pending

                            </div>

                        </div>


                    </div>



                    <!-- SALDO ADMIN -->

                    <div class="d-flex gap-3">


                        <div class="stat-icon mb-0">

                            <i class="bi bi-bank"></i>

                        </div>


                        <div>

                            <strong style="font-size:12px;">
                                Saldo Admin
                            </strong>


                            <div class="section-subtitle">

                                <?= rupiah($saldo_admin); ?>

                                dari checkout disetujui

                            </div>

                        </div>


                    </div>


                </div>

            </div>


        </div>



        <!-- =================================================
             MENU CEPAT
        ================================================= -->

        <div class="section-card mb-4">


            <div class="section-title">
                Menu Cepat
            </div>


            <div class="section-subtitle mb-3">
                Akses fitur administrasi EcoBank.
            </div>


            <div class="row g-2">


                <!-- DATA SAMPAH -->

                <div class="col">

                    <a
                        href="sampah.php"
                        class="quick-btn d-block"
                    >

                        <i class="bi bi-recycle"></i>

                        <span>
                            Data Sampah
                        </span>

                    </a>

                </div>


                <!-- SETOR -->

                <div class="col">

                    <a
                        href="persetujuan_setor.php"
                        class="quick-btn d-block"
                    >

                        <i class="bi bi-box-seam"></i>

                        <span>
                            Persetujuan Setor
                        </span>

                    </a>

                </div>


                <!-- TARIK -->

                <div class="col">

                    <a
                        href="persetujuan_tarik.php"
                        class="quick-btn d-block"
                    >

                        <i class="bi bi-cash-coin"></i>

                        <span>
                            Persetujuan Tarik
                        </span>

                    </a>

                </div>


                <!-- CHECKOUT -->

                <div class="col">

                    <a
                        href="persetujuan_checkout.php"
                        class="quick-btn d-block"
                    >

                        <i class="bi bi-cart3"></i>

                        <span>
                            Persetujuan Checkout
                        </span>

                    </a>

                </div>


                <!-- RIWAYAT -->

                <div class="col">

                    <a
                        href="riwayat_transaksi.php"
                        class="quick-btn d-block"
                    >

                        <i class="bi bi-receipt"></i>

                        <span>
                            Riwayat Transaksi
                        </span>

                    </a>

                </div>


            </div>


        </div>



        <!-- =================================================
             RINGKASAN SISTEM
        ================================================= -->

        <div class="table-card">


            <div class="p-3 border-bottom">


                <div class="section-title">
                    Ringkasan Sistem
                </div>


                <div class="section-subtitle">
                    Informasi status EcoBank
                </div>


            </div>



            <div class="table-responsive">


                <table class="table">


                    <thead>

                        <tr>

                            <th>
                                FITUR
                            </th>

                            <th>
                                JUMLAH
                            </th>

                            <th>
                                STATUS
                            </th>

                            <th>
                                AKSI
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <!-- PENGGUNA -->

                        <tr>

                            <td>

                                <i class="bi bi-people me-2"></i>

                                Pengguna

                            </td>


                            <td>

                                <?= number_format(
                                    $total_user
                                ); ?>

                            </td>


                            <td>

                                <span
                                    class="status status-success"
                                >
                                    Aktif
                                </span>

                            </td>


                            <td>
                                -
                            </td>

                        </tr>



                        <!-- SETORAN -->

                        <tr>

                            <td>

                                <i
                                    class="bi bi-box-seam me-2"
                                ></i>

                                Setoran Pending

                            </td>


                            <td>

                                <?= number_format(
                                    $pending_setor
                                ); ?>

                            </td>


                            <td>


                                <?php if (
                                    $pending_setor > 0
                                ): ?>

                                    <span
                                        class="status status-pending"
                                    >
                                        Menunggu
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="status status-success"
                                    >
                                        Selesai
                                    </span>

                                <?php endif; ?>


                            </td>


                            <td>

                                <a
                                    href="persetujuan_setor.php"
                                    class="btn btn-sm btn-black"
                                >
                                    Buka
                                </a>

                            </td>

                        </tr>



                        <!-- TARIK -->

                        <tr>

                            <td>

                                <i
                                    class="bi bi-cash-coin me-2"
                                ></i>

                                Tarik Saldo Pending

                            </td>


                            <td>

                                <?= number_format(
                                    $pending_tarik
                                ); ?>

                            </td>


                            <td>


                                <?php if (
                                    $pending_tarik > 0
                                ): ?>

                                    <span
                                        class="status status-pending"
                                    >
                                        Menunggu
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="status status-success"
                                    >
                                        Selesai
                                    </span>

                                <?php endif; ?>


                            </td>


                            <td>

                                <a
                                    href="persetujuan_tarik.php"
                                    class="btn btn-sm btn-black"
                                >
                                    Buka
                                </a>

                            </td>

                        </tr>



                        <!-- CHECKOUT -->

                        <tr>

                            <td>

                                <i
                                    class="bi bi-cart3 me-2"
                                ></i>

                                Checkout Pending

                            </td>


                            <td>

                                <?= number_format(
                                    $pending_checkout
                                ); ?>

                            </td>


                            <td>


                                <?php if (
                                    $pending_checkout > 0
                                ): ?>

                                    <span
                                        class="status status-pending"
                                    >
                                        Menunggu
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="status status-success"
                                    >
                                        Selesai
                                    </span>

                                <?php endif; ?>


                            </td>


                            <td>

                                <a
                                    href="persetujuan_checkout.php"
                                    class="btn btn-sm btn-black"
                                >
                                    Buka
                                </a>

                            </td>

                        </tr>



                        <!-- SALDO ADMIN -->

                        <tr>

                            <td>

                                <i
                                    class="bi bi-bank2 me-2"
                                ></i>

                                Saldo Admin

                            </td>


                            <td>

                                <?= rupiah(
                                    $saldo_admin
                                ); ?>

                            </td>


                            <td>

                                <span
                                    class="status status-success"
                                >
                                    Aktif
                                </span>

                            </td>


                            <td>

                                <a
                                    href="persetujuan_checkout.php"
                                    class="btn btn-sm btn-black"
                                >
                                    Checkout
                                </a>

                            </td>

                        </tr>


                    </tbody>


                </table>


            </div>


        </div>


    </div>


</main>



<!-- =====================================================
     BOOTSTRAP JS
===================================================== -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

