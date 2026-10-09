<?php
require_once "auth_session.php";
require_once "db.php";

if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $department = trim($_POST['department'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === '' || $department === '') {
        $message = "Please fill in all fields with valid details.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "This email is already registered! Please login.";
            $check->close();
        } else {
            $check->close();
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare(
                "INSERT INTO users (name, email, password, department) VALUES (?, ?, ?, ?)"
            );
            $insert->bind_param("ssss", $name, $email, $passwordHash, $department);

            if ($insert->execute()) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['name'] = $name;
                $insert->close();
                header("Location: dashboard.php");
                exit();
            }

            $message = "Registration failed. Please try again.";
            $insert->close();
        }
    }
}
?>


<!DOCTYPE html>

<html>

<head>

    <title>Career AI - Student Registration</title>


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

            margin: 60px auto;

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


        input,
        select {

            width: 100%;

            padding: 12px;

            margin-top: 8px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 15px;

        }


        input:focus,
        select:focus {

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


        .login-link {

            text-align: center;

            margin-top: 20px;

        }


        .login-link a {

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


        <a href="login.php">

            Login

        </a>


    </nav>


</header>



<div class="container">


    <h2>

        Student Registration

    </h2>



    <form method="POST">


        <label>

            Name

        </label>


        <input

            type="text"

            name="name"

            required

        >



        <br><br>



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



        <label>

            Engineering Department

        </label>



        <select

            name="department"

            required

        >


            <option value="">

                Select Department

            </option>


            <option value="CSE">

                CSE

            </option>


            <option value="IT">

                IT

            </option>


            <option value="ECE">

                ECE

            </option>


            <option value="EEE">

                EEE

            </option>


            <option value="Mechanical">

                Mechanical

            </option>


            <option value="Civil">

                Civil

            </option>


            <option value="Chemical">

                Chemical

            </option>


            <option value="AI & DS">

                AI & DS

            </option>


            <option value="AI & ML">

                AI & ML

            </option>


        </select>



        <br><br>



        <button

            type="submit"

            name="register"

        >

            Create Account

        </button>


    </form>



    <?php if ($message != ""): ?>


        <p class="message">

            <?php

            echo htmlspecialchars($message);

            ?>

        </p>


    <?php endif; ?>



    <div class="login-link">


        Already have an account?


        <a href="login.php">

            Login

        </a>


    </div>


</div>


</body>

</html>