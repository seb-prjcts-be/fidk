<?php
//----------------------------------------------------------------------------------------------------------------------------------------------------
$time_start = microtime(true); 

if(!isset($actie)){
$actie="";	
}


if($actie!="all")
{
	require_once("data/includes/connection_inc.php");
	require_once("data/includes/functions_inc.php");
}

$statement1="TRUNCATE TABLE tbl_artists;";
mysqli_query($conn1,$statement1);

$statement1="TRUNCATE TABLE tbl_series;";
mysqli_query($conn1,$statement1);

$statement1="TRUNCATE TABLE tbl_images;";
mysqli_query($conn1,$statement1);

//basismap:
$dir = "fidk/";

//----------------------------------------------------------------------------------------------------------------------------------------------------
//ARTISTS
//----------------------------------------------------------------------------------------------------------------------------------------------------

$id_artist="";


$statement1="";
$a = scandir($dir);
$statement1.="INSERT INTO tbl_artists (artist_name,artist_folder_name) VALUES ";

foreach ($a as $value) {
		if($value != '.' && $value != '..'){
		$clean_value=clean($value);
		$statement1.="('".$clean_value."','".$value."'), ";
		}
}
$statement1 = rtrim($statement1, ', ');
$statement1.="; ";

mysqli_query($conn1,$statement1);

echo $statement1."<br><br>";
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start).'<br>';
echo "<hr>";



//----------------------------------------------------------------------------------------------------------------------------------------------------
//SERIES
//----------------------------------------------------------------------------------------------------------------------------------------------------

$statement1="";
$statement2="";
$statement1.=("INSERT INTO tbl_series (serie_artist_fk,serie_name,serie_folder_name) VALUES ");

				$sql1="SELECT * FROM tbl_artists";
				$rs = mysqli_query($conn1,$sql1);
				while ($row=mysqli_fetch_array($rs))
					{
					extract($row);
					
						$dir2 = $dir.$artist_folder_name."/";
						//echo $dir2;
						$id_artist=$artist_pk;

						$a = scandir($dir2);
						foreach ($a as $value) 
						{
							if($value != '.' && $value != '..')
							{
							$clean_value=clean($value);		
							$statement1.="(".$id_artist.",'".$clean_value."','".$value."'), ";
							}
						
						}
					}
					
$statement1 = rtrim($statement1, ', ');
$statement1.=";";

echo $statement1."<br><br>";
mysqli_query($conn1,$statement1);
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start).'<br><hr>';


//----------------------------------------------------------------------------------------------------------------------------------------------------
//IMAGES
//----------------------------------------------------------------------------------------------------------------------------------------------------

$statement1="";
$statement2="";
$statement1.=("INSERT INTO tbl_images (image_serie_fk,image_artist_fk,image_document_name,image_official_title,image_width,image_height) VALUES ");

				$sql1="SELECT * FROM tbl_artists";
				$rs = mysqli_query($conn1,$sql1);
	
				while ($row=mysqli_fetch_array($rs))
					{
					extract($row);
						
						if($value != '.' && $value != '..')
						{
							$sql2="SELECT * FROM tbl_series WHERE serie_artist_fk=".$artist_pk;
							echo $sql2;
							$rs2 = mysqli_query($conn2,$sql2);
							while ($row2=mysqli_fetch_array($rs2))
							{
								extract($row2);
										
								$dir3 = $dir.$artist_folder_name."/".$serie_folder_name."/";
								echo $artist_folder_name;
								$id_artist=$artist_pk;
								$id_serie=$serie_pk;
								$a = scandir($dir3);
									foreach ($a as $value) 
									{
										if($value != '.' && $value != '..' && $value!='thumb')
										{
											if(is_file($dir3.$value))
											{
											$info = getimagesize($dir3.$value);	
											$width=$info[0];
											$height=$info[1];
												if(substr($dir3.$value, -4)==".jpg" || substr($dir3.$value, -4)==".JPG" || substr($dir3.$value, -4)==".png" || substr($dir3.$value, -4)==".PNG" || substr($dir3.$value, -4)=="JPEG" || substr($dir3.$value, -4)=="jpeg" )
												{
												$statement1.= "(".$id_serie.",".$id_artist.",'".$value."','',".$width.",".$height."), ";
												}
												
											}
										}
									}				
							}
						}
					}
$statement1 = rtrim($statement1, ', ');
$statement1.=";";

mysqli_query($conn1,$statement1);

echo $statement1."<br><br>";

echo 'Total execution time in seconds: ' . (microtime(true) - $time_start).'<br><hr>';
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie!="all")
{
require("connection_close_inc.php");
}

?>
