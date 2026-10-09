<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$job_title = isset($_GET['job']) ? $_GET['job'] : '';

if ($job_title == '') {
    die("No job selected.");
}


/* =========================
   JOB DATABASE
========================= */

$jobs = [

    "Software Developer" => [
        "department" => ["CSE", "IT"],
        "skills" => ["java", "python", "c++", "sql", "git"]
    ],

    "Web Developer" => [
        "department" => ["CSE", "IT"],
        "skills" => ["html", "css", "javascript", "php", "mysql"]
    ],

    "Data Analyst" => [
        "department" => ["CSE", "IT", "AI & DS", "AI & ML"],
        "skills" => ["python", "sql", "pandas", "excel", "power bi"]
    ],

    "Embedded Engineer" => [
        "department" => ["ECE", "EEE"],
        "skills" => ["c", "c++", "embedded c", "microcontroller", "arduino"]
    ],

    "VLSI Engineer" => [
        "department" => ["ECE"],
        "skills" => ["verilog", "vhdl", "digital electronics", "vlsi", "asic"]
    ],

    "IoT Developer" => [
        "department" => ["ECE", "EEE"],
        "skills" => ["iot", "arduino", "raspberry pi", "python", "mqtt"]
    ],

    "Electrical Engineer" => [
        "department" => ["EEE"],
        "skills" => ["electrical machines", "power systems", "matlab", "autocad"]
    ],

    "Control Systems Engineer" => [
        "department" => ["EEE", "ECE"],
        "skills" => ["control systems", "matlab", "simulink", "plc"]
    ],

    "Design Engineer" => [
        "department" => ["Mechanical"],
        "skills" => ["autocad", "solidworks", "catia", "cad"]
    ],

    "Production Engineer" => [
        "department" => ["Mechanical"],
        "skills" => ["manufacturing", "production", "cad", "quality"]
    ],

    "Civil Engineer" => [
        "department" => ["Civil"],
        "skills" => ["autocad", "civil 3d", "surveying", "structural analysis"]
    ],

    "Structural Engineer" => [
        "department" => ["Civil"],
        "skills" => ["structural analysis", "staad pro", "autocad", "revit"]
    ],

    "Process Engineer" => [
        "department" => ["Chemical"],
        "skills" => ["process design", "chemical process", "matlab", "process simulation"]
    ],

    "Quality Engineer" => [
        "department" => ["Chemical"],
        "skills" => ["quality control", "quality assurance", "process", "safety"]
    ],

    "Data Scientist" => [
        "department" => ["AI & DS", "AI & ML"],
        "skills" => ["python", "machine learning", "pandas", "numpy", "sql"]
    ],

    "Machine Learning Engineer" => [
        "department" => ["AI & DS", "AI & ML", "CSE"],
        "skills" => ["python", "machine learning", "tensorflow", "pytorch"]
    ],

    "AI Engineer" => [
        "department" => ["AI & DS", "AI & ML", "CSE"],
        "skills" => ["python", "machine learning", "deep learning", "nlp"]
    ]
];


if (!isset($jobs[$job_title])) {
    die("Invalid job selected.");
}

$job = $jobs[$job_title];


/* =========================
   GET STUDENT SKILLS
========================= */

$sql = "SELECT skill FROM student_skills WHERE user_id='$user_id'";
$result = $conn->query($sql);

$student_skills = [];

while ($row = $result->fetch_assoc()) {
    $student_skills[] = strtolower(trim($row['skill']));
}


/* =========================
   FIND MISSING SKILLS
========================= */

$missing_skills = [];
$matched_skills = [];

foreach ($job['skills'] as $required_skill) {

    $found = false;

    foreach ($student_skills as $student_skill) {

        if (
            strtolower($required_skill) == $student_skill ||
            strpos($student_skill, strtolower($required_skill)) !== false ||
            strpos(strtolower($required_skill), $student_skill) !== false
        ) {
            $found = true;
            break;
        }
    }

    if ($found) {
        $matched_skills[] = $required_skill;
    } else {
        $missing_skills[] = $required_skill;
    }
}


