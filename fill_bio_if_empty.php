<?php
if(!isset($actie)){
$actie="";	
}

if($actie!="all")
{
	require_once("data/includes/connection_inc.php");
	function clean($string)
	{	 
    $string=str_replace("_"," ",$string);
	$string=ucwords($string);
	$string=str_replace(" Iii"," III",$string);
	$string=str_replace(" Ii"," II",$string);
	return $string;  
	}
}
$sql1="SELECT * FROM tbl_artists";
$rs = mysqli_query($conn1,$sql1);
while ($row=mysqli_fetch_array($rs))
{
	extract($row);
		$sql2_val_check="SELECT *,count(*) AS count FROM tbl_artist_bio WHERE artist_bio_artist_name='".$artist_name."'";
		echo $sql2_val_check."<br>";
		$rs2 =  mysqli_query($conn1,$sql2_val_check);
		$count_replacements=0;
			while ($row2=mysqli_fetch_array($rs2))
			{
			extract($row2);
				if($count==0)
				{
					$statement1="";
					$statement1.="INSERT INTO tbl_artist_bio (artist_bio_artist_name) VALUES ";
					$statement1.="('".$artist_name."'); ";
						mysqli_query($conn1,$statement1);
						echo $artist_bio_artist_name.": toegevoegd...<br>";
						$count_replacements++;
				}
				else
				{
				echo $artist_bio_artist_name.": bestaat al...<br>";
				}
			}	
}


$statement1="";
$statement2="";

//---------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<br><hr><br>";
$sql1="SELECT * FROM tbl_series";
$rs = mysqli_query($conn1,$sql1);
$statement1="INSERT INTO tbl_serie_bio (serie_bio_artist_fk,serie_bio_artist_name,serie_bio_serie_name,serie_bio_txt) VALUES ";
while ($row=mysqli_fetch_array($rs))
{
	extract($row);
		$count_replacements=0;
		$serie_artist_namedb="";
		$sql2val_check="SELECT *,count(*) AS count FROM tbl_serie_bio WHERE serie_bio_serie_name='".$serie_name."' AND serie_bio_artist_fk=".$serie_artist_fk; 
		$rs2 = mysqli_query($conn2,$sql2val_check);
			while ($row2=mysqli_fetch_array($rs2))
			{
			extract($row2);
			
					$sql3="SELECT * FROM tbl_artists WHERE artist_pk=".$serie_artist_fk;
					$rs3 = mysqli_query($conn3,$sql3);
						while ($row3=mysqli_fetch_array($rs3))
						{
							extract($row3);
							$serie_artist_namedb=$artist_name;
						}
				
						if($count==0)
						{						
						$statement1.="(".$serie_artist_fk.",'".$serie_artist_namedb."','".$serie_name."',''), ";
						$count_replacements++;
						}
						else
						{
						$statement2.="UPDATE tbl_serie_bio SET tbl_serie_bio_artist_name='".$serie_artist_namedb."' WHERE serie_bio_artist_fk=".$serie_artist_fk.", ";
						mysqli_query($conn3,$statement2);
						echo $serie_name.": updated...<br>";
						}
			}	
}
$statement1 = rtrim($statement1, ', '); 
$statement1.="; ";

$statement2 = rtrim($statement2, ', '); 
$statement2.="; ";
echo $statement2;

mysqli_query($conn1,$statement1);
mysqli_query($conn1,$statement2);

if($count_replacements!=0)
{
echo $statement1."<br>";
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------

echo "<br><hr><br>";
$sql1="SELECT * FROM tbl_images";
$rs = mysqli_query($conn1,$sql1);

//image_bio_image_document_name = UNIQUE
$statement1="INSERT IGNORE INTO tbl_image_bio (image_bio_image_document_name,image_bio_image_title,image_bio_image_txt) VALUES ";
while ($row=mysqli_fetch_array($rs))
{
	extract($row);			
	$statement1.="('".$image_document_name."','',''), ";
}

$statement1 = rtrim($statement1, ', '); 
$statement1.="; ";

echo $statement1."<br>";

mysqli_query($conn1,$statement1);

//---------------------------------------------------------------------------------------------------------------------------------------------------------------
if($actie!="all")
{
require("connection_close_inc.php");
}
?>
