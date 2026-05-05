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
?>
<script>
function myFunction() {
  var x = document.getElementById("div_bio");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
<?php
echo "</HEAD>\n<BODY>\n";

// Navigation menu for public browsing
echo "<div class='public-nav' style='text-align: right; padding: 10px;'>";
echo "<a href='public_browse.php'>Home</a> | ";
echo "<a href='public_browse.php?actie=toon_alles&size=60'>Alle fotografen</a> | ";
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==1) {
    echo "<a href='frameset_manage.php'>Beheerder omgeving</a> | ";
    echo "<a href='index.php?actie=out'>Uitloggen (" . $_SESSION['user_name'] . ")</a>";
} else {
    echo "<a href='index.php'>Inloggen</a>";
}
echo "</div>";

//----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	echo "<H1>Fotografen in de kijker...</H1>";
	echo "<div style='margin-bottom: 20px;'><p>FIDK (Fotograaf in de kijker) is een dynamisch online platform dat oorspronkelijk ontwikkeld werd voor leerlingen uit de richting audiovisuele vorming. Op de site wordt op regelmatige basis een selectie fotografen en hun meest opvallende reeksen uitgelicht, waarbij de focus ligt op het inspireren van jonge makers en het delen van uiteenlopende visies op beeld. Omdat het platform slechts een tijdelijke weerslag is, is de collectie fotografen en series beperkt.</p><p><em>Disclaimer: deze informatie is illustratief en niet bedoeld als juridisch of officieel advies.</em></p></div>";
	echo "<H2>Bladeren</H2>";
	
	echo "<ul><li><a href='public_browse.php?actie=toon_alles&size=60'>Toon alles (thumbnails - 60px).</a>";
	echo "<li><a href='public_browse.php?actie=toon_alles&size=120'>Toon alles (thumbnails - 120px).</a></ul>";
	}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="search_bio")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
	echo "<H1>Bio zoekresultaten</H1>";
	echo "<H2>Fotografen</H2>";
	
		$sql1="SELECT * FROM tbl_artist_bio WHERE artist_bio_txt LIKE '%".$search_bio."%'";

		$rs = mysqli_query($conn1,$sql1);
		$count=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			
			$artist_bio_txt_to_show=str_replace($search_bio,"<span style='color:red;'>".$search_bio."</span>", $artist_bio_txt);
			$artist_bio_txt_to_show=str_replace(ucfirst($search_bio),"<span style='color:red;'>".ucfirst($search_bio)."</span>", $artist_bio_txt_to_show);
			echo "<p>".$artist_bio_artist_name."</p>";
			echo "<p>".$artist_bio_txt_to_show."</p>";
			$count++;
		}
		echo "<p>(".$count.")</p>";
	
	echo "<H2>Reeksen</H2>";
	
	$sql1="SELECT * FROM tbl_serie_bio,tbl_series  WHERE serie_name=serie_bio_serie_name AND serie_bio_txt LIKE '%".$search_bio."%'";
	$rs = mysqli_query($conn1,$sql1);
		$count=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			
			$serie_bio_txt_to_show=str_replace($search_bio,"<span style='color:red;'>".$search_bio."</span>", $serie_bio_txt);
			$serie_bio_txt_to_show=str_replace(ucfirst($search_bio),"<span style='color:red;'>".ucfirst($search_bio)."</span>", $serie_bio_txt_to_show);
			echo "<p>".$serie_bio_artist_name." - <a href='public_browse.php?actie=serie&ser_id=".$serie_pk."&art_id=".$serie_bio_artist_fk."'>".$serie_bio_serie_name."</a></p>";
			echo "<p>".$serie_bio_txt_to_show."</p>";
			$count++;
		}
		echo "<p>(".$count.")</p>";
}
		
