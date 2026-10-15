<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| CEK SUDAH LOGIN
|--------------------------------------------------------------------------
*/

function wajib_login()
{
    if (!isset($_SESSION['status']) || $_SESSION['status'] != "sukses") {

        header("Location: ../login.php");
        exit;

    }
}


/*
|--------------------------------------------------------------------------
| CEK ROLE
|--------------------------------------------------------------------------
*/

function wajib_role($role)
{
    wajib_login();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != $role) {

        echo "Anda tidak memiliki akses ke halaman ini.";
        exit;

    }
}

?>