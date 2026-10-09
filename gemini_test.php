<?php

$keyFile = "C:/Muthu/gemini_key.txt";

if (!file_exists($keyFile)) {
    die("API key file not found.");
}

$apiKey = trim(file_get_contents($keyFile));

if (empty($apiKey)) {
    die("API key is empty.");
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . urlencode($api_key);
$data = [
    "contents" => [
        [
            "parts" => [
                [
                    "text" => "Say Hello to Career AI in one short sentence."
                ]
            ]
        ]
    ]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

if ($response === false) {
    die("CURL Error: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);



$result = json_decode($response, true);

echo "<h2>HTTP Status: " . $httpCode . "</h2>";

if (isset($result["candidates"][0]["content"]["parts"][0]["text"])) {

    echo "<h2>Gemini Connected Successfully!</h2>";

    echo "<p>";
    echo htmlspecialchars(
        $result["candidates"][0]["content"]["parts"][0]["text"]
    );
    echo "</p>";

} else {

    echo "<h2>Gemini API Response</h2>";

    echo "<pre>";
    echo htmlspecialchars($response);
    echo "</pre>";

}

?>