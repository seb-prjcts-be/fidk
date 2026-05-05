<?php
//----------------------------------------------------------------------------------------------------------------------------------------------------
if(!isset($actie)){
$actie="";	
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie!="all")
//----------------------------------------------------------------------------------------------------------------------------------------------------	
{
require_once("data/includes/connection_inc.php");
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
$time_start = microtime(true); 


		$sql1="SELECT * FROM tbl_artist_corrections";
		$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);

			$sql2="SELECT * FROM tbl_artists WHERE artist_folder_name='".$artist_corrections_artist_foldername."'";
			$rs2 = mysqli_query($conn2,$sql2);
				while ($row2=mysqli_fetch_array($rs2))
				{
				extract($row2);
				$old_artist_name=$artist_name;
				}
			
			$sql3="UPDATE tbl_artists SET artist_name='".$artist_corrections_artist_name."' WHERE artist_folder_name='".$artist_corrections_artist_foldername."'";
			echo $sql3."<br>";
			mysqli_query($conn2,$sql3);
			
			$sql4="UPDATE tbl_artist_bio SET artist_bio_artist_name='".$artist_corrections_artist_name."' WHERE artist_bio_artist_name='".$old_artist_name."'";
			echo $sql4."<br>";
			mysqli_query($conn2,$sql4);

		}	

//------------------------------------------------------------------------------------------------------------------------------------------------------------------

		$sql1="SELECT * FROM tbl_serie_corrections";
		$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);

			$sql2="SELECT * FROM tbl_series WHERE serie_folder_name='".$serie_corrections_serie_foldername."'";
			$rs2 = mysqli_query($conn2,$sql2);
			while ($row2=mysqli_fetch_array($rs2))
			{
			extract($row2);
			$old_serie_name=$serie_name;
			}
			
			$sql3="UPDATE tbl_series SET serie_name='".$serie_corrections_serie_name."' WHERE serie_folder_name='".$serie_corrections_serie_foldername."'";
			echo $sql3."<br>";
			mysqli_query($conn2,$sql3);
			
			$sql4="UPDATE tbl_serie_bio SET serie_bio_serie_name='".$serie_corrections_serie_name."' WHERE serie_bio_serie_name='".$old_serie_name."'";
			echo $sql4."<br>";
			mysqli_query($conn2,$sql4);

		}	
	
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start).'<br>';
echo "<hr>";
//----------------------------------------------------------------------------------------------------------------------------------------------------
require("connection_close_inc.php");
?>