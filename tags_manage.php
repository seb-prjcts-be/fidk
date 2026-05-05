<?php
require_once("data/includes/connection_inc.php");

$show_vars=0;

session_start();

// RESULTATEN OPHALEN
$url="tags_manage.php";

	foreach($_GET as $key => $val) 
	{
     if( is_array( $key ) ) {
         foreach( $key as $thing) {
         }
     } else {
		  ${$key}=$val;
     }
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------

	$actie="";
	if(isset($_GET["actie"])) {$actie=$_GET["actie"]; $url.="?actie=".$actie; } else {$actie="toon_alles";}

	$art_id=0;
	if(isset($_GET["art_id"])) {$art_id=$_GET["art_id"]; $url.="&art_id=".$art_id;}

	$ser_id=0;
	if(isset($_GET["ser_id"])) {$ser_id=$_GET["ser_id"]; $url.="&ser_id=".$ser_id;}

	$img_id=0;
	if(isset($_GET["img_id"])) {	$img_id=$_GET["img_id"]; $url.="&img_id=".$img_id;}
	
	$image_document_name="";
	if(isset($_GET["image_document_name"])) {$image_document_name=$_GET["image_document_name"]; $url.="&image_document_name=".$image_document_name;}

	$tag_pk=0;
	if(isset($_GET["tag_pk"])) {$tag_pk=$_GET["tag_pk"];}
	
	$new_tag_name="";

// RESULTATEN BEWERKEN ACHTER DE SCHERMEN
//----------------------------------------------------------------------------------------------------------------------------------------------------

if($actie=="stick_existing_tag")
	{
		$sql1="INSERT INTO tbl_link_tag (link_tag_image_document_name,link_tag_tag_fk) VALUES ('".$image_document_name."',".$tag_pk.")";
		mysqli_query($conn1,$sql1);
		$actie="toon_tags";
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="tag_rename")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		
		if(isset($_GET["old_tag_name"]))
		{
		$old_tag_name=$_GET["old_tag_name"];
		}	
		
		if(isset($_POST["tag_name"]))
		{
		$new_tag_name=$_POST["tag_name"];
		}	
		
		$sql1="UPDATE tbl_tags SET tag='".$new_tag_name."' WHERE tag='".$old_tag_name."'";

		$rs = mysqli_query($conn1,$sql1);
		$actie="toon_alles";
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="stick_existing_tag_to_series")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
	while ($row=mysqli_fetch_array($rs))
			{
			extract($row);
				$img_pk_to_insert=$image_pk;
				
				$sql2="SELECT * FROM tbl_link_tag WHERE link_tag_image_document_name='".$image_document_name."' AND link_tag_tag_fk=".$tag_pk;
				$rs2 = mysqli_query($conn2,$sql2);
				$count=0;
				while ($row2=mysqli_fetch_array($rs2))
				{
				extract($row2);
				$count+=1;
				}
				
				if($count==0)
				{
				$sql3="INSERT IGNORE INTO tbl_link_tag (link_tag_image_document_name,link_tag_tag_fk) VALUES ('".$image_document_name."',".$tag_pk.")";
				mysqli_query($conn3,$sql3);
				}

			}			
		$actie="toon_reeks";
	}
	
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="remove_existing_tag")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		$sql1="DELETE FROM tbl_link_tag WHERE link_tag_image_document_name='".$image_document_name."' AND link_tag_tag_fk=".$tag_pk;
		mysqli_query($conn1,$sql1);
		$actie="toon_tags";
	}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="remove_existing_tag_from_serie")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		
 foreach($_GET as $key => $val) 
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

		$sql1="SELECT * FROM tbl_link_tag JOIN tbl_tags ON tag_pk=link_tag_tag_fk WHERE link_tag_image_document_name LIKE '%".$serie_folder_name."%' AND tag='".$tag."'"; 
		$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
				{
				extract($row);
					$sql2="DELETE FROM tbl_link_tag WHERE link_tag_image_document_name='".$link_tag_image_document_name."' AND link_tag_tag_fk=".$tag_pk; 
					mysqli_query($conn2,$sql2);
				}
		
		$actie="toon_reeks";

	}	
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="delete_existing_tag")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		
		$tag=$_GET["tag"];
		$sql1="DELETE FROM tbl_link_tag WHERE link_tag_tag_fk=".$tag_pk;
		mysqli_query($conn1,$sql1);

		$sql2="DELETE FROM tbl_tags WHERE tag_pk=".$tag_pk;
		mysqli_query($conn2,$sql2);
		
		echo "<p>De tag <b>&quot;".$tag."&quot;</b> werd met succes verwijderd...</p><hr>";
		$actie="toon_alles";
	}	
	
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="new_tag_name_no_clicks")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		if(isset($_POST["new_tag_name"]))
		{
			$new_tag_name=strtolower($_POST["new_tag_name"]);
		}

		$sql1="INSERT IGNORE INTO tbl_tags (tag) VALUES ('".$new_tag_name."')";
		$rs = mysqli_query($conn1,$sql1);
		$actie="toon_alles";
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="new_tag_name")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
		if(isset($_POST["new_tag_name"]))
		{
			$new_tag_name=strtolower($_POST["new_tag_name"]);
		}
		
		if($new_tag_name!="")
		{
			$count=0;
			$sql1="SELECT * FROM tbl_tags where tag='".$new_tag_name."'";
			$rs= mysqli_query($conn1,$sql1);
			while ($row=mysqli_fetch_array($rs))
			{
			extract($row);
					
					$count+=1;		
			}
			
			//tag zit nog niet in de db
			if($count==0)
			{	
			//nieuwe tag inserten
			$sql1="INSERT INTO tbl_tags (tag) VALUES ('".$new_tag_name."')";
			mysqli_query($conn1,$sql1);
			
				if($img_id!=0)
				{
					//connectie maken met beeld
					$sql1="INSERT INTO tbl_link_tag (link_tag_image_document_name,link_tag_tag_fk) VALUES ('".$image_document_name."',(SELECT tag_pk FROM tbl_tags WHERE tag='".$new_tag_name."'))";
					mysqli_query($conn1,$sql1);
					$actie="toon_tags";
					}
				}	
				else
				{
					//tag zit wel al in de db
					//image koppelen met bestaande tag	
					if($img_id!=0)
					{
					$sql1="INSERT INTO tbl_link_tag (link_tag_image_document_name,link_tag_tag_fk) VALUES ('".$image_document_name."',(SELECT tag_pk FROM tbl_tags WHERE tag='".$new_tag_name."'))";
					mysqli_query($conn1,$sql1);
					$actie="toon_tags";
					}
				}
		}
	}	
