<?php
/**
 * Test API Web Access
 * 
 * Dit script test of de OpenAI API webtoegang heeft door een specifieke vraag te stellen
 * die alleen kan worden beantwoord met actuele informatie van het web.
 */

// Laad de API-configuratie
require_once("ai_update.php");

// Functie om te testen of de API webtoegang heeft
function test_api_web_access() {
    global $openai_api_key;
    
    // Als er geen API-sleutel is ingesteld, return false
    if (empty($openai_api_key) || $openai_api_key === "YOUR_OPENAI_API_KEY") {
        return [
            'success' => false,
            'message' => 'Geen API-sleutel ingesteld'
        ];
    }
    
    try {
        // Bereid een vraag voor die actuele informatie vereist
        $prompt = "Wat is de huidige datum en tijd? Geef alleen de datum en tijd, niets anders.";
        
        // Bereid de API-aanvraag voor met tools voor webtoegang
        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'Je bent een behulpzame assistent die actuele informatie kan opzoeken.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 50,
            // Voeg tools toe om webtoegang te testen
            'tools' => [
                [
                    'type' => 'function',
                    'function' => [
                        'name' => 'search_web',
                        'description' => 'Zoek actuele informatie op het web',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => 'De zoekopdracht om uit te voeren'
                                ]
                            ],
                            'required' => ['query']
                        ]
                    ]
                ]
            ],
            'tool_choice' => 'auto'
        ];
        
        // Stuur de aanvraag naar de OpenAI API
        $ch = curl_init("https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $openai_api_key
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            return [
                'success' => false,
                'message' => 'API-fout: ' . curl_error($ch)
            ];
        }
        
        curl_close($ch);
        
        // Verwerk het antwoord
        $response_data = json_decode($response, true);
        
        // Controleer of er tool_calls zijn gebruikt (teken van webtoegang)
        $has_web_access = false;
        $response_content = '';
        
        if (isset($response_data['choices'][0]['message'])) {
            $message = $response_data['choices'][0]['message'];
            
            // Controleer op tool_calls
            if (isset($message['tool_calls']) && !empty($message['tool_calls'])) {
                foreach ($message['tool_calls'] as $tool_call) {
                    if ($tool_call['function']['name'] === 'search_web') {
                        $has_web_access = true;
                        break;
                    }
                }
            }
            
            // Haal de content op
            if (isset($message['content'])) {
                $response_content = $message['content'];
            }
        }
        
        return [
            'success' => true,
            'has_web_access' => $has_web_access,
            'response' => $response_content,
            'raw_response' => $response_data
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Exception: ' . $e->getMessage()
        ];
    }
}

// Voer de test uit
$test_result = test_api_web_access();

// Toon het resultaat
header('Content-Type: application/json');
echo json_encode($test_result, JSON_PRETTY_PRINT);
?>
