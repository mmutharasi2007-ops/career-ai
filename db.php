<?php
/*
 * Local database settings belong in db.local.php (not committed to GitHub),
 * or set the CAREER_DB_HOST, CAREER_DB_USER, CAREER_DB_PASSWORD and
 * CAREER_DB_NAME environment variables in the web server.
 */
$localConfig = __DIR__ . DIRECTORY_SEPARATOR . 'db.local.php';

if (is_file($localConfig)) {
    require $localConfig;
} else {
    $servername = getenv('CAREER_DB_HOST') ?: 'localhost';
    $username = getenv('CAREER_DB_USER');
    $password = getenv('CAREER_DB_PASSWORD');
    $database = getenv('CAREER_DB_NAME') ?: 'career_ai';
}

if (!isset($servername, $username, $password, $database) ||
    !is_string($username) || $username === '' ||
    !is_string($password) || $password === '') {
    error_log('Career AI database settings are missing.');
    http_response_code(500);
    exit('Database configuration is missing. Please configure db.local.php.');
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    error_log('Career AI database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Unable to connect to the database. Please try again later.');
}

$conn->set_charset('utf8mb4');
?>