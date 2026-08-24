<?php

require_once __DIR__ . '/config.php';


/*
|--------------------------------------------------------------------------
| Already Logged In
|--------------------------------------------------------------------------
*/

if (
    isset($_SESSION['admin_logged_in']) &&
    $_SESSION['admin_logged_in'] === true
) {
    header('Location: dashboard.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $username === ADMIN_USERNAME &&
        $password === ADMIN_PASSWORD
    ) {

        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');

        header('Location: dashboard.php');
        exit;
    } else {

        $error = 'Invalid username or password.';
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Login | Intelligent Resume Screening
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    <style>
        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            font-family: 'Poppins', sans-serif;

            background:
                radial-gradient(circle at 10% 20%,
                    #d9d7ff 0%,
                    transparent 35%),
                radial-gradient(circle at 90% 80%,
                    #c9bfff 0%,
                    transparent 35%),
                #e9e8ff;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 35px;

            overflow-x: hidden;

        }


        /*
        |--------------------------------------------------------------------------
        | MAIN CONTAINER
        |--------------------------------------------------------------------------
        */

        .page-container {

            width: 100%;

            max-width: 1380px;

            background: #ffffff;

            border-radius: 30px;

            box-shadow:
                0 25px 70px rgba(57, 46, 130, 0.18);

            overflow: hidden;

            position: relative;

        }


        /*
        |--------------------------------------------------------------------------
        | NAVBAR
        |--------------------------------------------------------------------------
        */

        .navbar-custom {

            height: 105px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 45px;

        }


        .brand {

            display: flex;

            align-items: center;

            gap: 20px;

            color: #3e3985;

            font-size: 30px;

            font-weight: 600;

        }


        .brand-icon {

            width: 58px;

            height: 58px;

            border-radius: 17px;

            background:
                linear-gradient(135deg,
                    #ed4c9b,
                    #df4b9b);

            display: flex;

            align-items: center;

            justify-content: center;

            color: white;

            font-size: 25px;

            box-shadow:
                0 8px 20px rgba(230, 72, 150, 0.25);

        }


        .nav-links {

            display: flex;

            align-items: center;

            gap: 45px;

        }


        .nav-links a {

            text-decoration: none;

            color: #403d70;

            font-size: 16px;

            font-weight: 500;

            transition: 0.3s;

        }


        .nav-links a:hover {

            color: #e84b9b;

        }


        .signup-btn {

            border: none;

            background:
                linear-gradient(135deg,
                    #ee4b9c,
                    #df4392);

            color: white !important;

            padding:
                12px 30px;

            border-radius: 30px;

            box-shadow:
                0 7px 18px rgba(228, 67, 145, 0.25);

        }


        /*
        |--------------------------------------------------------------------------
        | CONTENT
        |--------------------------------------------------------------------------
        */

        .hero {

            display: flex;

            align-items: center;

            padding:
                30px 45px 60px;

        }


        .hero-left {

            width: 43%;

            padding:
                20px 30px;

        }


        .hero-right {

            width: 57%;

            display: flex;

            justify-content: center;

            align-items: center;

            position: relative;

        }


        /*
        |--------------------------------------------------------------------------
        | LEFT HEADING
        |--------------------------------------------------------------------------
        */

        .small-title {

            color: #5a9fd2;

            font-size: 18px;

            font-weight: 500;

            letter-spacing: 1px;

            margin-bottom: 12px;

        }


        .main-title {

            color: #302d79;

            font-size: 48px;

            line-height: 1.2;

            font-weight: 600;

            margin-bottom: 10px;

        }


        .main-title span {

            color: #e64b99;

        }


        .subtitle {

            color: #679bd0;

            font-size: 23px;

            font-weight: 500;

            margin-bottom: 18px;

        }


        .description {

            color: #6f7181;

            font-size: 15px;

            line-height: 1.9;

            max-width: 520px;

            margin-bottom: 30px;

        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN CARD
        |--------------------------------------------------------------------------
        */

        .login-box {

            width: 100%;

            max-width: 460px;

            padding:
                28px 32px;

            background: #ffffff;

            border-radius: 18px;

            border:
                1px solid #eeeeF8;

            box-shadow:
                0 10px 30px rgba(57, 52, 120, 0.08);

        }


        .login-heading {

            color: #302d79;

            font-size: 24px;

            font-weight: 600;

            margin-bottom: 4px;

        }


        .login-description {

            color: #8a8b99;

            font-size: 13px;

            margin-bottom: 20px;

        }


        .form-label {

            color: #454263;

            font-size: 14px;

            font-weight: 500;

            margin-bottom: 8px;

        }


        .form-control {

            height: 48px;

            border-radius: 10px;

            border:
                1px solid #dedeee;

            padding:
                0 15px;

            color: #333;

            font-size: 14px;

        }


        .form-control:focus {

            border-color: #df4a97;

            box-shadow:
                0 0 0 3px rgba(225, 73, 151, 0.1);

        }


        .password-wrapper {

            position: relative;

        }


        .password-wrapper .form-control {

            padding-right: 50px;

        }


        .toggle-password {

            position: absolute;

            right: 12px;

            top: 50%;

            transform:
                translateY(-50%);

            border: none;

            background: transparent;

            color: #777;

            cursor: pointer;

        }


        .login-button {

            width: 100%;

            height: 50px;

            border: none;

            border-radius: 12px;

            color: white;

            font-size: 15px;

            font-weight: 600;

            background:
                linear-gradient(135deg,
                    #3172d1,
                    #3172d1);

            box-shadow:
                0 8px 20px rgba(225, 64, 143, 0.2);

            transition: 0.3s;

        }


        .login-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 25px rgba(225, 64, 143, 0.3);

        }


        .error-box {

            background: #fff0f3;

            border:
                1px solid #ffc8d4;

            color: #c73556;

            padding: 10px 13px;

            border-radius: 9px;

            font-size: 13px;

            margin-bottom: 18px;

        }


        /*
        |--------------------------------------------------------------------------
        | ILLUSTRATION
        |--------------------------------------------------------------------------
        */

        .illustration {

            width: 95%;

            max-width: 650px;

            height: 560px;

            position: relative;

        }


        .circle-bg {

            position: absolute;

            width: 480px;

            height: 480px;

            border-radius: 50%;

            background:
                #e6f7ff;

            right: 55px;

            top: 45px;

        }


        .circle-bg-two {

            position: absolute;

            width: 260px;

            height: 260px;

            border-radius: 50%;

            background:
                #f1edff;

            right: -10px;

            bottom: 10px;

        }


        /*
        |--------------------------------------------------------------------------
        | LAPTOP
        |--------------------------------------------------------------------------
        */

        .laptop {

            position: absolute;

            width: 500px;

            height: 330px;

            background:
                #332b91;

            border-radius:
                20px 20px 8px 8px;

            right: 55px;

            top: 115px;

            border:
                12px solid #5c55b8;

            box-shadow:
                0 20px 35px rgba(46, 39, 130, 0.22);

        }


        .screen {

            width: 100%;

            height: 100%;

            background:
                #ffffff;

            border-radius: 6px;

            overflow: hidden;

        }


        .screen-header {

            height: 52px;

            background:
                #938de8;

            display: flex;

            align-items: center;

            padding: 0 18px;

        }


        .screen-search {

            width: 220px;

            height: 27px;

            background:
                #d5d2ff;

            border-radius: 5px;

        }


        .screen-content {

            padding: 18px;

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 12px;

        }


        .screen-card {

            height: 85px;

            border-radius: 10px;

            background:
                #eef1ff;

        }


        .screen-card.pink {

            background:
                #ffd5eb;

        }


        .screen-card.blue {

            background:
                #d7f4ff;

        }


        .screen-card.yellow {

            background:
                #fff1b9;

        }


        .screen-card.green {

            background:
                #ccefed;

        }


        .screen-bottom {

            margin: 5px 18px;

            height: 14px;

            width: 65%;

            border-radius: 10px;

            background:
                #b9b7e8;

        }


        .laptop-base {

            position: absolute;

            width: 550px;

            height: 22px;

            background:
                #7879a7;

            right: 30px;

            top: 450px;

            border-radius:
                0 0 30px 30px;

            transform:
                perspective(200px) rotateX(-10deg);

        }


        /*
        |--------------------------------------------------------------------------
        | DECORATIVE PEOPLE
        |--------------------------------------------------------------------------
        */

        .person {

            position: absolute;

            z-index: 5;

        }


        .person-one {

            left: 65px;

            top: 105px;

        }


        .person-two {

            right: 80px;

            top: 315px;

        }


        .head {

            width: 32px;

            height: 35px;

            border-radius: 50%;

            background:
                #f2b28f;

            margin: auto;

        }


        .body {

            width: 60px;

            height: 95px;

            background:
                #e84d9b;

            border-radius:
                25px 25px 8px 8px;

        }


        .person-two .body {

            background:
                #3172d1;

        }


        .legs {

            display: flex;

            justify-content: center;

            gap: 8px;

        }


        .leg {

            width: 14px;

            height: 65px;

            background:
                #27345d;

            border-radius: 5px;

        }


        /*
        |--------------------------------------------------------------------------
        | FLOATING CARDS
        |--------------------------------------------------------------------------
        */

        .floating-card {

            position: absolute;

            z-index: 8;

            background: white;

            border-radius: 12px;

            box-shadow:
                0 10px 25px rgba(40, 38, 100, 0.15);

        }


        .login-floating {

            width: 130px;

            height: 80px;

            left: 15px;

            top: 300px;

            padding: 12px;

        }


        .login-floating .avatar-small {

            width: 30px;

            height: 30px;

            border-radius: 50%;

            background: #f6cf44;

            margin-bottom: 8px;

        }


        .floating-line {

            width: 80%;

            height: 7px;

            border-radius: 5px;

            background: #d7d8e8;

            margin-bottom: 6px;

        }


        .chart-floating {

            width: 115px;

            height: 100px;

            right: 0;

            top: 55px;

            padding: 15px;

        }


        .bars {

            height: 60px;

            display: flex;

            align-items: end;

            gap: 6px;

        }


        .bar {

            width: 14px;

            border-radius:
                5px 5px 0 0;

        }


        .bar.one {

            height: 25px;

            background: #5d57ca;

        }


        .bar.two {

            height: 42px;

            background: #e94b9b;

        }


        .bar.three {

            height: 55px;

            background: #49c7bd;

        }


        .bar.four {

            height: 35px;

            background: #f4c542;

        }


        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1100px) {

            .nav-links {

                gap: 20px;

            }


            .hero-left {

                width: 50%;

            }


            .hero-right {

                width: 50%;

            }


            .main-title {

                font-size: 40px;

            }


            .illustration {

                transform:
                    scale(0.85);

            }

        }


        @media (max-width: 850px) {

            body {

                padding: 15px;

            }


            .page-container {

                min-height: auto;

                border-radius: 20px;

            }


            .navbar-custom {

                height: 85px;

                padding:
                    0 25px;

            }


            .brand {

                font-size: 22px;

            }


            .brand-icon {

                width: 45px;

                height: 45px;

            }


            .nav-links a:not(.signup-btn) {

                display: none;

            }


            .hero {

                flex-direction: column;

                padding:
                    25px;

            }


            .hero-left,
            .hero-right {

                width: 100%;

            }


            .hero-left {

                padding: 20px 0;

                text-align: center;

            }


            .description {

                margin-left: auto;

                margin-right: auto;

            }


            .login-box {

                margin:
                    0 auto;

                text-align: left;

            }


            .hero-right {

                min-height: 450px;

            }

        }


        @media (max-width: 600px) {

            .navbar-custom {

                padding:
                    0 15px;

            }


            .brand {

                gap: 10px;

                font-size: 18px;

            }


            .signup-btn {

                padding:
                    9px 18px;

            }


            .main-title {

                font-size: 32px;

            }


            .subtitle {

                font-size: 18px;

            }


            .illustration {

                transform:
                    scale(0.62);

                transform-origin:
                    top center;

                width: 650px;

            }

        }
    </style>

</head>


<body>


    <div class="page-container">

        <!-- =====================================================
         HERO
    ====================================================== -->

        <section class="hero">

            <div class="hero-right">


                <div class="illustration">


                    <!-- BACKGROUND -->

                    <div class="circle-bg"></div>

                    <div class="circle-bg-two"></div>


                    <!-- LAPTOP -->

                    <div class="laptop">


                        <div class="screen">


                            <div class="screen-header">

                                <div class="screen-search"></div>

                            </div>


                            <div class="screen-content">


                                <div
                                    class="screen-card yellow"></div>


                                <div
                                    class="screen-card blue"></div>


                                <div
                                    class="screen-card pink"></div>


                                <div
                                    class="screen-card green"></div>


                            </div>


                            <div class="screen-bottom"></div>

                            <div class="screen-bottom"
                                style="width:45%;"></div>

                            <div class="screen-bottom"
                                style="width:55%;"></div>


                        </div>


                    </div>


                    <div class="laptop-base"></div>


                    <!-- PERSON LEFT -->

                    <div
                        class="person person-one">

                        <div class="head"></div>

                        <div class="body"></div>

                        <div class="legs">

                            <div class="leg"></div>

                            <div class="leg"></div>

                        </div>

                    </div>


                    <!-- PERSON RIGHT -->

                    <div
                        class="person person-two">

                        <div class="head"></div>

                        <div class="body"></div>

                        <div class="legs">

                            <div class="leg"></div>

                            <div class="leg"></div>

                        </div>

                    </div>


                    <!-- LOGIN FLOATING CARD -->

                    <div
                        class="floating-card login-floating">

                        <div class="avatar-small"></div>

                        <div
                            class="floating-line"></div>

                        <div
                            class="floating-line"
                            style="width:60%;"></div>

                    </div>


                    <!-- CHART -->

                    <div
                        class="floating-card chart-floating">

                        <div
                            class="bars">

                            <div
                                class="bar one"></div>

                            <div
                                class="bar two"></div>

                            <div
                                class="bar three"></div>

                            <div
                                class="bar four"></div>

                        </div>

                    </div>


                </div>


            </div>
            <!-- LEFT -->

            <div class="hero-left">

                <!-- LOGIN -->

                <div
                    class="login-box"
                    id="login">


                    <h3 class="login-heading">

                        Welcome

                    </h3>


                    <p class="login-description">

                        Login to your recruiter dashboard

                    </p>


                    <?php if ($error !== ''): ?>

                        <div class="error-box">

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>


                    <form
                        method="POST"
                        action="">


                        <!-- USERNAME -->

                        <div class="mb-3">


                            <label
                                class="form-label">

                                Username

                            </label>


                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Enter your username"
                                required
                                autocomplete="username">


                        </div>


                        <!-- PASSWORD -->

                        <div class="mb-3">


                            <label
                                class="form-label">

                                Password

                            </label>


                            <div class="password-wrapper">


                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password">


                                <button
                                    type="button"
                                    class="toggle-password"
                                    onclick="togglePassword()">

                                    👁

                                </button>


                            </div>


                        </div>


                        <div
                            class="d-flex justify-content-between align-items-center mb-4">

                            <div>

                                <input
                                    type="checkbox"
                                    id="remember">

                                <label
                                    for="remember"
                                    class="small text-muted">

                                    Remember me

                                </label>

                            </div>


                            <a
                                href="#"
                                class="small text-decoration-none"
                                style="color:#3172d1;">

                                Forgot password?

                            </a>

                        </div>


                        <!-- LOGIN -->

                        <button
                            type="submit"
                            class="login-button">

                            Login to Dashboard

                        </button>


                    </form>


                    <div
                        class="text-center mt-3">

                        <small class="text-muted">

                            Demo:
                            <strong>admin</strong>
                            /
                            <strong>admin123</strong>

                        </small>

                    </div>


                </div>


            </div>



        </section>


    </div>


    <script>
        /*
|--------------------------------------------------------------------------
| Password Toggle
|--------------------------------------------------------------------------
*/

        function togglePassword() {

            const password =
                document.getElementById(
                    'password'
                );


            if (
                password.type === 'password'
            ) {

                password.type = 'text';

            } else {

                password.type = 'password';

            }

        }
    </script>


</body>

</html>