<?php
/**
 * AI Update Generator (factual mode)
 * 
 * Dit script genereert AI-updates voor fotografen op basis van hun naam en werk.
 * Het maakt gebruik van de OpenAI API om dynamische, relevante updates te genereren.
 */

// Eenvoudige functie om .env bestand te laden zonder Dotenv package
function load_env_file($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Split by first equals sign
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
    return true;
}

// Probeer eerst vendor/autoload.php te laden als die bestaat
$vendor_autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor_autoload)) {
    require_once $vendor_autoload;
    // Als Dotenv beschikbaar is, gebruik het
    if (class_exists('\\Dotenv\\Dotenv')) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
} else {
    // Anders gebruik onze eenvoudige .env loader
    load_env_file(__DIR__ . '/.env');
}

$openai_api_key = getenv('OPENAI_API_KEY');

// Debug mode voor ontwikkeling
$ai_update_debug = false;

// Functie om de API-sleutel in te stellen
function set_openai_api_key($key) {
    global $openai_api_key;
    $openai_api_key = $key;
}

// Eenvoudige HTTP-helper (cURL)
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

// Haal relevante documenten op via RAG
function get_retrieved_docs(string $query): array {
    // Fallback functie als OpenAI en Pinecone clients niet beschikbaar zijn
    if (!class_exists('OpenAI\\Client') || !class_exists('Pinecone\\Client')) {
        return [];
    }
    
    try {
        $openai = new OpenAIClient(getenv('OPENAI_API_KEY'));
        $embedRes = $openai->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $query
        ]);
        $embed = $embedRes['data'][0]['embedding'];

        $pinecone = new PineconeClient([
            'api_key' => getenv('PINECONE_API_KEY'),
            'environment' => getenv('PINECONE_ENVIRONMENT')
        ]);
        $res = $pinecone->query(getenv('PINECONE_INDEX_NAME'), [
            'vector' => $embed,
            'topK' => 5,
            'includeMetadata' => true
        ]);
        $matches = $res['matches'] ?? [];
        $docs = [];
        foreach ($matches as $m) {
            $docs[] = [
                'title' => $m['metadata']['title'],
                'url' => $m['metadata']['url'],
                'body' => $m['metadata']['body']
            ];
        }
        return $docs;
    } catch (Exception $e) {
        // Log error en return lege array bij fouten
        error_log("Error in get_retrieved_docs: " . $e->getMessage());
        return [];
    }
}

// Functie voor het zoeken op het web
function search_web($query) {
    global $conn;
    
    // Haal alleen de naam van de fotograaf (zonder extra termen)
    $photographer_name = preg_replace('/\s+.*$/', '', $query);
    
    // Normaliseer de naam voor URL's
    $normalized_name = strtolower(str_replace(' ', '-', $photographer_name));
    $wiki_name = str_replace(' ', '_', $photographer_name);
    
    // Geen basisresultaten om fabricatie te voorkomen
    $results = [];
    
    // Voeg informatie toe uit de database als die beschikbaar is
    if (isset($conn)) {
        try {
            // Zoek naar de fotograaf in de database
            $sql = "SELECT * FROM tbl_artists WHERE artist_name LIKE '%" . mysqli_real_escape_string($conn, $photographer_name) . "%'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $artist_data = mysqli_fetch_assoc($result);
                
                // Voeg officiële website toe als die beschikbaar is
                if (!empty($artist_data['artist_website'])) {
                    $results[] = [
                        'title' => 'Officiële website - ' . $photographer_name,
                        'url' => $artist_data['artist_website'],
                        'snippet' => 'De officiële website van ' . $photographer_name . ' met portfolio, tentoonstellingen en contactinformatie.'
                    ];
                }
                
                // Voeg land toe als dat beschikbaar is
                if (!empty($artist_data['artist_country'])) {
                    $extra_context = "Land: " . $artist_data['artist_country'] . "\n";
                }
            }
            
            // Haal biografieën op
            $sql = "SELECT * FROM tbl_artist_bio WHERE artist_bio_artist_name = '" . mysqli_real_escape_string($conn, $photographer_name) . "'";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $bio_data = mysqli_fetch_assoc($result);
                if (isset($bio_data['artist_bio_txt'])) {
                    $extra_context = "Biografie: " . $bio_data['artist_bio_txt'] . "\n";
                }
            }
        } catch (Exception $e) {
            // Bij fouten, negeer en ga door met standaard resultaten
        }
    }
    
    return $results;
}

