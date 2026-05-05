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

echo "<!DOCTYPE html><HTML>\n<HEAD>\n<META charset=\"UTF-8\">\n<TITLE>FIDK</TITLE>\n";
//echo "<script async src='https://cse.google.com/cse.js?cx=51c1a24db6bf3c4f3'></script>";
if($actie=="bio")
{
echo "<link href=\"https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css\" rel=\"stylesheet\">";
echo "<script src=\"https://code.jquery.com/jquery-3.5.1.min.js\"></script>";
echo "<script src=\"https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js\"></script>";
// echo "<link href=\"https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css\" rel=\"stylesheet\">";
// echo "<script src=\"https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js\"></script>";
echo "<link href=\"summernote/summernote.min.css\" rel=\"stylesheet\">";
echo "<script src=\"summernote/summernote.min.js\"></script>";
echo "<script src=\"summernote/lang/summernote-nl-NL.js\"></script>";
}
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n";

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="naam_fotograaf_aanpassen")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	
		echo "<table>";
		$sql1="SELECT * FROM tbl_artist_bio WHERE artist_bio_pk=".$art_id;
		$rs = mysqli_query($conn1, $sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$artist_bio_pk=$artist_bio_pk;
		}	
		
		$sql1="SELECT * FROM tbl_artists WHERE artist_pk=".$art_id;
		$rs = mysqli_query($conn1, $sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$artist_folder_name=$artist_folder_name;
			$old_artist_name=$artist_name;
		}	
		
		//in alle db aanpassen
		$new_artist_name=str_replace("'","&apos;",$new_artist_name);
		
		$sql1="UPDATE tbl_artists SET artist_name='".$new_artist_name."' WHERE artist_pk=".$art_id;
		$rs = mysqli_query($conn1, $sql1);
	
		$sql1="UPDATE tbl_artist_bio SET artist_bio_artist_name='".$new_artist_name."' WHERE artist_bio_pk=".$artist_bio_pk;
		$rs = mysqli_query($conn1, $sql1);
		
		$sql_select="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$old_artist_name."'";
		$rs = mysqli_query($conn1, $sql_select);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$sql2="UPDATE tbl_artist_media SET artist_media_artist_name='".$new_artist_name."' WHERE artist_media_pk='".$artist_media_pk."'";
			$rs2 = mysqli_query($conn2, $sql2);
		}	

		//serie_corrections_serie_foldername in UNIQUE
		$sql1="INSERT IGNORE INTO tbl_artist_corrections (artist_corrections_artist_name, artist_corrections_artist_foldername) VALUES ('".$new_artist_name."','".$artist_folder_name."')";
		$rs = mysqli_query($conn1, $sql1);
	
		$artist_name=$new_artist_name;
		$art_id=$art_id;
		$actie="serie_beheren";

}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="serienaam_aanpassen")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
		$sql1="SELECT * FROM tbl_series WHERE serie_pk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
		echo "<table>";
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$serie_folder_namedb=$serie_folder_name;
			$old_serie_name=$serie_name;
			$serie_pkdb=$serie_pk;
		}	
		
		//in alle db aanpassen
		$new_serie_name=str_replace("'","&apos;",$new_serie_name);

		$sql1="UPDATE tbl_series SET serie_name='".$new_serie_name."' WHERE serie_pk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
			
			$tbl="tbl_serie_bio";
			$col="serie_bio_serie_name";
			
			if(fnc_check_results($tbl,$col,$old_serie_name))
			{
				$sql1="UPDATE tbl_serie_bio SET serie_bio_serie_name='".$new_serie_name."' WHERE serie_bio_pk=".$serie_pkdb;	
			}	
			else	
			{
				$sql1="INSERT INTO tbl_serie_bio (serie_bio_serie_name,serie_bio_pk) VALUES ('".$new_serie_name."',".$serie_pkdb.")";		
			}			
			
		$rs = mysqli_query($conn1, $sql1);
		//serie_corrections_serie_foldername in UNIQUE
		$sql1="INSERT IGNORE INTO tbl_serie_corrections (serie_correctione_serie_name, serie_corrections_serie_foldername) VALUES ('".$new_serie_name."','".$serie_folder_namedb."')";
		$rs = mysqli_query($conn1, $sql1);
	
		$serie_name=$new_serie_name;
		//dan na fill altijd kijken of er namen in db aangepast moeten worden
	 	$actie="serie_beheren";
	
	
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="insert_media")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	
	$artist_media_description="";
	$artist_media_keywords="";	
	if($media_type!="03facebook_official" AND $media_type!="04facebook" AND $media_type!="07instagram")
	{
		if(get_meta_tags($media_content)) 
		{
  			$meta_tags = get_meta_tags($media_content);
			if(isset($meta_tags["description"])) {$artist_media_description=$meta_tags["description"];}
			if(isset($meta_tags["keywords"])){$artist_media_keywords=$meta_tags["keywords"];}
		} 
		else 
		{
			$artist_media_description="";
			$artist_media_keywords="";	
		}		
	}
	
	
	$sql1="INSERT INTO tbl_artist_media (artist_media_artist_name,concerning,artist_media_type,artist_media_content,artist_media_description,artist_media_keywords) VALUES ('".$artist_name."','".$concerning."','".$media_type."','".$media_content."','".$artist_media_description."','".$artist_media_keywords."' )";		
	//echo $sql1;
	$rs = mysqli_query($conn1, $sql1);
	$actie="media";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="afbeelding_bio_aanpassen")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{

		$tbl="tbl_image_bio";
		$col="image_bio_image_document_name";
		
		$afbeelding_titel=str_replace("'","&apos;",$afbeelding_titel);
		$afbeelding_bio=str_replace("'","&apos;",$afbeelding_bio);
		
			if(fnc_check_results($tbl,$col,$image_document_name))
			{
				$sql1="UPDATE tbl_image_bio SET image_bio_image_title=\"".$afbeelding_titel."\",image_bio_image_txt=\"".$afbeelding_bio."\" WHERE image_bio_image_document_name=\"".$image_document_name."\"";
			}	
			else	
			{
				$sql1="INSERT INTO tbl_image_bio (image_bio_image_document_name,image_bio_image_title,image_bio_image_txt) VALUES (\"".$image_document_name."\",\"".$afbeelding_titel."\",\"".$afbeelding_bio."\")";		
			}	
			
		$rs = mysqli_query($conn1, $sql1);

		$actie="serie_beheren";
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="delete_img")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	echo "<H1>Collectie Beheren</H1>";
	echo "<H2>Artist</H2>".PHP_EOL;	

	$sql1="DELETE FROM tbl_images WHERE image_document_name='".$image_document_name."'";  
	$sql2="DELETE FROM tbl_link_tag WHERE link_tag_image_document_name='".$image_document_name."'"; 
	$rs = mysqli_query($conn1, $sql1);
	$rs = mysqli_query($conn1, $sql2);
	
	if (file_exists($thumb))
	{
		if(unlink($thumb))
		{
			echo "<p>Thumnail voor ".$image_document_name." succesvol verwijderd<br>.";
		}
	}
	else
	{
     echo "<p>Thumbnail voor ".$image_document_name." niet gevonden.<br>";
	}
	
	if (file_exists($url))
	{
		if(unlink($url))
		{
			echo "Bestand ".$image_document_name." succesvol verwijderd</p>.";
		}
	}
	else
	{
     echo "Bestand ".$image_document_name." niet gevonden.</p>";
	}
	
	$actie="serie_beheren";
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="serie_beheren")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
		echo "<H1>".$artist_name."</H1>";
		echo "<H2>".$serie_name."</H2>".PHP_EOL;
		
		$url3="&ser_id=".$ser_id."&art_id=".$art_id."&artist_name=".$artist_name."&artist_folder_name=".$artist_folder_name."&serie_name=".$serie_name;
		echo "<table><tr><td><form action='preview_manage_col.php?actie=naam_fotograaf_aanpassen".$url3."' name='artist_name_change' id='artist_name_change' method='post'><input id='new_artist_name' name='new_artist_name' value='".$artist_name."'><input type='submit' value='Fotograaf wijzigen'></form></td></tr></table>";

		$url4="&art_id=".$art_id."&ser_id=".$ser_id."&artist_name=".$artist_name."&artist_folder_name=".$artist_folder_name."&serie_name=".$serie_name;
		echo "<table><tr><td><form action='preview_manage_col.php?actie=serienaam_aanpassen".$url4."' name='serie_name_change' id='serie_name_change' method='post'><input id='new_serie_name' name='new_serie_name' value='".$serie_name."'><input type='submit' value='Serie wijzigen'></form></td></tr></table><hr>";

		$url5="ser_id=".$ser_id."&art_id=".$art_id."&artist_name=".$artist_name."&artist_folder_name=".$artist_folder_name."&serie_name=".$serie_name;

		$sql1="SELECT * FROM tbl_series JOIN tbl_images ON serie_pk=image_serie_fk WHERE serie_pk=".$ser_id;
		$rs = mysqli_query($conn1, $sql1);
		echo "<table width='100%' cellspacing='4' cellpadding='4'>";
		while ($row=mysqli_fetch_array($rs))
		{

			extract($row);
			
			echo "<tr>";
			echo "<td valign='top'>";
			$img_url= "fidk/".$artist_folder_name."/".$serie_folder_name."/".$image_document_name;
			$thumb_url= "fidk/".$artist_folder_name."/".$serie_folder_name."/thumb/".$image_document_name;
			echo "<img src='".$img_url."' width='180'>";
			echo "</td><td valign='top'>";
			echo "<form action='preview_manage_col.php?actie=afbeelding_bio_aanpassen&image_document_name=".$image_document_name."&".$url5."' name='afbeelding_titel_aanpassen' id='afbeelding_titel_aanpassen' method='post'>";

				$sql2="SELECT *,count(*) AS nr FROM tbl_image_bio WHERE image_bio_image_document_name='".$image_document_name."'";
				$rs2 = mysqli_query($conn2, $sql2);
				while ($row2=mysqli_fetch_array($rs2))
				{
					extract($row2);
					if($nr>0)
					{
						echo "<p><input id='afbeelding_titel' name='afbeelding_titel' value='".$image_bio_image_title."' size='50'> (titel)<br><br>";
						echo "<input id='afbeelding_bio' name='afbeelding_bio' value='".$image_bio_image_txt."' size='50'> (bio)</p>";
					}	
					else	
					{
						echo "<p><input id='afbeelding_titel' name='afbeelding_titel' value='' size='50'> (titel)<br><br>";
						echo "<input id='afbeelding_bio' name='afbeelding_bio' value='' size='50'> (bio)</p>";
					}			
				}
			echo "<input type='submit' value='wijzigen'></form>";
			echo "<p align='left'><a href='preview_manage_col.php?actie=delete_img&url=".$img_url."&thumb=".$thumb_url."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&serie_name=".$serie_name."&artist_name=".$artist_name."&artist_folder_name=".$artist_folder_name."' style='color:red;'>definitief verwijderen</a> (".$image_width." x ".$image_height.")</p>";
			echo "</td>";
			echo "</tr>";
			echo "<tr><td colspan='2'><hr>";
			echo "<td></td></tr>";
		}
		
}

