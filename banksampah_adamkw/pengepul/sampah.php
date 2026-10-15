
<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("pengepul");


// ==================================================
// AMBIL DATA USER
// ==================================================

$id_user = (int) $_SESSION['id_user'];

$query_user = mysqli_query(
    $conn,
    "SELECT nama, username, saldo
     FROM users
     WHERE id_user = $id_user"
);

if (!$query_user) {
    die("Gagal mengambil data user: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($query_user);

if (!$user) {
    die("Data user tidak ditemukan.");
}


// ==================================================
// AMBIL DATA SAMPAH
// ==================================================

$query_sampah = mysqli_query(
    $conn,
    "SELECT
        id_sampah,
        jenis_sampah,
        harga_per_kg,
        stok_kg
     FROM sampah
     ORDER BY jenis_sampah ASC"
);

if (!$query_sampah) {
    die(
        "Gagal mengambil data sampah: "
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

    <title>
        Harga & Stok - EcoBank
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

            top: 0;

            left: 0;

            width: 250px;

            height: 100vh;

            background: #111;

            color: white;

            padding: 25px 18px;

            z-index: 1000;

            display: flex;

            flex-direction: column;

            box-shadow:
                5px 0 25px
                rgba(0,0,0,.08);

        }


        /* BRAND */

        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                5px 10px 25px;

            border-bottom:
                1px solid #2d2d2d;

            margin-bottom: 25px;

        }


        .brand-icon {

            width: 43px;

            height: 43px;

            border-radius: 12px;

            background: white;

            color: #111;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

        }


        .brand-text h4 {

            margin: 0;

            font-size: 18px;

            font-weight: 700;

        }


        .brand-text small {

            color: #aaa;

            font-size: 11px;

        }


        /* USER */

        .sidebar-user {

            background: #1c1c1c;

            border-radius: 14px;

            padding: 15px;

            margin-bottom: 25px;

        }


        .sidebar-user .user-icon {

            width: 40px;

            height: 40px;

            border-radius: 50%;

            background: white;

            color: #111;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 10px;

        }


        .sidebar-user .name {

            font-weight: 600;

            font-size: 14px;

        }


        .sidebar-user .role {

            color: #aaa;

            font-size: 11px;

            margin-top: 2px;

        }


        /* MENU */

        .menu-label {

            color: #777;

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1px;

            padding: 0 12px;

            margin-bottom: 10px;

        }


        .sidebar-menu {

            display: flex;

            flex-direction: column;

            gap: 6px;

        }


        .sidebar-menu a {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #bdbdbd;

            text-decoration: none;

            padding: 12px 14px;

            border-radius: 10px;

            font-size: 13px;

            font-weight: 600;

            transition: .2s;

        }


        .sidebar-menu a i {

            font-size: 17px;

            width: 22px;

            text-align: center;

        }


        .sidebar-menu a:hover {

            background: #1f1f1f;

            color: white;

        }


        .sidebar-menu a.active {

            background: white;

            color: #111;

        }


        /* LOGOUT */

        .sidebar-bottom {

            margin-top: auto;

            border-top:
                1px solid #2d2d2d;

            padding-top: 15px;

        }


        .logout-link {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #aaa;

            text-decoration: none;

            padding: 12px 14px;

            border-radius: 10px;

            font-size: 13px;

            font-weight: 600;

        }


        .logout-link:hover {

            background: #1f1f1f;

            color: white;

        }


        /* ==================================================
           CONTENT
        ================================================== */

        .main-content {

            margin-left: 250px;

            min-height: 100vh;

            padding: 30px;

        }


        .content-wrapper {

            max-width: 1450px;

            margin: auto;

        }


        /* ==================================================
           HEADER
        ================================================== */

        .page-header {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            padding: 25px 30px;

            margin-bottom: 25px;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.05);

        }


        .page-header h2 {

            margin: 0;

            font-weight: 700;

        }


        .page-header p {

            margin: 7px 0 0;

            color: #777;

        }


        .header-icon {

            width: 50px;

            height: 50px;

            border-radius: 14px;

            background: #111;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 22px;

        }


        /* ==================================================
           SALDO
        ================================================== */

        .saldo-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            padding: 22px 25px;

            margin-bottom: 25px;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.05);

        }


        .saldo-label {

            color: #777;

            font-size: 13px;

        }


        .saldo-value {

            margin: 4px 0 0;

            font-size: 25px;

            font-weight: 700;

        }


        .saldo-icon {

            width: 50px;

            height: 50px;

            border-radius: 13px;

            background: #111;

            color: white;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 21px;

        }


        /* ==================================================
           MAIN CARD
        ================================================== */

        .main-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            overflow: hidden;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.05);

        }


        .card-header-custom {

            padding: 22px 25px;

            border-bottom:
                1px solid #e8e8e8;

        }


        .card-header-custom h5 {

            margin: 0;

            font-weight: 700;

        }


        .card-header-custom small {

            color: #777;

        }


        /* ==================================================
           TABLE
        ================================================== */

        .table {

            margin: 0;

        }


        .table thead th {

            background: #111;

            color: white;

            border: none;

            padding: 15px;

            font-size: 12px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 16px 15px;

            vertical-align: middle;

            border-color: #ededed;

            font-size: 13px;

        }


        .table tbody tr:hover {

            background: #fafafa;

        }


        .jenis {

            font-weight: 700;

        }


        .harga {

            font-weight: 700;

        }


        /* ==================================================
           STOK BADGE
        ================================================== */

        .stok-tersedia {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            background: #f1f1f1;

            color: #111;

            padding: 7px 11px;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 600;

        }


        .stok-habis {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            background: #e9ecef;

            color: #777;

            padding: 7px 11px;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 600;

        }


        /* ==================================================
           CHECKOUT BUTTON
        ================================================== */

        .btn-checkout {

            background: #111;

            color: white;

            border: none;

            border-radius: 9px;

            padding: 8px 14px;

            font-size: 12px;

            font-weight: 600;

            text-decoration: none;

            display: inline-block;

        }


        .btn-checkout:hover {

            background: #333;

            color: white;

        }


        /* ==================================================
           EMPTY
        ================================================== */

        .empty-state {

            padding: 60px !important;

            text-align: center;

            color: #777;

        }


        .empty-icon {

            font-size: 50px;

            margin-bottom: 10px;

        }


        /* ==================================================
           FOOTER
        ================================================== */

        .footer {

            text-align: center;

            color: #777;

            margin-top: 25px;

            padding-bottom: 20px;

        }


        /* ==================================================
           RESPONSIVE
        ================================================== */

        @media (max-width: 768px) {

            .sidebar {

                width: 220px;

            }


            .main-content {

                margin-left: 220px;

                padding: 15px;

            }

        }

    </style>

