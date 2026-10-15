<div class="0">











</div>
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("admin");


// ==================================================
// DATA TRANSAKSI SETOR
// ==================================================

$query_setor = mysqli_query(
    $conn,
    "SELECT
        t.id_transaksi,
        t.tanggal,
        t.berat_kg,
        t.total_rp,
        u.nama,
        u.username,
        u.role,
        s.jenis_sampah
     FROM transaksi t
     LEFT JOIN users u
        ON t.id_user = u.id_user
     LEFT JOIN sampah s
        ON t.id_sampah = s.id_sampah
     WHERE t.tipe = 'setor'
     ORDER BY t.id_transaksi DESC"
);


// ==================================================
// DATA TARIK SALDO
// ==================================================

$query_tarik = mysqli_query(
    $conn,
    "SELECT
        ts.id_tarik,
        ts.tanggal,
        ts.nominal,
        ts.status,
        u.nama,
        u.username,
        u.role
     FROM tarik_saldo ts
     LEFT JOIN users u
        ON ts.id_user = u.id_user
     ORDER BY ts.id_tarik DESC"
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

    <title>Riwayat Transaksi - EcoBank</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        /* =========================================
           GLOBAL
        ========================================= */

        body {

            margin: 0;

            background: #f5f5f5;

            color: #111827;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

        }


        /* =========================================
           CONTAINER
        ========================================= */

        .page-container {

            max-width: 1400px;

            margin: auto;

            padding: 40px 20px;

        }


        /* =========================================
           HEADER
        ========================================= */

        .page-header {

            background: #ffffff;

            border: 1px solid #e5e5e5;

            border-radius: 18px;

            padding: 25px 30px;

            margin-bottom: 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.05);

        }


        .page-header h2 {

            margin: 0;

            font-weight: 800;

            color: #111111;

        }


        .page-header p {

            margin: 7px 0 0;

            color: #777777;

        }


        /* =========================================
           BUTTON
        ========================================= */

        .btn-black {

            background: #111111;

            color: #ffffff;

            border: none;

            padding: 11px 18px;

            border-radius: 10px;

            font-weight: 600;

            text-decoration: none;

            transition: 0.2s;

            display: inline-block;

        }


        .btn-black:hover {

            background: #333333;

            color: #ffffff;

            transform: translateY(-1px);

        }


        /* =========================================
           SECTION CARD
        ========================================= */

        .transaction-card {

            background: #ffffff;

            border: 1px solid #e5e5e5;

            border-radius: 18px;

            overflow: hidden;

            margin-bottom: 25px;

            box-shadow:
                0 8px 25px
                rgba(0, 0, 0, 0.05);

        }


        .transaction-header {

            padding: 20px 25px;

            border-bottom: 1px solid #eeeeee;

            background: #ffffff;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .transaction-header h5 {

            margin: 0;

            font-weight: 700;

            color: #111111;

        }


        .transaction-header span {

            font-size: 13px;

            color: #777777;

        }


        .transaction-body {

            padding: 0;

        }


        /* =========================================
           TABLE
        ========================================= */

        .table {

            margin-bottom: 0;

        }


        .table thead th {

            background: #111111;

            color: #ffffff;

            border: none;

            padding: 15px;

            font-size: 13px;

            font-weight: 600;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 15px;

            vertical-align: middle;

            border-color: #eeeeee;

            color: #333333;

        }


        .table tbody tr:hover {

            background: #fafafa;

        }


        /* =========================================
           USER
        ========================================= */

        .user-name {

            font-weight: 700;

            color: #111111;

        }


        .username {

            color: #888888;

            font-size: 12px;

        }


        /* =========================================
           BADGE
        ========================================= */

        .badge-role {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 700;

            background: #eeeeee;

            color: #222222;

        }


        .badge-status {

            display: inline-block;

            padding: 6px 11px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: 700;

        }


        .status-pending {

            background: #eeeeee;

            color: #333333;

        }


        .status-diterima {

            background: #111111;

            color: #ffffff;

        }


        .status-ditolak {

            background: #dddddd;

            color: #111111;

        }


        /* =========================================
           EMPTY
        ========================================= */

        .empty-data {

            padding: 45px 20px !important;

            text-align: center;

            color: #888888;

        }


        .empty-icon {

            font-size: 35px;

            margin-bottom: 10px;

        }


        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 768px) {

            .page-container {

                padding: 20px 12px;

            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;

                padding: 20px;

            }


            .page-header .btn-black {

                width: 100%;

                text-align: center;

            }


            .transaction-header {

                padding: 17px;

            }

        }

    </style>

</head>


<body>