//--------------------------------------------------------------------------
if($actie=="bio_update")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	if(isset($artist_name))
	{
	$artist_bio_txt=htmlspecialchars($bio,ENT_QUOTES);
	$art_id=$_GET["art_id"];
	
	$tbl="tbl_artist_bio";
	$col="artist_bio_artist_name";
			
		if(fnc_check_results($tbl,$col,$artist_name))
		{
			$sql1="UPDATE tbl_artist_bio SET artist_bio_artist_name='".$artist_name."',artist_bio_txt='".$artist_bio_txt."' WHERE artist_bio_artist_name='".$artist_name."'";
			//echo $sql1;
			$rs = mysqli_query($conn1, $sql1);
		}
		else
		{
			if($artist_bio_txt!="")
			{
			$sql1="INSERT INTO tbl_artist_bio (artist_bio_pk,artist_bio_artist_name,artist_bio_txt) VALUES (".$art_id.",'".$artist_name."','".$artist_bio_txt."')";	
			//echo $sql1;
			$rs = mysqli_query($conn1, $sql1);
			}
		}
		

		$sql1="SELECT * FROM tbl_series WHERE serie_artist_fk=".$art_id;
		$rs1 = mysqli_query($conn1, $sql1);
		while ($row1=mysqli_fetch_array($rs1))
		{
			extract($row1);
			$seriedb="";
			$bio_count=0;
			
			
			$sql2="SELECT * FROM tbl_serie_bio WHERE serie_bio_serie_name='".$serie_name."'";
			$rs2 = mysqli_query($conn2, $sql2);

				while ($row2=mysqli_fetch_array($rs2))
				{
				extract($row2);
				$bio_count++;
				}
			
			$serie_bio_txt_to_db="";
			$serie_bio_txt_to_db=$_POST[str_replace(" ","_",$serie_name)];
			$serie_bio_txt_to_db=htmlspecialchars($serie_bio_txt_to_db,ENT_QUOTES);
			
			if($bio_count>0)
			{
			$sql2="UPDATE tbl_serie_bio SET serie_bio_txt='".$serie_bio_txt_to_db."' WHERE serie_bio_serie_name='".$serie_name."' AND serie_bio_artist_fk=".$art_id;
			//echo $sql2;
			$rs2 = mysqli_query($conn2, $sql2);
			}
			else
			{
				if($serie_bio_txt_to_db!="")
				{
			  	$sql2="INSERT INTO tbl_serie_bio (serie_bio_txt,serie_bio_serie_name,serie_bio_artist_name,serie_bio_artist_fk) VALUES ('".$serie_bio_txt_to_db."','".$serie_name."','".$artist_name."',".$art_id.")";
				//echo $sql2;
				$rs2 = mysqli_query($conn2, $sql2);
				}
			}
		}
	$actie='';
	}
}	
	
