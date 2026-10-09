<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "vendor/autoload.php";
include "db.php";

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

$message = "";
$resume_text = "";
$ai_result = "";

{

    $user_id = $_SESSION['user_id'];

    $upload_folder = "uploads/";

    $pdf_file  = $upload_folder . "resume_" . $user_id . ".pdf";
    $docx_file = $upload_folder . "resume_" . $user_id . ".docx";
    $doc_file  = $upload_folder . "resume_" . $user_id . ".doc";


    /* =========================
       PDF RESUME
       ========================= */

    if (file_exists($pdf_file)) {

        try {

            $parser = new Parser();

            $pdf = $parser->parseFile($pdf_file);

            $resume_text = $pdf->getText();

        } catch (Exception $e) {

            $message = "PDF reading failed: " . $e->getMessage();
        }
    }


    /* =========================
       DOCX RESUME
       ========================= */

    elseif (file_exists($docx_file)) {

        try {

            $phpWord = IOFactory::load($docx_file);

            foreach ($phpWord->getSections() as $section) {

                foreach ($section->getElements() as $element) {

                    if (method_exists($element, 'getText')) {

                        $resume_text .= $element->getText() . "\n";
                    }
                }
            }

        } catch (Exception $e) {

            $message = "DOCX reading failed: " . $e->getMessage();
        }
    }


    /* =========================
       OLD DOC
       ========================= */

    elseif (file_exists($doc_file)) {

        $message =
            "Old DOC format is not supported yet. Please upload PDF or DOCX.";
    }


    else {

        $message =
            "No resume found. Please upload your resume first.";
    }


    /* =========================
       SEND RESUME TO GROQ
       ========================= */

    if (!empty($resume_text)) {

        $resume_text = trim($resume_text);

        $message = "Resume text extracted successfully!";


        try {

            /* =========================
               READ GROQ API KEY
               ========================= */

            $keyFile = "C:/Muthu/groq_key.txt";


            if (!file_exists($keyFile)) {

                throw new Exception(
                    "Groq API key file not found."
                );
            }


            $apiKey = trim(
                file_get_contents($keyFile)
            );


            if (empty($apiKey)) {

                throw new Exception(
                    "Groq API key is empty."
                );
            }


            /* =========================
               AI PROMPT
               ========================= */

            $prompt = "

You are an AI career advisor for engineering students.

Analyze the student's resume carefully.

IMPORTANT RULES:

1. Use ONLY information present in the resume.
2. Do NOT invent skills.
3. Do NOT invent education.
4. Do NOT invent projects.
5. Do NOT invent work experience.
6. Identify the actual technical skills.
7. Identify the actual soft skills if mentioned.
8. Recommend career roles based only on the resume.
9. Identify skills missing for those career roles.
10. Give a realistic career readiness score.

Return the answer in this exact structure:

1. EXTRACTED SKILLS

Technical Skills:
- List each technical skill on a separate line.
- Use bullet points starting with -

Soft Skills:
- List each soft skill on a separate line.
- Use bullet points starting with -

2. EDUCATION AND EXPERIENCE

Education:
- Summarize the education found in the resume.

Projects:
- List important projects found in the resume.

Experience:
- List work/internship experience if present.

3. SUITABLE JOB ROLES

Recommend 3 suitable job roles.

For each role give:
- Job Role
- Match Percentage
- Reason

4. CAREER READINESS SCORE

Give a score out of 100.

Explain why the score was given.

5. MISSING SKILLS

For the best recommended career role, list the important missing skills.

6. PERSONALIZED LEARNING ROADMAP

Create a step-by-step roadmap.

Include:

Step 1:
Step 2:
Step 3:
Step 4:
Step 5:

7. CAREER RECOMMENDATION

Give the BEST career direction for this student.

Explain clearly why this career matches the student's actual resume.

8. INTERVIEW PREPARATION

Generate 5 interview questions based specifically on the student's resume.

Here is the student's resume:

-------------------------
START RESUME
-------------------------

" . $resume_text . "

-------------------------
END RESUME
-------------------------

";


            /* =========================
               GROQ API
               ========================= */

            $url =
                "https://api.groq.com/openai/v1/chat/completions";


            $data = [
                   "model" => "openai/gpt-oss-120b",
                
                "messages" => [

                    [
                        "role" => "system",

                        "content" =>
                            "You are an expert AI career advisor for engineering students."
                    ],

                    [
                        "role" => "user",

                        "content" => $prompt
                    ]

                ],

                "temperature" => 0.3,

                "max_completion_tokens" => 3000
            ];


            /* =========================
               CURL
               ========================= */

            $ch = curl_init($url);


            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );

            curl_setopt(
                $ch,
                CURLOPT_POST,
                true
            );

            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $apiKey
                ]
            );

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($data)
            );

            curl_setopt(
                $ch,
                CURLOPT_TIMEOUT,
                120
            );

            curl_setopt(
                $ch,
                CURLOPT_CONNECTTIMEOUT,
                30
            );


            $response = curl_exec($ch);


            /* =========================
               CURL ERROR
               ========================= */

            if ($response === false) {

                throw new Exception(
                    "CURL Error: " . curl_error($ch)
                );
            }


            /* =========================
               HTTP STATUS
               ========================= */

            $httpCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );


            /* =========================
               JSON RESPONSE
               ========================= */

            $result = json_decode(
                $response,
                true
            );


            /* =========================
               GROQ SUCCESS RESPONSE
               ========================= */

            if (
                isset(
                    $result["choices"][0]["message"]["content"]
                )
            ) {

                $ai_result =
                    $result["choices"][0]["message"]["content"];


                $message =
                    "Resume analyzed successfully by Career AI!";


                /* =========================
                   EXTRACT TECHNICAL SKILLS
                   ========================= */

                $skill_section = "";


                if (
                    preg_match(
                        '/Technical Skills:(.*?)(Soft Skills:|2\.\s*EDUCATION)/is',
                        $ai_result,
                        $matches
                    )
                ) {

                    $skill_section = $matches[1];
                }


                /* =========================
                   GET SKILLS
                   ========================= */

                preg_match_all(
                    '/[-•]\s*([A-Za-z][A-Za-z0-9+#.\- ]{1,60})/',
                    $skill_section,
                    $skill_matches
                );


                $skills =
                    $skill_matches[1] ?? [];


                /* =========================
                   SAVE SKILLS TO DATABASE
                   ========================= */

                if (!empty($skills)) {

                    $stmt = $conn->prepare(
                        "INSERT IGNORE INTO student_skills
                        (user_id, skill, skill_type)
                        VALUES (?, ?, ?)"
                    );


                    if ($stmt) {

                        $skill_type = "technical";


                        foreach ($skills as $skill) {

                            $skill = trim($skill);


                            if ($skill == "") {
                                continue;
                            }


                            $stmt->bind_param(
                                "iss",
                                $user_id,
                                $skill,
                                $skill_type
                            );


                            $stmt->execute();
                        }


                        $stmt->close();
                    }
                }

            }


            /* =========================
               GROQ ERROR
               ========================= */

            else {

                if (
                    isset(
                        $result["error"]["message"]
                    )
                ) {

                    throw new Exception(
                        $result["error"]["message"]
                    );
                }


                throw new Exception(
                    "Groq returned an unexpected response. HTTP Status: "
                    . $httpCode
                );
            }

        }


        catch (Exception $e) {

            $ai_result =
                "Groq AI analysis failed: "
                . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>AI Resume Analysis - Career AI</title>


<style>

body {

    margin: 0;

    font-family: Arial, sans-serif;

    background: #f4f7fb;

    color: #222;
}


.navbar {

    background: #182848;

    color: white;

    padding: 18px 40px;

    display: flex;

    justify-content: space-between;

    align-items: center;
}


.navbar h2 {

    margin: 0;
}


.navbar a {

    color: white;

    text-decoration: none;

    margin-left: 20px;
}


.container {

    width: 90%;

    max-width: 1000px;

    margin: 40px auto;
}


.card {

    background: white;

    padding: 30px;

    border-radius: 12px;

    box-shadow: 0 4px 15px rgba(0,0,0,0.08);

    margin-bottom: 25px;
}


h1 {

    color: #182848;
}


h2 {

    color: #182848;

    margin-top: 25px;
}


.message {

    padding: 15px;

    background: #e8f5e9;

    color: #2e7d32;

    border-radius: 8px;

    margin-bottom: 20px;
}


.error {

    padding: 15px;

    background: #ffebee;

    color: #c62828;

    border-radius: 8px;

    margin-bottom: 20px;
}


button {

    background: #182848;

    color: white;

    border: none;

    padding: 13px 25px;

    border-radius: 7px;

    cursor: pointer;

    font-size: 16px;
}


button:hover {

    background: #263d6b;
}


.ai-result {

    background: #f8f9fc;

    padding: 25px;

    border-radius: 10px;

    white-space: pre-wrap;

    line-height: 1.7;

    border-left: 5px solid #182848;
}


.job-button {

    display: inline-block;

    background: #182848;

    color: white;

    padding: 14px 22px;

    border-radius: 8px;

    text-decoration: none;

    margin-top: 20px;
}


.job-button:hover {

    background: #263d6b;
}


.footer {

    text-align: center;

    padding: 20px;

    color: #777;

    margin-top: 40px;
}

</style>

</head>


<body>


<div class="navbar">

    <h2>Career AI</h2>

    <div>

        <a href="dashboard.php">Dashboard</a>

        <a href="resume.php">Resume</a>

        <a href="logout.php">Logout</a>

    </div>

</div>


<div class="container">


<div class="card">

    <h1>🤖 AI Resume Analysis</h1>

    <p>
        Upload your resume and let Career AI analyze your
        skills, education, projects and career opportunities.
    </p>


    <?php if (!empty($message)): ?>

        <div class="message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <?php if (!empty($ai_result)): ?>

        <h2>📄 AI Analysis Result</h2>


        <div class="ai-result">

            <?php echo htmlspecialchars($ai_result); ?>

        </div>


        <a
            href="jobs.php"
            class="job-button"
        >
            🔍 Find Job Opportunities
        </a>


    <?php endif; ?>

</div>


</div>


<div class="footer">

    © 2026 Career AI | AI-Powered Career Readiness Platform

</div>


</body>

</html>