</head>


<body>


<!-- ==================================================
     SIDEBAR
================================================== -->

<aside class="sidebar">


    <!-- BRAND -->

    <div class="brand">

        <div class="brand-icon">

            <i class="bi bi-recycle"></i>

        </div>


        <div class="brand-text">

            <h4>EcoBank</h4>

            <small>Bank Sampah</small>

        </div>

    </div>


    <!-- USER -->

    <div class="sidebar-user">

        <div class="user-icon">

            <i class="bi bi-person"></i>

        </div>


        <div class="name">

            <?= htmlspecialchars(
                $user['nama']
            ); ?>

        </div>


        <div class="role">

            <i class="bi bi-person-badge me-1"></i>

            Pengepul

        </div>

    </div>


    <!-- MENU -->

    <div class="menu-label">

        Menu Utama

    </div>


    <nav class="sidebar-menu">


        <!-- DASHBOARD -->

        <a href="dashboard.php">

            <i class="bi bi-grid-1x2"></i>

            Dashboard

        </a>


        <!-- HARGA & STOK -->

        <a
            href="sampah.php"
            class="active"
        >

            <i class="bi bi-recycle"></i>

            Harga & Stok

        </a>


        <!-- PEMBELIAN -->

        <a href="pembelian.php">

            <i class="bi bi-cart-check"></i>

            Pembelian Saya

        </a>


    </nav>


    <!-- LOGOUT -->

    <div class="sidebar-bottom">

        <a
            href="../logout.php"
            class="logout-link"
            onclick="return confirm('Yakin ingin logout?');"
        >

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>

    </div>


</aside>


<!-- ==================================================
     MAIN
================================================== -->

<main class="main-content">


