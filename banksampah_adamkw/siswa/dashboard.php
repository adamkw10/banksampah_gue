<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("siswa");

$id_user = $_SESSION['id_user'];


// =========================================
// AMBIL DATA SISWA
// =========================================

$query_user = mysqli_query(
    $conn,
    "SELECT nama, username, saldo
     FROM users
     WHERE id_user = '$id_user'"
);

$user = mysqli_fetch_assoc($query_user);

$nama = $user['nama'] ?? $_SESSION['nama'];
$username = $user['username'] ?? $_SESSION['username'];
$saldo = $user['saldo'] ?? 0;


// =========================================
// HITUNG JUMLAH SETORAN
// =========================================

$query_setor = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM transaksi
     WHERE id_user = '$id_user'
     AND tipe = 'setor'
     AND status != 'ditolak'"
);

$total_setor = mysqli_fetch_assoc($query_setor)['total'] ?? 0;


// =========================================
// HITUNG TOTAL BERAT SETORAN
// =========================================

$query_berat = mysqli_query(
    $conn,
    "SELECT COALESCE(SUM(berat_kg),0) AS total
     FROM transaksi
     WHERE id_user = '$id_user'
     AND tipe = 'setor'
     AND status != 'ditolak'"
);

$total_berat = mysqli_fetch_assoc($query_berat)['total'] ?? 0;

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

<title>Dashboard Siswa - EcoBank</title>


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

        background: #f5f5f5;

        color: #111;

        font-family:
            Arial,
            Helvetica,
            sans-serif;

    }


    /* ================================
       SIDEBAR
    ================================ */

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

        font-size: 22px;

        font-weight: 700;

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


    .menu-title {

        font-size: 11px;

        color: #888;

        text-transform: uppercase;

        letter-spacing: 1px;

        margin: 10px 12px;

    }


    .sidebar a {

        display: flex;

        align-items: center;

        gap: 12px;

        color: #aaa;

        text-decoration: none;

        padding: 12px 14px;

        border-radius: 10px;

        margin-bottom: 6px;

        transition: 0.2s;

    }


    .sidebar a:hover,
    .sidebar a.active {

        background: white;

        color: #111;

    }


    .logout-link {

        position: absolute;

        bottom: 25px;

        left: 18px;

        right: 18px;

    }


    /* ================================
       MAIN
    ================================ */

    .main {

        margin-left: 250px;

        padding: 35px;

    }


    /* ================================
       HEADER
    ================================ */

    .topbar {

        display: flex;

        justify-content: space-between;

        align-items: center;

        margin-bottom: 30px;

    }


    .topbar h2 {

        font-weight: 700;

        margin-bottom: 5px;

    }


    .topbar p {

        color: #777;

        margin: 0;

    }


    .profile {

        display: flex;

        align-items: center;

        gap: 12px;

    }


    .profile-icon {

        width: 45px;
        height: 45px;

        border-radius: 50%;

        background: #111;

        color: white;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 20px;

    }


    /* ================================
       SALDO
    ================================ */

    .saldo-card {

        background: #111;

        color: white;

        border-radius: 18px;

        padding: 28px;

        min-height: 180px;

        position: relative;

        overflow: hidden;

    }


    .saldo-card::after {

        content: "";

        position: absolute;

        width: 180px;
        height: 180px;

        border: 35px solid rgba(255,255,255,0.05);

        border-radius: 50%;

        right: -50px;

        top: -60px;

    }


    .saldo-label {

        color: #aaa;

        font-size: 14px;

    }


    .saldo-value {

        font-size: 32px;

        font-weight: 700;

        margin-top: 10px;

    }


    /* ================================
       STAT CARD
    ================================ */

    .stat-card {

        background: white;

        border: 1px solid #e8e8e8;

        border-radius: 18px;

        padding: 24px;

        height: 100%;

        transition: 0.2s;

    }


    .stat-card:hover {

        transform: translateY(-3px);

        box-shadow:
            0 10px 25px rgba(0,0,0,0.06);

    }


    .stat-icon {

        width: 45px;
        height: 45px;

        border-radius: 12px;

        background: #f1f1f1;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 20px;

        margin-bottom: 15px;

    }


    .stat-number {

        font-size: 25px;

        font-weight: 700;

    }


    .stat-label {

        color: #777;

        font-size: 14px;

    }


    /* ================================
       MENU CARD
    ================================ */

    .menu-card {

        background: white;

        border: 1px solid #e8e8e8;

        border-radius: 18px;

        padding: 25px;

    }


    .menu-card h5 {

        font-weight: 700;

        margin-bottom: 20px;

    }


    .feature {

        display: flex;

        align-items: center;

        gap: 15px;

        padding: 17px;

        border: 1px solid #eee;

        border-radius: 14px;

        text-decoration: none;

        color: #111;

        transition: 0.2s;

        height: 100%;

    }


    .feature:hover {

        background: #111;

        color: white;

        transform: translateY(-2px);

    }


    .feature-icon {

        width: 45px;
        height: 45px;

        min-width: 45px;

        border-radius: 12px;

        background: #111;

        color: white;

        display: flex;

        align-items: center;
        justify-content: center;

        font-size: 20px;

    }


    .feature:hover .feature-icon {

        background: white;

        color: #111;

    }


    .feature-title {

        font-weight: 700;

        margin-bottom: 3px;

    }


    .feature-desc {

        font-size: 12px;

        color: #888;

    }


    .feature:hover .feature-desc {

        color: #bbb;

    }


    /* ================================
       RESPONSIVE
    ================================ */

    @media (max-width: 768px) {

        .sidebar {

            width: 70px;

            padding: 20px 10px;

        }


        .brand span,
        .sidebar a span,
        .menu-title {

            display: none;

        }


        .sidebar a {

            justify-content: center;

        }


        .logout-link {

            left: 10px;
            right: 10px;

        }


        .main {

            margin-left: 70px;

            padding: 20px;

        }


        .topbar {

            align-items: flex-start;

        }


        .profile {

            display: none;

        }

    }

