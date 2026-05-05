<?php
require_once("cloudservices.inc.php");

$actie="";
if(isset($_GET["actie"]))
{
	$actie=$_GET["actie"];
}


if($actie=="media")
{
	$art_id=$_GET["art_id"];
	$artist_name=$_GET["artist_name"];

	$fm="folder_manage_col.php";
	$pm="preview_manage_col.php?actie=media&artist_name=".$artist_name."&art_id=".$art_id;
	$tm="tags_manage_col.php";
}
else
{
	$fm="folder_manage_col.php";
	$pm="preview_manage_col.php";
}

if($_SESSION["loggedin"]==1)
{
	echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n</HEAD>\n";
	echo "<frameset cols=\"28%,*\">"; 
		echo "<frame src=\"".$fm."\" name=\"folder\" frameborder=\"0\">\n"; 
		echo "<frameset rows=\"40,*\">"; 
			echo "<frame src=\"menu.inc.php?menu=manage_col\" name=\"menu\" frameborder=\"0\">\n"; 
			echo "<frameset cols=\"99%,1%\">\n"; 
			// echo "<frame src=\"".$pm."\" name=\"preview\" frameborder=\"0\">\n"; 
			// echo "<frameset rows=\"*,120\">"; 
				// echo "<frame src=\"".$tm."\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">";
			    echo "<frame src=\"".$pm."\" name=\"preview\" frameborder=\"0\">\n"; 
				echo "<frame src=\"blank.html\" name=\"info\" frameborder=\"0\" scrolling=\"no\">"; 
			echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</HTML>\n";
	
	
	
		// echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n</HEAD>\n";
	// echo "<frameset cols=\"28%,*\">"; 
		// echo "<frame src=\"folder_manage_col.php\" name=\"folder\" frameborder=\"0\">\n"; 
		// echo "<frameset rows=\"40,*\">"; 
			// echo "<frame src=\"menu.inc.php?menu=manage_col\" name=\"menu\" frameborder=\"0\">\n"; 
			// echo "<frameset cols=\"99%,1%\">\n"; 
			// echo "<frame src=\"preview_manage_col.php\" name=\"preview\" frameborder=\"0\">\n"; 
			// echo "<frame src=\"blank.html\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">\n"; 
	// echo "</frameset>\n";
	// echo "</frameset>\n";
	// echo "</frameset>\n";
	// echo "</HTML>\n";
	
	
	
	// echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n</HEAD>\n";
	// echo "<frameset rows=\"40,*\">"; 
	// echo "<frame src=\"menu.inc.php?menu=manage_col\" name=\"menu\" frameborder=\"0\">\n"; 
	// echo "<frameset cols=\"30%,50%,20%\">\n"; 
	// echo "<frame src=\"folder_manage_col.php\" name=\"folder\" frameborder=\"0\">\n"; 
	// echo "<frame src=\"preview_manage_col.php\" name=\"preview\" frameborder=\"0\">\n"; 
	// echo "<frame src=\"blank.html\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">\n"; 
	// echo "</frameset>\n";
	// echo "</frameset>\n";
	// echo "</HTML>\n";
}
else
{
	echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n</HEAD>\n";
	echo "<frameset rows=\"40,*\">"; 
	echo "<frame src=\"menu.inc.php\" name=\"menu\" frameborder=\"0\">\n"; 
	echo "<frameset cols=\"30%,50%,20%\">\n"; 
	echo "<frame src=\"blank.html\" name=\"folder\" frameborder=\"0\">\n"; 
	echo "<frame src=\"blank.html\" name=\"preview\" frameborder=\"0\">\n"; 
	echo "<frame src=\"blank.html\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">\n"; 
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</HTML>\n";
}
?>