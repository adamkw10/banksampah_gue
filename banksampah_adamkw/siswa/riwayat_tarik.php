
<?php

include "../config/koneksi.php";
include "../config/auth.php";

wajib_role("siswa");

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

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Riwayat Tarik Saldo - EcoBank</title>

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

        /* HEADER */

        .page-header {
            background: #111;
            color: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .page-header h2 {
            font-weight: 700;
        }

        .page-header p {
            color: #bdbdbd;
        }

        /* CARD */

        .main-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .card-title-custom {
            font-weight: 700;
            color: #111;
        }

        /* TABLE */

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
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f8f8f8;
        }

        /* NOMINAL */

        .nominal {
            font-weight: 700;
            color: #111;
        }

        /* BADGE */

        .badge {
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
        }

        /* BUTTON */

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

        /* EMPTY */

        .empty-state {
            padding: 50px 20px;
            text-align: center;
            color: #777;
        }

        .empty-state i {
            font-size: 45px;
            color: #aaa;
        }

        /* FOOTER BUTTON */

        .bottom-menu {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

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

                    <i class="bi bi-clock-history"></i>

                    Riwayat Tarik Saldo

                </h2>

                <p class="mb-0">

                    Lihat seluruh riwayat pengajuan penarikan saldo kamu.

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
         TABEL RIWAYAT
    ========================================== -->

    <div class="main-card">

        <div class="card-body p-0">


            <div class="p-4 border-bottom">

                <h5 class="card-title-custom mb-1">

                    <i class="bi bi-list-ul"></i>

                    Daftar Penarikan

                </h5>

                <small class="text-secondary">

                    Status pengajuan penarikan saldo EcoBank.

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
                                Nominal
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if ($query && mysqli_num_rows($query) > 0) : ?>


                        <?php $no = 1; ?>


                        <?php while ($row = mysqli_fetch_assoc($query)) : ?>


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


                                <!-- NOMINAL -->

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


                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        $row['status'] == 'pending'
                                    ) : ?>


                                        <span class="badge bg-warning text-dark">

                                            <i class="bi bi-hourglass-split"></i>

                                            Pending

                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'diterima'
                                    ) : ?>


                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle"></i>

                                            Diterima

                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'ditolak'
                                    ) : ?>


                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle"></i>

                                            Ditolak

                                        </span>


                                    <?php else : ?>


                                        <span class="badge bg-secondary">

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
                                class="empty-state"
                            >

                                <i class="bi bi-wallet2"></i>

                                <h5 class="mt-3">
                                    Belum Ada Riwayat
                                </h5>

                                <p class="mb-0">
                                    Kamu belum pernah mengajukan penarikan saldo.
                                </p>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- =========================================
         MENU BAWAH
    ========================================== -->

    <div class="bottom-menu mt-4">

        <a
            href="../tarik/index.php"
            class="btn btn-ecobank"
        >

            <i class="bi bi-wallet2"></i>

            Ajukan Tarik Saldo

        </a>


        <a
            href="dashboard.php"
            class="btn btn-outline-dark"
        >

            <i class="bi bi-grid"></i>

            Dashboard

        </a>

    </div>


</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


</body>

</html>