//----------------------------------------------------------------------------------------------------------------------------------------------------	
	if($actie=="serie" && $ser_id !=0 && $art_id !=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	$sql1="SELECT * FROM tbl_series WHERE serie_pk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$serie_namedb=$serie_name;
			$serie_name_url_enc=$serie_folder_name;
		}
	
	$sql1="SELECT * FROM tbl_artists WHERE artist_pk=".$art_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			echo "<h1>".$artist_name."</h1>";
			$artist_namedb=$artist_name;
			$artist_name_url_enc=$artist_folder_name;
		}	
		
		echo "<h2>".$serie_name."</h2>";	
		
		$sql1="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$artist_name."' ORDER BY artist_media_type LIMIT 1";
		$rs = mysqli_query($conn1,$sql1);
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
			
			echo "<p>".$artist_media_type_to_show."<a href='".$artist_media_content."' target='_BLANK'>".$artist_media_content."</a></p>";
		}
		
		// Only show management links if logged in
		if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==1) {
		    echo "<p><a href='frameset_manage_col.php?actie=media&art_id=".$art_id."&artist_name=".$artist_name."'>media beheer</a> | <a href='generate_pdf.php?art_id=".$art_id."&ser_id=".$ser_id."'>genereer PDF</a></p>";
		} else {
		    echo "<p><a href='generate_pdf.php?art_id=".$art_id."&ser_id=".$ser_id."'>genereer PDF</a></p>";
		}
		
	$is_content=0;
	 $sql1="SELECT * FROM tbl_artist_bio WHERE artist_bio_artist_name='".$artist_name."'";
	 $rs = mysqli_query($conn1,$sql1);
	 $bio="";
		echo "<div id='div_bio'>";
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$bio=$artist_bio_txt;
			$bio=str_replace("\n","<br>",$bio);
				if($bio)
				{
					echo "<p>".htmlspecialchars_decode($bio)."</p>";
					$is_content++;
				}
		}
	
   
	$sql1="SELECT * FROM tbl_serie_bio WHERE serie_bio_serie_name='".$serie_name."' AND serie_bio_artist_fk=".$art_id;
	$rs = mysqli_query($conn1,$sql1);
	$bio="";
	
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$bio=$serie_bio_txt;
			$bio=str_replace("\n","<br>",$bio);
			if($bio)
			{
					echo "<p>".htmlspecialchars_decode($bio)."</p>";
					$is_content++;		
			}
		}
   echo "</div>";
   if($is_content>0)
   {
   		echo "<p><a onclick='myFunction()' href='#'>toon/verberg bio's</a></p><hr>";
	}
	else
	{
		echo "<hr>";
	}
	
	//------------------
	
	$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/thumb/".$image_document_name;
			$im_w=$image_width;
			$im_h=$image_height;
			
			if($im_w>$im_h)
			{
				$f=$im_h/60;	
				echo "<img src='".$url."'  width='".$im_w/$f."' height='60'> ";

			}
			else
			{
				$f=$im_h/60;
				echo "<img src='".$url."'  width='".$im_w/$f."' height='60'> ";
			}
		}
	echo "<br>";
	
	//----------------------
	
	$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
			$download_url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
			echo "<hr><table width='100%'>";
			echo "<tr>";
			echo "<td width=440>";
			echo "<img src='".$url."'  width='400'>";
			echo "</td>";
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
			
			echo "<li>".$artist_namedb;
			echo "<li>".$serie_namedb;
			$download_url=fnc_convert_text($download_url);
			echo "<li><a href=".$download_url." download>download</a> (".$image_width."px -".$image_height." px)";
		
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
		
			$counttag=0;
			$csv="";
			$sql2="SELECT * FROM tbl_tags,tbl_link_tag WHERE link_tag_tag_fk=tag_pk AND link_tag_image_document_name='".$image_document_name."'";
			$rs2 = mysqli_query($conn2,$sql2);
				while ($row2=mysqli_fetch_array($rs2))
				{
				extract($row2);
				$csv.=$tag.", ";
				$counttag+=1;
				}
	
			if($counttag>0)
			{
			$csv = rtrim($csv, ', ');
			echo "</ul><li>tags: ".$csv." (".$counttag.")";
			}
			else
			{
			echo "</ul><li>tags: (0)";
			}
		
			// Only show tag management if logged in
			if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==1) {
				$tag_this="";
				$tag_this.=" | <a href='frameset_manage.php?actie=tag_this";
				$tag_this.="&ser_id=".$ser_id;
				$tag_this.="&art_id=".$art_id;
				$tag_this.="&img_id=".$image_pk;
				$tag_this.="&image_document_name=".$image_document_name;
				$tag_this.="&url=".$url;
				$tag_this.="' target='_top'>tag nu</a>";
			
				echo $tag_this;
			}
			
			echo "</td>";
			echo "</tr>";
			echo "</table>";
		
		}
		echo "<a href='generate_pdf.php?art_id=".$art_id."&ser_id=".$ser_id."'>Genereer PDF</a>";
	}