</style>
```

</head>

<body>

<!-- =====================================================
     SIDEBAR
===================================================== -->

<div class="sidebar">

```
<div class="brand">

    <div class="brand-icon">
        ♻️
    </div>

    <span>
        EcoBank
    </span>

</div>


<div class="menu-title">
    Menu Utama
</div>


<a
    href="dashboard.php"
    class="active"
>

    <i class="bi bi-grid"></i>

    <span>
        Dashboard
    </span>

</a>


<a href="../setor/index.php">

    <i class="bi bi-recycle"></i>

    <span>
        Setor Sampah
    </span>

</a>


<a href="../tarik/index.php">

    <i class="bi bi-wallet2"></i>

    <span>
        Tarik Saldo
    </span>

</a>


<div class="menu-title mt-4">
    Riwayat
</div>


<a href="riwayat.php">

    <i class="bi bi-clock-history"></i>

    <span>
        Riwayat Setor
    </span>

</a>


<a href="riwayat_tarik.php">

    <i class="bi bi-cash-stack"></i>

    <span>
        Riwayat Tarik
    </span>

</a>


<a
    href="../logout.php"
    class="logout-link"
>

    <i class="bi bi-box-arrow-right"></i>

    <span>
        Logout
    </span>

</a>
```

</div>

<!-- =====================================================
     MAIN
===================================================== -->

<div class="main">

```
<!-- HEADER -->

<div class="topbar">


    <div>

        <h2>
            Dashboard
        </h2>

        <p>
            Selamat datang kembali di EcoBank ♻️
        </p>

    </div>


    <div class="profile">

        <div class="profile-icon">

            <i class="bi bi-person"></i>

        </div>

        <div>

            <strong>
                <?= htmlspecialchars($nama); ?>
            </strong>

            <br>

            <small class="text-secondary">
                Siswa
            </small>

        </div>

    </div>


