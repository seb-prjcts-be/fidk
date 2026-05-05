<?php
/**
 * Test AI Update
 * 
 * Dit script test de AI-update functionaliteit voor een specifieke fotograaf.
 */

// Laad het AI-update script
require_once("ai_update.php");

// Specificeer een fotograaf om te testen
$photographer_name = "Adriana Duque";
$series = ["Icons I", "Icons II", "Infants"];
$photographer_id = null; // Optioneel, als je een specifieke fotograaf-ID hebt

// Genereer de update
$update = generate_ai_update($photographer_name, $series, $photographer_id);

// Toon het resultaat
echo "<h1>AI Update Test</h1>";
echo "<h2>Fotograaf: $photographer_name</h2>";
echo "<h3>Series: " . implode(", ", $series) . "</h3>";
echo "<hr>";

echo "<h3>Ruwe update:</h3>";
echo "<pre>" . htmlspecialchars($update) . "</pre>";
echo "<hr>";

echo "<h3>Geformatteerde update:</h3>";
echo $update;
echo "<hr>";

// Test de has_news logica
$has_news = true;
if (empty(trim($update)) || 
    $update === 'NO_VERIFIED_INFO_FOUND' ||
    strpos($update, "NO_VERIFIED_INFO_FOUND") !== false ||
    strpos($update, "geen recente informatie") !== false ||
    strpos($update, "geen actuele informatie") !== false ||
    strpos($update, "geen specifieke informatie") !== false ||
    strpos($update, "Helaas") === 0) {
    $has_news = false;
}

echo "<h3>Has News:</h3>";
echo $has_news ? "JA" : "NEE";
?>