// RESULTATEN TONEN
//--------------------------------------------------------------------------
	
echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n<H1>Tags</H1>\n<H2>Tags Beheren</H2>\n";

//--------------------------------------------------------------------------
if($actie=="toon_alles")
//----------------------------------------------------------------------------------------------------------------------------------------------------
	{
	$sql1="SELECT * FROM tbl_tags ORDER BY tag";
	$rs= mysqli_query($conn1,$sql1);
	echo "<table width='100%'>";
	
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					$tag_namedb=$tag;
					$tag_pkdb=$tag_pk;
					$counttags=0;
					$sql2="SELECT * FROM tbl_link_tag WHERE link_tag_tag_fk=".$tag_pkdb;
					$rs2= mysqli_query($conn2,$sql2);
					while ($row2=mysqli_fetch_array($rs2))
					{
					extract($row2);
					$counttags++;
					}	
					
					echo "<tr><td>";
					if($new_tag_name!="")
					{
						if($new_tag_name==$tag_namedb)
						{
						echo "<p><b>".$tag_namedb."</b></p></td><td><a style='color:red;' alt='Deze tag en alle referenties definitief verwijderen?' href='tags_manage.php?actie=delete_existing_tag&tag_pk=".$tag_pk."&ser_id=".$ser_id."&art_id=".$art_id."&tag=".$tag_namedb."'>verwijderen (".$counttags." ref.)</a>"; 
						}
						else
						{
						echo "<p>".$tag_namedb."</p></td><td><a style='color:red;' alt='Deze tag en alle referenties definitief verwijderen?' href='tags_manage.php?actie=delete_existing_tag&tag_pk=".$tag_pk."&ser_id=".$ser_id."&art_id=".$art_id."&tag=".$tag_namedb."'>verwijderen (".$counttags." ref.)</a>"; 
						}
						
					}
					else
					{
						echo "<p>".$tag_namedb."</p></td><td><a style='color:red;' alt='Deze tag en alle referenties definitief verwijderen?' href='tags_manage.php?actie=delete_existing_tag&tag_pk=".$tag_pk."&ser_id=".$ser_id."&art_id=".$art_id."&tag=".$tag_namedb."'>verwijderen (".$counttags." ref.)</a>"; 
					}
				//}
					
					echo "</td><td><a href='info_manage.php?actie=rename_tag&tag=".$tag_namedb."' target='info'>hernoem</a>";
					echo "</td></tr>";
		}
		echo "</table>";