</div>


<!-- =====================================================
     SALDO
===================================================== -->

<div class="row g-4 mb-4">


    <div class="col-lg-8">

        <div class="saldo-card">

            <div class="saldo-label">
                Saldo EcoBank Saya
            </div>

            <div class="saldo-value">

                Rp
                <?= number_format(
                    $saldo,
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

            <div class="mt-4">

                <a
                    href="../tarik/index.php"
                    class="btn btn-light"
                >

                    <i class="bi bi-wallet2 me-2"></i>

                    Tarik Saldo

                </a>

            </div>

        </div>

    </div>


    <div class="col-lg-4">

        <div class="stat-card">

            <div class="stat-icon">
                ♻️
            </div>

            <div class="stat-number">

                <?= number_format(
                    $total_setor
                ); ?>

            </div>

            <div class="stat-label">
                Total Setoran
            </div>

        </div>

    </div>


</div>


<!-- =====================================================
     STATISTIK
===================================================== -->

<div class="row g-4 mb-4">


    <div class="col-md-6">

        <div class="stat-card">

            <div class="stat-icon">

                <i class="bi bi-recycle"></i>

            </div>

            <div class="stat-number">

                <?= number_format(
                    $total_berat,
                    2,
                    ',',
                    '.'
                ); ?>

                Kg

            </div>

            <div class="stat-label">
                Total Sampah yang Disetor
            </div>

        </div>

    </div>


    <div class="col-md-6">

        <div class="stat-card">

            <div class="stat-icon">

                <i class="bi bi-person"></i>

            </div>

            <div class="stat-number">

                <?= htmlspecialchars(
                    $username
                ); ?>

            </div>

            <div class="stat-label">
                Username Akun
            </div>

        </div>

    </div>


</div>


<!-- =====================================================
     FITUR
===================================================== -->

<div class="menu-card">


    <h5>
        Akses Cepat
    </h5>


    <div class="row g-3">


        <!-- SETOR -->

        <div class="col-md-6">

            <a
                href="../setor/index.php"
                class="feature"
            >

                <div class="feature-icon">

                    <i class="bi bi-recycle"></i>

                </div>

                <div>

                    <div class="feature-title">
                        Setor Sampah
                    </div>

                    <div class="feature-desc">
                        Ajukan setoran sampah baru
                    </div>

                </div>

            </a>

        </div>


        <!-- TARIK -->

        <div class="col-md-6">

            <a
                href="../tarik/index.php"
                class="feature"
            >

                <div class="feature-icon">

                    <i class="bi bi-wallet2"></i>

                </div>

                <div>

                    <div class="feature-title">
                        Tarik Saldo
                    </div>

                    <div class="feature-desc">
                        Ajukan pencairan saldo
                    </div>

                </div>

            </a>

        </div>


        <!-- RIWAYAT SETOR -->

        <div class="col-md-6">

            <a
                href="riwayat.php"
                class="feature"
            >

                <div class="feature-icon">

                    <i class="bi bi-clock-history"></i>

                </div>

                <div>

                    <div class="feature-title">
                        Riwayat Setor
                    </div>

                    <div class="feature-desc">
                        Lihat transaksi setoran
                    </div>

                </div>

            </a>

        </div>


        <!-- RIWAYAT TARIK -->

        <div class="col-md-6">

            <a
                href="riwayat_tarik.php"
                class="feature"
            >

                <div class="feature-icon">

                    <i class="bi bi-cash-stack"></i>

                </div>

                <div>

                    <div class="feature-title">
                        Riwayat Tarik
                    </div>

                    <div class="feature-desc">
                        Lihat riwayat pencairan saldo
                    </div>

                </div>

            </a>

        </div>


    </div>

</div>


<!-- FOOTER -->

<div class="text-center mt-4">

    <small class="text-secondary">

        EcoBank Sampah ♻️ — Kelola sampah, kelola nilai.

    </small>

</div>
```

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
