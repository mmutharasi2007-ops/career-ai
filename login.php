<?php
require_once "auth_session.php";
require_once "db.php";

if (!empty($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $message = "Invalid email or password!";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $validPassword = false;
        if ($user) {
            $storedPassword = (string)$user['password'];
            if (password_verify($password, $storedPassword)) {
                $validPassword = true;
            } else {
                // Temporary compatibility: upgrade an old plain-text password after a successful login.
                $passwordInfo = password_get_info($storedPassword);
                if (($passwordInfo['algo'] ?? null) === null &&
                    hash_equals($storedPassword, $password)) {
                    $validPassword = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upgrade = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upgrade->bind_param("si", $newHash, $user['id']);
                    $upgrade->execute();
                    $upgrade->close();
                }
            }
        }

        if ($validPassword && $user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['name'] = $user['name'];
            header("Location: dashboard.php");
            exit();
        }

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