/* =========================
   SAVE ROADMAP
========================= */

foreach ($missing_skills as $skill) {

    $safe_skill = $conn->real_escape_string($skill);
    $safe_job = $conn->real_escape_string($job_title);

    $sql_save = "
        INSERT INTO learning_roadmap
        (user_id, job_role, skill, status)
        VALUES
        ('$user_id', '$safe_job', '$safe_skill', 'Pending')
    ";

    $conn->query($sql_save);
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Learning Roadmap - Career AI</title>

<style>

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
}

.header {
    background: #182848;
    color: white;
    text-align: center;
    padding: 25px;
}

.container {
    width: 85%;
    max-width: 900px;
    margin: 40px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

h2 {
    color: #182848;
}

.job-title {
    color: #304a80;
    font-size: 25px;
    font-weight: bold;
}

.skill-card {
    background: #eef3ff;
    padding: 20px;
    margin: 15px 0;
    border-radius: 10px;
    border-left: 5px solid #304a80;
}

.skill-name {
    font-size: 20px;
    font-weight: bold;
    color: #182848;
}

.step {
    margin-top: 10px;
    line-height: 1.6;
}

.success {
    background: #e7f7ed;
    padding: 18px;
    border-radius: 8px;
}

.button {
    display: block;
    text-align: center;
    text-decoration: none;
    background: #304a80;
    color: white;
    padding: 14px;
    margin-top: 15px;
    border-radius: 7px;
    font-weight: bold;
}

.button:hover {
    background: #182848;
}

</style>

</head>

<body>


<div class="header">

<h1>Career AI</h1>

<p>Personalized Learning Roadmap</p>

</div>


<div class="container">


<div class="card">

<h2>Your Career Goal</h2>

<div class="job-title">
<?php echo htmlspecialchars($job_title); ?>
</div>

<p>
This roadmap is generated using your resume skills and the requirements of the selected job.
</p>

</div>


<?php if (count($missing_skills) > 0): ?>

<div class="card">

<h2>📚 Skills You Need to Learn</h2>

<?php foreach ($missing_skills as $skill): ?>

<div class="skill-card">

<div class="skill-name">
<?php echo htmlspecialchars($skill); ?>
</div>

<div class="step">

<strong>Step 1:</strong>
Learn the basic concepts of
<?php echo htmlspecialchars($skill); ?>.

</div>

<div class="step">

<strong>Step 2:</strong>
Practice small programs or practical exercises related to
<?php echo htmlspecialchars($skill); ?>.

</div>

<div class="step">

<strong>Step 3:</strong>
Build a mini project using
<?php echo htmlspecialchars($skill); ?>.

</div>

<div class="step">

<strong>Step 4:</strong>
Add the project and skill to your resume after gaining practical knowledge.

</div>

</div>

<?php endforeach; ?>

</div>

<?php else: ?>

<div class="card">

<div class="success">

<h2>🎉 You Are Almost Job Ready!</h2>

<p>
Your resume already contains all the required skills for this job.
</p>

</div>

</div>

<?php endif; ?>


<div class="card">

<h2>🚀 Final Career Roadmap</h2>

<p>Follow these steps:</p>

<p>1️⃣ Learn the missing skills</p>

<p>2️⃣ Practice each skill</p>

<p>3️⃣ Build practical projects</p>

<p>4️⃣ Update your resume</p>

<p>5️⃣ Practice interview questions</p>

<p>6️⃣ Apply for suitable jobs</p>

</div>


<a
href="simulator.php?job=<?php echo urlencode($job_title); ?>"
class="button">

🎯 Try What-If Skill Simulator

</a>


<a
href="interview.php?job=<?php echo urlencode($job_title); ?>"
class="button">

🤖 Start AI Interview Preparation

</a>


<a
href="jobs.php"
class="button">

🔍 Find More Job Opportunities

</a>


</div>

</body>

</html>