<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Career AI - Dashboard</title>

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

        .welcome {
            text-align: center;
            padding: 45px 20px 25px;
        }

        .welcome h1 {
            color: #182848;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
        }

        .features {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px #d5d9e5;
        }

        .card h3 {
            color: #182848;
            margin-bottom: 12px;
        }

        .card p {
            color: #666;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background: #182848;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }

        .button:hover {
            background: #304a80;
        }

        .logout {
            background: #c0392b;
        }

    </style>

</head>

<body>

<header>

    <div class="logo">
        Career AI
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="logout.php">Logout</a>
    </nav>

</header>


<section class="welcome">

    <h1>
        Welcome, <?php echo $_SESSION['name']; ?>!
    </h1>

    <p>
        Your AI-powered career dashboard
    </p>

</section>


<section class="features">


    <div class="card">

        <h3>Resume Analysis</h3>

        <p>
            Upload your resume and analyze your
            skills and qualifications.
        </p>

        <a href="resume.php" class="button">
            Upload Resume
        </a>

    </div>


    <div class="card">

        <h3>Job Matching</h3>

        <p>
            Find jobs that match your skills
            and qualifications.
        </p>

        <a href="jobs.php" class="button">
            Find Jobs
        </a>

    </div>


    <div class="card">

        <h3>Skill Gap Analysis</h3>

        <p>
            Identify the skills you need to
            improve for your target job.
        </p>

        <a href="match.php" class="button">
            Check Skills
        </a>

    </div>


    <div class="card">

        <h3>Career Roadmap</h3>

        <p>
            Get a personalized learning path
            to become job-ready.
        </p>

        <a href="roadmap.php" class="button">
            View Roadmap
        </a>

    </div>


    <div class="card">

        <h3>What-If Simulator</h3>

        <p>
            See how adding new skills can
            improve your job match.
        </p>

        <a href="simulator.php" class="button">
            Try Simulator
        </a>

    </div>


    <div class="card">

        <h3>AI Interview</h3>

        <p>
            Practice interview questions
            generated using AI.
        </p>

        <a href="interview.php" class="button">
            Start Interview
        </a>

    </div>


</section>

</body>

</html>