//----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	echo "<H1>Database</H1>".PHP_EOL;
	echo "<H2>Collectie Beheren</H2>".PHP_EOL;	
	
	
	//echo "<div class='gcse-search'></div>";
	
	echo "<H3>Databases genereren</H3>".PHP_EOL;
		if($_SESSION['user_pk']==2)
		{		
	echo "<p>Database volledig verwijderen, folder inlezen en nieuwe database genereren.</p>".PHP_EOL;
	echo "<li>fill_artists_series_images.php".PHP_EOL;
	echo "<p>Update corrections.</p>".PHP_EOL;
	echo "<li>update_corrections.php".PHP_EOL;
	echo "<hr><p>Attempt ALL</p>".PHP_EOL;
	echo "<li>fill_attempt_all.php".PHP_EOL;
	// echo "<hr><p>Folder uploaden</p>".PHP_EOL;
	// echo "<li><a href='upload_folder.php'>upload_folder.php</a>".PHP_EOL;
	echo "<hr><p>Media meta data (duurt ongeveer 2 minuten)</p>".PHP_EOL;
	echo "<li>update_media_meta_data.php".PHP_EOL;
		}
		else
		{
	echo "<p>Database volledig verwijderen, folder inlezen en nieuwe database genereren.</p>".PHP_EOL;
	echo "<li><a href='fill_artists_series_images.php'>fill_artists_series_images.php</a>".PHP_EOL;
	echo "<br><br><p>Update corrections.</p>".PHP_EOL;
	echo "<li><a href='update_corrections.php'>update_corrections.php</a>".PHP_EOL;
	
	echo "<hr><p>Attempt ALL</p>".PHP_EOL;
	echo "<li><a href='fill_attempt_all.php'>fill_attempt_all.php</a>".PHP_EOL;
	
	echo "<hr><p>Backup Database</p>".PHP_EOL;
	echo "<li><a href='backup_db.php'>backup_db.php</a>".PHP_EOL;
	
	echo "<hr><p>Media meta data (duurt ongeveer 2 minuten)</p>".PHP_EOL;
	echo "<li><a href='update_media_meta_data.php'>update_media_meta_data.php</a>".PHP_EOL;
	
		}
	}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="bio")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	if(isset($art_id))
	{
	echo "<h1>".$artist_name."</h1>".PHP_EOL;
	echo "<table><tr><td>".PHP_EOL;
	echo "<form id='artist bio' method='post' action='preview_manage_col.php?actie=bio_update&art_id=".$art_id."&artist_name=".$artist_name."'>".PHP_EOL;
		
		$sql1="SELECT * FROM tbl_artist_bio WHERE artist_bio_artist_name='".$artist_name."'";
		$rs = mysqli_query($conn1, $sql1);
		$biodb="";
		$image_new_pk=0;
		$bio_count=0;	
			while ($row=mysqli_fetch_array($rs))
			{
			extract($row);
			$biodb=$artist_bio_txt;
			}
			
				echo "<textarea id='bio' name='bio' rows='20' cols='140'>".$biodb."</textarea>".PHP_EOL;

	   echo "<script>";
      		echo "$('#bio').summernote({".PHP_EOL;
      		echo "placeholder: 'bio kunstenaar',".PHP_EOL;
      		echo "tabsize: 2,".PHP_EOL;
      		echo "height: 120, width: 1000,".PHP_EOL;
      		echo "toolbar: [['style', ['style']], ['font', ['bold', 'italic', 'underline', 'clear']], ['color', ['color']],['para', ['ul', 'ol', 'paragraph']], ['insert', ['link']], ['view', ['codeview', 'help']]]});".PHP_EOL;
     	echo "</script>".PHP_EOL;		
						

				$sql2="SELECT * FROM tbl_series WHERE serie_artist_fk=".$art_id;
				$rs2 = mysqli_query($conn2, $sql2);
				while ($row2=mysqli_fetch_array($rs2))
				{
					extract($row2);
					$seriedb="";
					
					echo "<h2>".$serie_name."</h2>".PHP_EOL;
					$serie_bio_txt_db="";
					$sql3="SELECT * FROM tbl_serie_bio WHERE serie_bio_serie_name='".$serie_name."'";
					$rs3 = mysqli_query($conn3, $sql3);

						while ($row3=mysqli_fetch_array($rs3))
						{
						extract($row3);
							$bio_count++;
							$serie_bio_txt_db=$serie_bio_txt;
						}
					
			
					if($bio_count>0)
					{
					//bestaat reeds
					echo "<textarea id='".str_replace(" ","_",$serie_name)."' name='".str_replace(" ","_",$serie_name)."' rows='20' cols='140'>".$serie_bio_txt_db."</textarea>".PHP_EOL;	
					}
					else
					{
					//bestaat nog niet
					echo "<textarea id='".str_replace(" ","_",$serie_name)."' name='".str_replace(" ","_",$serie_name)."' rows='20' cols='140'></textarea>".PHP_EOL;
					}
					
			echo "<script>";
      			echo "$('#".str_replace(" ","_",$serie_name)."').summernote({".PHP_EOL;
      			echo "placeholder: 'bio kunstenaar',".PHP_EOL;
      			echo "tabsize: 2,".PHP_EOL;
      			echo "height: 120, width: 1000,".PHP_EOL;
      			echo "toolbar: [['style', ['style']], ['font', ['bold', 'italic', 'underline', 'clear']], ['color', ['color']],['para', ['ul', 'ol', 'paragraph']], ['insert', ['link']], ['view', ['codeview', 'help']]]});".PHP_EOL;
     		echo "</script>".PHP_EOL;	
				}	
	
	
			echo "<br><br><input type='submit' value='Bio aanpassen'>".PHP_EOL;
			echo "</form></td></tr></table><br><br><hr>".PHP_EOL;
			//echo "<div class='gcse-search'></div>";
	}

}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="media")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	$time_start = microtime(true); 
	if(isset($art_id))
	{
	echo "<h1>".$artist_name."</h1><br><br>".PHP_EOL;
	echo "<table width='100%'><tr><td>".PHP_EOL;
	echo "<h3>Fotograaf</h3>";
		$sql1="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$artist_name."' AND concerning='".$artist_name."'";
		$rs = mysqli_query($conn1, $sql1);
		$biodb="";
		echo "<ul>";	
			while ($row=mysqli_fetch_array($rs))
			{
				extract($row);

				if($artist_media_type=="01website_official"){$artist_media_type_to_show="Offici&euml;le website: ";}
				if($artist_media_type=="02wiki_nl"){$artist_media_type_to_show="Wiki(nl): ";}
				if($artist_media_type=="03facebook_official"){$artist_media_type_to_show="Facebook officieel: ";}
				if($artist_media_type=="04facebook"){$artist_media_type_to_show="Facebook: ";}
				if($artist_media_type=="05website"){$artist_media_type_to_show="Website: ";}
				if($artist_media_type=="06wiki_en"){$artist_media_type_to_show="Wiki(en): ";}
				if($artist_media_type=="07instagram"){$artist_media_type_to_show="Instagram: ";}
				if($artist_media_type=="08vimeo"){$artist_media_type_to_show="Vimeo: ";}
				if($artist_media_type=="09youtube"){$artist_media_type_to_show="Youtube: ";}
				if($artist_media_type=="10pdf"){$artist_media_type_to_show="PDF: ";}
				
				echo "<table style='border: 1px solid #9a9a9a' width='80%'><tr><td style='padding: 10px;'>";
				echo "<p>".fnc_clean($concerning)." - ".$artist_media_type_to_show."<a href='".$artist_media_content."' target='_BLANK'>".$artist_media_content."</a></p>";

					if($artist_media_description!="") {echo "<p>".ucfirst(str_replace(".",".<br>",$artist_media_description))."</p>"; }
					if($artist_media_keywords!=""){echo "<p>Keywords:<i>".$artist_media_keywords."</i></p>";}

				echo "</td></tr></table><br>";
						
			}
		echo "</ul>";
		echo "<h3>Reeks(en)</h3>";
		$sql1="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$artist_name."' AND concerning<>'".$artist_name."' ORDER BY concerning";
		$rs = mysqli_query($conn1, $sql1);
		$biodb="";
		echo "<ul>";	
			while ($row=mysqli_fetch_array($rs))
			{
				extract($row);

				if($artist_media_type=="01website_official"){$artist_media_type_to_show="Offici&euml;le website: ";}
				if($artist_media_type=="02wiki_nl"){$artist_media_type_to_show="Wiki(nl): ";}
				if($artist_media_type=="03facebook_official"){$artist_media_type_to_show="Facebook officieel: ";}
				if($artist_media_type=="04facebook"){$artist_media_type_to_show="Facebook: ";}
				if($artist_media_type=="05website"){$artist_media_type_to_show="Website: ";}
				if($artist_media_type=="06wiki_en"){$artist_media_type_to_show="Wiki(en): ";}
				if($artist_media_type=="07instagram"){$artist_media_type_to_show="Instagram: ";}
				if($artist_media_type=="08vimeo"){$artist_media_type_to_show="Vimeo: ";}
				if($artist_media_type=="09youtube"){$artist_media_type_to_show="Youtube: ";}
				if($artist_media_type=="10pdf"){$artist_media_type_to_show="PDF: ";}
			
				$time_start = microtime(true); 
				echo "<table style='border: 1px solid #9a9a9a' width='80%'><tr><td style='padding: 10px;'>";
				echo "<p>".fnc_clean($concerning)." - ".$artist_media_type_to_show."<a href='".$artist_media_content."' target='_BLANK'>".$artist_media_content."</a></p>";

					if($artist_media_description!="") {echo "<p>".ucfirst(str_replace(".",".<br>",$artist_media_description))."</p>"; }
					if($artist_media_keywords!=""){echo "<p>Keywords:<i>".$artist_media_keywords."</i></p>";}

				echo "</td></tr></table><br>";
						
			}
		echo "</ul>";

	echo "<hr><h2>Toevoegen</h2>";
	echo "<p>over | type | inhoud</p>";
	echo "<form id='artist bio' method='post' action='preview_manage_col.php?actie=insert_media&art_id=".$art_id."&artist_name=".$artist_name."'>".PHP_EOL;
	echo "<select name='concerning'>";
			echo "<option value='".$artist_name."'>".$artist_name."</option>";
				$sql1="SELECT * FROM tbl_series WHERE serie_artist_fk=".$art_id;
				echo $sql1;
				$rs = mysqli_query($conn1, $sql1);
				while ($row=mysqli_fetch_array($rs))
				{
				extract($row);
				echo "<option value='".$serie_folder_name."'>".$serie_name."</option>";
				}
				echo "<option value='ander'>Ander</option>";
	echo "</select> ";
	
	
	echo "<select name='media_type'>";
	echo "<option value='01website_official'>Website - officieel</option>";
	echo "<option value='02wiki_nl'>Wiki (nl)</option>";
	echo "<option value='03facebook_official'>Facebook - officieel</option>";
	echo "<option value='04facebook'>Facebook</option>";
	echo "<option value='05website'>Andere website</option>";
	echo "<option value='06wiki_en'>Wiki (en)</option>";
	echo "<option value='07instagram'>Instagram</option>";
	echo "<option value='08vimeo'>Vimeo</option>";
	echo "<option value='09youtube'>Youtube</option>";
	echo "<option value='10pdf'>PDF</option>";

	echo "</select> ";
	echo "<input type='text' id='media_content' name='media_content' style='width:450px'>";
	echo "<br><br><input type='submit' value='Media toevoegen'>".PHP_EOL;
	echo "</form></td></tr></table><br><br><hr>".PHP_EOL;
	echo "<div class='gcse-search'></div>";
	}
	
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
// INACTIEF
//----------------------------------------------------------------------------------------------------------------------------------------------------

  if($actie=="upload_images_to_serie")
  {
	
	$new_image_name=$_POST[$new_image_name];
	$new_image_pk=$_POST[$new_image_pk];
	$upload_url=$_POST[$upload_url];
	
	//$files = array_filter($_FILES['upload']['name']); //something like that to be used before processing files.
    // Count # of uploaded files in arrayup
	$total = count($_FILES['upload']['name']);

	// Loop through each file
	for( $i=0 ; $i < $total ; $i++ ) {

		//Get the temp file path
		$tmpFilePath = $_FILES['upload']['tmp_name'][$i];

			//Make sure we have a file path
			if ($tmpFilePath != ""){
			//Setup our new file path
			 $newFilePath = "./".$upload_url.$_FILES['upload']['name'][$i];

				//Upload the file into the temp dir
				if(move_uploaded_file($tmpFilePath, $newFilePath)) 
				{
				//Handle other code here

				}
			}
		}
  }
