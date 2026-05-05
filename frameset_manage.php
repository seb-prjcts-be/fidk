<?php
require_once("cloudservices.inc.php");

// Check if user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != 1) {
    header("Location: index.php?actie=toon_formulier");
    exit;
}

$actie="";
if(isset($_GET["actie"]))
{
	$actie=$_GET["actie"];
}

if($actie=="tag_this")
{
	$art_id=$_GET["art_id"];
	$ser_id=$_GET["ser_id"];
	$img_id=$_GET["img_id"];
	$image_document_name=$_GET["image_document_name"];
	$url=$_GET["url"];
	
	$fm="folder_manage.php";
	$pm="preview_manage.php?actie=zoom&ser_id=".$ser_id."&art_id=".$art_id."&img_id=".$img_id."&image_document_name=".$image_document_name."&url=".$url;
	$tm="tags_manage.php?actie=toon_tags&img_id=".$img_id."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&art_id=".$art_id;
}
else
{
	$fm="folder_manage.php";
	$pm="preview_manage.php";
	$tm="tags_manage.php";
}

// Since we've already checked for login at the top, we can proceed with displaying the admin interface
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
"http://www.w3.org/TR/html4/frameset.dtd"> 
<?php	
	echo "<HTML>\n<HEAD>\n<TITLE>FIDK - Beheerder Omgeving</TITLE>\n</HEAD>\n";
	echo "<frameset cols=\"28%,*\">";
		echo "<frame src=\"".$fm."\" name=\"folder\" frameborder=\"0\">\n"; 
		echo "<frameset rows=\"40,*\">";
			echo "<frame src=\"menu.inc.php?menu=manage\" name=\"menu\" frameborder=\"0\">\n"; 
			echo "<frameset cols=\"70%,30%\">\n"; 
			echo "<frame src=\"".$pm."\" name=\"preview\" frameborder=\"0\">\n"; 
			echo "<frameset rows=\"*,120\">";
				echo "<frame src=\"".$tm."\" name=\"tags\" frameborder=\"0\" scrolling=\"auto\">";
				echo "<frame src=\"info_manage.php\" name=\"info\" frameborder=\"0\" scrolling=\"no\">";
			echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</frameset>\n";
	echo "</HTML>\n";
?>