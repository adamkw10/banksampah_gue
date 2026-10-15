
<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("pengepul");


// ==================================================
// DATA USER
// ==================================================

$id_user = (int) $_SESSION['id_user'];

$query_user = mysqli_query(
    $conn,
    "SELECT nama, username, saldo
     FROM users
     WHERE id_user = $id_user
     LIMIT 1"
);

if (!$query_user) {
    die("Gagal mengambil data user: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($query_user);

if (!$user) {
    die("Data user tidak ditemukan.");
}


// ==================================================
// JUMLAH PEMBELIAN
// ==================================================

// Semua pembelian
$query_total_pembelian = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pembelian
     WHERE id_user = $id_user"
);

$total_pembelian = 0;

if ($query_total_pembelian) {
    $data = mysqli_fetch_assoc($query_total_pembelian);
    $total_pembelian = (int) $data['total'];
}


// ==================================================
// PEMBELIAN PENDING
// ==================================================

$query_pending = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pembelian
     WHERE id_user = $id_user
     AND status = 'pending'"
);

$total_pending = 0;

if ($query_pending) {
    $data = mysqli_fetch_assoc($query_pending);
    $total_pending = (int) $data['total'];
}


// ==================================================
// PEMBELIAN DISETUJUI
// ==================================================

$query_disetujui = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS jumlah,
        COALESCE(SUM(total_rp), 0) AS total_nilai
     FROM pembelian
     WHERE id_user = $id_user
     AND status = 'disetujui'"
);

$jumlah_disetujui = 0;
$total_nilai_pembelian = 0;

if ($query_disetujui) {

    $data = mysqli_fetch_assoc($query_disetujui);

    $jumlah_disetujui = (int) $data['jumlah'];

    $total_nilai_pembelian = (float) $data['total_nilai'];
}


// ==================================================
// PEMBELIAN DITOLAK
// ==================================================

$query_ditolak = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM pembelian
     WHERE id_user = $id_user
     AND status = 'ditolak'"
);

$total_ditolak = 0;

if ($query_ditolak) {

    $data = mysqli_fetch_assoc($query_ditolak);

    $total_ditolak = (int) $data['total'];
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
    Dashboard Pengepul - EcoBank
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

/* ==================================================
   GLOBAL
================================================== */

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


/* ==================================================
   SIDEBAR
================================================== */

.sidebar {

    position: fixed;

    left: 0;
    top: 0;

    width: 225px;

    height: 100vh;

    background: #111;

    color: white;

    padding: 24px 15px;

    z-index: 1000;

}


/* LOGO */

.logo-area {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 0 9px 22px;

    border-bottom: 1px solid #303030;

    margin-bottom: 25px;

}


.logo-icon {

    width: 42px;
    height: 42px;

    background: white;

    color: #111;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 21px;

}


.logo-title {

    font-size: 17px;

    font-weight: 700;

}


.logo-subtitle {

    font-size: 9px;

    color: #aaa;

    margin-top: 2px;

    letter-spacing: .4px;

}


/* USER */

.user-box {

    background: #1d1d1d;

    border-radius: 12px;

    padding: 15px;

    margin-bottom: 25px;

}


.user-icon {

    width: 38px;
    height: 38px;

    background: white;

    color: #111;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 10px;

}


.user-name {

    font-weight: 700;

    font-size: 14px;

}


.user-role {

    font-size: 11px;

    color: #aaa;

    margin-top: 2px;

}


/* MENU TITLE */

.menu-title {

    color: #777;

    font-size: 10px;

    font-weight: 600;

    margin: 0 10px 9px;

    text-transform: uppercase;

}


/* MENU */

.sidebar-menu {

    display: flex;

    flex-direction: column;

    gap: 5px;

}


.sidebar-menu a {

    color: #cfcfcf;

    text-decoration: none;

    padding: 11px 13px;

    border-radius: 9px;

    display: flex;

    align-items: center;

    gap: 12px;

    font-size: 13px;

    transition: .2s;

}


.sidebar-menu a i {

    font-size: 16px;

}


.sidebar-menu a:hover {

    background: #292929;

    color: white;

}


.sidebar-menu a.active {

    background: white;

    color: #111;

    font-weight: 600;

}


/* LOGOUT */

.sidebar-bottom {

    position: absolute;

    left: 15px;
    right: 15px;
    bottom: 20px;

    border-top: 1px solid #303030;

    padding-top: 16px;

}


.logout {

    color: #ccc;

    text-decoration: none;

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 10px 13px;

    font-size: 13px;

}


.logout:hover {

    color: white;

}


/* ==================================================
   MAIN
================================================== */

.main-content {

    margin-left: 225px;

    min-height: 100vh;

}


/* ==================================================
   TOPBAR
================================================== */

.topbar {

    height: 82px;

    background: white;

    border-bottom: 1px solid #e5e7eb;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 30px;

}


.search-box {

    width: 340px;

    background: #f3f4f6;

    border-radius: 30px;

    padding: 10px 17px;

    display: flex;

    align-items: center;

    gap: 10px;

    color: #aaa;

    font-size: 12px;

}


.search-box i {

    font-size: 15px;

}


.profile-area {

    display: flex;

    align-items: center;

    gap: 18px;

}


.notification {

    font-size: 19px;

    color: #111;

}


.profile {

    display: flex;

    align-items: center;

    gap: 10px;

}


.profile-avatar {

    width: 36px;
    height: 36px;

    background: #111;

    color: white;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 700;

}


.profile-name {

    font-size: 12px;

    font-weight: 700;

}


.profile-role {

    font-size: 9px;

    color: #999;

}


/* ==================================================
   CONTENT
================================================== */

.content {

    padding: 28px;

    max-width: 1450px;

}


/* ==================================================
   HEADER CARD
================================================== */

.page-header {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 16px;

    padding: 25px 28px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 22px;

}


.page-header h1 {

    margin: 0;

    font-size: 28px;

    font-weight: 700;

}


.page-header p {

    margin: 7px 0 0;

    color: #777;

    font-size: 13px;

}


.header-icon {

    width: 44px;
    height: 44px;

    background: #111;

    color: white;

    border-radius: 12px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

}


/* ==================================================
   WELCOME
================================================== */

.welcome {

    background: #111;

    color: white;

    border-radius: 16px;

    padding: 25px 28px;

    margin-bottom: 22px;

}


.welcome h3 {

    margin: 0;

    font-size: 21px;

    font-weight: 700;

}


.welcome p {

    margin: 7px 0 0;

    color: #ccc;

    font-size: 12px;

}


/* ==================================================
   STAT CARDS
================================================== */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 14px;

    margin-bottom: 22px;

}


