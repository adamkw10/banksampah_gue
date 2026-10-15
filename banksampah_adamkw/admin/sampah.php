<?php

include "../config/koneksi.php";
include "../config/auth.php";
include "../config/functions.php";

wajib_role("admin");


// =========================================
// TAMBAH DATA SAMPAH
// =========================================

if (isset($_POST['tambah'])) {

    $jenis_sampah = mysqli_real_escape_string(
        $conn,
        $_POST['jenis_sampah']
    );

    $harga_per_kg = (int) $_POST['harga_per_kg'];
    $stok_kg = (float) $_POST['stok_kg'];

    $query = mysqli_query(
        $conn,
        "INSERT INTO sampah
        (jenis_sampah, harga_per_kg, stok_kg)
        VALUES
        ('$jenis_sampah', '$harga_per_kg', '$stok_kg')"
    );

    if ($query) {
        header("Location: sampah.php?pesan=tambah");
        exit;
    } else {
        echo "Gagal menambahkan data: " . mysqli_error($conn);
    }
}


// =========================================
// HAPUS DATA SAMPAH
// =========================================

if (isset($_GET['hapus'])) {

    $id_sampah = (int) $_GET['hapus'];

    $query = mysqli_query(
        $conn,
        "DELETE FROM sampah
         WHERE id_sampah = '$id_sampah'"
    );

    if ($query) {
        header("Location: sampah.php?pesan=hapus");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
}


// =========================================
// DATA EDIT
// =========================================

$data_edit = null;

if (isset($_GET['edit'])) {

    $id_sampah = (int) $_GET['edit'];

    $query_edit = mysqli_query(
        $conn,
        "SELECT *
         FROM sampah
         WHERE id_sampah = '$id_sampah'"
    );

    $data_edit = mysqli_fetch_assoc($query_edit);
}


// =========================================
// UPDATE DATA SAMPAH
// =========================================

if (isset($_POST['update'])) {

    $id_sampah = (int) $_POST['id_sampah'];

    $jenis_sampah = mysqli_real_escape_string(
        $conn,
        $_POST['jenis_sampah']
    );

    $harga_per_kg = (int) $_POST['harga_per_kg'];
    $stok_kg = (float) $_POST['stok_kg'];

    $query = mysqli_query(
        $conn,
        "UPDATE sampah SET
            jenis_sampah = '$jenis_sampah',
            harga_per_kg = '$harga_per_kg',
            stok_kg = '$stok_kg'
         WHERE id_sampah = '$id_sampah'"
    );

    if ($query) {
        header("Location: sampah.php?pesan=update");
        exit;
    } else {
        echo "Gagal mengubah data: " . mysqli_error($conn);
    }
}


// =========================================
// AMBIL SEMUA DATA SAMPAH
// =========================================

$query_sampah = mysqli_query(
    $conn,
    "SELECT *
     FROM sampah
     ORDER BY id_sampah DESC"
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

<title>Data Sampah - EcoBank</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>

    * {
        box-sizing: border-box;
    }

    body {

        margin: 0;

        font-family:
            Arial,
            Helvetica,
            sans-serif;

        background: #f5f5f5;

        color: #111;
    }


    /* =================================
       CONTAINER
    ================================= */

    .main-container {

        max-width: 1250px;

        margin: auto;

        padding: 35px 20px 50px;
    }


    /* =================================
       HEADER
    ================================= */

    .page-header {

        background: #111;

        color: white;

        border-radius: 20px;

        padding: 25px 30px;

        margin-bottom: 25px;

        display: flex;

        justify-content: space-between;

        align-items: center;

        box-shadow:
            0 10px 30px rgba(0,0,0,0.12);
    }


    .page-header h2 {

        margin: 0;

        font-size: 27px;

        font-weight: 700;
    }


    .page-header p {

        margin: 7px 0 0;

        color: #bdbdbd;

        font-size: 14px;
    }


    .admin-badge {

        background: white;

        color: #111;

        padding: 10px 16px;

        border-radius: 12px;

        font-weight: 600;

        font-size: 14px;
    }


    /* =================================
       CARD
    ================================= */

    .eco-card {

        background: white;

        border: 1px solid #e8e8e8;

        border-radius: 18px;

        box-shadow:
            0 8px 25px rgba(0,0,0,0.06);

        margin-bottom: 25px;

        overflow: hidden;
    }


    .eco-card-header {

        padding: 20px 24px;

        border-bottom: 1px solid #eeeeee;

        display: flex;

        justify-content: space-between;

        align-items: center;
    }


    .eco-card-header h5 {

        margin: 0;

        font-weight: 700;

        color: #111;
    }


    .eco-card-body {

        padding: 25px;
    }


    /* =================================
       FORM
    ================================= */

    .form-label {

        font-weight: 600;

        font-size: 14px;

        color: #222;
    }


    .form-control {

        border: 1px solid #dddddd;

        border-radius: 11px;

        padding: 12px 14px;

        background: #fafafa;

        transition: 0.2s;
    }


    .form-control:focus {

        border-color: #111;

        box-shadow:
            0 0 0 3px rgba(0,0,0,0.08);

        background: white;
    }


    /* =================================
       BUTTON
    ================================= */

    .btn-black {

        background: #111;

        color: white;

        border: none;

        border-radius: 10px;

        padding: 11px 18px;

        font-weight: 600;

        transition: 0.2s;
    }


    .btn-black:hover {

        background: #2b2b2b;

        color: white;

        transform: translateY(-1px);
    }


    .btn-light-custom {

        background: #f1f1f1;

        color: #111;

        border: 1px solid #dddddd;

        border-radius: 10px;

        padding: 10px 17px;

        font-weight: 600;
    }


    .btn-light-custom:hover {

        background: #e5e5e5;

        color: #111;
    }


    .btn-edit {

        background: #111;

        color: white;

        border: none;

        border-radius: 8px;

        padding: 7px 12px;

        font-size: 13px;

        text-decoration: none;
    }


    .btn-edit:hover {

        background: #333;

        color: white;
    }


    .btn-delete {

        background: white;

        color: #111;

        border: 1px solid #cccccc;

        border-radius: 8px;

        padding: 7px 12px;

        font-size: 13px;

        text-decoration: none;
    }


    .btn-delete:hover {

        background: #111;

        color: white;

        border-color: #111;
    }


    /* =================================
       TABLE
    ================================= */

    .table {

        margin: 0;

        vertical-align: middle;
    }


    .table thead th {

        background: #111;

        color: white;

        border: none;

        padding: 15px;

        font-size: 13px;

        font-weight: 600;
    }


    .table tbody td {

        padding: 15px;

        border-color: #eeeeee;

        color: #333;
    }


    .table tbody tr {

        transition: 0.15s;
    }


    .table tbody tr:hover {

        background: #fafafa;
    }


    .badge-stock {

        background: #111;

        color: white;

        border-radius: 20px;

        padding: 7px 11px;

        font-size: 12px;
    }


    /* =================================
       ALERT
    ================================= */

    .custom-alert {

        background: white;

        border: 1px solid #dddddd;

        color: #111;

        border-left: 5px solid #111;

        border-radius: 12px;

        padding: 15px 18px;

        margin-bottom: 20px;

        box-shadow:
            0 5px 15px rgba(0,0,0,0.04);
    }


    /* =================================
       EMPTY
    ================================= */

    .empty-data {

        text-align: center;

        padding: 45px !important;

        color: #888;
    }


    /* =================================
       FOOTER BUTTON
    ================================= */

    .bottom-actions {

        margin-top: 25px;

        display: flex;

        justify-content: space-between;

        align-items: center;
    }


    /* =================================
       RESPONSIVE
    ================================= */

    @media (max-width: 768px) {

        .page-header {

            flex-direction: column;

            align-items: flex-start;

            gap: 15px;
        }

        .admin-badge {

            align-self: flex-start;
        }

        .eco-card-body {

            padding: 18px;
        }

    }

</style>
```

</head>

<body>

<div class="main-container">

```
<!-- =================================
     HEADER
================================= -->

<div class="page-header">

    <div>

        <h2>
            ♻️ Data Sampah
        </h2>

        <p>
            Kelola jenis sampah, harga, dan stok EcoBank.
        </p>

    </div>


    <div class="admin-badge">

        Admin Panel

    </div>

</div>


<!-- =================================
     PESAN
================================= -->

<?php if (isset($_GET['pesan'])) : ?>

    <?php if ($_GET['pesan'] == "tambah") : ?>

        <div class="custom-alert">
            ✓ Data sampah berhasil ditambahkan.
        </div>

    <?php elseif ($_GET['pesan'] == "update") : ?>

        <div class="custom-alert">
            ✓ Data sampah berhasil diperbarui.
        </div>

    <?php elseif ($_GET['pesan'] == "hapus") : ?>

        <div class="custom-alert">
            ✓ Data sampah berhasil dihapus.
        </div>

    <?php endif; ?>

<?php endif; ?>


<!-- =================================
     FORM TAMBAH / EDIT
================================= -->

<div class="eco-card">


    <div class="eco-card-header">

        <h5>

            <?= $data_edit
                ? '✏️ Edit Data Sampah'
                : '➕ Tambah Data Sampah';
            ?>

        </h5>

    </div>


    <div class="eco-card-body">


        <form method="POST">


            <?php if ($data_edit) : ?>

                <input
                    type="hidden"
                    name="id_sampah"
                    value="<?= $data_edit['id_sampah']; ?>"
                >

            <?php endif; ?>


            <div class="row">


                <!-- JENIS -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Jenis Sampah
                    </label>

                    <input
                        type="text"
                        name="jenis_sampah"
                        class="form-control"
                        placeholder="Contoh: Botol Plastik"
                        value="<?= $data_edit
                            ? htmlspecialchars($data_edit['jenis_sampah'])
                            : '';
                        ?>"
                        required
                    >

                </div>


                <!-- HARGA -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Harga per Kg
                    </label>

                    <input
                        type="number"
                        name="harga_per_kg"
                        class="form-control"
                        placeholder="Contoh: 4000"
                        value="<?= $data_edit
                            ? $data_edit['harga_per_kg']
                            : '';
                        ?>"
                        required
                    >

                </div>


                <!-- STOK -->

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Stok Kg
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="stok_kg"
                        class="form-control"
                        placeholder="Contoh: 10"
                        value="<?= $data_edit
                            ? $data_edit['stok_kg']
                            : '';
                        ?>"
                        required
                    >

                </div>


            </div>


            <?php if ($data_edit) : ?>

                <button
                    type="submit"
                    name="update"
                    class="btn-black"
                >
                    Simpan Perubahan
                </button>

                <a
                    href="sampah.php"
                    class="btn-light-custom"
                >
                    Batal
                </a>

            <?php else : ?>

                <button
                    type="submit"
                    name="tambah"
                    class="btn-black"
                >
                    + Tambah Sampah
                </button>

            <?php endif; ?>


        </form>


    </div>

</div>


<!-- =================================
     TABEL
================================= -->

<div class="eco-card">


    <div class="eco-card-header">

        <h5>
            📦 Daftar Sampah
        </h5>

        <span class="text-secondary small">
            Data EcoBank
        </span>

    </div>


    <div class="table-responsive">


        <table class="table">


            <thead>

                <tr>

                    <th>
                        ID
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
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if (mysqli_num_rows($query_sampah) > 0) : ?>


                <?php while ($sampah = mysqli_fetch_assoc($query_sampah)) : ?>


                    <tr>


                        <td>

                            <strong>
                                #<?= $sampah['id_sampah']; ?>
                            </strong>

                        </td>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $sampah['jenis_sampah']
                                ); ?>
                            </strong>

                        </td>


                        <td>

                            <?= rupiah(
                                $sampah['harga_per_kg']
                            ); ?>

                        </td>


                        <td>

                            <span class="badge-stock">

                                <?= number_format(
                                    $sampah['stok_kg'],
                                    2,
                                    ',',
                                    '.'
                                ); ?>

                                Kg

                            </span>

                        </td>


                        <td>

                            <a
                                href="sampah.php?edit=<?= $sampah['id_sampah']; ?>"
                                class="btn-edit"
                            >
                                Edit
                            </a>


                            <a
                                href="sampah.php?hapus=<?= $sampah['id_sampah']; ?>"
                                class="btn-delete"
                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                            >
                                Hapus
                            </a>

                        </td>


                    </tr>


                <?php endwhile; ?>


            <?php else : ?>


                <tr>

                    <td
                        colspan="5"
                        class="empty-data"
                    >

                        📭 Belum ada data sampah.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    </div>

</div>


<!-- =================================
     BOTTOM ACTION
================================= -->

<div class="bottom-actions">


    <a
        href="dashboard.php"
        class="btn-light-custom"
    >
        ← Kembali ke Dashboard
    </a>


    <a
        href="../logout.php"
        class="btn-black"
    >
        Logout
    </a>


</div>
```

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>
