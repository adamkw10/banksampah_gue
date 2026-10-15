<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("guru");

$id_user = (int) $_SESSION['id_user'];


// =========================================
// AMBIL RIWAYAT TARIK SALDO
// =========================================

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM tarik_saldo
     WHERE id_user = '$id_user'
     ORDER BY id_tarik DESC"
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

<title>Riwayat Tarik - EcoBank</title>


<!-- Bootstrap 5 -->

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
        background: #f5f5f5;
        font-family: Arial, Helvetica, sans-serif;
        color: #111;
        font-size: 16px;
    }


    /* =====================================
       SIDEBAR
    ===================================== */

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 215px;
        height: 100vh;
        background: #111;
        color: white;
        padding: 28px 15px;
        z-index: 1000;
    }


    .brand {
        padding: 0 10px 25px;
        border-bottom: 1px solid #333;
        margin-bottom: 25px;
    }


    .brand-box {
        display: flex;
        align-items: center;
        gap: 12px;
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

        font-size: 21px;
    }


    .brand-title {
        font-size: 19px;
        font-weight: 700;
        line-height: 1.1;
    }


    .brand-subtitle {
        font-size: 10px;
        color: #aaa;
        margin-top: 3px;
    }


    .menu-title {
        color: #888;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
        margin: 22px 10px 10px;
        text-transform: uppercase;
    }


    .sidebar a {
        display: flex;
        align-items: center;
        gap: 12px;

        padding: 13px 11px;
        margin-bottom: 4px;

        color: #ddd;
        text-decoration: none;

        border-radius: 9px;

        font-size: 15px;
        font-weight: 500;

        transition: .2s;
    }


    .sidebar a i {
        font-size: 17px;
        width: 18px;
    }


    .sidebar a:hover {
        background: #222;
        color: white;
    }


    .sidebar a.active {
        background: white;
        color: #111;
        font-weight: 700;
    }


    .sidebar a.logout {
        color: #ff5555;
        margin-top: 15px;
    }


    .sidebar a.logout:hover {
        background: #2a1717;
    }


    /* =====================================
       MAIN
    ===================================== */

    .main {
        margin-left: 215px;
        min-height: 100vh;
        padding: 48px 30px;
    }


    .content {
        max-width: 1400px;
        margin: auto;
    }


    /* =====================================
       HEADER
    ===================================== */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 30px;
    }


    .page-header h1 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 5px;
    }


    .page-header p {
        font-size: 16px;
        color: #777;
        margin: 0;
    }


    .profile {
        background: white;
        border-radius: 15px;
        padding: 11px 18px;

        display: flex;
        align-items: center;
        gap: 12px;

        box-shadow: 0 3px 15px rgba(0,0,0,.05);
    }


    .profile-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #111;
        color: white;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 15px;
    }


    .profile-name {
        font-size: 14px;
        font-weight: 700;
    }


    .profile-role {
        font-size: 12px;
        color: #777;
    }


    /* =====================================
       STAT CARD
    ===================================== */

    .info-card {
        background: #111;
        color: white;

        border-radius: 18px;
        padding: 25px;

        margin-bottom: 28px;

        box-shadow: 0 15px 30px rgba(0,0,0,.12);
    }


    .info-title {
        font-size: 15px;
        color: #bbb;
        margin-bottom: 8px;
    }


    .info-card h2 {
        font-size: 30px;
        font-weight: 800;
        margin: 0;
    }


    .info-card p {
        font-size: 14px;
        color: #bbb;
        margin-top: 10px;
        margin-bottom: 0;
    }


    /* =====================================
       TABLE CARD
    ===================================== */

    .history-card {
        background: white;
        border-radius: 18px;

        border: 1px solid #e5e5e5;

        padding: 25px;

        box-shadow: 0 4px 15px rgba(0,0,0,.04);
    }


    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;

        margin-bottom: 20px;
    }


    .history-header h3 {
        font-size: 22px;
        font-weight: 800;
        margin: 0;
    }


    .history-header p {
        margin: 4px 0 0;
        color: #777;
        font-size: 14px;
    }


    /* =====================================
       TABLE
    ===================================== */

    .table {
        margin-bottom: 0;
        font-size: 15px;
    }


    .table thead th {
        background: #f5f5f5;
        color: #333;

        font-size: 14px;
        font-weight: 700;

        padding: 15px 14px;

        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }


    .table tbody td {
        padding: 17px 14px;
        vertical-align: middle;
        border-color: #eee;

        font-size: 15px;
    }


    .table tbody tr:hover {
        background: #fafafa;
    }


    /* =====================================
       STATUS
    ===================================== */

    .status {
        display: inline-flex;
        align-items: center;

        padding: 7px 12px;
        border-radius: 8px;

        font-size: 13px;
        font-weight: 700;
    }


    .status-pending {
        background: #fff3cd;
        color: #856404;
    }


    .status-diterima {
        background: #d1e7dd;
        color: #0f5132;
    }


    .status-ditolak {
        background: #f8d7da;
        color: #842029;
    }


    .status-lain {
        background: #e9ecef;
        color: #495057;
    }


    /* =====================================
       NOMINAL
    ===================================== */

    .nominal {
        font-weight: 800;
        font-size: 16px;
    }


    /* =====================================
       BUTTON
    ===================================== */

    .btn-ecobank {
        background: #111;
        color: white;

        border: none;
        border-radius: 9px;

        padding: 12px 20px;

        font-size: 15px;
        font-weight: 700;

        transition: .2s;
    }


    .btn-ecobank:hover {
        background: #2a2a2a;
        color: white;
    }


    .btn-back {
        background: white;
        color: #111;

        border: 1px solid #ddd;
        border-radius: 9px;

        padding: 11px 18px;

        font-size: 15px;
        font-weight: 600;
    }


    .btn-back:hover {
        background: #eee;
        color: #111;
    }


    /* =====================================
       EMPTY
    ===================================== */

    .empty {
        text-align: center;
        padding: 55px 20px;
        color: #777;
    }


    .empty-icon {
        width: 60px;
        height: 60px;

        background: #111;
        color: white;

        border-radius: 15px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 15px;

        font-size: 25px;
    }


    .empty h4 {
        font-size: 19px;
        font-weight: 700;
        color: #222;
    }


    .empty p {
        font-size: 14px;
    }


    /* =====================================
       FOOTER
    ===================================== */

    .footer {
        margin-top: 30px;
        padding-top: 20px;

        border-top: 1px solid #ddd;

        display: flex;
        justify-content: space-between;

        color: #888;
        font-size: 13px;
    }


    /* =====================================
       RESPONSIVE
    ===================================== */

    @media (max-width: 900px) {

        .sidebar {
            width: 190px;
        }

        .main {
            margin-left: 190px;
            padding: 30px 20px;
        }

        .page-header h1 {
            font-size: 27px;
        }

    }


    @media (max-width: 700px) {

        .sidebar {
            position: relative;
            width: 100%;
            height: auto;
        }

        .main {
            margin-left: 0;
            padding: 25px 15px;
        }

        .page-header {
            align-items: flex-start;
            gap: 15px;
        }

        .profile {
            display: none;
        }

        .page-header h1 {
            font-size: 25px;
        }

        .history-card {
            padding: 15px;
        }

        .table {
            min-width: 650px;
        }

        .footer {
            flex-direction: column;
            gap: 8px;
        }

    }

