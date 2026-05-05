<?php

require_once("cloudservices.inc.php");

//-------------------------------------------------------------------------------------------------------------------------------------------------
$letter="a-z";
if(isset($_GET["letter"]))
{
	$letter=$_GET["letter"];
}

$abc = array ("a-z","z-a","a", "b", "c", "d", "e","f", "g", "h", "i", "j", "k", "l", "m", "n", "o","p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

//-------------------------------------------------------------------------------------------------------------------------------------------------

function encode($string)
{ 
	$string=trim($string);
    $string=str_replace(" ","_",$string);
	$string=strtolower($string);
	return $string;  
}

//-------------------------------------------------------------------------------------------------------------------------------------------------

if($loggedin==1)
{
	require_once("data/includes/connection_inc.php");
	$actie="";
	if(isset($_GET["actie"]))
	{
		$actie=$_GET["actie"];
	}
	else
	{
		$actie="toon_db";
	}
	
//-------------------------------------------------------------------------------------------------------------------------------------------------	
echo "<HTML>\n<HEAD>\n<META charset=\"UTF-8\">\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n";

//-------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_db")
//-------------------------------------------------------------------------------------------------------------------------------------------------	
{
$abc_string="";
foreach($abc as $val) 
{
	if($val==$letter)
	{
		$abc_string.="<a href='folder_manage_col.php?letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
	}
	else
	{
		$abc_string.="<a href='folder_manage_col.php?letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";	
	}
}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";

echo "<p>".$abc_string."</p>";


if($letter!="a-z" && $letter!="z-a")
{
	$sql1="SELECT * FROM tbl_artists WHERE artist_name LIKE '".$letter."%' ORDER BY artist_name ;";
	echo "<H1>Folder</H1>\n<H2>Collectie Beheren</H2><h3>Fotografen (".$letter.")</h3>\n";
}
else
{
	if($letter=="a-z"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name"; }
	if($letter=="z-a"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name DESC"; }
	echo "<H1>Folder</H1>\n<H2>Bladeren</H2><h3>Fotografen (".$letter.")</h3>\n";
}		
				$rs = mysqli_query($conn1,$sql1);
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
					
					$cur_artist_name=$artist_name;
					$cur_artist_pk=$artist_pk;
					$cur_artist_folder_name=$artist_folder_name;
					echo "<li>".$artist_name_show." <a href='preview_manage_col.php?actie=bio&art_id=".$cur_artist_pk."&artist_name=".$cur_artist_name."' target='preview'>bio&rsquo;s</a>";
					echo " - <a href='preview_manage_col.php?actie=media&art_id=".$artist_pk."&artist_name=".$artist_name."' target='preview'>media</a> <br>";
					
					$url1="?actie=frm_add_images_to_serie&art_id=".$cur_artist_pk;
					$url2="?actie=frm_add_serie&art_id=".$cur_artist_pk."&artist_folder_name=".$cur_artist_folder_name;
					
					
						$sql2="SELECT * FROM tbl_series WHERE serie_artist_fk=".$cur_artist_pk;
						$rs2 = mysqli_query($conn2,$sql2);
							echo "<ul>";
							while ($row2=mysqli_fetch_array($rs2))
							{
							extract($row2);
					
							$url3="&ser_id=".$serie_pk."&art_id=".$cur_artist_pk."&artist_name=".$cur_artist_name."&artist_folder_name=".$cur_artist_folder_name."&serie_name=".$serie_name;
		
							echo "<li>".$serie_name." <a href='preview_manage_col.php?actie=serie_beheren".$url3."' target='preview'>beheer</a>";
						}				
					echo "</ul>";				
				}
			echo "</ul>";			
}

if($letter!="a-z" && $letter!="z-a")
{
	$sql1="SELECT * FROM tbl_series WHERE serie_name LIKE '".$letter."%' ORDER BY serie_name ;";
	echo "<h3>Reeksen (".$letter.")</h3>\n";
		
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
					
					echo "<li>".$artist_name." <a href='preview_manage_col.php?actie=bio&art_id=".$artist_pk."&artist_name=".$artist_name."' target='preview'>";
					echo "bio&rsquo;s</a>";
					echo " - <a href='preview_manage_col.php?actie=media&art_id=".$artist_pk."&artist_name=".$artist_name."' target='preview'>media</a> <br>";
					echo "<ul>";
					
					$url3="&ser_id=".$serie_pk."&art_id=".$cur_artist_pk."&artist_name=".$artist_name."&artist_folder_name=".$artist_folder_name."&serie_name=".$serie_name;
						
						if($letter!="a-z")
						{
						$serie_name_show=substr_replace(substr($serie_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
						}
						else
						{
						$serie_name_show=$serie_name;
						}

					echo "<li>".$serie_name_show." <a href='preview_manage_col.php?actie=serie_beheren".$url3."' target='preview'>beheer</a>";
											
					}
					echo "</ul>";				
				}
				echo "</ul>";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
echo "</BODY>\n</HTML>\n";
} 

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>