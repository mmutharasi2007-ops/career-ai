<?php

set_time_limit(150);

require_once "auth_session.php";

include "db.php";


/* =========================
   CHECK LOGIN
========================= */

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit();
}


$user_id = $_SESSION['user_id'];


/* =========================
   GET SELECTED JOB
========================= */

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
        "skills" => [
            "java",
            "python",
            "c++",
            "sql",
            "git"
        ]
    ],

    "Web Developer" => [
        "department" => ["CSE", "IT"],
        "skills" => [
            "html",
            "css",
            "javascript",
            "php",
            "mysql"
        ]
    ],

    "Data Analyst" => [
        "department" => [
            "CSE",
            "IT",
            "AI & DS",
            "AI & ML"
        ],
        "skills" => [
            "python",
            "sql",
            "pandas",
            "excel",
            "power bi"
        ]
    ],

    "Embedded Engineer" => [
        "department" => ["ECE", "EEE"],
        "skills" => [
            "c",
            "c++",
            "embedded c",
            "microcontroller",
            "arduino"
        ]
    ],

    "VLSI Engineer" => [
        "department" => ["ECE"],
        "skills" => [
            "verilog",
            "vhdl",
            "digital electronics",
            "vlsi",
            "asic"
        ]
    ],

    "IoT Developer" => [
        "department" => ["ECE", "EEE"],
        "skills" => [
            "iot",
            "arduino",
            "raspberry pi",
            "python",
            "mqtt"
        ]
    ],

    "Electrical Engineer" => [
        "department" => ["EEE"],
        "skills" => [
            "electrical machines",
            "power systems",
            "matlab",
            "autocad"
        ]
    ],

    "Control Systems Engineer" => [
        "department" => ["EEE", "ECE"],
        "skills" => [
            "control systems",
            "matlab",
            "simulink",
            "plc"
        ]
    ],

    "Design Engineer" => [
        "department" => ["Mechanical"],
        "skills" => [
            "autocad",
            "solidworks",
            "catia",
            "cad"
        ]
    ],

    "Production Engineer" => [
        "department" => ["Mechanical"],
        "skills" => [
            "manufacturing",
            "production",
            "cad",
            "quality"
        ]
    ],

    "Civil Engineer" => [
        "department" => ["Civil"],
        "skills" => [
            "autocad",
            "civil 3d",
            "surveying",
            "structural analysis"
        ]
    ],

    "Structural Engineer" => [
        "department" => ["Civil"],
        "skills" => [
            "structural analysis",
            "staad pro",
            "autocad",
            "revit"
        ]
    ],

    "Process Engineer" => [
        "department" => ["Chemical"],
        "skills" => [
            "process design",
            "chemical process",
            "matlab",
            "process simulation"
        ]
    ],

    "Quality Engineer" => [
        "department" => ["Chemical"],
        "skills" => [
            "quality control",
            "quality assurance",
            "process",
            "safety"
        ]
    ],

    "Data Scientist" => [
        "department" => ["AI & DS", "AI & ML"],
        "skills" => [
            "python",
            "machine learning",
            "pandas",
            "numpy",
            "sql"
        ]
    ],

    "Machine Learning Engineer" => [
        "department" => [
            "AI & DS",
            "AI & ML",
            "CSE"
        ],
        "skills" => [
            "python",
            "machine learning",
            "tensorflow",
            "pytorch"
        ]
    ],

    "AI Engineer" => [
        "department" => [
            "AI & DS",
            "AI & ML",
            "CSE"
        ],
        "skills" => [
            "python",
            "machine learning",
            "deep learning",
            "nlp"
        ]
    ]

];


/* =========================
   CHECK JOB
========================= */

if (!isset($jobs[$job_title])) {

    die("Invalid job selected.");
}


$job = $jobs[$job_title];


/* =========================
   GET STUDENT SKILLS
========================= */

$sql = "
    SELECT skill
    FROM student_skills
    WHERE user_id='$user_id'
";


$result = $conn->query($sql);


$student_skills = [];


while ($row = $result->fetch_assoc()) {

    $student_skills[] =
        trim($row['skill']);
}


/* =========================
   FIND MISSING SKILLS
========================= */

$missing_skills = [];


foreach ($job['skills'] as $required_skill) {

    $found = false;


    foreach ($student_skills as $student_skill) {

        if (

            strtolower($required_skill)
            ==
            strtolower($student_skill)

            ||

            strpos(
                strtolower($student_skill),
                strtolower($required_skill)
            ) !== false

            ||

            strpos(
                strtolower($required_skill),
                strtolower($student_skill)
            ) !== false

        ) {

            $found = true;

            break;
        }
    }


    if (!$found) {

        $missing_skills[] =
            $required_skill;
    }
}


/* =========================
   READ GROQ API KEY
========================= */

$key_file = "C:/Muthu/groq_key.txt";


if (!file_exists($key_file)) {

    die("Groq API key file not found.");
}


$api_key = trim(
    file_get_contents($key_file)
);


if ($api_key == '') {

    die("Groq API key is empty.");
}


/* =========================
   PREPARE TEXT
========================= */

$skills_text =
    !empty($student_skills)
    ? implode(", ", $student_skills)
    : "No skills found";


$missing_text =
    !empty($missing_skills)
    ? implode(", ", $missing_skills)
    : "No major missing skills";


/* =========================
   GROQ PROMPT
========================= */

$prompt = "

You are an AI career interview assistant.

Student's target job:
$job_title

Student's resume skills:
$skills_text

Missing skills for this job:
$missing_text


