<?php

session_start();

include "db.php";


/* =========================
   ALREADY LOGGED IN
========================= */

if (isset($_SESSION['user_id'])) {

    header("Location: dashboard.php");

    exit();
}


$message = "";


/* =========================
   LOGIN
========================= */

if (isset($_POST['login'])) {

    $email = $_POST['email'];

    $password = $_POST['password'];


    /* =========================
       CHECK EMAIL + PASSWORD
    ========================= */

    $sql = "SELECT * FROM users
            WHERE email='$email'
            AND password='$password'";


    $result = $conn->query($sql);


    if ($result->num_rows == 1) {


        /* =========================
           GET USER DETAILS
        ========================= */

        $user = $result->fetch_assoc();


        /* =========================
           CREATE LOGIN SESSION
        ========================= */

        $_SESSION['user_id'] = $user['id'];

        $_SESSION['name'] = $user['name'];


        /* =========================
           LOGIN SUCCESS
        ========================= */

        header("Location: dashboard.php");

        exit();


    } else {


        /* =========================
           LOGIN FAILED
        ========================= */

        $message = "Invalid email or password!";

    }

}

?>


<!DOCTYPE html>

<html>

<head>

    <title>Career AI - Login</title>


    <style>


        * {

            margin: 0;

            padding: 0;

            box-sizing: border-box;

        }


        body {

            font-family: Arial, sans-serif;

            background: #f5f7ff;

        }


        header {

            background: #182848;

            padding: 20px 70px;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .logo {

            color: white;

            font-size: 26px;

            font-weight: bold;

        }


        nav a {

            color: white;

            text-decoration: none;

            margin-left: 30px;

        }


        nav a:hover {

            color: #8ec5fc;

        }


        .container {

            width: 420px;

            margin: 70px auto;

            background: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow: 0 5px 20px #d5d9e5;

        }


        .container h2 {

            text-align: center;

            color: #182848;

            margin-bottom: 25px;

        }


        label {

            font-weight: bold;

            color: #333;

        }


        input {

            width: 100%;

            padding: 12px;

            margin-top: 8px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 15px;

        }


        input:focus {

            border-color: #182848;

            outline: none;

        }


        button {

            width: 100%;

            padding: 13px;

            background: #182848;

            color: white;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 16px;

        }


        button:hover {

            background: #304a80;

        }


        .message {

            text-align: center;

            margin-top: 20px;

            color: red;

            font-weight: bold;

        }


        .register-link {

            text-align: center;

            margin-top: 20px;

        }


        .register-link a {

            color: #182848;

            text-decoration: none;

            font-weight: bold;

        }


    </style>

</head>


<body>


<header>


    <div class="logo">

        Career AI

    </div>


    <nav>


        <a href="index.php">

            Home

        </a>


        <a href="register.php">

            Register

        </a>


    </nav>


</header>



<div class="container">


    <h2>

        Student Login

    </h2>



    <form method="POST">


        <label>

            Email

        </label>


        <input

            type="email"

            name="email"

            required

        >



        <br><br>



        <label>

            Password

        </label>


        <input

            type="password"

            name="password"

            required

        >



        <br><br>



        <button

            type="submit"

            name="login"

        >

            Login

        </button>


    </form>



    <?php if ($message != "") { ?>

        <p class="message">

            <?php echo htmlspecialchars($message); ?>

        </p>

    <?php } ?>



    <div class="register-link">

        Don't have an account?


        <a href="register.php">

            Create Account

        </a>

    </div>


</div>


</body>

</html>