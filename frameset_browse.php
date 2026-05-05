<?php
include("cloudservices.inc.php");

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != 1) {
    header("Location: index.php?actie=toon_formulier");
    exit;
}

// Since we've already checked for login at the top, we can proceed with displaying the admin interface
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
"http://www.w3.org/TR/html4/frameset.dtd"> 
<?php	
	echo "<HTML>\n<HEAD>\n<TITLE>FIDK - Admin Bladeren</TITLE>\n";
	echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>";
	echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js' integrity='sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p' crossorigin='anonymous'></script>";
	echo "</HEAD>\n";
	echo "<frameset cols=\"28%,*\">";
		echo "<frame src=\"folder_browse.php\" name=\"folder\" frameborder=\"0\">\n";
		echo "<frameset rows=\"40,*\">";
			echo "<frame src=\"menu.inc.php?menu=browse\" name=\"menu\" frameborder=\"0\">\n";
			echo "<frameset cols=\"70%,30%\">\n";
			echo "<frame src=\"preview_browse.php\" name=\"preview\" frameborder=\"0\">\n";
			echo "<frameset rows=\"*,120\">";
				echo "<frame src=\"tags_browse.php\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">";
				echo "<frame src=\"info_browse.php\" name=\"info\" frameborder=\"0\" scrolling=\"no\">";
			echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</HTML>\n";
?>