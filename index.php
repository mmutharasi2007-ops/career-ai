<!DOCTYPE html>
<html>

<head>

    <title>Career AI</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7ff;
            color: #222;
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

        .hero {
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 70px 10%;
        }

        .hero-text {
            width: 55%;
        }

        .hero-text h1 {
            font-size: 48px;
            color: #182848;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 18px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .button {
            display: inline-block;
            background: #182848;
            color: white;
            padding: 14px 30px;
            border-radius: 6px;
            text-decoration: none;
        }

        .button:hover {
            background: #304a80;
        }

        .hero-box {
            width: 330px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px #d5d9e5;
            text-align: center;
        }

        .hero-box h2 {
            color: #182848;
            margin-bottom: 20px;
        }

        .hero-box p {
            color: #666;
            line-height: 1.8;
        }

        .features {
            background: white;
            padding: 60px 10%;
            text-align: center;
        }

        .features h2 {
            color: #182848;
            margin-bottom: 40px;
        }

        .cards {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .card {
            width: 220px;
            padding: 25px;
            background: #f5f7ff;
            border-radius: 10px;
        }

        .card h3 {
            color: #182848;
            margin-bottom: 12px;
        }

        .card p {
            color: #666;
            line-height: 1.5;
        }

        footer {
            background: #182848;
            color: white;
            text-align: center;
            padding: 20px;
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

        <a href="register.php">
            Register
        </a>

    </nav>

</header>



<section class="hero">

    <div class="hero-text">

        <h1>
            Build Your Career With AI
        </h1>

        <p>
            Discover suitable jobs, identify your skill gaps,
            improve your career readiness and prepare for
            interviews using Artificial Intelligence.
        </p>


        <!-- Get Started → Register Page -->

        <a href="register.php" class="button">
            Get Started
        </a>

    </div>


    <div class="hero-box">

        <h2>
            Career AI
        </h2>

        <p>

            Resume Analysis

            <br><br>

            Job Matching

            <br><br>

            Skill Gap Analysis

            <br><br>

            Career Roadmap

            <br><br>

            AI Interview Preparation

        </p>

    </div>

</section>



<section class="features">

    <h2>
        What Career AI Offers
    </h2>


    <div class="cards">


        <div class="card">

            <h3>
                Resume Analysis
            </h3>

            <p>
                Analyze your resume and identify your skills.
            </p>

        </div>


        <div class="card">

            <h3>
                Job Matching
            </h3>

            <p>
                Find jobs that match your skills.
            </p>

        </div>


        <div class="card">

            <h3>
                Skill Gap
            </h3>

            <p>
                Discover the skills you need to improve.
            </p>

        </div>


        <div class="card">

            <h3>
                Career Roadmap
            </h3>

            <p>
                Get a personalized learning path.
            </p>

        </div>


    </div>

</section>



<footer>

    <p>
        © 2026 Career AI | AI-Powered Career Readiness Platform
    </p>

</footer>


</body>

</html>