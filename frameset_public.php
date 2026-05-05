<?php
require_once("data/includes/connection_inc.php");
require_once("data/includes/functions_inc.php");
session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
"http://www.w3.org/TR/html4/frameset.dtd"> 
<?php	
	echo "<HTML>\n<HEAD>\n<META charset=\"UTF-8\">\n<TITLE>FIDK - Fotografen in de kijker...</TITLE>\n";
	// Externe Bootstrap CDN-links tijdelijk uitgeschakeld om prestatieproblemen te verhelpen
	// echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>";
	// echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>";
	echo "</HEAD>\n";
	echo "<frameset cols=\"28%,*\">";
		echo "<frame src=\"folder_public.php\" name=\"folder\" frameborder=\"0\">\n"; 
		echo "<frameset rows=\"40,*\">";
			echo "<frame src=\"menu.inc.php?menu=public\" name=\"menu\" frameborder=\"0\">\n"; 
			echo "<frameset cols=\"70%,30%\">\n"; 
			echo "<frame src=\"preview_public.php\" name=\"preview\" frameborder=\"0\">\n"; 
			echo "<frameset rows=\"*,120\">";
				echo "<frame src=\"tags_public.php\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">";
				echo "<frame src=\"info_public.php\" name=\"info\" frameborder=\"0\" scrolling=\"no\">"; 
			echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>