.stat-card {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 15px;

    padding: 20px;

    min-height: 140px;

    position: relative;

}


.stat-card.dark {

    background: #111;

    color: white;

}


.stat-icon {

    width: 39px;
    height: 39px;

    background: #f1f2f4;

    color: #111;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 14px;

}


.stat-card.dark .stat-icon {

    background: #292929;

    color: white;

}


.stat-label {

    color: #777;

    font-size: 11px;

    margin-bottom: 5px;

}


.stat-card.dark .stat-label {

    color: #bbb;

}


.stat-value {

    font-size: 22px;

    font-weight: 700;

}


.stat-desc {

    margin-top: 8px;

    color: #999;

    font-size: 9px;

}


/* ==================================================
   LOWER CONTENT
================================================== */

.lower-grid {

    display: grid;

    grid-template-columns: 2fr 1fr;

    gap: 18px;

}


/* ==================================================
   QUICK MENU
================================================== */

.section-card {

    background: white;

    border: 1px solid #e5e7eb;

    border-radius: 16px;

    overflow: hidden;

}


.section-header {

    padding: 20px 22px;

    border-bottom: 1px solid #eee;

}


.section-header h5 {

    margin: 0;

    font-size: 16px;

    font-weight: 700;

}


.section-header p {

    margin: 5px 0 0;

    color: #999;

    font-size: 11px;

}


.quick-menu {

    padding: 18px;

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 12px;

}


.quick-item {

    text-decoration: none;

    color: #111;

    border: 1px solid #e5e7eb;

    border-radius: 13px;

    padding: 17px;

    transition: .2s;

}


.quick-item:hover {

    background: #f8f8f8;

    color: #111;

    transform: translateY(-2px);

}


.quick-icon {

    width: 37px;
    height: 37px;

    background: #111;

    color: white;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 12px;

}


.quick-item strong {

    display: block;

    font-size: 13px;

}


.quick-item span {

    display: block;

    margin-top: 4px;

    font-size: 10px;

    color: #999;

}


/* ==================================================
   STATUS
================================================== */

.status-list {

    padding: 8px 20px 18px;

}


.status-item {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 13px 0;

    border-bottom: 1px solid #eee;

}


.status-item:last-child {

    border-bottom: none;

}