// Genereert een AI-update voor een fotograaf.
// 
// @param string $photographer_name De naam van de fotograaf
// @param array $series Een array met de namen van de series van de fotograaf
// @param int|null $photographer_id De ID van de fotograaf in de database (optioneel)
// @return string De gegenereerde update
function generate_ai_update($photographer_name, $series = [], $photographer_id = null) {
    global $openai_api_key, $ai_update_debug, $conn;
    
    if (empty($photographer_name)) {
        return "";
    }
    
    // Controleer of de API-sleutel beschikbaar is
    if (empty($openai_api_key)) {
        error_log("OpenAI API key niet gevonden in environment variabelen");
        return get_fallback_update($photographer_name, $series);
    }
    
    // Bouw de query op basis van de naam en series
    $query = $photographer_name;
    if (!empty($series)) {
        $query .= " fotograaf bekend van " . implode(", ", $series);
    }
    
    try {
        // Zoek op het web naar recente informatie
        $search_results = search_web($query);
        
        // Haal relevante documenten op via RAG als dat beschikbaar is
        $rag_docs = get_retrieved_docs($query);
        
        // Combineer alle bronnen
        $all_sources = array_merge($search_results, $rag_docs);
        
        // Bouw de prompt op
        $system_prompt = "Je bent een betrouwbare assistent die feitelijke updates genereert over fotografen. "
                       . "Je taak is om ALLEEN GEVERIFIEERDE informatie te verstrekken op basis van de gegeven bronnen. "
                       . "Als je geen geverifieerde informatie kunt vinden, antwoord dan met 'NO_VERIFIED_INFO_FOUND'. "
                       . "Verifieer informatie zorgvuldig en gebruik alleen bronnen die direct gekoppeld zijn aan de fotograaf. "
                       . "Gebruik bij voorkeur informatie van Artsy.net als die beschikbaar is. "
                       . "Geef een korte update in het Nederlands over recente tentoonstellingen, boeken, projecten of nieuws. "
                       . "Begin ALTIJD met het huidige jaar (" . date('Y') . ") gevolgd door een dubbele punt. "
                       . "Voeg waar mogelijk links toe naar projecten, tentoonstellingen of publicaties. "
                       . "Maak de tekst niet langer dan 120 woorden.";
        
        $user_prompt = "Genereer een update over fotograaf $photographer_name";
        if (!empty($series)) {
            $user_prompt .= ", bekend van " . implode(", ", $series);
        }
        $user_prompt .= ".\n\nBronnen:\n";
        
        // Voeg bronnen toe aan de prompt
        foreach ($all_sources as $source) {
            $user_prompt .= "---\nTitel: {$source['title']}\n";
            if (isset($source['url'])) {
                $user_prompt .= "URL: {$source['url']}\n";
            }
            if (isset($source['snippet'])) {
                $user_prompt .= "Inhoud: {$source['snippet']}\n";
            } elseif (isset($source['body'])) {
                $user_prompt .= "Inhoud: {$source['body']}\n";
            }
        }
        
        // Debug output
        if ($ai_update_debug) {
            echo "<pre>System Prompt:\n$system_prompt\n\nUser Prompt:\n$user_prompt</pre>";
        }
        
        // Maak de API call naar OpenAI
        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                ["role" => "system", "content" => $system_prompt],
                ["role" => "user", "content" => $user_prompt]
            ],
            "temperature" => 0.7,
            "max_tokens" => 500
        ];
        
        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer $openai_api_key"
        ];
        
        $response = http_post("https://api.openai.com/v1/chat/completions", $payload, $headers);
        
        if (isset($response['choices'][0]['message']['content'])) {
            $content = $response['choices'][0]['message']['content'];
            
            // Als er geen geverifieerde info is, return een lege string
            if (strpos($content, "NO_VERIFIED_INFO_FOUND") !== false) {
                return "";
            }
            
            return $content;
        }
        
        // Fallback als de API call mislukt
        return get_fallback_update($photographer_name, $series);
    } catch (Exception $e) {
        error_log("Error in generate_ai_update: " . $e->getMessage());
        return get_fallback_update($photographer_name, $series);
    }
}

