<?php

session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get student's department */
$sql = "SELECT department FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

$user = $result->fetch_assoc();

$department = $user['department'];


/* Get AI extracted student skills */
$skill_sql = "SELECT skill FROM student_skills
              WHERE user_id='$user_id'";

$skill_result = $conn->query($skill_sql);

$student_skills = [];

while ($row = $skill_result->fetch_assoc()) {
    $student_skills[] = strtolower(trim($row['skill']));
}


/*
==================================================
JOB DATABASE
Department + Required Skills
==================================================
*/

$jobs = [

    /* ================= CSE / IT ================= */

    [
        "title" => "Software Developer",
        "department" => ["CSE", "IT"],
        "skills" => ["java", "python", "c++", "sql", "git"]
    ],

    [
        "title" => "Web Developer",
        "department" => ["CSE", "IT"],
        "skills" => ["html", "css", "javascript", "php", "mysql"]
    ],

    [
        "title" => "Data Analyst",
        "department" => ["CSE", "IT", "AI & DS", "AI & ML"],
        "skills" => ["python", "sql", "pandas", "excel", "power bi"]
    ],


    /* ================= ECE ================= */

    [
        "title" => "Embedded Engineer",
        "department" => ["ECE", "EEE"],
        "skills" => ["c", "c++", "embedded c", "microcontroller", "arduino"]
    ],

    [
        "title" => "VLSI Engineer",
        "department" => ["ECE"],
        "skills" => ["verilog", "vhdl", "digital electronics", "vlsi", "asic"]
    ],

    [
        "title" => "IoT Developer",
        "department" => ["ECE", "EEE"],
        "skills" => ["iot", "arduino", "raspberry pi", "python", "mqtt"]
    ],


    /* ================= EEE ================= */

    [
        "title" => "Electrical Engineer",
        "department" => ["EEE"],
        "skills" => ["electrical machines", "power systems", "matlab", "autocad"]
    ],

    [
        "title" => "Control Systems Engineer",
        "department" => ["EEE", "ECE"],
        "skills" => ["control systems", "matlab", "simulink", "plc"]
    ],


    /* ================= MECHANICAL ================= */

    [
        "title" => "Design Engineer",
        "department" => ["Mechanical"],
        "skills" => ["autocad", "solidworks", "catia", "cad"]
    ],

    [
        "title" => "Production Engineer",
        "department" => ["Mechanical"],
        "skills" => ["manufacturing", "production", "cad", "quality"]
    ],


    /* ================= CIVIL ================= */

    [
        "title" => "Civil Engineer",
        "department" => ["Civil"],
        "skills" => ["autocad", "civil 3d", "surveying", "structural analysis"]
    ],

    [
        "title" => "Structural Engineer",
        "department" => ["Civil"],
        "skills" => ["structural analysis", "staad pro", "autocad", "revit"]
    ],


    /* ================= CHEMICAL ================= */

    [
        "title" => "Process Engineer",
        "department" => ["Chemical"],
        "skills" => ["process design", "chemical process", "matlab", "process simulation"]
    ],

    [
        "title" => "Quality Engineer",
        "department" => ["Chemical"],
        "skills" => ["quality control", "quality assurance", "process", "safety"]
    ],


    /* ================= AI & DS / AI & ML ================= */

    [
        "title" => "Data Scientist",
        "department" => ["AI & DS", "AI & ML"],
        "skills" => ["python", "machine learning", "pandas", "numpy", "sql"]
    ],

    [
        "title" => "Machine Learning Engineer",
        "department" => ["AI & DS", "AI & ML", "CSE"],
        "skills" => ["python", "machine learning", "tensorflow", "pytorch"]
    ],

    [
        "title" => "AI Engineer",
        "department" => ["AI & DS", "AI & ML", "CSE"],
        "skills" => ["python", "machine learning", "deep learning", "nlp"]
    ]

];


/*
==================================================
CALCULATE JOB MATCH
==================================================
*/

$recommended_jobs = [];

foreach ($jobs as $job) {

    /* Department match */
    if (!in_array($department, $job['department'])) {
        continue;
    }

    $required_skills = $job['skills'];

    $matched_skills = [];
    $missing_skills = [];

    foreach ($required_skills as $required_skill) {

        if (in_array(strtolower($required_skill), $student_skills)) {

            $matched_skills[] = $required_skill;

        } else {

            $missing_skills[] = $required_skill;

        }
    }

    $total_skills = count($required_skills);

    $matched_count = count($matched_skills);

    if ($total_skills > 0) {

        $match_score = round(
            ($matched_count / $total_skills) * 100
        );

    } else {

        $match_score = 0;
    }


    /*
    Only show jobs having at least
    one matching skill
    */

    if ($matched_count > 0) {

        $job['match_score'] = $match_score;

        $job['matched_skills'] = $matched_skills;

        $job['missing_skills'] = $missing_skills;

        $recommended_jobs[] = $job;
    }
}


/*
Sort highest match first
*/

usort(
    $recommended_jobs,
    function ($a, $b) {

        return $b['match_score'] - $a['match_score'];
    }
);

?>

<!DOCTYPE html>

<html>

<head>

<title>Career AI - Job Opportunities</title>

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
    margin-left: 25px;
}

.container {
    width: 90%;
    max-width: 1100px;
    margin: 40px auto;
}

h1 {
    color: #182848;
    margin-bottom: 10px;
}

.subtitle {
    color: #666;
    margin-bottom: 30px;
}

.job-card {
    background: white;
    padding: 25px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px #dfe3ed;
}

.job-card h2 {
    color: #182848;
    margin-bottom: 10px;
}

.score {
    font-size: 22px;
    font-weight: bold;
    color: green;
    margin: 15px 0;
}

.skills {
    margin: 10px 0;
}

.matched {
    color: green;
    font-weight: bold;
}

.missing {
    color: #d35400;
    font-weight: bold;
}

.button {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 20px;
    background: #182848;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}

.button:hover {
    background: #304a80;
}

.no-job {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
}

</style>

</head>

<body>

<header>

<div class="logo">
Career AI
</div>

<nav>

<a href="dashboard.php">Dashboard</a>

<a href="resume.php">Resume</a>

<a href="logout.php">Logout</a>

</nav>

</header>


<div class="container">

<h1>
Recommended Job Opportunities
</h1>

<p class="subtitle">

Department:
<strong><?php echo htmlspecialchars($department); ?></strong>

</p>


<?php if (count($recommended_jobs) > 0) { ?>


<?php foreach ($recommended_jobs as $job) { ?>

<div class="job-card">

<h2>
<?php echo htmlspecialchars($job['title']); ?>
</h2>


<div class="score">

Match Score:
<?php echo $job['match_score']; ?>%

</div>


<div class="skills">

<span class="matched">
Matched Skills:
</span>

<?php

if (count($job['matched_skills']) > 0) {

echo implode(", ", $job['matched_skills']);

} else {

echo "None";

}

?>

</div>


<div class="skills">

<span class="missing">
Missing Skills:
</span>

<?php

if (count($job['missing_skills']) > 0) {

echo implode(", ", $job['missing_skills']);

} else {

echo "None";

}

?>

</div>


<a
href="match.php?job=<?php echo urlencode($job['title']); ?>"
class="button"
>

Check Job Match

</a>

</div>

<?php } ?>


<?php } else { ?>


<div class="no-job">

<h2>No suitable jobs found</h2>

<p>
Your department or current resume skills do not match
the available job requirements yet.
</p>

<p>
Try improving your missing skills through the learning roadmap.
</p>

</div>


<?php } ?>


</div>

</body>

</html>