.status-icon {

    width: 35px;
    height: 35px;

    background: #f1f2f4;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

}


.status-title {

    font-size: 12px;

    font-weight: 600;

}


.status-desc {

    font-size: 10px;

    color: #999;

    margin-top: 2px;

}


/* ==================================================
   FOOTER
================================================== */

.footer {

    text-align: center;

    color: #999;

    font-size: 10px;

    margin-top: 25px;

}


/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 1100px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

    .lower-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 768px) {

    .sidebar {

        width: 190px;

    }

    .main-content {

        margin-left: 190px;

    }

    .topbar {

        padding: 0 18px;

    }

    .search-box {

        width: 230px;

    }

    .content {

        padding: 18px;

    }

    .page-header {

        padding: 20px;

    }

}


@media (max-width: 600px) {

    .sidebar {

        position: relative;

        width: 100%;

        height: auto;

    }

    .main-content {

        margin-left: 0;

    }

    .sidebar-bottom {

        position: static;

        margin-top: 20px;

    }

    .topbar {

        height: auto;

        padding: 15px;

    }

    .search-box {

        display: none;

    }

    .stats-grid {

        grid-template-columns: 1fr;

    }

    .quick-menu {

        grid-template-columns: 1fr;

    }

}

</style>

</head>


<body>


<!-- ==================================================
     SIDEBAR
================================================== -->

<aside class="sidebar">


    <!-- LOGO -->

    <div class="logo-area">

        <div class="logo-icon">

            <i class="bi bi-recycle"></i>

        </div>

        <div>

            <div class="logo-title">
                EcoBank
            </div>

            <div class="logo-subtitle">
                BANK SAMPAH
            </div>

        </div>

    </div>


    <!-- USER -->

    <div class="user-box">

        <div class="user-icon">

            <i class="bi bi-person"></i>

        </div>

        <div class="user-name">

            <?= htmlspecialchars($user['nama']); ?>

        </div>

        <div class="user-role">

            Pengepul

        </div>

    </div>


    <!-- MENU -->

    <div class="menu-title">
        Menu Utama
    </div>


    <nav class="sidebar-menu">


        <!-- DASHBOARD -->

        <a
            href="dashboard.php"
            class="active"
        >

            <i class="bi bi-grid-1x2"></i>

            Dashboard

        </a>


        <!-- HARGA & STOK -->

        <a href="sampah.php">

            <i class="bi bi-recycle"></i>

            Harga & Stok

        </a>


        <!-- PEMBELIAN -->

        <a href="pembelian.php">

            <i class="bi bi-cart3"></i>

            Pembelian Saya

        </a>


    </nav>


    <!-- LOGOUT -->

    <div class="sidebar-bottom">

        <a
            href="../logout.php"
            class="logout"
        >

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>


</aside>



<!-- ==================================================
     MAIN CONTENT
================================================== -->