//----------------------------------------------------------------------------------------------------------------------------------------------------
	if($actie=="search_images_to_tag" && $tag_pk !=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
  {

	echo "<h1>Preview</h1>";
	$folder_name_to_show="";
	$sql1="SELECT * FROM tbl_images,tbl_link_tag,tbl_series,tbl_artists WHERE tbl_artists.artist_pk=serie_artist_fk AND image_document_name=link_tag_image_document_name AND image_serie_fk=serie_pk AND link_tag_tag_fk=".$tag_pk;
	$rs = mysqli_query($conn1,$sql1);
	$countrs=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$countrs+=1;
			$art_id="";
			
			if($folder_name_to_show != $serie_folder_name)
			{
			echo "<hr><h2>".$artist_name.": ".$serie_name."</h2>";
			$folder_name_to_show=$serie_folder_name;
			}
			
			$serie_namedb=$serie_name;
			$serie_name_url_enc=$serie_folder_name;
			$artist_namedb=$artist_name;
			$artist_name_url_enc=$artist_folder_name;
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
			$download_url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
			echo "<hr><table width='100%'>";
			echo "<tr>";
			echo "<td width=440>";
			echo "<img src='".$url."'  width='400'>";
			echo "</td>";
			echo "<td valign='top'>";
			echo "<li>".$artist_namedb;
			echo "<li>".$serie_namedb;
			echo "<li>".$image_document_name;
			$download_url=fnc_convert_text($download_url);
			echo "<li><a href=".$download_url." download>download</a> (".$image_width."px -".$image_height." px)";
			
				$sql2="SELECT * FROM tbl_tags,tbl_link_tag WHERE link_tag_tag_fk=tag_pk AND link_tag_image_document_name='".$image_document_name."'";
				$csv="";
				$counttag=0;
				$rs2 = mysqli_query($conn2,$sql2);

				while ($row2=mysqli_fetch_array($rs2))
					{
					extract($row2);
					$csv.=$tag.", ";
					$counttag+=1;
		}
	
		if($counttag>0)
		{
		$csv = rtrim($csv, ', ');
		echo "<li>tags: ".$csv." (".$counttag.")";
		}
		else
		{
		echo "<p>Nog geen tags gekoppeld aan deze afbeelding...</p>";
		}
				
			echo "</td>";
			echo "</tr>";
			echo "</table>";
		}
		if($countrs==0)
		{
		echo "<p>Nog geen afbeeldingen gekoppeld aan deze tag...</p>";
		}
  }
//----------------------------------------------------------------------------------------------------------------------------------------------------
 if($actie=="toon_alles")
//----------------------------------------------------------------------------------------------------------------------------------------------------
  {

	//$size=$_GET["size"];

	echo "<h1>Preview</h1>";
	echo "<H2>Bladeren</H2>";	
	
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
		$abc_string.="<a href='public_browse.php?actie=toon_alles&size=".$size."&letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
	}
	else
	{
		$abc_string.="<a href='public_browse.php?actie=toon_alles&size=".$size."&letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";	
	}
}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";
	
	$folder_name_to_show="";
	$artist_to_show="";

echo "<p>".$abc_string."</p>";		

if($letter!="a-z")
{
	$sql1="SELECT * FROM tbl_images,tbl_series,tbl_artists WHERE tbl_artists.artist_pk=serie_artist_fk AND image_serie_fk=serie_pk AND artist_name LIKE '".$letter."%' ORDER BY artist_name,serie_name";
	echo "<h3>Fotografen (".$letter.")</h3>\n";
}
else
{
	$sql1="SELECT * FROM tbl_images,tbl_series,tbl_artists WHERE tbl_artists.artist_pk=serie_artist_fk AND image_serie_fk=serie_pk ORDER BY artist_name,serie_name";
	echo "<h3>Fotografen (a-z)</h3>\n";
}

	$rs = mysqli_query($conn1,$sql1);
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
				
				if($size==60)
				{
					echo "<hr><p>".$artist_name_show." - <a href='public_browse.php?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."'>".$serie_name."</a></p>";
				}
				else
				{
					echo "<hr><h2>".$artist_name_show."<br><a href='public_browse.php?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."'>".$serie_name."</a></h2>";
				}
				
				$folder_name_to_show=$serie_folder_name;
				$artist_to_show=$artist_name;
			}
				$serie_name_url_enc=$serie_folder_name;
				$artist_name_url_enc=$artist_folder_name;
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/thumb/".$image_document_name;
			
			
			
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
		}
	
  }
 //----------------------------------------------------------------------------------------------------------------------------------------------------
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>
