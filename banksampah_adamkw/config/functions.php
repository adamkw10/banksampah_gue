<?php


/*
|--------------------------------------------------------------------------
| REDIRECT SESUAI ROLE
|--------------------------------------------------------------------------
*/

function redirect_role($role)
{
    if ($role == "admin") {

        header("Location: admin/dashboard.php");
        exit;

    } elseif ($role == "siswa") {

        header("Location: siswa/dashboard.php");
        exit;

    } elseif ($role == "guru") {

        header("Location: guru/dashboard.php");
        exit;

    } elseif ($role == "pengepul") {

        header("Location: pengepul/dashboard.php");
        exit;

    } else {

        header("Location: login.php");
        exit;

    }
}


/*
|--------------------------------------------------------------------------
| FORMAT RUPIAH
|--------------------------------------------------------------------------
*/

function rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

?>