
<?php

session_start();

include "config/koneksi.php";
include "config/functions.php";


/*
|--------------------------------------------------------------------------
| JIKA SUDAH LOGIN
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION['status']) &&
    $_SESSION['status'] == "sukses"
) {

    redirect_role($_SESSION['role']);

}


/*
|--------------------------------------------------------------------------
| PROSES LOGIN
|--------------------------------------------------------------------------
*/

$error = "";

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string(
        $conn,
        $_POST['username']
    );

    $password = $_POST['password'];


    /*
    |--------------------------------------------------------------------------
    | CARI USER
    |--------------------------------------------------------------------------
    */

    $query = mysqli_query(
        $conn,
        "SELECT *
         FROM users
         WHERE username = '$username'
         LIMIT 1"
    );


    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);


        /*
        |--------------------------------------------------------------------------
        | CEK PASSWORD
        |--------------------------------------------------------------------------
        */

        if (
            password_verify(
                $password,
                $user['password']
            )
        ) {


            /*
            |--------------------------------------------------------------------------
            | SIMPAN SESSION
            |--------------------------------------------------------------------------
            */

            $_SESSION['status'] = "sukses";

            $_SESSION['id_user'] =
                $user['id_user'];

            $_SESSION['nama'] =
                $user['nama'];

            $_SESSION['username'] =
                $user['username'];

            $_SESSION['saldo'] =
                $user['saldo'];

            $_SESSION['role'] =
                $user['role'];


            /*
            |--------------------------------------------------------------------------
            | REDIRECT
            |--------------------------------------------------------------------------
            */

            redirect_role(
                $user['role']
            );


        } else {

            $error =
                "Password yang kamu masukkan salah.";

        }

    } else {

        $error =
            "Username tidak ditemukan.";

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
        Login - Bank Sampah
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

        /* ==================================================
           GLOBAL
        ================================================== */

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f7f7f7 0%,
                    #ffffff 50%,
                    #eeeeee 100%
                );

            color: #111111;

        }


        /* ==================================================
           BACKGROUND
        ================================================== */

        .login-wrapper {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 25px;

        }


        .login-container {

            width: 100%;

            max-width: 430px;

        }


        /* ==================================================
           BRAND
        ================================================== */

        .brand {

            text-align: center;

            margin-bottom: 25px;

        }


        .logo-icon {

            width: 75px;

            height: 75px;

            margin: 0 auto 18px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 22px;

            background: #111111;

            color: #ffffff;

            font-size: 35px;

            box-shadow:
                0 12px 30px
                rgba(0, 0, 0, 0.15);

        }


        .brand h1 {

            font-size: 30px;

            font-weight: 800;

            margin: 0;

            letter-spacing: -0.5px;

        }


        .brand p {

            margin-top: 8px;

            color: #777777;

            font-size: 14px;

        }


        /* ==================================================
           LOGIN CARD
        ================================================== */

        .login-card {

            background: #ffffff;

            border: 1px solid #e5e5e5;

            border-radius: 22px;

            padding: 32px;

            box-shadow:
                0 20px 50px
                rgba(0, 0, 0, 0.08);

        }


        .login-title {

            font-size: 21px;

            font-weight: 800;

            margin-bottom: 5px;

        }


        .login-subtitle {

            color: #777777;

            font-size: 14px;

            margin-bottom: 25px;

        }


        /* ==================================================
           FORM
        ================================================== */

        .form-label {

            font-weight: 600;

            font-size: 14px;

            margin-bottom: 8px;

        }


        .input-group {

            border-radius: 11px;

            overflow: hidden;

        }


        .input-group-text {

            background: #f7f7f7;

            border-color: #dddddd;

            color: #555555;

            min-width: 48px;

            justify-content: center;

        }


        .form-control {

            border-color: #dddddd;

            padding: 12px 13px;

            font-size: 14px;

        }


        .form-control:focus {

            border-color: #111111;

            box-shadow:
                0 0 0 0.15rem
                rgba(0, 0, 0, 0.08);

        }


        .password-button {

            border-color: #dddddd;

            background: #ffffff;

            color: #555555;

        }


        .password-button:hover {

            background: #f5f5f5;

            color: #111111;

        }


        /* ==================================================
           LOGIN BUTTON
        ================================================== */

        .btn-ecobank {

            width: 100%;

            background: #111111;

            color: #ffffff;

            border: none;

            padding: 13px;

            border-radius: 11px;

            font-weight: 700;

            font-size: 14px;

            transition: 0.2s;

        }


        .btn-ecobank:hover {

            background: #2b2b2b;

            color: #ffffff;

            transform: translateY(-1px);

            box-shadow:
                0 8px 20px
                rgba(0, 0, 0, 0.15);

        }


        /* ==================================================
           ERROR
        ================================================== */

        .login-error {

            background: #f5f5f5;

            color: #222222;

            border: 1px solid #dddddd;

            border-left: 4px solid #111111;

            border-radius: 10px;

            padding: 12px 14px;

            font-size: 13px;

            margin-bottom: 20px;

        }


        /* ==================================================
           FOOTER
        ================================================== */

        .login-footer {

            text-align: center;

            margin-top: 22px;

            color: #999999;

            font-size: 12px;

        }


        /* ==================================================
           RESPONSIVE
        ================================================== */

        @media (max-width: 480px) {

            .login-wrapper {

                padding: 15px;

            }


            .login-card {

                padding: 25px 20px;

                border-radius: 18px;

            }


            .logo-icon {

                width: 65px;

                height: 65px;

                font-size: 30px;

            }


            .brand h1 {

                font-size: 26px;

            }

        }

    </style>

