<?php
require_once("data/includes/connection_inc.php");

//-------------------------------------------------------------------------------------------------------------------------------------------------
$letter="a-z";
if(isset($_GET["letter"]))
{
	$letter=$_GET["letter"];
}

$abc = array ("a-z","z-a","a", "b", "c", "d", "e","f", "g", "h", "i", "j", "k", "l", "m", "n", "o","p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
//-------------------------------------------------------------------------------------------------------------------------------------------------
$serie_selected="";
if(isset($_GET["ser_id"]))
{
	$serie_selected=$_GET["ser_id"];
}
//-------------------------------------------------------------------------------------------------------------------------------------------------
echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n";
//-------------------------------------------------------------------------------------------------------------------------------------------------

$abc_string="";

foreach($abc as $val) 
{
	if($val==$letter)
	{
		$abc_string.="<a href='folder_manage.php?letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
	}
	else
	{
		$abc_string.="<a href='folder_manage.php?letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";	
	}
}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";

echo "<p>".$abc_string."</p>";


if($letter!="a-z" && $letter!="z-a")
{
	$sql1="SELECT * FROM tbl_artists WHERE artist_name LIKE '".$letter."%' ORDER BY artist_name ;";
	echo "<H1>Folder</H1>\n<H2>Tags Beheren</H2><h3>Fotografen (".$letter.")</h3>\n";
}
else
{
	if($letter=="a-z"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name"; }
	if($letter=="z-a"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name DESC"; }
	echo "<H1>Folder</H1>\n<H2>Bladeren</H2><h3>Fotografen (".$letter.")</h3>\n";
}	
		
//-------------------------------------------------------------------------------------------------------------------------------------------------

				$rs = mysqli_query($conn1, $sql1);
				echo "<ul>";
				while ($row=mysqli_fetch_array($rs))
					{
					extract($row);
					
						if($letter!="a-z" && $letter!="z-a")
						{
						$artist_name_show=substr_replace(substr($artist_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
						}
						else
						{
						$artist_name_show=$artist_name;
						}
					
					echo "<li>".$artist_name_show."<br>\n";
					$sql2="SELECT * FROM tbl_series WHERE serie_artist_fk=".$artist_pk;
					$rs2 = mysqli_query($conn2, $sql2);
					echo "<ul>";
					while ($row2=mysqli_fetch_array($rs2))
					{
					extract($row2);
		
						$url1="?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."&letter=".$letter;
						$url2="?actie=toon_reeks&ser_id=".$serie_pk."&art_id=".$artist_pk;
					
						if($serie_selected==$serie_pk)
						{
						echo "<li><a  style=\"color:red;\" onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."';parent.tags.location='tags_manage.php".$url2."';parent.folder.location='folder_manage.php".$url1."';\" href=\"#\">".$serie_name."</a>\n";	
						}
						else
						{
						echo "<li><a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."';parent.tags.location='tags_manage.php".$url2."';parent.folder.location='folder_manage.php".$url1."';\" href=\"#\">".$serie_name."</a>\n";						
						}
					}
					echo "</ul>";				
				}
				echo "</ul>";

//----------------------------------------------------------------------------------------------------------------------------------------------
if($letter!="a-z" && $letter!="z-a")
{
	$sql1="SELECT * FROM tbl_series WHERE serie_name LIKE '".$letter."%' ORDER BY serie_name ;";
	echo "<h3>Reeksen (".$letter.")</h3>\n";

//-------------------------------------------------------------------------------------------------------------------------------------------------
	

				$rs = mysqli_query($conn1,$sql1);
				echo "<ul>";
				while ($row=mysqli_fetch_array($rs))
					{
					extract($row);
					$sql2="SELECT * FROM tbl_artists WHERE artist_pk=".$serie_artist_fk;
					$rs2 = mysqli_query($conn2,$sql2);
					while ($row2=mysqli_fetch_array($rs2))
					{
					extract($row2);
					echo "<li>".$artist_name."<br>";
					echo "<ul>";
					$url1="?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."&letter=".$letter;

						if($letter!="a-z")
						{
						$serie_name_show=substr_replace(substr($serie_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
						}
						else
						{
						$serie_name_show=$serie_name;
						}


						if($serie_selected==$serie_pk)
						{
							echo "<li><a style=\"color:red;\" onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.tags.location='tags_manage.php".$url2."';parent.folder.location='folder_manage.php".$url1."';\" href=\"#\">".$serie_name_show."</a>\n";	
						}
						else
						{
							echo "<li><a onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.tags.location='tags_manage.php".$url2."';parent.folder.location='folder_manage.php".$url1."';\" href=\"#\">".$serie_name_show."</a>\n";								
						}							
					}
					echo "</ul>";				
				}
				echo "</ul>";
}

echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>