
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("siswa");

$id_user = (int) $_SESSION['id_user'];


// =========================================
// AMBIL RIWAYAT SETOR
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
// TOTAL
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

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Setor - EcoBank</title>


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

        body {

            background: #f5f5f5;

            font-family: Arial, sans-serif;

            color: #111;

        }


        /* ================================
           HEADER
        ================================= */

        .page-header {

            background: #111;

            color: white;

            border-radius: 20px;

            padding: 28px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.08);

        }


        .page-header h2 {

            font-weight: 700;

        }


        .page-header p {

            color: #bdbdbd;

        }


        /* ================================
           STATISTIC CARD
        ================================= */

        .stat-card {

            background: white;

            border: none;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.06);

            transition: 0.2s;

        }


        .stat-card:hover {

            transform: translateY(-3px);

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

            font-size: 20px;

            margin-bottom: 15px;

        }


        .stat-title {

            color: #777;

            font-size: 14px;

        }


        .stat-value {

            font-size: 25px;

            font-weight: 700;

            color: #111;

        }


        /* ================================
           MAIN CARD
        ================================= */

        .main-card {

            background: white;

            border: none;

            border-radius: 20px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.06);

            overflow: hidden;

        }


        .card-header-custom {

            padding: 20px 24px;

            border-bottom: 1px solid #eee;

        }


        .card-header-custom h5 {

            font-weight: 700;

            margin: 0;

        }


        /* ================================
           TABLE
        ================================= */

        .table {

            margin-bottom: 0;

        }


        .table thead {

            background: #111;

            color: white;

        }


        .table thead th {

            border: none;

            padding: 15px;

            font-size: 14px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 15px;

            vertical-align: middle;

        }


        .table tbody tr:hover {

            background: #f8f8f8;

        }


        /* ================================
           TOTAL
        ================================= */

        .total-value {

            font-weight: 700;

            color: #111;

        }


        /* ================================
           BUTTON
        ================================= */

        .btn-ecobank {

            background: #111;

            color: white;

            border: none;

            border-radius: 10px;

            padding: 10px 18px;

            font-weight: 600;

            transition: 0.2s;

        }


        .btn-ecobank:hover {

            background: #333;

            color: white;

            transform: translateY(-1px);

        }


        .btn-outline-dark {

            border-radius: 10px;

        }


        /* ================================
           EMPTY STATE
        ================================= */

        .empty-state {

            padding: 50px 20px;

            text-align: center;

            color: #777;

        }


        .empty-state i {

            font-size: 45px;

            color: #aaa;

        }


        /* ================================
           MOBILE
        ================================= */

        @media (max-width: 768px) {

            .page-header {

                padding: 20px;

            }


            .page-header h2 {

                font-size: 22px;

            }

        }

    </style>

</head>


<body>


<div class="container py-5">


    <!-- =========================================
         HEADER
    ========================================== -->

    <div class="page-header mb-4">


        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">


            <div>

                <h2 class="mb-2">

                    <i class="bi bi-recycle"></i>

                    Riwayat Setor

                </h2>


                <p class="mb-0">

                    Riwayat seluruh setoran sampah kamu di EcoBank.

                </p>

            </div>


            <a
                href="dashboard.php"
                class="btn btn-light"
            >

                <i class="bi bi-arrow-left"></i>

                Dashboard

            </a>


        </div>

    </div>



    <!-- =========================================
         STATISTIK
    ========================================== -->

    <div class="row g-3 mb-4">


        <!-- TOTAL SETORAN -->

        <div class="col-md-4">

            <div class="stat-card h-100">

                <div class="card-body p-4">


                    <div class="stat-icon">

                        <i class="bi bi-recycle"></i>

                    </div>


                    <div class="stat-title">

                        Total Setoran

                    </div>


                    <div class="stat-value">

                        <?= number_format(
                            $total['jumlah']
                        ); ?>

                    </div>


                    <small class="text-secondary">

                        Transaksi setoran

                    </small>


                </div>

            </div>

        </div>



        <!-- TOTAL BERAT -->

        <div class="col-md-4">

            <div class="stat-card h-100">

                <div class="card-body p-4">


                    <div class="stat-icon">

                        <i class="bi bi-box-seam"></i>

                    </div>


                    <div class="stat-title">

                        Total Berat

                    </div>


                    <div class="stat-value">

                        <?= number_format(
                            $total['berat'],
                            2,
                            ',',
                            '.'
                        ); ?>

                        Kg

                    </div>


                    <small class="text-secondary">

                        Total sampah yang disetor

                    </small>


                </div>

            </div>

        </div>



        <!-- TOTAL PENDAPATAN -->

        <div class="col-md-4">

            <div class="stat-card h-100">

                <div class="card-body p-4">


                    <div class="stat-icon">

                        <i class="bi bi-wallet2"></i>

                    </div>


                    <div class="stat-title">

                        Total Pendapatan

                    </div>


                    <div class="stat-value">

                        <?= rupiah(
                            $total['total']
                        ); ?>

                    </div>


                    <small class="text-secondary">

                        Nilai seluruh setoran

                    </small>


                </div>

            </div>

        </div>


    </div>



    <!-- =========================================
         TABEL
    ========================================== -->

    <div class="main-card">


        <div class="card-header-custom">


            <h5>

                <i class="bi bi-list-ul"></i>

                Daftar Setoran

            </h5>


            <small class="text-secondary">

                Detail transaksi setoran sampah kamu.

            </small>


        </div>



        <div class="table-responsive">


            <table class="table table-hover align-middle">


                <thead>

                    <tr>

                        <th class="text-center">
                            No
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Jenis Sampah
                        </th>

                        <th>
                            Harga/Kg
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


                    <?php $no = 1; ?>


                    <?php while (
                        $row = mysqli_fetch_assoc($query)
                    ): ?>


                        <tr>


                            <!-- NO -->

                            <td class="text-center">

                                <?= $no++; ?>

                            </td>


                            <!-- TANGGAL -->

                            <td>

                                <i class="bi bi-calendar3 text-secondary"></i>

                                <?= htmlspecialchars(
                                    $row['tanggal']
                                ); ?>

                            </td>


                            <!-- JENIS -->

                            <td>

                                <strong>

                                    <?= htmlspecialchars(
                                        $row['jenis_sampah'] ?? '-'
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

                                <span class="total-value">

                                    <?= rupiah(
                                        $row['total_rp']
                                    ); ?>

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <span class="badge bg-success">

                                    <i class="bi bi-check-circle"></i>

                                    Diterima

                                </span>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="7"
                            class="empty-state"
                        >

                            <i class="bi bi-recycle"></i>


                            <h5 class="mt-3">

                                Belum Ada Riwayat

                            </h5>


                            <p class="mb-0">

                                Kamu belum memiliki transaksi setoran.

                            </p>

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>


    </div>



    <!-- =========================================
         MENU BAWAH
    ========================================== -->

    <div class="d-flex gap-2 flex-wrap mt-4">


        <a
            href="../setor/index.php"
            class="btn btn-ecobank"
        >

            <i class="bi bi-recycle"></i>

            Setor Sampah

        </a>


        <a
            href="dashboard.php"
            class="btn btn-outline-dark"
        >

            <i class="bi bi-grid"></i>

            Dashboard

        </a>


        <a
            href="riwayat_tarik.php"
            class="btn btn-outline-dark"
        >

            <i class="bi bi-clock-history"></i>

            Riwayat Tarik

        </a>


        <a
            href="../logout.php"
            class="btn btn-outline-danger"
        >

            <i class="bi bi-box-arrow-right"></i>

            Logout

        </a>


    </div>


</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>
