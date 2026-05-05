<?php
// Load .env manually
function load_env($path) {
    if (!file_exists($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Load .env from current directory
load_env(__DIR__ . '/.env');

$openai_api_key = getenv('OPENAI_API_KEY');

// Validate API key
if (!$openai_api_key) {
    echo "❌ API key not found. Make sure it's set in .env as OPENAI_API_KEY=...";
    exit;
}

// Get name from URL
$photographer_name = isset($_GET['name']) ? trim($_GET['name']) : '';
if (!$photographer_name) {
    echo "❌ Please provide a photographer name using ?name=...";
    exit;
}

// GPT prompt
$system_prompt = "Je bent een betrouwbare assistent die alleen GEVERIFIEERDE en SPECIFIEKE informatie geeft over fotografen. "
    . "Vermeld ALLEEN details als je ze zeker weet en verzin niks. Als er geen geverifieerde info is, antwoord dan met 'NO_VERIFIED_INFO_FOUND'. "
    . "Gebruik het huidige jaar (2025) en schrijf in het Nederlands.";
$user_prompt = "Geef feitelijke informatie over fotograaf {$photographer_name}.";

// OpenAI payload
$payload = [
    "model" => "gpt-4o",
    "messages" => [
        ["role" => "system", "content" => $system_prompt],
        ["role" => "user", "content" => $user_prompt]
    ],
    "temperature" => 0.3,
    "max_tokens" => 500
];

// cURL call
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $openai_api_key"
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Show response or error
if ($error) {
    echo "❌ cURL error: $error";
    exit;
}

$data = json_decode($response, true);

if (isset($data['choices'][0]['message']['content'])) {
    echo nl2br(htmlspecialchars($data['choices'][0]['message']['content']));
} else {
    echo "❌ API response error:\n<pre>" . htmlspecialchars($response) . "</pre>";
}
