<?php
require_once("data/includes/connection_inc.php");

$show_vars=0;

//session_start();
$url="tags_browse.php";

	$actie="toon_alles";
	if(isset($_GET["actie"])) {$actie=$_GET["actie"]; $url.="?actie=".$actie;}

	$art_id=0;
	if(isset($_GET["art_id"])) {$art_id=$_GET["art_id"]; $url.="&art_id=".$art_id;}

	$ser_id=0;
	if(isset($_GET["ser_id"])) {$ser_id=$_GET["ser_id"]; $url.="&ser_id=".$ser_id;}

	$img_id=0;
	if(isset($_GET["img_id"])) {$img_id=$_GET["img_id"]; $url.="&img_id=".$img_id;}
	
	$image_document_name="";
	if(isset($_GET["image_document_name"])) {$image_document_name=$_GET["image_document_name"]; $url.="&image_document_name=".$image_document_name; }

	$tag_pk=0;
	if(isset($_GET["tag_pk"])) {$tag_pk=$_GET["tag_pk"];}

//----------------------------------------------------------------------------------------------------------------------------------------------------
	
echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n<H1>Tags</H1>\n<H2>Bladeren</H2>\n";

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_alles")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
echo "<h3>Zoek op tag</h3>";
	$sql1="SELECT * FROM tbl_tags ORDER BY tag";
	$rs = mysqli_query($conn1,$sql1);
	echo "<ul>";
	
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					$tag_namedb=$tag;
					$tag_pkdb=$tag_pk;
					$counttags=0;
						$sql2="SELECT * FROM tbl_link_tag WHERE link_tag_tag_fk=".$tag_pkdb;
						$rs2 = mysqli_query($conn2,$sql2);
						while ($row2=mysqli_fetch_array($rs2))
						{
						extract($row2);
						$counttags++;
						}	
					$url1="?actie=search_images_to_tag&tag_pk=".$tag_pkdb;
					$url2="?actie=results&tag_name=".$tag_namedb;		
					
					//parent.info.location='info_browse.php.".$url2."';
					echo "<li>".$tag_namedb." <a href=\"#\" onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';\">zoek</a> (".$counttags.")";	
		}		
	
}
//--------------------------------------------------------------------------
if($show_vars!=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
echo "<ul>";
echo "<li>art_id: ".$art_id;
echo "<li>ser_id: ".$ser_id;
echo "<li>img_id: ".$img_id;
echo "</ul>";
}
//--------------------------------------------------------------------------
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>