//----------------------------------------------------------------------------------------------------------------------------------------------------  

	//frm_add_images_to_serie
	if($actie=="frm_add_images_to_serie" && $ser_id !=0 && $art_id !=0)
	{
	//er is op reeks gedrukt, herschrijf vorige image info	
		
	$sql1="SELECT * FROM tbl_series WHERE serie_pk=".$ser_id;
	$rs = mysqli_query($conn1, $sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					$serie_pkdb=$serie_pk;
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
					
					$artis_pkdb=$artist_pk;
					$artist_namedb=$artist_name;
					$artist_name_url_enc=$artist_folder_name;

		}	
		
	$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs = mysqli_query($conn1, $sql1);
	$image_new_pk=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			
					$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;						
					$url1="?actie=zoom&img_id=".$image_pk."&url=".$url;
					$image_new_pk=$image_pk;
					
					echo "<a onclick=\"javascript:parent.preview.location='preview_manage_col.php".$url1."';\" href='#'><img src='".$url."' width='100'></a>";				
		}	
	echo "<br><hr><p>Maak een nieuwe tag aan:</p>".PHP_EOL;
	$image_new_pk++;
	$new_image_name=$artist_name_url_enc."_".$serie_name_url_enc."_";
	$upload_url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/";
	//genereer tabel en formulier
	echo "<table><tr><td>".PHP_EOL;
	echo "<form id='img_upload' method='post' action='tags_manage.php?actie=upload_images_to_serie'>".PHP_EOL;
	echo "<input name='upload' type='file' multiple='multiple'>".PHP_EOL;
	echo "<input type='hidden' name='new_image_name' value='".$new_image_name."'>".PHP_EOL;
	echo "<input type='hidden' name='new_image_pk' value='".$image_new_pk."'>".PHP_EOL;
	echo "<input type='hidden' name='upload_url' value='".$upload_url."'>".PHP_EOL;
	echo "<input type='submit' value='Upload'>".PHP_EOL;
	echo "</form></td></tr></table><br><hr>".PHP_EOL;
	}
//----------------------------------------------------------------------------------------------------------------------------------------------------	
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>