<div class="content-wrapper">


    <!-- HEADER -->

    <div class="page-header">

        <div
            class="
                d-flex
                justify-content-between
                align-items-center
            "
        >

            <div>

                <h2>

                    Harga & Stok Sampah

                </h2>


                <p>

                    Lihat harga pembelian dan stok sampah
                    yang tersedia di EcoBank.

                </p>

            </div>


            <div class="header-icon">

                <i class="bi bi-recycle"></i>

            </div>

        </div>

    </div>


    <!-- SALDO -->

    <div class="saldo-card">

        <div
            class="
                d-flex
                justify-content-between
                align-items-center
            "
        >

            <div>

                <div class="saldo-label">

                    <i class="bi bi-wallet2 me-1"></i>

                    Saldo Anda

                </div>


                <div class="saldo-value">

                    Rp
                    <?= number_format(
                        $user['saldo'],
                        0,
                        ',',
                        '.'
                    ); ?>

                </div>

            </div>


            <div class="saldo-icon">

                <i class="bi bi-wallet2"></i>

            </div>

        </div>

    </div>


    <!-- DATA SAMPAH -->

    <div class="main-card">


        <!-- CARD HEADER -->

        <div class="card-header-custom">

            <div
                class="
                    d-flex
                    justify-content-between
                    align-items-center
                "
            >

                <div>

                    <h5>

                        <i class="bi bi-box-seam me-2"></i>

                        Daftar Harga & Stok

                    </h5>


                    <small>

                        Harga dan stok yang dapat dibeli
                        oleh pengepul.

                    </small>

                </div>


                <span
                    class="
                        badge
                        bg-dark
                        rounded-pill
                        px-3
                        py-2
                    "
                >

                    <?= number_format(
                        mysqli_num_rows($query_sampah)
                    ); ?>

                    Jenis

                </span>

            </div>

        </div>


        <!-- TABLE -->

        <div class="table-responsive">

            <table class="table align-middle">


                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Jenis Sampah
                        </th>

                        <th>
                            Harga / Kg
                        </th>

                        <th>
                            Stok
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


                <?php if (
                    mysqli_num_rows($query_sampah) > 0
                ): ?>


                    <?php
                    $no = 1;

                    while (
                        $sampah =
                        mysqli_fetch_assoc(
                            $query_sampah
                        )
                    ):
                    ?>


                        <tr>


                            <!-- NO -->

                            <td>

                                <?= $no++; ?>

                            </td>


                            <!-- JENIS -->

                            <td>

                                <div class="jenis">

                                    <?= htmlspecialchars(
                                        $sampah['jenis_sampah']
                                    ); ?>

                                </div>

                            </td>


                            <!-- HARGA -->

                            <td>

                                <span class="harga">

                                    Rp
                                    <?= number_format(
                                        $sampah['harga_per_kg'],
                                        0,
                                        ',',
                                        '.'
                                    ); ?>

                                </span>

                            </td>


                            <!-- STOK -->

                            <td>

                                <?= number_format(
                                    $sampah['stok_kg'],
                                    2,
                                    ',',
                                    '.'
                                ); ?>

                                Kg

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if (
                                    $sampah['stok_kg'] > 0
                                ): ?>

                                    <span
                                        class="stok-tersedia"
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-check-circle
                                            "
                                        ></i>

                                        Tersedia

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="stok-habis"
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-x-circle
                                            "
                                        ></i>

                                        Stok Habis

                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- AKSI -->

                            <td>

                                <?php if (
                                    $sampah['stok_kg'] > 0
                                ): ?>

                                    <a
                                        href="checkout.php?id_sampah=<?= (int) $sampah['id_sampah']; ?>"
                                        class="btn-checkout"
                                    >

                                        <i
                                            class="
                                                bi
                                                bi-cart-plus
                                                me-1
                                            "
                                        ></i>

                                        Beli

                                    </a>

                                <?php else: ?>

                                    <span
                                        class="text-secondary"
                                        style="font-size:12px;"
                                    >

                                        Tidak tersedia

                                    </span>

                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            class="empty-state"
                        >

                            <div class="empty-icon">

                                <i
                                    class="
                                        bi
                                        bi-box-seam
                                    "
                                ></i>

                            </div>


                            <strong>

                                Belum ada data sampah

                            </strong>


                            <div>

                                Data harga dan stok
                                belum tersedia.

                            </div>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


    <!-- FOOTER -->

    <div class="footer">

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

