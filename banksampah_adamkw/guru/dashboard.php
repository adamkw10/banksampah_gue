
<?php

// ==========================================
// KONEKSI DATABASE
// ==========================================

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("guru");


// ==========================================
// AMBIL ID USER
// ==========================================

$id_user = (int) $_SESSION['id_user'];


// ==========================================
// AMBIL SALDO GURU
// ==========================================

$query_saldo = mysqli_query(
    $conn,
    "SELECT saldo
     FROM users
     WHERE id_user = '$id_user'"
);

$data_saldo = mysqli_fetch_assoc($query_saldo);

$saldo = $data_saldo['saldo'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Guru - EcoBank</title>


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

            border-bottom:
                1px solid #333;

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
           TOP HEADER
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

            box-shadow:
                0 5px 20px rgba(0,0,0,.05);

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
           SALDO CARD
        ====================================== */

        .saldo-card {

            background: #111;

            color: white;

            border-radius: 20px;

            padding: 28px;

            margin-bottom: 25px;

            position: relative;

            overflow: hidden;

            box-shadow:
                0 15px 35px rgba(0,0,0,.15);

        }


        .saldo-card::after {

            content: "";

            position: absolute;

            width: 180px;
            height: 180px;

            border: 1px solid #444;

            border-radius: 50%;

            right: -70px;
            top: -70px;

        }


        .saldo-label {

            color: #aaa;

            font-size: 14px;

            margin-bottom: 8px;

        }


        .saldo-value {

            font-size: 34px;

            font-weight: 700;

            margin-bottom: 8px;

        }


        .saldo-info {

            color: #aaa;

            font-size: 13px;

        }


        /* ======================================
           MENU CARD
        ====================================== */

        .section-title {

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 15px;

        }


        .menu-card {

            background: white;

            border: 1px solid #e8e8e8;

            border-radius: 16px;

            padding: 22px;

            height: 100%;

            transition: .2s;

        }


        .menu-card:hover {

            transform: translateY(-3px);

            box-shadow:
                0 12px 30px rgba(0,0,0,.08);

        }


        .menu-icon {

            width: 48px;
            height: 48px;

            border-radius: 13px;

            background: #111;

            color: white;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 21px;

            margin-bottom: 15px;

        }


        .menu-card h5 {

            font-weight: 700;

            margin-bottom: 6px;

        }


        .menu-card p {

            color: #777;

            font-size: 13px;

            min-height: 40px;

        }


        .btn-black {

            background: #111;

            border: none;

            color: white;

            width: 100%;

            padding: 10px;

            border-radius: 9px;

            font-weight: 600;

            text-decoration: none;

            display: inline-block;

            text-align: center;

            transition: .2s;

        }


        .btn-black:hover {

            background: #333;

            color: white;

        }


        /* ======================================
           FOOTER
        ====================================== */

        .footer {

            margin-top: 35px;

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

                align-items: flex-start;

                gap: 15px;

                flex-direction: column;

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


    <a
        href="dashboard.php"
        class="active"
    >

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


    <a href="riwayat.php">

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
                Dashboard Guru
            </h2>

            <p>
                Kelola aktivitas EcoBank kamu dengan mudah.
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
         SALDO
    ======================================= -->

    <div class="saldo-card">


        <div class="saldo-label">

            Saldo EcoBank Saya

        </div>


        <div class="saldo-value">

            Rp <?= number_format(
                $saldo,
                0,
                ',',
                '.'
            ); ?>

        </div>


        <div class="saldo-info">

            Saldo bertambah setelah pengajuan setor
            disetujui oleh admin.

        </div>


    </div>



    <!-- ======================================
         MENU
    ======================================= -->

    <div class="section-title">

        Aktivitas EcoBank

    </div>


    <div class="row g-4">


        <!-- SETOR -->

        <div class="col-md-6 col-xl-3">

            <div class="menu-card">

                <div class="menu-icon">

                    <i class="bi bi-recycle"></i>

                </div>


                <h5>
                    Setor Sampah
                </h5>


                <p>
                    Ajukan setoran sampah dan
                    tunggu persetujuan admin.
                </p>


                <a
                    href="../setor/index.php"
                    class="btn-black"
                >

                    Mulai Setor

                </a>

            </div>

        </div>



        <!-- TARIK -->

        <div class="col-md-6 col-xl-3">

            <div class="menu-card">

                <div class="menu-icon">

                    <i class="bi bi-wallet2"></i>

                </div>


                <h5>
                    Tarik Saldo
                </h5>


                <p>
                    Ajukan penarikan saldo
                    dari akun EcoBank.
                </p>


                <a
                    href="../tarik/index.php"
                    class="btn-black"
                >

                    Tarik Saldo

                </a>

            </div>

        </div>



        <!-- RIWAYAT SETOR -->

        <div class="col-md-6 col-xl-3">

            <div class="menu-card">

                <div class="menu-icon">

                    <i class="bi bi-clock-history"></i>

                </div>


                <h5>
                    Riwayat Setor
                </h5>


                <p>
                    Lihat seluruh riwayat
                    setoran sampah kamu.
                </p>


                <a
                    href="riwayat.php"
                    class="btn-black"
                >

                    Lihat Riwayat

                </a>

            </div>

        </div>



        <!-- RIWAYAT TARIK -->

        <div class="col-md-6 col-xl-3">

            <div class="menu-card">

                <div class="menu-icon">

                    <i class="bi bi-receipt"></i>

                </div>


                <h5>
                    Riwayat Tarik
                </h5>


                <p>
                    Lihat status seluruh
                    penarikan saldo kamu.
                </p>


                <a
                    href="riwayat_tarik.php"
                    class="btn-black"
                >

                    Lihat Riwayat

                </a>

            </div>

        </div>


    </div>



    <!-- FOOTER -->

    <div class="footer">

        EcoBank Sampah &copy; <?= date('Y'); ?>

        <span class="float-end">
            Dashboard Guru
        </span>

    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>