</style>
```

</head>

<body>

<!-- =========================================
     SIDEBAR
========================================= -->

<aside class="sidebar">

```
<!-- BRAND -->

<div class="brand">

    <div class="brand-box">

        <div class="brand-icon">
            ♻️
        </div>

        <div>

            <div class="brand-title">
                EcoBank
            </div>

            <div class="brand-subtitle">
                Sistem Bank Sampah
            </div>

        </div>

    </div>

</div>


<!-- MENU UTAMA -->

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


<!-- RIWAYAT -->

<div class="menu-title">
    Riwayat
</div>


<a href="riwayat.php">

    <i class="bi bi-clock-history"></i>

    Riwayat Setor

</a>


<a
    href="riwayat_tarik.php"
    class="active"
>

    <i class="bi bi-receipt"></i>

    Riwayat Tarik

</a>


<!-- LOGOUT -->

<a
    href="../logout.php"
    class="logout"
>

    <i class="bi bi-box-arrow-right"></i>

    Logout

</a>
```

</aside>

<!-- =========================================
     MAIN
========================================= -->

<main class="main">

<div class="content">

```
<!-- =====================================
     HEADER
====================================== -->

<div class="page-header">


    <div>

        <h1>
            Riwayat Tarik Saldo
        </h1>

        <p>
            Lihat seluruh riwayat penarikan saldo EcoBank kamu.
        </p>

    </div>


    <div class="profile">

        <div class="profile-icon">

            <i class="bi bi-person"></i>

        </div>

        <div>

            <div class="profile-name">

                <?= htmlspecialchars(
                    $_SESSION['nama']
                ); ?>

            </div>

            <div class="profile-role">
                Guru
            </div>

        </div>

    </div>


