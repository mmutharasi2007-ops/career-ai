<?php

require_once "auth_session.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['upload'])) {

    if (isset($_FILES['resume'])) {

        $file_name = $_FILES['resume']['name'];
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_size = $_FILES['resume']['size'];

        $file_ext = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        $allowed = ["pdf", "doc", "docx"];

        if (!in_array($file_ext, $allowed)) {

            $message = "Only PDF, DOC and DOCX files are allowed.";

        } elseif ($file_size > 5000000) {

            $message = "File size must be less than 5 MB.";

        } else {

            if (!is_dir("uploads")) {
                mkdir("uploads");
            }

            $new_name = "resume_" . $_SESSION['user_id'] . "." . $file_ext;

            $destination = "uploads/" . $new_name;

            if (move_uploaded_file($file_tmp, $destination)) {

                $message = "Resume uploaded successfully!";

            } else {

                $message = "Resume upload failed.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Career AI - Resume Upload</title>

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
            width: 500px;
            margin: 70px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px #d5d9e5;
            text-align: center;
        }

        h2 {
            color: #182848;
            margin-bottom: 15px;
        }

        .description {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        input[type="file"] {
            width: 100%;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #182848;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #304a80;
        }

        .message {
            margin-top: 20px;
            color: green;
            font-weight: bold;
        }

        .analyze-button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 13px;
            background: #304a80;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }

        .analyze-button:hover {
            background: #182848;
        }

    </style>

</head>

<body>


<header>

    <div class="logo">
        Career AI
    </div>

    <nav>

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<div class="container">

    <h2>
        Upload Your Resume
    </h2>


    <p class="description">

        Upload your resume so Career AI can analyze
        your skills, education and qualifications.

    </p>


    <form method="POST" enctype="multipart/form-data">

        <input
            type="file"
            name="resume"
            accept=".pdf,.doc,.docx"
            required
        >

        <button type="submit" name="upload">
            Upload Resume
        </button>

    </form>


    <p class="message">

        <?php echo $message; ?>

    </p>


    <?php

    if ($message == "Resume uploaded successfully!") {

    ?>

        <a
            href="analyze_resume.php"
            class="analyze-button"
        >
            Analyze My Resume
        </a>

    <?php

    }

    ?>

</div>


</body>

</html>