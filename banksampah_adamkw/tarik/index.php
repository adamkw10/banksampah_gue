
<?php

include "../config/koneksi.php";
include "../config/auth.php";


// Hanya siswa dan guru yang boleh menarik saldo
if (
    $_SESSION['role'] != 'siswa' &&
    $_SESSION['role'] != 'guru'
) {
    header("Location: ../login.php");
    exit;
}


$id_user = $_SESSION['id_user'];


// =========================================
// AMBIL SALDO TERBARU
// =========================================

$query_user = mysqli_query(
    $conn,
    "SELECT saldo
     FROM users
     WHERE id_user = '$id_user'"
);

$user = mysqli_fetch_assoc($query_user);

$saldo = $user['saldo'] ?? 0;


// =========================================
// PROSES PENGAJUAN TARIK
// =========================================

if (isset($_POST['tarik'])) {

    $nominal = (int) $_POST['nominal'];


    if ($nominal <= 0) {

        $error = "Nominal penarikan harus lebih dari Rp0.";

    } elseif ($nominal > $saldo) {

        $error = "Saldo kamu tidak mencukupi.";

    } else {

        $tanggal = date('Y-m-d');


        $query = mysqli_query(
            $conn,
            "INSERT INTO tarik_saldo
            (
                id_user,
                nominal,
                tanggal,
                status
            )
            VALUES
            (
                '$id_user',
                '$nominal',
                '$tanggal',
                'pending'
            )"
        );


        if ($query) {

            header("Location: index.php?pesan=berhasil");
            exit;

        } else {

            $error = "Pengajuan gagal dibuat.";

        }
    }
}


// =========================================
// RIWAYAT PENARIKAN
// =========================================

$query_riwayat = mysqli_query(
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

    <title>
        Tarik Saldo - EcoBank
    </title>


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

            color: #111111;

            font-family: Arial, sans-serif;

        }


        /* =================================
           HEADER
        ================================= */

        .page-header {

            background: #111111;

            color: white;

            border-radius: 20px;

            padding: 30px;

            margin-bottom: 25px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.08);

        }


        .page-header h2 {

            font-weight: 700;

        }


        .page-header p {

            color: #cccccc;

        }


        .eco-icon {

            width: 50px;

            height: 50px;

            background: white;

            color: #111111;

            border-radius: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 24px;

        }


        /* =================================
           CARD
        ================================= */

        .card {

            border: none;

            border-radius: 18px;

            box-shadow:
                0 8px 25px rgba(0,0,0,0.07);

        }


        .card-header {

            background: #111111;

            color: white;

            border-radius:
                18px 18px 0 0 !important;

            padding: 18px 20px;

        }


        /* =================================
           SALDO
        ================================= */

        .saldo-card {

            background: #111111;

            color: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 10px 30px rgba(0,0,0,0.10);

        }


        .saldo-card small {

            color: #bdbdbd;

        }


        .saldo-card h2 {

            font-weight: 700;

            margin-top: 8px;

        }


        /* =================================
           FORM
        ================================= */

        .form-control {

            border: 1px solid #dddddd;

            border-radius: 12px;

            padding: 12px;

        }


        .form-control:focus {

            border-color: #111111;

            box-shadow:
                0 0 0 0.2rem rgba(0,0,0,0.08);

        }


        .input-group-text {

            background: #111111;

            color: white;

            border: 1px solid #111111;

        }


        /* =================================
           BUTTON
        ================================= */

        .btn-black {

            background: #111111;

            color: white;

            border: none;

            border-radius: 12px;

            padding: 11px 20px;

            font-weight: 600;

            transition: 0.2s;

        }


        .btn-black:hover {

            background: #000000;

            color: white;

            transform: translateY(-1px);

        }


        .btn-outline-black {

            border: 1px solid #111111;

            color: #111111;

            border-radius: 12px;

            padding: 10px 18px;

            font-weight: 600;

        }


        .btn-outline-black:hover {

            background: #111111;

            color: white;

        }


        /* =================================
           TABLE
        ================================= */

        .table {

            margin-bottom: 0;

        }


        .table thead {

            background: #111111;

            color: white;

        }


        .table thead th {

            padding: 14px;

            white-space: nowrap;

        }


        .table tbody td {

            padding: 14px;

        }


        .table tbody tr:hover {

            background: #f7f7f7;

        }


        /* =================================
           BADGE
        ================================= */

        .badge {

            padding: 8px 12px;

            border-radius: 20px;

        }


        /* =================================
           ALERT
        ================================= */

        .alert {

            border: none;

            border-radius: 14px;

        }


        /* =================================
           INFO
        ================================= */

        .info-box {

            background: #f8f8f8;

            border: 1px solid #eeeeee;

            border-radius: 14px;

            padding: 15px;

        }


    </style>

</head>


<body>