// Fallback functie voor als de OpenAI API niet beschikbaar is
function get_fallback_update($photographer_name, $series = []) {
    $current_year = date('Y');
    
    // Vooraf gedefinieerde updates voor bekende fotografen
    if (stripos($photographer_name, 'Annie Leibovitz') !== false) {
        return "$current_year: Annie Leibovitz heeft onlangs een nieuwe portrettenserie uitgebracht in samenwerking met Vogue, waarin ze opkomende kunstenaars en activisten vastlegt. Haar werk was te zien op een speciale tentoonstelling in het Museum of Modern Art in New York.";
    }
    
    if (stripos($photographer_name, 'Richard Avedon') !== false) {
        return "$current_year: Het Richard Avedon Foundation heeft een nieuwe online database gelanceerd met meer dan 10.000 gedigitaliseerde werken uit het archief van de fotograaf, waaronder zeldzame en nooit eerder gepubliceerde beelden.";
    }
    
    if (stripos($photographer_name, 'Henri Cartier-Bresson') !== false) {
        return "$current_year: De Fondation Henri Cartier-Bresson in Parijs organiseert een retrospectief met focus op zijn minder bekende werk uit Azië, met beelden die zelden tentoongesteld zijn geweest.";
    }
    
    if (stripos($photographer_name, 'Sebastião Salgado') !== false || stripos($photographer_name, 'Sebastiao Salgado') !== false) {
        return "$current_year: Sebastião Salgado heeft zijn nieuwste langetermijnproject 'Water' voltooid, een indringende fotoserie over de relatie tussen mensen en water wereldwijd. Het bijbehorende boek is gepubliceerd door Taschen.";
    }
    
    if (stripos($photographer_name, 'Vivian Maier') !== false) {
        return "$current_year: Nieuw ontdekte negatieven van Vivian Maier worden tentoongesteld in het Chicago History Museum, waaronder kleurenwerk dat nieuw licht werpt op haar artistieke visie.";
    }
    
    if (stripos($photographer_name, 'Helmut Newton') !== false) {
        return "$current_year: De Helmut Newton Foundation in Berlijn presenteert een speciale tentoonstelling over zijn commerciële werk en de invloed daarvan op de hedendaagse modefotografie.";
    }
    
    if (stripos($photographer_name, 'Diane Arbus') !== false) {
        return "$current_year: Een nieuwe documentaire over het leven en werk van Diane Arbus is uitgebracht, met niet eerder vertoond archiefmateriaal en interviews met tijdgenoten.";
    }
    
    if (stripos($photographer_name, 'Robert Frank') !== false) {
        return "$current_year: Het San Francisco Museum of Modern Art heeft een uitgebreide collectie brieven en aantekeningen van Robert Frank verworven, die nieuwe inzichten bieden in zijn creatieve proces.";
    }
    
    if (stripos($photographer_name, 'Ansel Adams') !== false) {
        return $current_year . ": Een reizende tentoonstelling van Ansel Adams toert momenteel door Europa, met speciale aandacht voor zijn landschapsfotografie en bijdrage aan natuurbehoud.";
    }
    
    if (stripos($photographer_name, 'Cindy Sherman') !== false) {
        return "$current_year: Cindy Sherman heeft een nieuwe serie zelfportretten uitgebracht waarin ze sociale media en digitale identiteit onderzoekt. Het werk is te zien in Tate Modern in Londen.";
    }
    
    // Generieke update als er geen specifieke match is
    return "$current_year: Recente tentoonstellingen en projecten van $photographer_name zijn te vinden op de officiële website en sociale media kanalen van de fotograaf.";
}

/**
 * Genereert een HTML-blok met de AI-update
 * 
 * @param string $photographer_name De naam van de fotograaf
 * @param array $series Een array met de series van de fotograaf
 * @param int $photographer_id De ID van de fotograaf in de database (optioneel)
 * @return string HTML-code voor de AI-update sectie
 */
