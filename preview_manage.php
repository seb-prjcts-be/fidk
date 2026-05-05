<?php
require_once("data/includes/connection_inc.php");
require_once("data/includes/functions_inc.php");
session_start();

//----------------------------------------------------------------------------------------------------------------------------------------------------
// $_REQUEST-variabelen opvragen 
//----------------------------------------------------------------------------------------------------------------------------------------------------

$actie="";

 foreach($_REQUEST as $key => $val) 
 {
     if( is_array( $key ) ) {
         foreach( $key as $thing) {
         }
     } else {
		  ${$key}=$val;
		  // echo $key."=".${$key};
		  // echo "<br>";
     }
 }

//----------------------------------------------------------------------------------------------------------------------------------------------------

echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n";

//----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	echo "<H1>Preview</H1>";
	echo "<H2>Tags Beheren</H2>";	

	echo "<p>Klik in het venster &quot;Tags&quot; om een tag te verwijderen.</p>";
	echo "<p>Of selecteer in het venster &quot;Folder&quot; een reeks of beeld.</p>";
	
	echo "<ul><li><a href='preview_manage.php?actie=toon_alles&size=60' target='preview'>Toon alles en tag (thumbnails - 60px).</a>";
	echo "<li><a href='preview_manage.php?actie=toon_alles&size=120' target='preview'>Toon alles  en tag (thumbnails - 120px).</a></ul>";
	echo "<ul>";
	echo "<li><a href='preview_manage.php?actie=toon_tag_management'>Tag-combinaties controleren en foute verwijderen.</a>";
	echo "</<ul>";
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="serie" && $ser_id !=0 && $art_id !=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{			
		$sql1="SELECT * FROM tbl_series WHERE serie_pk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
			while ($row=mysqli_fetch_array($rs))
			{
			extract($row);
			$serie_namedb=$serie_name;
			$serie_name_url_enc=$serie_folder_name;
			}
		
		//toon de artiest
		$sql1="SELECT * FROM tbl_artists WHERE artist_pk=".$art_id;
		$rs = mysqli_query($conn1, $sql1);
			while ($row=mysqli_fetch_array($rs))
			{
				extract($row);
				echo "<h1>".$artist_name."</h1>";
				echo "<h2>".$serie_name."</h2>";
				$artist_namedb=$artist_name;
				$artist_name_url_enc=$artist_folder_name;
			}	
	   
		$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
			while ($row=mysqli_fetch_array($rs))
			{
				extract($row);
			
					$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
					$url1="?actie=zoom&ser_id=".$ser_id."&art_id=".$art_id."&img_id=".$image_pk."&image_document_name=".$image_document_name."&url=".$url;
					$url2="?actie=toon_tags&img_id=".$image_pk."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&art_id=".$art_id;
					$url3="?actie=nieuwe_tag_aan_afbeeldings&img_id=".$image_pk."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&art_id=".$art_id;
			
					echo "<a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."'; parent.tags.location='tags_manage.php".$url2."';\" href=\"#\"><img src='".$url."' width='400'></a><br><br>";
				
			}
	}
	
 //----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="zoom" && $img_id !=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		$sql1="SELECT * FROM tbl_series WHERE serie_pk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
			while ($row=mysqli_fetch_array($rs))
			{
				extract($row);
				$serie_namedb=$serie_name;
				$serie_name_url_enc=$serie_folder_name;
			}
		
			//toon de artiest
			$sql1="SELECT * FROM tbl_artists WHERE artist_pk=".$art_id;
			$rs = mysqli_query($conn1, $sql1);
				while ($row=mysqli_fetch_array($rs))
				{
					extract($row);
					$artist_namedb=$artist_name;
					$artist_name_url_enc=$artist_folder_name;
				}	
			
			$count=0;
			
			$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
			$rs = mysqli_query($conn1, $sql1);
				while ($row=mysqli_fetch_array($rs))
				{
					extract($row);
					$nav[$image_pk]=$image_document_name;
					$count++;
				}
	
			$sql1="SELECT * FROM tbl_images WHERE image_pk=".$img_id;
			$rs = mysqli_query($conn1, $sql1);
				while ($row=mysqli_fetch_array($rs))
				{
					extract($row);
					$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
					$download_url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
					echo "<table width='100%'>";
						echo "<tr>";
						echo "<td align='left'>";
						
						$url_full="<a href='full.php?img_id=".$img_id."&url=".$url; 
						if($image_width>$image_height){$url_full.="&orr=landscape' target='_blank'>";} else {$url_full.="&orr=portrait' target='_blank'>";}
						
						echo $url_full; 
							if($image_width>$image_height)
							{
								echo "<img src='".$url."' width='720'>";
								$compact=720;
							}
							else
							{
								echo "<img src='".$url."' height='580'>";
								$compact=720;
							}
						echo "</a>";	
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td valign='top'>";
							$sql2="SELECT * FROM tbl_image_bio WHERE image_bio_image_document_name='".$image_document_name."'";
							$rs2 = mysqli_query($conn2,$sql2);
							while ($row2=mysqli_fetch_array($rs2))
							{
							extract($row2);
								if($image_bio_image_title!="")
								{
								echo "<h3>".$image_bio_image_title."</h3>";
								}
							}
						
							echo "<li>".$artist_namedb." | ".$serie_namedb; //." | ".$image_document_name;
							echo "<li><a href=".$download_url." download>download</a> (".$image_width."px -".$image_height." px)".PHP_EOL;
				
						$sql2="SELECT * FROM tbl_tags,tbl_link_tag WHERE link_tag_tag_fk=tag_pk AND link_tag_image_document_name='".$image_document_name."'";
						$csv="";
						$counttag=0;
						$rs2 = mysqli_query($conn2, $sql2);

							while ($row2=mysqli_fetch_array($rs2))
							{
							extract($row2);
							$csv.=$tag.", ";
							$counttag+=1;
							}
	
							if($counttag>0)
							{
							$csv = rtrim($csv, ', ');
							}
							else
							{
							echo "<li>tags: (0)<br><hr>";
							}
							
						echo "</td>";
					echo "</tr>";
				echo "</table>".PHP_EOL;
				echo "<table width='".$compact."'>";
				echo "<tr>";
				echo "<td align='left'>";
				
				foreach ($nav as $key=>$value)
				{
				$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/thumb/".$value;
				$url1="?actie=zoom&ser_id=".$ser_id."&art_id=".$art_id."&img_id=".$key."&image_document_name=".$value."&url=".$url;
				$url2="?actie=toon_tags&img_id=".$key."&image_document_name=".$value."&ser_id=".$ser_id."&art_id=".$art_id;
				
				echo "<a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."'; parent.tags.location='tags_manage.php".$url2."';\" href=\"#\">";
				echo "<img src='".$url."' width='100' height='100' hspace='2' vspace='2'>";
				echo "</a>".PHP_EOL;
				}
				echo "</td>";
				echo "</tr>";
				echo "</table>".PHP_EOL;
		}		
		//echo "<hr>";
		}	
		
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_alles")
//----------------------------------------------------------------------------------------------------------------------------------------------------
  {

	$size=$_GET["size"];

	echo "<H1>Preview</H1>";
	echo "<H2>Tags Beheren</H2>";	
	
$letter="";
if(isset($_GET["letter"]))
{
	$letter=$_GET["letter"];
}
else
{
	$letter="a-z";
}

$abc = array ("a-z","a", "b", "c", "d", "e","f", "g", "h", "i", "j", "k", "l", "m", "n", "o","p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

$abc_string="";
foreach($abc as $val) 
{
	if($val==$letter)
	{
		$abc_string.="<a href='preview_manage.php?actie=toon_alles&size=".$size."&letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
	}
	else
	{
		$abc_string.="<a href='preview_manage.php?actie=toon_alles&size=".$size."&letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";	
	}
}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";

echo "<p>".$abc_string."</p>";	

if($letter!="a-z")
{
	$sql2="SELECT * FROM tbl_images,tbl_series,tbl_artists WHERE tbl_artists.artist_pk=serie_artist_fk AND image_serie_fk=serie_pk AND artist_name LIKE '".$letter."%' ORDER BY artist_name,serie_name";
	echo "<h3>Fotografen (".$letter.")</h3>\n";
}
else
{
	$sql2="SELECT * FROM tbl_images,tbl_series,tbl_artists WHERE tbl_artists.artist_pk=serie_artist_fk AND image_serie_fk=serie_pk ORDER BY artist_name,serie_name";
	echo "<h3>Fotografen (a-z)</h3>\n";
}
	
	$folder_name_to_show="";
	$artist_to_show="";
	
	$rs = mysqli_query($conn1, $sql2);
	$countrs=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$countrs+=1;
			$art_id="";
			$im_w=$image_width;
			$im_h=$image_height;
			
			if($folder_name_to_show != $serie_folder_name || $artist_to_show != $artist_name)
			{
				
				if($letter!="a-z")
					{
						$artist_name_show=substr_replace(substr($artist_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
					}
					else
					{
						$artist_name_show=$artist_name;
					}
				
				$url1="?actie=serie&ser_id=".$serie_pk."&art_id=".$serie_artist_fk;
				$url2="?actie=toon_reeks&ser_id=".$serie_pk."&art_id=".$serie_artist_fk;
					
				if($size==60)
				{
					echo "<hr><p>".$artist_name_show." - <a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."';parent.tags.location='tags_manage.php".$url2."';\" href=\"#\">".$serie_name."</a></p>\n";
				}
				else
				{
					echo "<hr><h2>".$artist_name_show."<br><a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."';parent.tags.location='tags_manage.php".$url2."';\" href=\"#\">".$serie_name."</a></h2>\n";
				}
			$folder_name_to_show=$serie_folder_name;
			$artist_to_show=$artist_name;
			}
					$serie_name_url_enc=$serie_folder_name;
					$artist_name_url_enc=$artist_folder_name;
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/thumb/".$image_document_name;
			
			$url1="?actie=zoom&ser_id=".$serie_pk."&art_id=".$serie_artist_fk."&img_id=".$image_pk."&image_document_name=".$image_document_name."&url=".$url;
			$url2="?actie=toon_tags&img_id=".$image_pk."&image_document_name=".$image_document_name."&ser_id=".$serie_pk."&art_id=".$serie_artist_fk;
				
			echo "<a onclick=\"javascript:parent.preview.location='preview_manage.php".$url1."'; parent.tags.location='tags_manage.php".$url2."';\" href=\"#\">";
			if($im_w>$im_h)
			{
				$f=$im_h/$size;	
				echo "<img src='".$url."'  width='".$im_w/$f."' height='".$size."' hspace='2' vspace='2'>";

			}
			else
			{
				$f=$im_h/$size;
				echo "<img src='".$url."'  width='".$im_w/$f."' height='".$size."' hspace='2' vspace='2'>";
			}
			//echo "<img src='".$url."' width='".$size."' height='".$size."' hspace='2' vspace='2'>";
			echo "</a>\n";
		}
	
  }
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_tag_management")
//----------------------------------------------------------------------------------------------------------------------------------------------------
  {
	 
	 $letter="";
	
	if(isset($letter))
	{
		$letter=$letter;
	}
	else
	{
		$letter="a-z";
	}

	$abc = array ("a-z","a", "b", "c", "d", "e","f", "g", "h", "i", "j", "k", "l", "m", "n", "o","p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

	$abc_string="";
	foreach($abc as $val) 
	{
		if($val==$letter)
		{
		$abc_string.="<a href='preview_manage.php?actie=toon_tag_management&letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
		}
		else
		{
		$abc_string.="<a href='preview_manage.php?actie=toon_tag_management&letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";	
		}
	}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";
	
	
if($letter!="a-z")
{
	$sql1="SELECT * FROM tbl_tags,tbl_link_tag WHERE link_tag_tag_fk=tag_pk  AND tag LIKE '".$letter."%' ORDER BY tag,link_tag_image_document_name";
}
else
{
	 $sql1="SELECT * FROM tbl_tags,tbl_link_tag WHERE link_tag_tag_fk=tag_pk ORDER BY tag,link_tag_image_document_name";
}

echo "<p>".$abc_string."</p>";	

	 echo "<hr><table width='100%'>";
	  
		$rs = mysqli_query($conn1, $sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
				echo "<tr>";
					echo "<td>";
					echo "<p>".$tag."</p>";
					echo "</td>";
					echo "<td>";
					echo "<p>".$link_tag_image_document_name."</p>";
					echo "</td>";
					echo "<td>";
					echo "<a target='tags' href='tags_manage.php?actie=remove_existing_tag&tag_pk=".$tag_pk."&image_document_name=".$link_tag_image_document_name."'>delete</a>";
					echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td colspan=3>";
					echo "<hr>";
					echo "</td>";
				echo "</tr>";
				
		}
	 echo "</table>";	  
  }
//---------------------------------------------------------------------------------------------------------------------------------------------------- 
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>