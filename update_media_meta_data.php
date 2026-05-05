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
$time_start_full = microtime(true); 


		$sql1="SELECT * FROM tbl_artist_media";
		$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$time_start = microtime(true); 
				$artist_media_description="";
				$artist_media_keywords="";
				if($artist_media_type!="03facebook_official" AND $artist_media_type!="04facebook" AND $artist_media_type!="07instagram")
				{
					if(get_meta_tags($artist_media_content)) 
					{
  					$meta_tags = get_meta_tags($artist_media_content);
					if(isset($meta_tags["description"])) {$artist_media_description=$meta_tags["description"];}
					if(isset($meta_tags["keywords"])){$artist_media_keywords=$meta_tags["keywords"];}
					} 
					else 
					{
					$artist_media_description="";
					$artist_media_keywords="";	
					}		
				}
			$sql2="UPDATE tbl_artist_media SET artist_media_description='".$artist_media_description."', artist_media_keywords='".$artist_media_keywords."' WHERE artist_media_pk='".$artist_media_pk."'";
			echo $sql2."<br>";
			mysqli_query($conn2,$sql2);
			echo "<code style='color:#ABB2B9'>Meta request: ". (microtime(true) - $time_start)." sec.</code><br>";

		}	

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
echo "<code style='color:#ABB2B9'>Totale meta request tijd: ". (microtime(true) - $time_start_full)." sec.</code><br>";
echo "<hr>";
//----------------------------------------------------------------------------------------------------------------------------------------------------
require("connection_close_inc.php");
?>