<div class="page-container">


    <!-- =========================================
         HEADER
    ========================================== -->

    <div class="page-header">

        <div>

            <h2>
                📋 Riwayat Transaksi
            </h2>

            <p>
                Semua aktivitas setor dan tarik saldo EcoBank.
            </p>

        </div>


        <a
            href="dashboard.php"
            class="btn-black"
        >
            ← Dashboard
        </a>

    </div>



    <!-- =========================================
         RIWAYAT SETOR
    ========================================== -->

    <div class="transaction-card">

        <div class="transaction-header">

            <h5>
                ♻️ Riwayat Setor Sampah
            </h5>

            <span>
                Data transaksi setoran
            </span>

        </div>


        <div class="transaction-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th>User</th>

                            <th>Role</th>

                            <th>Jenis Sampah</th>

                            <th>Berat</th>

                            <th>Total</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $query_setor &&
                        mysqli_num_rows($query_setor) > 0
                    ) : ?>


                        <?php $no = 1; ?>


                        <?php while (
                            $row = mysqli_fetch_assoc($query_setor)
                        ) : ?>


                            <tr>

                                <td>

                                    <?= $no++; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['tanggal']
                                    ); ?>

                                </td>


                                <td>

                                    <div class="user-name">

                                        <?= htmlspecialchars(
                                            $row['nama'] ?? '-'
                                        ); ?>

                                    </div>

                                    <div class="username">

                                        <?= htmlspecialchars(
                                            $row['username'] ?? '-'
                                        ); ?>

                                    </div>

                                </td>


                                <td>

                                    <span class="badge-role">

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $row['role'] ?? '-'
                                            )
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['jenis_sampah'] ?? '-'
                                    ); ?>

                                </td>


                                <td>

                                    <?= number_format(
                                        $row['berat_kg'],
                                        2,
                                        ',',
                                        '.'
                                    ); ?>

                                    Kg

                                </td>


                                <td>

                                    <strong>

                                        <?= rupiah(
                                            $row['total_rp']
                                        ); ?>

                                    </strong>

                                </td>

                            </tr>


                        <?php endwhile; ?>


                    <?php else : ?>


                        <tr>

                            <td
                                colspan="7"
                                class="empty-data"
                            >

                                <div class="empty-icon">
                                    📭
                                </div>

                                Belum ada transaksi setor.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- =========================================
         RIWAYAT TARIK
    ========================================== -->

    <div class="transaction-card">

        <div class="transaction-header">

            <h5>
                💸 Riwayat Tarik Saldo
            </h5>

            <span>
                Data penarikan saldo
            </span>

        </div>


        <div class="transaction-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Tanggal</th>

                            <th>User</th>

                            <th>Role</th>

                            <th>Nominal</th>

                            <th>Status</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $query_tarik &&
                        mysqli_num_rows($query_tarik) > 0
                    ) : ?>


                        <?php $no = 1; ?>


                        <?php while (
                            $row = mysqli_fetch_assoc($query_tarik)
                        ) : ?>


                            <tr>

                                <td>

                                    <?= $no++; ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $row['tanggal']
                                    ); ?>

                                </td>


                                <td>

                                    <div class="user-name">

                                        <?= htmlspecialchars(
                                            $row['nama'] ?? '-'
                                        ); ?>

                                    </div>

                                    <div class="username">

                                        <?= htmlspecialchars(
                                            $row['username'] ?? '-'
                                        ); ?>

                                    </div>

                                </td>


                                <td>

                                    <span class="badge-role">

                                        <?= htmlspecialchars(
                                            ucfirst(
                                                $row['role'] ?? '-'
                                            )
                                        ); ?>

                                    </span>

                                </td>


                                <td>

                                    <strong>

                                        <?= rupiah(
                                            $row['nominal']
                                        ); ?>

                                    </strong>

                                </td>


                                <td>


                                    <?php if (
                                        $row['status'] == 'pending'
                                    ) : ?>

                                        <span
                                            class="badge-status status-pending"
                                        >
                                            Pending
                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'diterima'
                                    ) : ?>

                                        <span
                                            class="badge-status status-diterima"
                                        >
                                            Diterima
                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'ditolak'
                                    ) : ?>

                                        <span
                                            class="badge-status status-ditolak"
                                        >
                                            Ditolak
                                        </span>


                                    <?php else : ?>

                                        <span
                                            class="badge-status status-pending"
                                        >

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
                                colspan="6"
                                class="empty-data"
                            >

                                <div class="empty-icon">
                                    📭
                                </div>

                                Belum ada transaksi tarik saldo.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>


</div>


</body>

</html>