</head>


<body>


<div class="login-wrapper">


    <div class="login-container">


        <!-- ==================================================
             BRAND
        ================================================== -->

        <div class="brand">

            <div class="logo-icon">

                ♻️

            </div>


            <h1>
                Bank Sampah
            </h1>


            <p>
                Ubah sampah menjadi nilai.
            </p>

        </div>



        <!-- ==================================================
             LOGIN CARD
        ================================================== -->

        <div class="login-card">


            <div class="login-title">

                Selamat datang 👋

            </div>


            <div class="login-subtitle">

                Masuk ke akun Bank Sampah kamu.

            </div>



            <!-- ERROR -->

            <?php if ($error != "") : ?>

                <div class="login-error">

                    <i class="bi bi-exclamation-circle me-1"></i>

                    <?= htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>



            <!-- FORM -->

            <form method="POST">


                <!-- USERNAME -->

                <div class="mb-3">

                    <label class="form-label">

                        Username

                    </label>


                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-person"></i>

                        </span>


                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            required
                        >

                    </div>

                </div>



                <!-- PASSWORD -->

                <div class="mb-4">

                    <label class="form-label">

                        Password

                    </label>


                    <div class="input-group">

                        <span class="input-group-text">

                            <i class="bi bi-lock"></i>

                        </span>


                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >


                        <button
                            type="button"
                            class="btn password-button"
                            onclick="togglePassword()"
                        >

                            <i
                                class="bi bi-eye"
                                id="eyeIcon"
                            ></i>

                        </button>

                    </div>

                </div>



                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    name="login"
                    class="btn btn-ecobank"
                >

                    Masuk ke Bank Sampah

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>


            </form>



            <!-- FOOTER -->

            <div class="login-footer">

                Bank Sampah ♻️

            </div>


        </div>

    </div>

</div>



<script>

function togglePassword()
{

    const password =
        document.getElementById("password");

    const icon =
        document.getElementById("eyeIcon");


    if (password.type === "password") {

        password.type = "text";

        icon.classList.remove(
            "bi-eye"
        );

        icon.classList.add(
            "bi-eye-slash"
        );

    } else {

        password.type = "password";

        icon.classList.remove(
            "bi-eye-slash"
        );

        icon.classList.add(
            "bi-eye"
        );

    }

}

</script>


</body>

</html>

