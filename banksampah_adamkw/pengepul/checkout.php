
<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("pengepul");


// =========================================
// CEK ID SAMPAH
// =========================================

if (!isset($_GET['id_sampah']) || !is_numeric($_GET['id_sampah'])) {
    die("ID sampah tidak ditemukan.");
}

$id_sampah = (int) $_GET['id_sampah'];


// =========================================
// AMBIL DATA SAMPAH
// =========================================

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM sampah
     WHERE id_sampah = '$id_sampah'"
);

if (!$query) {
    die("Gagal mengambil data sampah: " . mysqli_error($conn));
}

$sampah = mysqli_fetch_assoc($query);

if (!$sampah) {
    die("Data sampah tidak ditemukan.");
}


// =========================================
// AMBIL DATA USER
// =========================================

$id_user = (int) $_SESSION['id_user'];

$query_user = mysqli_query(
    $conn,
    "SELECT nama, username, saldo
     FROM users
     WHERE id_user = '$id_user'"
);

$user = mysqli_fetch_assoc($query_user);


// =========================================
// PROSES CHECKOUT
// =========================================

if (isset($_POST['checkout'])) {

    $berat = isset($_POST['berat_kg'])
        ? (float) $_POST['berat_kg']
        : 0;

    if ($berat <= 0) {

        $error = "Berat harus lebih dari 0 Kg.";

    } elseif ($berat > (float) $sampah['stok_kg']) {

        $error = "Stok tidak mencukupi.";

    } else {

        $harga = (int) $sampah['harga_per_kg'];

        $total = $berat * $harga;

        $tanggal = date('Y-m-d');


        // =========================================
        // SIMPAN CHECKOUT SEBAGAI PENDING
        // =========================================

        $query_insert = mysqli_query(
            $conn,
            "INSERT INTO pembelian
            (
                id_user,
                id_sampah,
                berat_kg,
                harga_per_kg,
                total_rp,
                tanggal,
                status
            )
            VALUES
            (
                '$id_user',
                '$id_sampah',
                '$berat',
                '$harga',
                '$total',
                '$tanggal',
                'pending'
            )"
        );


        if ($query_insert) {

            header("Location: pembelian.php?pesan=berhasil");
            exit;

        } else {

            $error = "Checkout gagal: " . mysqli_error($conn);

        }
    }
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
        Checkout Sampah - EcoBank
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


        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {

            position: fixed;

            top: 0;

            left: 0;

            width: 215px;

            height: 100vh;

            background: #111;

            color: white;

            padding: 24px 15px;

            z-index: 1000;

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 5px 8px 20px;

            border-bottom: 1px solid #2d2d2d;

            margin-bottom: 20px;

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


        .brand-title {

            font-size: 17px;

            font-weight: 700;

            line-height: 1;

        }


        .brand-subtitle {

            font-size: 9px;

            color: #999;

            margin-top: 5px;

        }


        .user-box {

            background: #1d1d1d;

            border-radius: 14px;

            padding: 14px;

            margin-bottom: 22px;

        }


        .user-avatar {

            width: 38px;

            height: 38px;

            background: white;

            color: #111;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 9px;

        }


        .user-name {

            font-weight: 700;

            font-size: 13px;

        }


        .user-role {

            color: #999;

            font-size: 10px;

            margin-top: 3px;

        }


        .menu-title {

            font-size: 9px;

            color: #777;

            text-transform: uppercase;

            margin: 0 8px 10px;

            letter-spacing: .5px;

        }


        .menu-link {

            display: flex;

            align-items: center;

            gap: 12px;

            color: #aaa;

            text-decoration: none;

            padding: 11px 12px;

            border-radius: 10px;

            margin-bottom: 4px;

            font-size: 13px;

            transition: .2s;

        }


        .menu-link:hover {

            background: #242424;

            color: white;

        }


        .menu-link.active {

            background: white;

            color: #111;

            font-weight: 600;

        }


        .menu-link i {

            width: 18px;

            text-align: center;

            font-size: 16px;

        }


        .sidebar-bottom {

            position: absolute;

            bottom: 20px;

            left: 15px;

            right: 15px;

            border-top: 1px solid #2d2d2d;

            padding-top: 15px;

        }


        .logout-link {

            color: #aaa;

        }


        .logout-link:hover {

            color: white;

            background: #242424;

        }


        /* =========================================
           MAIN CONTENT
        ========================================= */

        .main-content {

            margin-left: 215px;

            min-height: 100vh;

            padding: 35px;

        }


        .content-wrapper {

            max-width: 1200px;

            margin: auto;

        }


        /* =========================================
           TOP HEADER
        ========================================= */

        .page-header {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            padding: 25px 28px;

            margin-bottom: 20px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 8px 25px
                rgba(0,0,0,.04);

        }


        .page-header h2 {

            margin: 0;

            font-size: 27px;

            font-weight: 700;

        }


        .page-header p {

            margin: 6px 0 0;

            color: #777;

            font-size: 13px;

        }


        .header-icon {

            width: 45px;

            height: 45px;

            background: #111;

            color: white;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

        }


        /* =========================================
           CHECKOUT CARD
        ========================================= */

        .checkout-card {

            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 18px;

            overflow: hidden;

            box-shadow:
                0 10px 30px
                rgba(0,0,0,.05);

        }


        .checkout-header {

            background: #111;

            color: white;

            padding: 22px 25px;

        }


        .checkout-header h5 {

            margin: 0;

            font-weight: 700;

        }


        .checkout-header p {

            margin: 5px 0 0;

            color: #aaa;

            font-size: 12px;

        }


        .checkout-body {

            padding: 28px;

        }


        /* =========================================
           INFO BOX
        ========================================= */

        .info-box {

            background: #f7f7f7;

            border: 1px solid #e5e5e5;

            border-radius: 13px;

            padding: 18px;

            margin-bottom: 22px;

        }


        .info-label {

            color: #777;

            font-size: 11px;

            margin-bottom: 5px;

        }


        .info-value {

            font-weight: 700;

            font-size: 16px;

        }


        .price {

            font-size: 20px;

        }


        /* =========================================
           FORM
        ========================================= */

        .form-label {

            font-weight: 600;

            font-size: 13px;

        }


        .form-control {

            border-radius: 10px;

            padding: 11px 13px;

            border: 1px solid #ddd;

        }


        .form-control:focus {

            border-color: #111;

            box-shadow:
                0 0 0 3px
                rgba(0,0,0,.08);

        }


        .btn-submit {

            background: #111;

            color: white;

            border: none;

            border-radius: 10px;

            padding: 11px 20px;

            font-weight: 600;

        }


        .btn-submit:hover {

            background: #2c2c2c;

            color: white;

        }


        .btn-back {

            background: white;

            color: #111;

            border: 1px solid #111;

            border-radius: 10px;

            padding: 10px 18px;

            font-weight: 600;

            text-decoration: none;

        }


        .btn-back:hover {

            background: #111;

            color: white;

        }


        /* =========================================
           NOTE
        ========================================= */

        .pending-info {

            background: #f1f1f1;

            border-radius: 12px;

            padding: 14px 16px;

            font-size: 12px;

            color: #666;

            margin-top: 20px;

        }


        /* =========================================
           FOOTER
        ========================================= */

        .footer {

            text-align: center;

            color: #888;

            font-size: 11px;

            margin-top: 25px;

        }


        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 768px) {

            .sidebar {

                width: 75px;

                padding: 20px 10px;

            }


            .brand {

                justify-content: center;

                padding-left: 0;

                padding-right: 0;

            }


            .brand-text,
            .user-box,
            .menu-title {

                display: none;

            }


            .menu-link {

                justify-content: center;

                padding: 12px;

            }


            .menu-link span {

                display: none;

            }


            .sidebar-bottom {

                left: 10px;

                right: 10px;

            }


            .main-content {

                margin-left: 75px;

                padding: 20px;

            }


            .page-header {

                padding: 20px;

            }


            .page-header h2 {

                font-size: 22px;

            }


            .checkout-body {

                padding: 20px;

            }

        }

    </style>

</head>


<body>


<!-- =========================================
     SIDEBAR
========================================= -->

<aside class="sidebar">


    <!-- BRAND -->

    <div class="brand">

        <div class="brand-icon">

            <i class="bi bi-recycle"></i>

        </div>


        <div class="brand-text">

            <div class="brand-title">
                EcoBank
            </div>

            <div class="brand-subtitle">
                BANK SAMPAH
            </div>

        </div>

    </div>


    <!-- USER -->

    <div class="user-box">

        <div class="user-avatar">

            <i class="bi bi-person"></i>

        </div>


        <div class="user-name">

            <?= htmlspecialchars(
                $user['nama'] ?? 'Pengepul'
            ); ?>

        </div>


        <div class="user-role">

            <i class="bi bi-person-badge"></i>
            Pengepul

        </div>

    </div>


    <!-- MENU -->

    <div class="menu-title">
        Menu Utama
    </div>


    <!-- DASHBOARD -->

    <a
        href="dashboard.php"
        class="menu-link"
    >

        <i class="bi bi-grid"></i>

        <span>
            Dashboard
        </span>

    </a>


    <!-- HARGA & STOK -->

    <a
        href="sampah.php"
        class="menu-link"
    >

        <i class="bi bi-recycle"></i>

        <span>
            Harga & Stok
        </span>

    </a>


    <!-- PEMBELIAN -->

    <a
        href="pembelian.php"
        class="menu-link"
    >

        <i class="bi bi-cart3"></i>

        <span>
            Pembelian Saya
        </span>

    </a>


    <!-- SIDEBAR BOTTOM -->

    <div class="sidebar-bottom">

        <a
            href="../logout.php"
            class="menu-link logout-link"
        >

            <i class="bi bi-box-arrow-left"></i>

            <span>
                Logout
            </span>

        </a>

    </div>


</aside>


<!-- =========================================
     MAIN CONTENT
========================================= -->

<main class="main-content">


<div class="content-wrapper">


    <!-- =========================================
         HEADER
    ========================================== -->

    <div class="page-header">


        <div>

            <h2>
                Checkout Sampah
            </h2>

            <p>
                Ajukan pembelian sampah kepada EcoBank
            </p>

        </div>


        <div class="header-icon">

            <i class="bi bi-cart-check"></i>

        </div>


    </div>


    <!-- =========================================
         ERROR
    ========================================== -->

    <?php if (isset($error)): ?>

        <div class="alert alert-danger border-0 rounded-3">

            <i class="bi bi-exclamation-triangle me-2"></i>

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- =========================================
         CHECKOUT CARD
    ========================================== -->

    <div class="checkout-card">


        <div class="checkout-header">

            <h5>

                <i class="bi bi-cart-check me-2"></i>

                Detail Pembelian

            </h5>

            <p>
                Periksa informasi sampah sebelum mengajukan checkout.
            </p>

        </div>


        <div class="checkout-body">


            <!-- =========================================
                 INFORMASI SAMPAH
            ========================================== -->

            <div class="row g-3">


                <!-- JENIS -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            JENIS SAMPAH
                        </div>

                        <div class="info-value">

                            <i class="bi bi-box-seam me-2"></i>

                            <?= htmlspecialchars(
                                $sampah['jenis_sampah']
                            ); ?>

                        </div>

                    </div>

                </div>


                <!-- HARGA -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            HARGA PER KG
                        </div>

                        <div class="info-value price">

                            Rp
                            <?= number_format(
                                $sampah['harga_per_kg'],
                                0,
                                ',',
                                '.'
                            ); ?>

                        </div>

                    </div>

                </div>


                <!-- STOK -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            STOK TERSEDIA
                        </div>

                        <div class="info-value">

                            <?= number_format(
                                $sampah['stok_kg'],
                                2,
                                ',',
                                '.'
                            ); ?>

                            Kg

                        </div>

                    </div>

                </div>


                <!-- SALDO -->

                <div class="col-md-6">

                    <div class="info-box">

                        <div class="info-label">
                            SALDO PENGEPUL
                        </div>

                        <div class="info-value price">

                            Rp
                            <?= number_format(
                                $user['saldo'] ?? 0,
                                0,
                                ',',
                                '.'
                            ); ?>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =========================================
                 FORM CHECKOUT
            ========================================== -->

            <form
                method="POST"
                id="checkoutForm"
            >


                <div class="mb-4">

                    <label class="form-label">

                        <i class="bi bi-box me-1"></i>

                        Jumlah yang Dibeli

                    </label>


                    <div class="input-group">

                        <input
                            type="number"
                            name="berat_kg"
                            id="berat_kg"
                            class="form-control"
                            min="0.01"
                            max="<?= (float) $sampah['stok_kg']; ?>"
                            step="0.01"
                            placeholder="Contoh: 10"
                            required
                        >

                        <span class="input-group-text">
                            Kg
                        </span>

                    </div>


                    <div class="form-text">

                        Maksimal
                        <?= number_format(
                            $sampah['stok_kg'],
                            2,
                            ',',
                            '.'
                        ); ?>
                        Kg

                    </div>

                </div>


                <!-- =========================================
                     ESTIMASI TOTAL
                ========================================== -->

                <div
                    class="alert alert-light border rounded-3"
                    id="totalBox"
                >

                    <div class="d-flex justify-content-between">

                        <span>
                            Estimasi Total
                        </span>

                        <strong id="totalHarga">
                            Rp 0
                        </strong>

                    </div>

                </div>


                <!-- =========================================
                     BUTTON
                ========================================== -->

                <div class="d-flex gap-2 flex-wrap">


                    <button
                        type="submit"
                        name="checkout"
                        class="btn btn-submit"
                    >

                        <i class="bi bi-send me-1"></i>

                        Ajukan Checkout

                    </button>


                    <a
                        href="sampah.php"
                        class="btn-back"
                    >

                        <i class="bi bi-arrow-left me-1"></i>

                        Kembali

                    </a>

                </div>


            </form>


            <!-- =========================================
                 INFO PENDING
            ========================================== -->

            <div class="pending-info">

                <i class="bi bi-info-circle me-2"></i>

                Setelah checkout diajukan, status pembelian akan menjadi
                <strong>Pending</strong> dan harus disetujui oleh admin
                sebelum stok dan saldo diproses.

            </div>


        </div>

    </div>


    <!-- FOOTER -->

    <div class="footer">

        EcoBank • Sistem Bank Sampah

    </div>


</div>


</main>


<!-- =========================================
     JAVASCRIPT
========================================= -->

<script>

const beratInput = document.getElementById('berat_kg');
const totalHarga = document.getElementById('totalHarga');

const hargaPerKg = <?= (float) $sampah['harga_per_kg']; ?>;


beratInput.addEventListener('input', function () {

    const berat = parseFloat(this.value) || 0;

    const total = berat * hargaPerKg;


    totalHarga.textContent =
        'Rp ' +
        total.toLocaleString('id-ID');

});

</script>


</body>

</html>


Dengan versi ini alurnya tetap **tidak langsung mengurangi saldo/stok**: tombol **Ajukan Checkout** hanya memasukkan data ke `pembelian` dengan status `pending`. Setelah itu admin yang menyetujui di **Persetujuan Checkout** baru memproses saldo, stok, dan transaksi.