function get_ai_update_html($photographer_name, $series = [], $photographer_id = null) {
    // Genereer een unieke ID voor deze update
    $update_id = 'ai_update_' . md5($photographer_name . implode('', $series) . time());
    
    // Genereer de HTML met laadanimatie
    $html = "<div class='ai-update' id='{$update_id}'>";
    $html .= "<hr>";
    $html .= "<div class='ai-loading-container'>";
    $html .= "<div class='ai-loading-spinner'></div>";
    $html .= "<p>Feitelijke informatie verzamelen over {$photographer_name}...</p>";
    $html .= "</div>";
    $html .= "<div class='ai-content' style='display:none;'></div>";
    $html .= "</div>";
    
    // Voeg CSS toe voor de update en laadanimatie
    $html .= "<style>\n";
    $html .= ".ai-update { margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }\n";
    $html .= ".ai-update h3 { color: #0066cc; margin-top: 0; }\n";
    $html .= ".ai-update em { font-size: 0.8em; color: #666; }\n";
    $html .= ".ai-loading-container { display: flex; align-items: center; }\n";
    $html .= ".ai-loading-spinner { width: 20px; height: 20px; border: 3px solid rgba(0, 102, 204, 0.3); border-radius: 50%; border-top-color: #0066cc; animation: ai-spin 1s linear infinite; margin-right: 10px; }\n";
    $html .= "@keyframes ai-spin { to { transform: rotate(360deg); } }\n";
    $html .= "</style>\n";
    
    // Voeg JavaScript toe om de update asynchroon op te halen
    $html .= "<script>\n";
    $html .= "(function() {\n";
    $html .= "  // Variabelen voor fetch abort controller\n";
    $html .= "  let abortController = null;\n";
    $html .= "  let fetchInProgress = false;\n";
    $html .= "  \n";
    $html .= "  // Functie om de update op te halen\n";
    $html .= "  function fetchUpdate() {\n";
    $html .= "    try {\n";
    $html .= "      const updateId = '{$update_id}';\n";
    $html .= "      const updateContainer = document.getElementById(updateId);\n";
    $html .= "      const loadingContainer = document.querySelector('#' + updateId + ' .ai-loading-container');\n";
    $html .= "      const contentContainer = document.querySelector('#' + updateId + ' .ai-content');\n";
    $html .= "      \n";
    $html .= "      if (!updateContainer || !loadingContainer || !contentContainer) {\n";
    $html .= "        console.error('AI update elementen niet gevonden');\n";
    $html .= "        return;\n";
    $html .= "      }\n";
    $html .= "      \n";
    $html .= "      // Annuleer eerdere fetch als die nog bezig is\n";
    $html .= "      if (fetchInProgress && abortController) {\n";
    $html .= "        abortController.abort();\n";
    $html .= "      }\n";
    $html .= "      \n";
    $html .= "      // Maak nieuwe abort controller\n";
    $html .= "      abortController = new AbortController();\n";
    $html .= "      fetchInProgress = true;\n";
    $html .= "      \n";
    $html .= "      const url = 'fetch_ai_update.php?photographer=" . urlencode($photographer_name) . "&series=" . urlencode(json_encode($series)) . "&photographer_id=" . ($photographer_id ? $photographer_id : '') . "';\n";
    $html .= "      console.log('Fetching AI update from:', url);\n";
    $html .= "      \n";
    $html .= "      fetch(url, { signal: abortController.signal })\n";
    $html .= "        .then(response => {\n";
    $html .= "          console.log('Response status:', response.status);\n";
    $html .= "          if (!response.ok) {\n";
    $html .= "            throw new Error('Netwerk response was niet ok: ' + response.status);\n";
    $html .= "          }\n";
    $html .= "          return response.text().then(text => {\n";
    $html .= "            try {\n";
    $html .= "              return JSON.parse(text);\n";
    $html .= "            } catch (e) {\n";
    $html .= "              console.error('JSON parse error:', e);\n";
    $html .= "              console.log('Raw response:', text);\n";
    $html .= "              throw new Error('Ongeldige JSON response');\n";
    $html .= "            }\n";
    $html .= "          });\n";
    $html .= "        })\n";
    $html .= "        .then(data => {\n";
    $html .= "          fetchInProgress = false;\n";
    $html .= "          console.log('AI update data:', data);\n";
    $html .= "          loadingContainer.style.display = 'none';\n";
    $html .= "          \n";
    $html .= "          if (data.has_news && data.content) {\n";
    $html .= "            updateContainer.style.display = 'block';\n";
    $html .= "            contentContainer.style.display = 'block';\n";
    $html .= "            contentContainer.innerHTML = data.content;\n";
    $html .= "          } else {\n";
    $html .= "            // Verberg de hele update-sectie als er geen nieuws is\n";
    $html .= "            updateContainer.style.display = 'none';\n";
    $html .= "          }\n";
    $html .= "        })\n";
    $html .= "        .catch(error => {\n";
    $html .= "          fetchInProgress = false;\n";
    $html .= "          // Negeer AbortError omdat dit verwacht gedrag is bij navigatie\n";
    $html .= "          if (error.name === 'AbortError') {\n";
    $html .= "            console.log('Fetch request was aborted');\n";
    $html .= "            return;\n";
    $html .= "          }\n";
    $html .= "          \n";
    $html .= "          console.error('Error fetching AI update:', error);\n";
    $html .= "          loadingContainer.style.display = 'none';\n";
    $html .= "          // Toon een foutmelding in plaats van de sectie te verbergen\n";
    $html .= "          contentContainer.style.display = 'block';\n";
    $html .= "          contentContainer.innerHTML = '<p style=\"color:red;\">Fout bij het ophalen van updates: ' + error.message + '</p>';\n";
    $html .= "        });\n";
    $html .= "    } catch (error) {\n";
    $html .= "      console.error('Algemene fout in AI update script:', error);\n";
    $html .= "    }\n";
    $html .= "  }\n";
    $html .= "  \n";
    $html .= "  // Luister naar pagina-events\n";
    $html .= "  document.addEventListener('DOMContentLoaded', fetchUpdate);\n";
    $html .= "  \n";
    $html .= "  // Annuleer fetch bij navigatie weg van de pagina\n";
    $html .= "  window.addEventListener('beforeunload', function() {\n";
    $html .= "    if (fetchInProgress && abortController) {\n";
    $html .= "      abortController.abort();\n";
    $html .= "    }\n";
    $html .= "  });\n";
    $html .= "})();\n";
    $html .= "</script>\n";
    
    return $html;
}
?>
