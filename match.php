<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Selected job title */
$job_title = isset($_GET['job']) ? $_GET['job'] : '';

if ($job_title == '') {
    die("No job selected.");
}

/* Job database */
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

/* Check job exists */
if (!isset($jobs[$job_title])) {
    die("Invalid job selected.");
}

$job = $jobs[$job_title];

/* Get student's department */
$sql_department = "SELECT department FROM users WHERE id='$user_id'";
$result_department = $conn->query($sql_department);

if ($result_department->num_rows == 0) {
    die("Student details not found.");
}

$user = $result_department->fetch_assoc();
$department = $user['department'];

/* Get AI extracted skills */
$sql_skills = "SELECT skill FROM student_skills WHERE user_id='$user_id'";
$result_skills = $conn->query($sql_skills);

$student_skills = [];

while ($row = $result_skills->fetch_assoc()) {
    $student_skills[] = strtolower(trim($row['skill']));
}

/* Compare skills */
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

/* Calculate score */
$total_skills = count($job['skills']);
$matched_count = count($matched_skills);

$match_score = 0;

if ($total_skills > 0) {
    $match_score = round(($matched_count / $total_skills) * 100);
}

/* Department compatibility */
$department_match = in_array($department, $job['department']);
?>

<!DOCTYPE html>
<html>
<head>

<title>Job Match - Career AI</title>

<style>

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
}

.header {
    background: #182848;
    color: white;
    padding: 20px;
    text-align: center;
}

.container {
    width: 80%;
    max-width: 800px;
    margin: 40px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

h2 {
    color: #182848;
}

.score {
    font-size: 45px;
    font-weight: bold;
    color: #304a80;
    text-align: center;
    margin: 20px;
}

.info {
    background: #eef3ff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.skill-box {
    padding: 15px;
    border-radius: 8px;
    margin-top: 10px;
}

.matched {
    background: #e7f7ed;
}

.missing {
    background: #fff1f1;
}

.skill {
    display: inline-block;
    padding: 8px 12px;
    margin: 5px;
    border-radius: 20px;
    background: white;
    border: 1px solid #ccc;
}

.button {
    display: block;
    text-align: center;
    text-decoration: none;
    background: #304a80;
    color: white;
    padding: 14px;
    margin-top: 20px;
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
    <p>AI-Powered Job Matching</p>
</div>

<div class="container">

<div class="card">

<h2><?php echo htmlspecialchars($job_title); ?></h2>

<div class="info">

<strong>Your Department:</strong>
<?php echo htmlspecialchars($department); ?>

<br><br>

<strong>Required Department:</strong>
<?php echo implode(", ", $job['department']); ?>

</div>

<?php if ($department_match): ?>

<div class="info">
    ✅ Your department is suitable for this job.
</div>

<?php else: ?>

<div class="info">
    ⚠️ Your department is not directly listed for this job.
</div>

<?php endif; ?>


<h3>Job Match Score</h3>

<div class="score">
    <?php echo $match_score; ?>%
</div>


<div class="skill-box matched">

<h3>✅ Skills You Already Have</h3>

<?php

if (count($matched_skills) > 0) {

    foreach ($matched_skills as $skill) {

        echo "<span class='skill'>" .
             htmlspecialchars($skill) .
             "</span>";
    }

} else {

    echo "No matching skills found.";

}

?>

</div>


<div class="skill-box missing">

<h3>❌ Skills You Need to Learn</h3>

<?php

if (count($missing_skills) > 0) {

    foreach ($missing_skills as $skill) {

        echo "<span class='skill'>" .
             htmlspecialchars($skill) .
             "</span>";
    }

} else {

    echo "🎉 You have all the required skills!";

}

?>

</div>


<a
href="roadmap.php?job=<?php echo urlencode($job_title); ?>"
class="button">

📚 View Personalized Learning Roadmap

</a>


<a href="jobs.php" class="button">

🔍 Back to Job Opportunities

</a>

</div>

</div>

</body>
</html>