<?PHP
echo "<br>";
$actie="all";
function clean($string)
{ 
    $string=str_replace("_"," ",$string);
	$string=ucwords($string);
	$string=str_replace(" Iii"," III",$string);
	$string=str_replace(" Ii"," II",$string);
	return $string;  
}
include("data/includes/connection_inc.php");
include("fill_artists_series_images.php");
include("update_corrections.php");
?>