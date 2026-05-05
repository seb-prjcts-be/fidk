<?php
require_once("data/includes/connection_inc.php");

if(isset($_GET["actie"])){$actie=$_GET["actie"];}
if(isset($_GET["art_id"])){$art_id=$_GET["art_id"];}else{$art_id=0;}
if(isset($_GET["ser_id"])){$ser_id=$_GET["ser_id"];}else{$ser_id=0;}
if(isset($_GET["img_id"])) {$img_id=$_GET["img_id"];}else{$img_id=0;}
if(isset($_GET["url"])){$url=$_GET["url"];}else{$url="";}

echo "<HTML><HEAD><TITLE>FIDK</TITLE><link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD><BODY>";
	
	echo "<h1>Info</h1>";
	echo "<ul>";
	if($ser_id!=0 && $art_id!=0)
	{
	$sql1="SELECT * FROM tbl_artists,tbl_series WHERE artist_pk=serie_artist_fk AND artist_pk=".$art_id." AND serie_pk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			echo "<li>fotograaf: ".$artist_name;
			echo "<li>serie: ".$serie_name;
		}
	}
	
	if($img_id!=0)
	{
	$sql1="SELECT * FROM tbl_images WHERE image_pk=".$img_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
				echo "<li>bestand: ".$image_document_name;
				if($image_official_title=="")
				{
					echo "<li>titel: geen";
				}
				else
				{
					echo "<li>titel: ".$image_official_title;
				}
		}
	
	}
	echo "</ul>";
	
echo "</BODY></HTML>";

require("connection_close_inc.php");
?>