
<?php

// =========================================
// KONFIGURASI & AUTHENTICATION
// =========================================

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("pengepul");


// =========================================
// ID PENGEPUL
// =========================================

$id_user = (int) $_SESSION['id_user'];


// =========================================
// AMBIL DATA USER
// =========================================

$query_user = mysqli_query(
    $conn,
    "SELECT nama, username, saldo
     FROM users
     WHERE id_user = '$id_user'"
);

$user = mysqli_fetch_assoc($query_user);


// =========================================
// AMBIL DATA PEMBELIAN
// =========================================

$query_pembelian = mysqli_query(
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
     AND t.tipe = 'jual'
     ORDER BY t.id_transaksi DESC"
);


// =========================================
// HITUNG TOTAL PEMBELIAN
// =========================================

$query_total = mysqli_query(
    $conn,
    "SELECT
        COUNT(*) AS jumlah_transaksi,
        COALESCE(SUM(berat_kg), 0) AS total_berat,
        COALESCE(SUM(total_rp), 0) AS total_uang
     FROM transaksi
     WHERE id_user = '$id_user'
     AND tipe = 'jual'"
);

$total_data = mysqli_fetch_assoc($query_total);

$jumlah_transaksi = (int) $total_data['jumlah_transaksi'];
$total_berat      = (float) $total_data['total_berat'];
$total_uang       = (float) $total_data['total_uang'];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pembelian Pengepul - EcoBank</title>


    <!-- BOOTSTRAP -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- BOOTSTRAP ICONS -->

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


        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 215px;

            height: 100vh;

            background: #111;

            color: white;

            padding: 25px 15px;

            z-index: 1000;

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 0 10px 25px;

            border-bottom: 1px solid #2d2d2d;

            margin-bottom: 22px;

        }


        .brand-icon {

            width: 38px;

            height: 38px;

            background: white;

            color: #111;

            border-radius: 10px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

        }


        .brand-text strong {

            display: block;

            font-size: 17px;

        }


        .brand-text small {

            color: #999;

            font-size: 9px;

        }


        .menu-title {

            color: #777;

            font-size: 10px;

            font-weight: 700;

            margin: 0 10px 10px;

            text-transform: uppercase;

            letter-spacing: .5px;

        }


        .sidebar-menu {

            display: flex;

            flex-direction: column;

            gap: 5px;

        }


        .sidebar-menu a {

            text-decoration: none;

            color: #aaa;

            padding: 11px 12px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            gap: 12px;

            font-size: 13px;

            font-weight: 600;

            transition: .2s;

        }


        .sidebar-menu a:hover {

            background: #292929;

            color: white;

        }


        .sidebar-menu a.active {

            background: white;

            color: #111;

        }


        .sidebar-menu i {

            font-size: 16px;

        }


        .sidebar-user {

            background: #1d1d1d;

            border-radius: 12px;

            padding: 13px;

            margin-bottom: 25px;

        }


        .sidebar-user-icon {

            width: 38px;

            height: 38px;

            background: white;

            color: #111;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 8px;

        }


        .sidebar-user strong {

            display: block;

            font-size: 12px;

        }


        .sidebar-user small {

            color: #999;

            font-size: 10px;

        }


        .sidebar-bottom {

            position: absolute;

            left: 15px;

            right: 15px;

            bottom: 25px;

        }


        .logout {

            border-top: 1px solid #2d2d2d;

            padding-top: 18px;

        }


        .logout a {

            color: #aaa;

            text-decoration: none;

            font-size: 13px;

            display: flex;

            gap: 10px;

            align-items: center;

            padding: 10px;

        }


        .logout a:hover {

            color: white;

        }


        /* =========================================
           MAIN
        ========================================= */

        .main-content {

            margin-left: 215px;

            min-height: 100vh;

        }


        .content-wrapper {

            max-width: 1350px;

            margin: auto;

            padding: 45px 35px;

        }


        /* =========================================
           TOP HEADER
        ========================================= */

        .page-header {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            padding: 25px;

            margin-bottom: 20px;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .page-header h1 {

            margin: 0;

            font-size: 30px;

            font-weight: 700;

        }


        .page-header p {

            margin: 6px 0 0;

            color: #777;

            font-size: 14px;

        }


        .header-icon {

            width: 45px;

            height: 45px;

            border-radius: 12px;

            background: #111;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

        }


        /* =========================================
           STATISTIC
        ========================================= */

        .stat-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 22px;

            height: 100%;

        }


        .stat-icon {

            width: 40px;

            height: 40px;

            background: #f1f2f4;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 15px;

        }


        .stat-label {

            color: #777;

            font-size: 12px;

            margin-bottom: 5px;

        }


        .stat-value {

            font-size: 23px;

            font-weight: 700;

        }


        /* =========================================
           MAIN CARD
        ========================================= */

        .main-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            overflow: hidden;

        }


        .card-header-custom {

            padding: 22px 25px;

            border-bottom: 1px solid #e5e7eb;

        }


        .card-header-custom h5 {

            margin: 0;

            font-weight: 700;

        }


        .card-header-custom p {

            margin: 5px 0 0;

            color: #888;

            font-size: 12px;

        }


        /* =========================================
           TABLE
        ========================================= */

        .table {

            margin-bottom: 0;

        }


        .table thead th {

            background: #111;

            color: white;

            border: none;

            padding: 14px;

            font-size: 11px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 14px;

            font-size: 13px;

            vertical-align: middle;

            border-color: #ededed;

        }


        .table tbody tr:hover {

            background: #fafafa;

        }


        .badge-success-custom {

            background: #111;

            color: white;

            border-radius: 20px;

            padding: 7px 11px;

            font-size: 10px;

        }


        .empty-state {

            padding: 60px !important;

            color: #777;

        }


        /* =========================================
           BUTTON
        ========================================= */

        .btn-black {

            background: #111;

            color: white;

            border: none;

            border-radius: 9px;

            padding: 9px 15px;

            font-weight: 600;

            text-decoration: none;

            font-size: 12px;

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

            font-weight: 600;

            text-decoration: none;

            font-size: 12px;

        }


        .btn-outline-black:hover {

            background: #111;

            color: white;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 900px) {

            .sidebar {

                width: 75px;

            }


            .brand-text,

            .menu-title,

            .sidebar-user strong,

            .sidebar-user small,

            .sidebar-menu span,

            .logout span {

                display: none;

            }


            .brand {

                justify-content: center;

                padding-left: 0;

                padding-right: 0;

            }


            .sidebar-user {

                text-align: center;

            }


            .sidebar-user-icon {

                margin: auto;

            }


            .sidebar-menu a {

                justify-content: center;

            }


            .logout a {

                justify-content: center;

            }


            .main-content {

                margin-left: 75px;

            }

        }


        @media (max-width: 600px) {

            .content-wrapper {

                padding: 20px 15px;

            }


            .page-header {

                padding: 20px;

            }


            .page-header h1 {

                font-size: 23px;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

<aside class="sidebar">


    <!-- BRAND -->

    <div class="brand">

        <div class="brand-icon">

            <i class="bi bi-recycle"></i>

        </div>


        <div class="brand-text">

            <strong>EcoBank</strong>

            <small>BANK SAMPAH</small>

        </div>

    </div>


    <!-- USER -->

    <div class="sidebar-user">

        <div class="sidebar-user-icon">

            <i class="bi bi-person"></i>

        </div>


        <strong>

            <?= htmlspecialchars(
                $user['nama'] ?? 'Pengepul'
            ); ?>

        </strong>


        <small>

            <i class="bi bi-person-badge"></i>
            Pengepul

        </small>

    </div>


    <!-- MENU -->

    <div class="menu-title">

        Menu Utama

    </div>


    <nav class="sidebar-menu">


        <!-- DASHBOARD -->

        <a href="dashboard.php">

            <i class="bi bi-grid-1x2"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- HARGA & STOK -->

        <a href="sampah.php">

            <i class="bi bi-recycle"></i>

            <span>
                Harga & Stok
            </span>

        </a>


        <!-- PEMBELIAN -->

        <a
            href="pembelian.php"
            class="active"
        >

            <i class="bi bi-cart3"></i>

            <span>
                Pembelian Saya
            </span>

        </a>


    </nav>


    <!-- LOGOUT -->

    <div class="sidebar-bottom">

        <div class="logout">

            <a href="../logout.php">

                <i class="bi bi-box-arrow-right"></i>

                <span>
                    Logout
                </span>

            </a>

        </div>

    </div>


</aside>



<!-- =====================================================
     MAIN CONTENT
===================================================== -->

<main class="main-content">


    <div class="content-wrapper">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="page-header">


            <div>

                <h1>

                    Pembelian Sampah

                </h1>


                <p>

                    Riwayat pembelian sampah Anda di EcoBank

                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-cart-check"></i>

            </div>


        </div>



        <!-- =================================================
             PESAN BERHASIL
        ================================================== -->

        <?php if (
            isset($_GET['success'])
            && $_GET['success'] == '1'
        ): ?>


            <div class="alert alert-dark border-0 rounded-3">

                <i class="bi bi-check-circle me-2"></i>

                <strong>
                    Checkout berhasil!
                </strong>

                Pembelian berhasil disimpan ke transaksi.

            </div>


        <?php endif; ?>



        <!-- =================================================
             STATISTIK
        ================================================== -->

        <div class="row g-3 mb-4">


            <!-- TOTAL TRANSAKSI -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-receipt"></i>

                    </div>


                    <div class="stat-label">

                        Total Transaksi

                    </div>


                    <div class="stat-value">

                        <?= number_format(
                            $jumlah_transaksi
                        ); ?>

                    </div>

                </div>

            </div>



            <!-- TOTAL BERAT -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-box-seam"></i>

                    </div>


                    <div class="stat-label">

                        Total Sampah Dibeli

                    </div>


                    <div class="stat-value">

                        <?= number_format(
                            $total_berat,
                            2,
                            ',',
                            '.'
                        ); ?>

                        Kg

                    </div>

                </div>

            </div>



            <!-- TOTAL PENGELUARAN -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-wallet2"></i>

                    </div>


                    <div class="stat-label">

                        Total Pengeluaran

                    </div>


                    <div class="stat-value">

                        <?= rupiah($total_uang); ?>

                    </div>

                </div>

            </div>


        </div>



        <!-- =================================================
             RIWAYAT PEMBELIAN
        ================================================== -->

        <div class="main-card">


            <!-- HEADER -->

            <div class="card-header-custom">

                <div class="d-flex justify-content-between align-items-center">


                    <div>

                        <h5>

                            Riwayat Pembelian

                        </h5>


                        <p>

                            Daftar transaksi checkout yang telah disetujui admin.

                        </p>

                    </div>


                    <span class="badge bg-dark rounded-pill px-3 py-2">

                        <?= number_format(
                            $jumlah_transaksi
                        ); ?>

                        Transaksi

                    </span>

                </div>

            </div>



            <!-- TABLE -->

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
                        $query_pembelian
                        &&
                        mysqli_num_rows(
                            $query_pembelian
                        ) > 0
                    ): ?>


                        <?php

                        $no = 1;

                        while (
                            $row = mysqli_fetch_assoc(
                                $query_pembelian
                            )
                        ):

                        ?>


                            <tr>


                                <!-- NO -->

                                <td>

                                    <?= $no++; ?>

                                </td>


                                <!-- TANGGAL -->

                                <td>

                                    <?= htmlspecialchars(
                                        $row['tanggal']
                                    ); ?>

                                </td>


                                <!-- JENIS SAMPAH -->

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $row['jenis_sampah']
                                            ?? '-'
                                        ); ?>

                                    </strong>

                                </td>


                                <!-- HARGA -->

                                <td>

                                    <?= rupiah(
                                        $row['harga_per_kg']
                                    ); ?>

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


                                <!-- TOTAL -->

                                <td>

                                    <strong>

                                        <?= rupiah(
                                            $row['total_rp']
                                        ); ?>

                                    </strong>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span class="badge-success-custom">

                                        <i class="bi bi-check-circle me-1"></i>

                                        Berhasil

                                    </span>

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
                                        font-size:48px;
                                        margin-bottom:10px;
                                    "
                                >

                                    <i class="bi bi-cart-x"></i>

                                </div>


                                <strong>

                                    Belum ada pembelian

                                </strong>


                                <div class="small mt-1">

                                    Belum ada transaksi pembelian
                                    yang disetujui admin.

                                </div>


                                <div class="mt-3">

                                    <a
                                        href="sampah.php"
                                        class="btn-black"
                                    >

                                        <i class="bi bi-recycle me-1"></i>

                                        Lihat Harga & Stok

                                    </a>

                                </div>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>


        </div>



        <!-- =================================================
             BUTTON
        ================================================== -->

        <div class="mt-4 d-flex gap-2">


            <a
                href="dashboard.php"
                class="btn-outline-black"
            >

                <i class="bi bi-arrow-left me-1"></i>

                Dashboard

            </a>


            <a
                href="sampah.php"
                class="btn-black"
            >

                <i class="bi bi-recycle me-1"></i>

                Harga & Stok

            </a>


        </div>



        <!-- =================================================
             FOOTER
        ================================================== -->

        <div class="text-center mt-4 text-secondary">

            <small>

                EcoBank • Sistem Bank Sampah

            </small>

        </div>


    </div>


</main>



<!-- BOOTSTRAP JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

