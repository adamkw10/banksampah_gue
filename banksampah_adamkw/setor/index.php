
<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";


// Hanya siswa dan guru yang boleh mengajukan setor
if (
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] != 'siswa' && $_SESSION['role'] != 'guru')
) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];


// ==================================================
// PROSES PENGAJUAN SETOR
// ==================================================

if (isset($_POST['ajukan'])) {

    $id_sampah = (int) $_POST['id_sampah'];
    $berat_kg = (float) $_POST['berat_kg'];

    if ($berat_kg <= 0) {
        header("Location: index.php?pesan=berat");
        exit;
    }

    $query_sampah = mysqli_query(
        $conn,
        "SELECT *
         FROM sampah
         WHERE id_sampah = '$id_sampah'"
    );

    $sampah = mysqli_fetch_assoc($query_sampah);

    if (!$sampah) {
        header("Location: index.php?pesan=sampah");
        exit;
    }

    $total_rp = $berat_kg * $sampah['harga_per_kg'];

    $query = mysqli_query(
        $conn,
        "INSERT INTO pengajuan_setor
        (
            id_user,
            id_sampah,
            berat_kg,
            total_rp,
            tanggal,
            status
        )
        VALUES
        (
            '$id_user',
            '$id_sampah',
            '$berat_kg',
            '$total_rp',
            CURDATE(),
            'pending'
        )"
    );

    if ($query) {
        header("Location: index.php?pesan=berhasil");
        exit;
    } else {
        echo "Gagal mengajukan setor: " . mysqli_error($conn);
    }
}


// ==================================================
// AMBIL DATA SAMPAH
// ==================================================

$query_sampah = mysqli_query(
    $conn,
    "SELECT *
     FROM sampah
     ORDER BY jenis_sampah ASC"
);


// ==================================================
// RIWAYAT PENGAJUAN USER
// ==================================================

$query_riwayat = mysqli_query(
    $conn,
    "SELECT
        pengajuan_setor.*,
        sampah.jenis_sampah

     FROM pengajuan_setor

     LEFT JOIN sampah
        ON pengajuan_setor.id_sampah = sampah.id_sampah

     WHERE pengajuan_setor.id_user = '$id_user'

     ORDER BY pengajuan_setor.id_pengajuan DESC"
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

    <title>Setor Sampah - EcoBank</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

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

        .page-header {
            background: #111111;
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-weight: 700;
        }

        .page-header p {
            color: #cccccc;
        }

        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
        }

        .card-header {
            background: #111111;
            color: white;
            border-radius: 18px 18px 0 0 !important;
            padding: 18px 20px;
        }

        .form-control,
        .form-select {
            border: 1px solid #dddddd;
            border-radius: 12px;
            padding: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #111111;
            box-shadow: 0 0 0 0.2rem rgba(0,0,0,0.08);
        }

        .btn-black {
            background: #111111;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 11px 20px;
            font-weight: 600;
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

        .badge {
            padding: 8px 12px;
            border-radius: 20px;
        }

        .alert {
            border: none;
            border-radius: 14px;
        }

        .eco-icon {
            width: 45px;
            height: 45px;
            background: white;
            color: #111111;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

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


    <!-- HEADER -->

    <div class="page-header">

        <div class="d-flex align-items-center gap-3">

            <div class="eco-icon">
                ♻️
            </div>

            <div>

                <h2 class="mb-1">
                    Setor Sampah
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


    <!-- PESAN -->

    <?php if (isset($_GET['pesan'])) : ?>

        <?php if ($_GET['pesan'] == 'berhasil') : ?>

            <div class="alert alert-success shadow-sm">

                <i class="bi bi-check-circle-fill me-2"></i>

                Pengajuan setor berhasil dibuat.
                Silakan tunggu persetujuan admin.

            </div>

        <?php elseif ($_GET['pesan'] == 'berat') : ?>

            <div class="alert alert-danger shadow-sm">

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                Berat sampah harus lebih dari 0 Kg.

            </div>

        <?php elseif ($_GET['pesan'] == 'sampah') : ?>

            <div class="alert alert-danger shadow-sm">

                <i class="bi bi-exclamation-circle-fill me-2"></i>

                Jenis sampah tidak ditemukan.

            </div>

        <?php endif; ?>

    <?php endif; ?>


    <!-- FORM SETOR -->

    <div class="card mb-4">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bi bi-recycle me-2"></i>

                Ajukan Setor Sampah

            </h5>

        </div>


        <div class="card-body p-4">

            <form method="POST">


                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Jenis Sampah

                    </label>

                    <select
                        name="id_sampah"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Sampah --
                        </option>

                        <?php while ($sampah = mysqli_fetch_assoc($query_sampah)) : ?>

                            <option
                                value="<?= $sampah['id_sampah']; ?>"
                            >

                                <?= htmlspecialchars(
                                    $sampah['jenis_sampah']
                                ); ?>

                                -
                                <?= rupiah(
                                    $sampah['harga_per_kg']
                                ); ?>/Kg

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="mb-4">

                    <label class="form-label fw-semibold">

                        Berat Sampah (Kg)

                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="berat_kg"
                            step="0.01"
                            min="0.01"
                            class="form-control"
                            placeholder="Contoh: 2.5"
                            required
                        >

                        <span class="input-group-text">
                            Kg
                        </span>

                    </div>

                </div>


                <div class="info-box mb-4">

                    <div class="d-flex gap-2">

                        <i class="bi bi-info-circle"></i>

                        <small>
                            Setelah pengajuan dibuat, admin akan
                            memeriksa dan menyetujui setoran kamu.
                        </small>

                    </div>

                </div>


                <button
                    type="submit"
                    name="ajukan"
                    class="btn btn-black"
                >

                    <i class="bi bi-send me-2"></i>

                    Ajukan Setor

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


    <!-- RIWAYAT -->

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bi bi-clock-history me-2"></i>

                Riwayat Pengajuan

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
                                Sampah
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

                    <?php if (mysqli_num_rows($query_riwayat) > 0) : ?>

                        <?php while ($row = mysqli_fetch_assoc($query_riwayat)) : ?>

                            <tr>

                                <td class="px-4">

                                    <?= htmlspecialchars(
                                        $row['tanggal']
                                    ); ?>

                                </td>

                                <td>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $row['jenis_sampah']
                                        ); ?>

                                    </strong>

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

                                <td>

                                    <?php if ($row['status'] == 'pending') : ?>

                                        <span class="badge bg-warning text-dark">

                                            <i class="bi bi-hourglass-split me-1"></i>

                                            Pending

                                        </span>

                                    <?php elseif ($row['status'] == 'diterima') : ?>

                                        <span class="badge bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Diterima

                                        </span>

                                    <?php else : ?>

                                        <span class="badge bg-danger">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Ditolak

                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else : ?>

                        <tr>

                            <td
                                colspan="5"
                                class="text-center py-5 text-secondary"
                            >

                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                Belum ada pengajuan setor.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- BOTTOM MENU -->

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