</div>


<!-- =====================================
     INFO
====================================== -->

<div class="info-card">


    <div class="info-title">
        Informasi Penarikan
    </div>


    <h2>
        💸 Riwayat Tarik Saldo
    </h2>


    <p>
        Semua pengajuan penarikan saldo kamu akan tampil di halaman ini.
    </p>


</div>


<!-- =====================================
     HISTORY
====================================== -->

<div class="history-card">


    <div class="history-header">

        <div>

            <h3>
                Daftar Penarikan
            </h3>

            <p>
                Status pengajuan tarik saldo kamu.
            </p>

        </div>


        <a
            href="../tarik/index.php"
            class="btn btn-ecobank"
        >

            <i class="bi bi-wallet2 me-1"></i>

            Ajukan Tarik

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
                        Nominal
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
            ) : ?>


                <?php $no = 1; ?>


                <?php while (
                    $row = mysqli_fetch_assoc($query)
                ) : ?>


                    <tr>


                        <td>

                            <strong>
                                <?= $no++; ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row['tanggal']
                            ); ?>

                        </td>


                        <td>

                            <span class="nominal">

                                Rp
                                <?= number_format(
                                    $row['nominal'],
                                    0,
                                    ',',
                                    '.'
                                ); ?>

                            </span>

                        </td>


                        <td>


                            <?php if (
                                $row['status'] == 'pending'
                            ) : ?>

                                <span class="status status-pending">

                                    <i class="bi bi-hourglass-split me-1"></i>

                                    Pending

                                </span>


                            <?php elseif (
                                $row['status'] == 'diterima'
                            ) : ?>

                                <span class="status status-diterima">

                                    <i class="bi bi-check-circle me-1"></i>

                                    Diterima

                                </span>


                            <?php elseif (
                                $row['status'] == 'ditolak'
                            ) : ?>

                                <span class="status status-ditolak">

                                    <i class="bi bi-x-circle me-1"></i>

                                    Ditolak

                                </span>


                            <?php else : ?>

                                <span class="status status-lain">

                                    <?= htmlspecialchars(
                                        $row['status']
                                    ); ?>

                                </span>

                            <?php endif; ?>


                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else : ?>


                <tr>

                    <td
                        colspan="4"
                    >

                        <div class="empty">


                            <div class="empty-icon">

                                <i class="bi bi-receipt"></i>

                            </div>


                            <h4>
                                Belum Ada Riwayat Penarikan
                            </h4>


                            <p>
                                Kamu belum pernah mengajukan penarikan saldo.
                            </p>


                            <a
                                href="../tarik/index.php"
                                class="btn btn-ecobank mt-2"
                            >

                                <i class="bi bi-wallet2 me-1"></i>

                                Ajukan Tarik Saldo

                            </a>


                        </div>

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>


</div>


<!-- =====================================
     BUTTON
====================================== -->

<div class="mt-4 d-flex gap-2 flex-wrap">


    <a
        href="../tarik/index.php"
        class="btn btn-ecobank"
    >

        <i class="bi bi-wallet2 me-1"></i>

        Tarik Saldo

    </a>


    <a
        href="dashboard.php"
        class="btn btn-back"
    >

        <i class="bi bi-arrow-left me-1"></i>

        Kembali ke Dashboard

    </a>


</div>


<!-- =====================================
     FOOTER
====================================== -->

<div class="footer">

    <span>
        EcoBank Sampah © 2026
    </span>

    <span>
        Riwayat Tarik Guru
    </span>

</div>
```

</div>

</main>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