$actie="toon_alles";
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_reeks")
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
echo "<p>Reeds gekoppelde tags aan reeks:</p>";
$csv="";
//SELECT * from tbl_link_tag where link_tag_image_document_name LIKE (select serie_folder_name,serie_name FROM tbl_series WHERE serie_name='Very Hidden People')

	$sql1="SELECT count(*) AS image_count FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs= mysqli_query($conn1,$sql1);
	while ($row=mysqli_fetch_array($rs)) {extract($row);}
	
	$tagcount=0;
	
	$sql1="SELECT serie_folder_name FROM tbl_series WHERE serie_pk=".$ser_id;
	$rs= mysqli_query($conn1,$sql1);
	while ($row=mysqli_fetch_array($rs)) {extract($row);}
	
	$sql1="SELECT * FROM tbl_link_tag lt JOIN tbl_tags t ON t.tag_pk = lt.link_tag_tag_fk WHERE link_tag_image_document_name LIKE  \"%".$serie_folder_name."%\""; 
	$rs= mysqli_query($conn1,$sql1);
	$count_tags=1;
	$count=0;
	$cur_tag="x";
	$new_tag="y";
	echo "<ul>";
		while ($row=mysqli_fetch_array($rs))
		{
		 extract($row);
			$cur_tag=$tag;
			
			if($new_tag==$cur_tag)
			{
				$count_tags++; 
			}
			elseif ($new_tag!=$cur_tag && $cur_tag!="x")
			{
				if($new_tag!="y" && $count_tags==$image_count)
				{
					$this_tag="<a href='tags_manage.php?actie=remove_existing_tag_from_serie&tag=".$new_tag."&ser_id=".$ser_id."'>".$new_tag."</a>";
					echo "<li>".$new_tag." <a href='tags_manage.php?actie=remove_existing_tag_from_serie&tag=".$new_tag."&serie_folder_name=".$serie_folder_name."&ser_id=".$ser_id."'>ontkoppel van reeks</a>";
					$csv.=$this_tag.", ";
					$count+=1;
				}
				$count_tags=1;
				$new_tag=$cur_tag;
			}
		}
		if($new_tag!="y" && $count_tags==$image_count)
		{
		$this_tag="<a href='tags_manage.php?actie=remove_existing_tag_from_serie&tag=".$new_tag."&ser_id=".$ser_id."'>".$cur_tag."</a>";
		echo  "<li>".$cur_tag." <a href='tags_manage.php?actie=remove_existing_tag_from_serie&tag=".$cur_tag."&serie_folder_name=".$serie_folder_name."&ser_id=".$ser_id."'>ontkoppel van reeks</a></ul>";
		$csv.=$this_tag.", ";
		$count+=1;
		}
		echo "</ul>";
		if($count>0){
		echo "<hr>";
		$csv = rtrim($csv, ', ');
		$csv.=".";
		echo $csv."<br><hr>";
		}


echo "<p>Koppel <b>alle beelden uit deze reeks</b> aan een tag.<p>";
	$sql1="SELECT * FROM tbl_tags ORDER BY tag";
	$rs= mysqli_query($conn1,$sql1);
	echo "<ul>";
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$tag_namedb=$tag;
			$tag_pkdb=$tag_pk;
			echo "<li>".$tag_namedb." <a href='tags_manage.php?actie=stick_existing_tag_to_series&tag_pk=".$tag_pk."&ser_id=".$ser_id."&art_id=".$art_id."'>koppel aan reeks</a>";		
		}		
	echo "</ul>";
	$actie="toon_reeks";
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_tags" && $img_id !=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
$csv="";
echo "<p>Reeds gekoppeld aan deze afbeelding:</p>";
echo "<ul>";
$sql1="SELECT * FROM tbl_tags,tbl_link_tag WHERE (tbl_tags.tag_pk=tbl_link_tag.link_tag_tag_fk) AND link_tag_image_document_name='".$image_document_name."' ORDER BY tbl_tags.tag";
	$rs= mysqli_query($conn1,$sql1);
	$count=0;
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$tag_namedb=$tag;
			$this_tag="<a href='tags_manage.php?actie=remove_existing_tag&tag_pk=".$tag_pk."&image_document_name=".$image_document_name."'>".$tag_namedb."</a>";
			echo "<li>".$tag." <a href='tags_manage.php?actie=remove_existing_tag&tag_pk=".$tag_pk."&image_document_name=".$image_document_name."'>ontkoppel</a>";
			$csv.=$this_tag.", ";
			$count+=1;
		}
		echo "</ul>";
		if($count>0){
		echo "<hr>";
		$csv = rtrim($csv, ', ');
		$csv.=".";
		echo $csv."<br><hr>";
		}
	}

if($img_id!=0)
{
echo "<p>Koppel tags aan deze afbeelding:</p>";

	$sql1="SELECT * FROM tbl_tags ORDER BY tag";
	$rs= mysqli_query($conn1,$sql1);
	echo "<ul>";
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$tag_namedb=$tag;
			$tag_pkdb=$tag_pk;
				$sql2="SELECT * FROM tbl_link_tag WHERE link_tag_tag_fk=".$tag_pk." AND link_tag_image_document_name='".$image_document_name."'";
				$rs2= mysqli_query($conn2,$sql2);
				$counttag=0;
				while ($row2=mysqli_fetch_array($rs2))
				{
					extract($row2);
					$counttag=$counttag+1;
				}
											
				if($counttag==0)
				{	
					echo "<li>".$tag_namedb." <a href='tags_manage.php?actie=stick_existing_tag&tag_pk=".$tag_pk."&img_id=".$img_id."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&art_id=".$art_id."'>koppel</a>";
				}
		}
		echo "</ul>";			
}

//----------------------------------------------------------------------------------------------------------------------------------------------------
if($show_vars!=0)
//----------------------------------------------------------------------------------------------------------------------------------------------------
{
echo "<ul>";
echo "<li>art_id: ".$art_id;
echo "<li>ser_id: ".$ser_id;
echo "<li>img_id: ".$img_id;
echo "<li>image_document_name: ".$image_document_name;
echo "/<ul>";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>