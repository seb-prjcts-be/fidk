<?php
/**
 * Eenvoudige AI Update Generator
 * Genereert feitelijke updates over fotografen via OpenAI GPT-4.
 */

// Laad .env-bestand
function load_env_file($path) {
    if (!file_exists($path)) return false;
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
    return true;
}

// Roep .env in de hoofdmap aan
load_env_file(__DIR__ . '/.env');

// Initialiseer API key
$openai_api_key = getenv('OPENAI_API_KEY');

// Maak het mogelijk om deze handmatig te overschrijven
function set_openai_api_key($key) {
    global $openai_api_key;
    $openai_api_key = $key;
    putenv("OPENAI_API_KEY=$key");
    $_ENV['OPENAI_API_KEY'] = $key;
    $_SERVER['OPENAI_API_KEY'] = $key;
}

// Eenvoudige HTTP helper
function http_post($url, $payload, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($ch);
    curl_close($ch);
    return json_decode($resp, true);
}

// Genereer feitelijke AI update
function generate_ai_update($photographer_name, $series = [], $photographer_id = null) {
    global $openai_api_key;

    if (empty($photographer_name) || empty($openai_api_key)) {
        return "Geen naam of API-sleutel gevonden.";
    }

    $series_info = !empty($series) ? ", bekend van " . implode(', ', $series) : '';
    $user_prompt = "Geef feitelijke informatie over fotograaf {$photographer_name}{$series_info}.";

    $system_prompt = "Je bent een betrouwbare assistent die feitelijke informatie geeft over fotografen. "
    . "Gebruik alleen informatie die je met hoge waarschijnlijkheid correct acht, maar verzin niks. "
    . "Laat irrelevante of onbewezen details weg. Als je niets weet, zeg dan 'NO_VERIFIED_INFO_FOUND'. "
    . "Gebruik het huidige jaar (2025) en schrijf in het Nederlands.";



    $payload = [
        "model" => "gpt-4o",
        "messages" => [
            ["role" => "system", "content" => $system_prompt],
            ["role" => "user", "content" => $user_prompt]
        ],
        "temperature" => 0.3,
        "max_tokens" => 500
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: " . "Bearer $openai_api_key"
    ];

    $response = http_post("https://api.openai.com/v1/chat/completions", $payload, $headers);
    $content = $response['choices'][0]['message']['content'] ?? '';

    if (stripos($content, 'NO_VERIFIED_INFO_FOUND') !== false || empty($content)) {
        return "";
    }

    return nl2br(htmlspecialchars($content));
}

// Optioneel: gebruik als standalone script
if (php_sapi_name() !== 'cli' && isset($_GET['name'])) {
    $name = $_GET['name'];
    $series = isset($_GET['series']) ? json_decode($_GET['series'], true) : [];
    echo generate_ai_update($name, $series);
}

function get_ai_update_html($photographer_name, $series = [], $photographer_id = null) {
    $update = generate_ai_update($photographer_name, $series, $photographer_id);

    // if (empty($update)) {
    //     return "<div class='ai-update'><em>Geen geverifieerde informatie gevonden over {$photographer_name}.</em></div>";
    // }

    $html = "<div class='ai-update'>";
    $html .= "<h3>AI Update over {$photographer_name}</h3>";
    $html .= "<div class='ai-content'>{$update}</div>";
    $html .= "</div>";

    $html .= "<style>
        .ai-update { margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
        .ai-update h3 { color: #0066cc; margin-top: 0; }
        .ai-update .ai-content { font-size: 0.95em; color: #222; }
    </style>";

    return $html;
}

?>