<main class="main-content">


    <!-- TOPBAR -->

    <header class="topbar">


        <div class="search-box">

            <i class="bi bi-search"></i>

            Cari fitur, data, atau transaksi...

        </div>


        <div class="profile-area">


            <i class="bi bi-bell notification"></i>


            <div class="profile">

                <div class="profile-avatar">

                    <?= strtoupper(
                        substr(
                            $user['nama'],
                            0,
                            1
                        )
                    ); ?>

                </div>


                <div>

                    <div class="profile-name">

                        <?= htmlspecialchars(
                            $user['nama']
                        ); ?>

                    </div>

                    <div class="profile-role">

                        Pengepul EcoBank

                    </div>

                </div>

            </div>


        </div>

    </header>



    <!-- CONTENT -->

    <div class="content">


        <!-- PAGE HEADER -->

        <div class="page-header">


            <div>

                <h1>
                    Dashboard Pengepul
                </h1>

                <p>
                    Kelola pembelian dan pantau aktivitas transaksi EcoBank.
                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-cart-check"></i>

            </div>


        </div>



        <!-- WELCOME -->

        <div class="welcome">

            <h3>

                Halo,
                <?= htmlspecialchars(
                    $user['nama']
                ); ?>
                👋

            </h3>

            <p>

                Selamat datang di dashboard pengepul EcoBank.
                Pilih menu di sebelah kiri untuk melihat harga,
                stok sampah, atau pembelian.

            </p>

        </div>



        <!-- ==================================================
             STATISTICS
        ================================================== -->

        <div class="stats-grid">


            <!-- SALDO -->

            <div class="stat-card dark">

                <div class="stat-icon">

                    <i class="bi bi-wallet2"></i>

                </div>

                <div class="stat-label">
                    Saldo Pengepul
                </div>

                <div class="stat-value">

                    Rp
                    <?= number_format(
                        $user['saldo'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </div>

                <div class="stat-desc">
                    Saldo yang tersedia
                </div>

            </div>


            <!-- TOTAL PEMBELIAN -->

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-cart3"></i>

                </div>

                <div class="stat-label">
                    Total Pembelian
                </div>

                <div class="stat-value">

                    <?= number_format(
                        $total_pembelian
                    ); ?>

                </div>

                <div class="stat-desc">
                    Semua transaksi pembelian
                </div>

            </div>


            <!-- PENDING -->

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-hourglass-split"></i>

                </div>

                <div class="stat-label">
                    Menunggu Persetujuan
                </div>

                <div class="stat-value">

                    <?= number_format(
                        $total_pending
                    ); ?>

                </div>

                <div class="stat-desc">
                    Checkout yang masih pending
                </div>

            </div>


            <!-- TOTAL NILAI -->

            <div class="stat-card">

                <div class="stat-icon">

                    <i class="bi bi-cash-stack"></i>

                </div>

                <div class="stat-label">
                    Nilai Pembelian
                </div>

                <div class="stat-value">

                    Rp
                    <?= number_format(
                        $total_nilai_pembelian,
                        0,
                        ',',
                        '.'
                    ); ?>

                </div>

                <div class="stat-desc">
                    Pembelian yang disetujui
                </div>

            </div>


        </div>



        <!-- ==================================================
             LOWER SECTION
        ================================================== -->

        <div class="lower-grid">


            <!-- MENU CEPAT -->

            <div class="section-card">


                <div class="section-header">

                    <h5>
                        Menu Cepat
                    </h5>

                    <p>
                        Akses fitur utama EcoBank
                    </p>

                </div>


                <div class="quick-menu">


                    <!-- HARGA STOK -->

                    <a
                        href="sampah.php"
                        class="quick-item"
                    >

                        <div class="quick-icon">

                            <i class="bi bi-recycle"></i>

                        </div>

                        <strong>
                            Harga & Stok
                        </strong>

                        <span>
                            Lihat harga dan stok sampah yang tersedia
                        </span>

                    </a>


                    <!-- PEMBELIAN -->

                    <a
                        href="pembelian.php"
                        class="quick-item"
                    >

                        <div class="quick-icon">

                            <i class="bi bi-cart-check"></i>

                        </div>

                        <strong>
                            Pembelian Saya
                        </strong>

                        <span>
                            Lihat riwayat dan status pembelian
                        </span>

                    </a>


                </div>


            </div>



            <!-- AKTIVITAS -->

            <div class="section-card">


                <div class="section-header">

                    <h5>
                        Aktivitas Pembelian
                    </h5>

                    <p>
                        Status transaksi kamu
                    </p>

                </div>


                <div class="status-list">


                    <!-- DISETUJUI -->

                    <div class="status-item">

                        <div class="status-icon">

                            <i class="bi bi-check-circle"></i>

                        </div>

                        <div>

                            <div class="status-title">

                                Pembelian Disetujui

                            </div>

                            <div class="status-desc">

                                <?= number_format(
                                    $jumlah_disetujui
                                ); ?>

                                transaksi berhasil

                            </div>

                        </div>

                    </div>


                    <!-- PENDING -->

                    <div class="status-item">

                        <div class="status-icon">

                            <i class="bi bi-clock"></i>

                        </div>

                        <div>

                            <div class="status-title">

                                Menunggu Persetujuan

                            </div>

                            <div class="status-desc">

                                <?= number_format(
                                    $total_pending
                                ); ?>

                                transaksi pending

                            </div>

                        </div>

                    </div>


                    <!-- DITOLAK -->

                    <div class="status-item">

                        <div class="status-icon">

                            <i class="bi bi-x-circle"></i>

                        </div>

                        <div>

                            <div class="status-title">

                                Pembelian Ditolak

                            </div>

                            <div class="status-desc">

                                <?= number_format(
                                    $total_ditolak
                                ); ?>

                                transaksi ditolak

                            </div>

                        </div>

                    </div>


                </div>


            </div>


        </div>



        <!-- FOOTER -->

        <div class="footer">

            EcoBank • Sistem Bank Sampah

        </div>


    </div>


</main>


</body>

</html>