<div class="container py-5">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="page-header">

        <div class="d-flex align-items-center gap-3">

            <div class="eco-icon">

                <i class="bi bi-wallet2"></i>

            </div>


            <div>

                <h2 class="mb-1">

                    Tarik Saldo

                </h2>

                <p class="mb-0">

                    Halo,
                    <strong>
                        <?= htmlspecialchars($_SESSION['nama']); ?>
                    </strong>

                    👋

                </p>

            </div>

        </div>

    </div>



    <!-- =====================================
         PESAN BERHASIL
    ====================================== -->

    <?php if (
        isset($_GET['pesan']) &&
        $_GET['pesan'] == 'berhasil'
    ) : ?>

        <div class="alert alert-success shadow-sm">

            <i class="bi bi-check-circle-fill me-2"></i>

            Pengajuan tarik saldo berhasil dibuat.
            Silakan tunggu persetujuan admin.

        </div>

    <?php endif; ?>



    <!-- =====================================
         ERROR
    ====================================== -->

    <?php if (isset($error)) : ?>

        <div class="alert alert-danger shadow-sm">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            <?= htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>



    <!-- =====================================
         SALDO
    ====================================== -->

    <div class="saldo-card mb-4">

        <small>

            <i class="bi bi-wallet2 me-2"></i>

            Saldo Saat Ini

        </small>


        <h2>

            Rp
            <?= number_format(
                $saldo,
                0,
                ',',
                '.'
            ); ?>

        </h2>

    </div>



    <!-- =====================================
         FORM PENARIKAN
    ====================================== -->

    <div class="card mb-4">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bi bi-cash-stack me-2"></i>

                Ajukan Penarikan

            </h5>

        </div>


        <div class="card-body p-4">


            <form method="POST">


                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Nominal Penarikan

                    </label>


                    <div class="input-group">

                        <span class="input-group-text">

                            Rp

                        </span>


                        <input
                            type="number"
                            name="nominal"
                            class="form-control"
                            placeholder="Contoh: 10000"
                            min="1"
                            max="<?= $saldo; ?>"
                            required
                        >

                    </div>


                    <small class="text-secondary">

                        Maksimal penarikan:
                        <strong>
                            Rp <?= number_format(
                                $saldo,
                                0,
                                ',',
                                '.'
                            ); ?>
                        </strong>

                    </small>

                </div>



                <div class="info-box mb-4">

                    <div class="d-flex gap-2">

                        <i class="bi bi-info-circle"></i>

                        <small>

                            Pengajuan akan berstatus
                            <strong>Pending</strong>
                            sampai disetujui oleh admin.

                        </small>

                    </div>

                </div>



                <button
                    type="submit"
                    name="tarik"
                    class="btn btn-black"
                >

                    <i class="bi bi-send me-2"></i>

                    Ajukan Tarik Saldo

                </button>


                <a
                    href="../<?= $_SESSION['role']; ?>/dashboard.php"
                    class="btn btn-outline-black ms-2"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Kembali

                </a>


            </form>

        </div>

    </div>



    <!-- =====================================
         RIWAYAT
    ====================================== -->

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bi bi-clock-history me-2"></i>

                Riwayat Penarikan

            </h5>

        </div>


        <div class="card-body p-0">


            <div class="table-responsive">

                <table class="table table-hover align-middle">


                    <thead>

                        <tr>

                            <th class="px-4">
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
                        mysqli_num_rows($query_riwayat) > 0
                    ) : ?>


                        <?php while (
                            $row = mysqli_fetch_assoc(
                                $query_riwayat
                            )
                        ) : ?>


                            <tr>


                                <td class="px-4">

                                    <?= htmlspecialchars(
                                        $row['tanggal']
                                    ); ?>

                                </td>


                                <td>

                                    <strong>

                                        Rp
                                        <?= number_format(
                                            $row['nominal'],
                                            0,
                                            ',',
                                            '.'
                                        ); ?>

                                    </strong>

                                </td>


                                <td>


                                    <?php if (
                                        $row['status'] == 'pending'
                                    ) : ?>

                                        <span class="badge bg-warning text-dark">

                                            <i class="bi bi-hourglass-split me-1"></i>

                                            Pending

                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'diterima'
                                    ) : ?>

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Diterima

                                        </span>


                                    <?php elseif (
                                        $row['status'] == 'ditolak'
                                    ) : ?>

                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle me-1"></i>

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
                                colspan="3"
                                class="text-center py-5 text-secondary"
                            >

                                <i
                                    class="bi bi-inbox fs-1 d-block mb-2"
                                ></i>

                                Belum ada riwayat penarikan.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </div>

    </div>



    <!-- FOOTER -->

    <div class="d-flex justify-content-between align-items-center mt-4">


        <span class="text-secondary">

            EcoBank Sampah ♻️

        </span>


        <a
            href="../logout.php"
            class="btn btn-outline-danger"
        >

            <i class="bi bi-box-arrow-right me-2"></i>

            Logout

        </a>


    </div>


</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

