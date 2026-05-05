<?PHP

$text2="<p>this 'is some' dangerous &quot;text&quot;</p>";

echo $text2."<br>";

//voor het opslaan htmlspecialchars(,ENT_QUOTES)
$text3=htmlspecialchars($text2,ENT_QUOTES);


echo $text3."<br>";

//voor het tonen htmlspecialchars_decode";
echo htmlspecialchars_decode($text3)."<br>";
?>