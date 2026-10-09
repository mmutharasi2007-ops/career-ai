<?php

set_time_limit(60);

$key_file = "C:/Muthu/gemini_key.txt";

if (!file_exists($key_file)) {
    die("API key file not found.");
}

$api_key = trim(file_get_contents($key_file));

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key="
     . urlencode($api_key);

$data = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "Give me 2 simple interview questions for an ECE engineering student."
                ]
            ]
        ]
    ]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

$response = curl_exec($ch);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

echo "<h2>Gemini Test</h2>";
echo "<p><b>HTTP Status:</b> " . $http_code . "</p>";

if ($curl_error != '') {
    echo "<p><b>cURL Error:</b> " . htmlspecialchars($curl_error) . "</p>";
}

echo "<pre>";
echo htmlspecialchars($response);
echo "</pre>";
?>
