<?php
// Script om alle verwijzingen naar de oude mapstructuur bij te werken

// Functie om recursief door mappen te zoeken
function updateReferences($directory, $oldPath, $newPath, &$count) {
    $files = scandir($directory);
    
    foreach ($files as $file) {
        // Sla . en .. over
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = $directory . '/' . $file;
        
        // Als het een map is, verwerk deze recursief
        if (is_dir($filePath)) {
            // Sla de data/Js!6$9#Ae7C& map over om problemen te voorkomen
            if ($filePath !== __DIR__ . '/data/Js!6$9#Ae7C&') {
                updateReferences($filePath, $oldPath, $newPath, $count);
            }
        } 
        // Als het een PHP-bestand is, verwerk het
        elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($filePath);
            $originalContent = $content;
            
            // Zoek naar alle mogelijke verwijzingen naar de oude mapstructuur
            // 1. require_once statements
            $pattern1 = '/require_once\\("' . preg_quote($oldPath, '/') . '\\/([^"]+)"\\);/';
            $content = preg_replace($pattern1, 'require_once("' . $newPath . '/$1");', $content);
            
            // 2. include statements
            $pattern2 = '/include\\("' . preg_quote($oldPath, '/') . '\\/([^"]+)"\\);/';
            $content = preg_replace($pattern2, 'include("' . $newPath . '/$1");', $content);
            
            // 3. require statements
            $pattern3 = '/require\\("' . preg_quote($oldPath, '/') . '\\/([^"]+)"\\);/';
            $content = preg_replace($pattern3, 'require("' . $newPath . '/$1");', $content);
            
            // 4. include_once statements
            $pattern4 = '/include_once\\("' . preg_quote($oldPath, '/') . '\\/([^"]+)"\\);/';
            $content = preg_replace($pattern4, 'include_once("' . $newPath . '/$1");', $content);
            
            // 5. Andere verwijzingen met enkele of dubbele aanhalingstekens
            $pattern5 = '/([\'"]{1})' . preg_quote($oldPath, '/') . '\\/([^\'"]+)([\'"]{1})/';
            $content = preg_replace($pattern5, '$1' . $newPath . '/$2$3', $content);
            
            // Als er wijzigingen zijn, schrijf het bestand terug
            if ($content !== $originalContent) {
                file_put_contents($filePath, $content);
                $count++;
                $relativePath = str_replace(__DIR__ . '/', '', $filePath);
                echo "Bijgewerkt: $relativePath<br>";
            }
        }
    }
}

// Hoofdscript
$oldPath = "data/Js!6$9#Ae7C&";
$newPath = "data/includes";
$count = 0;

// Start de recursieve verwerking vanaf de hoofdmap
updateReferences(__DIR__, $oldPath, $newPath, $count);

echo "<h2>Klaar! $count bestanden bijgewerkt.</h2>";
?>
