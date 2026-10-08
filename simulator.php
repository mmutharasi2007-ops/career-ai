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
   FIND CURRENT MATCH
========================= */

$matched_skills = [];
$missing_skills = [];

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


$total_skills = count($job['skills']);
$current_count = count($matched_skills);

$current_score = 0;

if ($total_skills > 0) {
    $current_score = round(($current_count / $total_skills) * 100);
}


/* =========================
   WHAT-IF SIMULATION
========================= */

$selected_skill = "";
$new_score = $current_score;
$improvement = 0;
$new_missing = $missing_skills;

if (isset($_POST['simulate'])) {

    $selected_skill = $_POST['skill'];

    if (in_array($selected_skill, $missing_skills)) {

        $new_count = $current_count + 1;

        $new_score = round(($new_count / $total_skills) * 100);

        $improvement = $new_score - $current_score;

        $new_missing = array_diff(
            $missing_skills,
            [$selected_skill]
        );
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>What-If Simulator - Career AI</title>

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
    max-width: 850px;
    margin: 40px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.job-title {
    color: #304a80;
    font-size: 25px;
    font-weight: bold;
}

.score {
    text-align: center;
    font-size: 45px;
    font-weight: bold;
    color: #304a80;
    margin: 20px;
}

select {
    width: 100%;
    padding: 13px;
    border: 1px solid #ccc;
    border-radius: 7px;
    font-size: 16px;
}

button {
    width: 100%;
    padding: 14px;
    margin-top: 15px;
    background: #304a80;
    color: white;
    border: none;
    border-radius: 7px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #182848;
}

.result {
    background: #e7f7ed;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.improvement {
    font-size: 25px;
    font-weight: bold;
    color: #16803c;
}

.skill {
    display: inline-block;
    background: #eef3ff;
    padding: 8px 12px;
    margin: 5px;
    border-radius: 20px;
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

<p>What-If Skill Simulator</p>

</div>


<div class="container">


<div class="card">

<h2>🎯 Career Goal</h2>

<div class="job-title">

<?php echo htmlspecialchars($job_title); ?>

</div>

<p>
See how learning one missing skill can improve your job match score.
</p>

</div>


<div class="card">

<h2>📊 Your Current Job Match</h2>

<div class="score">

<?php echo $current_score; ?>%

</div>

<p style="text-align:center;">

You currently have

<strong><?php echo $current_count; ?></strong>

out of

<strong><?php echo $total_skills; ?></strong>

required skills.

</p>

</div>


<?php if (count($missing_skills) > 0): ?>

<div class="card">

<h2>🔮 What If You Learn a New Skill?</h2>

<form method="POST">

<label>
<strong>Select a missing skill:</strong>
</label>

<select name="skill" required>

<option value="">
-- Select Skill --
</option>

<?php foreach ($missing_skills as $skill): ?>

<option value="<?php echo htmlspecialchars($skill); ?>">

<?php echo htmlspecialchars($skill); ?>

</option>

<?php endforeach; ?>

</select>

<button type="submit" name="simulate">

🚀 Simulate Improvement

</button>

</form>

</div>

<?php endif; ?>


<?php if (isset($_POST['simulate']) && $selected_skill != ""): ?>

<div class="card">

<h2>📈 Simulation Result</h2>

<div class="result">

<p>
If you learn:
</p>

<h2>
<?php echo htmlspecialchars($selected_skill); ?>
</h2>

<p>
Your Job Match Score can improve from
</p>

<h2>
<?php echo $current_score; ?>%
 →
 <?php echo $new_score; ?>%
</h2>

<div class="improvement">

+<?php echo $improvement; ?>%

</div>

<p>
potential improvement
</p>

</div>

</div>


<div class="card">

<h2>📚 Remaining Skills</h2>

<?php

if (count($new_missing) > 0) {

    foreach ($new_missing as $skill) {

        echo "<span class='skill'>" .
             htmlspecialchars($skill) .
             "</span>";
    }

} else {

    echo "<h3>🎉 You have all the required skills!</h3>";
}

?>

</div>

<?php endif; ?>


<a
href="roadmap.php?job=<?php echo urlencode($job_title); ?>"
class="button">

📚 View Learning Roadmap

</a>


<a
href="interview.php?job=<?php echo urlencode($job_title); ?>"
class="button">

🤖 Start AI Interview

</a>


<a
href="jobs.php"
class="button">

🔍 Find More Job Opportunities

</a>


</div>

</body>

</html>