<?PHP
require_once("data/includes/connection_inc.php");
session_start();

$url=$_REQUEST["url"];
$orr=$_REQUEST["orr"];
$img_id=$_REQUEST["img_id"];

$val="SELECT * FROM tbl_images WHERE image_pk=".$img_id;

$rs = mysqli_query($conn1, $val);
while ($row=mysqli_fetch_array($rs))
	{
	extract($row);
	$width=$image_width;
	$height=$image_height;
	}

if($orr=="portrait")
{
	echo "<img src='".$url."' width='100%'>";
}
else
{
	echo "<img src='".$url."' heigth='100%'>";
}

require("connection_close_inc.php");
?>