Generate exactly 10 personalized interview questions.


Follow this structure:

1. Basic technical question
2. Technical question based on the student's skills
3. Technical question based on the selected job
4. Practical technical question
5. Real-world scenario question
6. Resume-based question
7. Project-based question
8. Question about a missing skill
9. Problem-solving question
10. HR/career question


IMPORTANT RULES:

- Generate exactly 10 questions.
- Do not provide answers.
- Do not provide explanations.
- Do not invent skills that are not listed.
- Questions should be suitable for an engineering student.
- Questions should be related to the selected job.
- At least some questions must use the student's actual skills.
- Include one question related to a missing skill.
- Return only the numbered questions.

";


/* =========================
   GROQ API URL
========================= */

$url =
    "https://api.groq.com/openai/v1/chat/completions";


/* =========================
   GROQ REQUEST DATA
========================= */

$data = [

   "model" => "openai/gpt-oss-120b",

    "messages" => [

        [

            "role" => "system",

            "content" =>
                "You are an expert AI interview assistant for engineering students."

        ],

        [

            "role" => "user",

            "content" => $prompt

        ]

    ],

    "temperature" => 0.3,

    "max_completion_tokens" => 1500

];


/* =========================
   CONVERT TO JSON
========================= */

$json_data =
    json_encode($data);


/* =========================
   CURL
========================= */

$ch = curl_init($url);


curl_setopt(
    $ch,
    CURLOPT_POST,
    true
);


curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    $json_data
);


curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [

        "Content-Type: application/json",

        "Authorization: Bearer " . $api_key

    ]
);


curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);


curl_setopt(
    $ch,
    CURLOPT_CONNECTTIMEOUT,
    30
);


curl_setopt(
    $ch,
    CURLOPT_TIMEOUT,
    120
);


/* =========================
   SEND REQUEST
========================= */

$response =
    curl_exec($ch);


$http_code =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );


$curl_error =
    curl_error($ch);


/* =========================
   PROCESS RESPONSE
========================= */

$questions = "";


if ($response === false) {

    $questions =
        "Groq connection failed."
        .
        "\n\ncURL Error: "
        .
        $curl_error;

}


elseif ($http_code == 200) {


    $result_data =
        json_decode(
            $response,
            true
        );


    if (

        isset(
            $result_data['choices'][0]['message']['content']
        )

    ) {


        $questions =
            $result_data['choices'][0]['message']['content'];


    }

    else {


        $questions =
            "AI did not return interview questions.";

    }

}


else {


    $error_message = "";


    $result_data =
        json_decode(
            $response,
            true
        );


    if (

        isset(
            $result_data['error']['message']
        )

    ) {


        $error_message =
            $result_data['error']['message'];

    }


    $questions =
        "Groq API error. HTTP Status: "
        .
        $http_code;


    if ($error_message != "") {


        $questions .=
            "\n\nError: "
            .
            $error_message;

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>AI Interview - Career AI</title>


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


.header h1 {

    margin: 0;

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


.job-title {

    color: #304a80;

    font-size: 25px;

    font-weight: bold;

}


.questions {

    background: #eef3ff;

    padding: 25px;

    border-radius: 10px;

    line-height: 1.8;

    white-space: pre-line;

}


.skill {

    display: inline-block;

    background: #f0f0f0;

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

    <p>
        AI-Powered Interview Preparation
    </p>

</div>


<div class="container">


<!-- =========================
     SELECTED JOB
========================= -->

<div class="card">

    <h2>🎯 Interview For</h2>


    <div class="job-title">

        <?php

        echo htmlspecialchars(
            $job_title
        );

        ?>

    </div>


    <p>

        The questions are personalized
        using your resume skills and
        the requirements of this job.

    </p>

</div>


<!-- =========================
     STUDENT SKILLS
========================= -->

<div class="card">

    <h2>🧠 Your Resume Skills</h2>


    <?php

    if (count($student_skills) > 0) {


        foreach (
            $student_skills
            as $skill
        ) {


            echo
                "<span class='skill'>"
                .
                htmlspecialchars($skill)
                .
                "</span>";

        }


    }

    else {


        echo "No skills found.";

    }

    ?>

</div>


<!-- =========================
     MISSING SKILLS
========================= -->

<div class="card">

    <h2>⚠️ Skills To Improve</h2>


    <?php

    if (
        count($missing_skills) > 0
    ) {


        foreach (
            $missing_skills
            as $skill
        ) {


            echo
                "<span class='skill'>"
                .
                htmlspecialchars($skill)
                .
                "</span>";

        }


    }

    else {


        echo
            "🎉 No major missing skills found.";

    }

    ?>

</div>


<!-- =========================
     AI QUESTIONS
========================= -->

<div class="card">

    <h2>
        🤖 AI Interview Questions
    </h2>


    <div class="questions">

        <?php

        echo nl2br(
            htmlspecialchars($questions)
        );

        ?>

    </div>

</div>


<!-- =========================
     WHAT IF SIMULATOR
========================= -->

<a
    href="simulator.php?job=<?php
        echo urlencode($job_title);
    ?>"
    class="button"
>

    🎯 Try What-If Skill Simulator

</a>


<!-- =========================
     LEARNING ROADMAP
========================= -->

<a
    href="roadmap.php?job=<?php
        echo urlencode($job_title);
    ?>"
    class="button"
>

    📚 View Learning Roadmap

</a>


<!-- =========================
     MORE JOBS
========================= -->

<a
    href="jobs.php"
    class="button"
>

    🔍 Find More Job Opportunities

</a>


</div>

</body>

</html>