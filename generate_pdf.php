<?php
require_once("data/includes/connection_inc.php");
require_once("data/includes/functions_inc.php");
require('fpdf.php');

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



$img_url="fidk/";

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
			$artist_namedb=$artist_name;
			$artist_name_url_enc=$artist_folder_name;
		}
		
	 $is_content_artist=0;
	 $sql1="SELECT * FROM tbl_artist_bio WHERE artist_bio_artist_name='".$artist_name."'";
	 $rs = mysqli_query($conn1,$sql1);
	 $bio="";
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
			$bio=$artist_bio_txt;
			$bio=str_replace("\n","<br>",$bio);
				if($bio)
				{
					$bio_to_show_art=strip_tags($bio) ;
					$bio_to_show_art=str_replace("&#039;","'",$bio_to_show_art);
					$is_content_artist++;
				}
		}

	$is_content_serie=0;
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
					$bio_to_show_ser=strip_tags($bio);
					$bio_to_show_ser=str_replace("&#039;","'",$bio_to_show_ser);
					$is_content_serie++;		
			}
		}



$pdf = new FPDF('P','mm',array(210,290));

$pdf->SetTitle($artist_name);
$pdf->AddPage();

//$pdf->SetAutoPageBreak(1,100);
		
$pdf->SetFont('Arial','',32);
$pdf->Cell(40,10,$artist_name);
$pdf->Ln(24);

$pdf->SetFont('Arial','',14);
$pdf->Cell(40,10,$serie_name);	
$pdf->Ln(14);


$sql1="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$artist_name."' ORDER BY artist_media_type LIMIT 1";
	$rs = mysqli_query($conn1,$sql1);
	while ($row=mysqli_fetch_array($rs))
	{
	extract($row);
		if($artist_media_type=="01website_official"){$artist_media_type_to_show=utf8_decode("Officiële website: ");}
		if($artist_media_type=="02wiki_nl"){$artist_media_type_to_show="Wiki(nl): ";}
		if($artist_media_type=="03facebook_official"){$artist_media_type_to_show=utf8_decode("Facebook officieel: ");}
		if($artist_media_type=="04facebook"){$artist_media_type_to_show="Facebook: ";}
		if($artist_media_type=="05website"){$artist_media_type_to_show="Website: ";}
		if($artist_media_type=="06wiki_en"){$artist_media_type_to_show="Wiki(en): ";}
		if($artist_media_type=="07instagram"){$artist_media_type_to_show="Instagram: ";}
		if($artist_media_type=="08vimeo"){$artist_media_type_to_show="Vimeo: ";}
		if($artist_media_type=="09youtube"){$artist_media_type_to_show="Youtube: ";}
		
$pdf->Ln(14);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,10,$artist_media_type_to_show);	
$pdf->Ln(10);
$pdf->SetTextColor(0,0,255);
$pdf->Write(5,$artist_media_content,$artist_media_content);
$pdf->SetTextColor(0);
$pdf->Ln(14);		
	}

$pdf->SetFont('Arial','',10);

if($is_content_artist>0)
{
$pdf->Write(5,$bio_to_show_art);	
}
$pdf->Ln(14);

$pdf->SetFont('Arial','',10);
if($is_content_serie>0)
{
$pdf->Write(5,$bio_to_show_ser);
}

$pdf->Ln(14);
$pdf->AddPage();	
$sql1="SELECT * FROM tbl_images WHERE image_serie_fk=".$ser_id;
	$rs = mysqli_query($conn1,$sql1);
		while ($row=mysqli_fetch_array($rs))
		{
			extract($row);
					
			$url="fidk/".$artist_name_url_enc."/".$serie_name_url_enc."/".$image_document_name;
$x = $pdf->GetX();
$x+=7.5;
$y = $pdf->GetY();	
$y+=7.5;		
$pdf->Image($url,$x,$y,175,0,'','');	
$pdf->SetY(-32);
$pdf->SetX(24);


				$sql2="SELECT * FROM tbl_image_bio WHERE image_bio_image_document_name='".$image_document_name."'";
				$rs2 = mysqli_query($conn2,$sql2);
				while ($row2=mysqli_fetch_array($rs2))
				{
				extract($row2);
					if($image_bio_image_title!="")
					{
$pdf->SetFont('Arial','',10);
$pdf->Write(5,str_replace("&apos;","'",$image_bio_image_title));	
					}
				}
$pdf->AddPage();		
	}				
		
$sql1="SELECT * FROM tbl_artist_media WHERE artist_media_artist_name='".$artist_name."' ORDER BY artist_media_type";
	$rs = mysqli_query($conn1,$sql1);
	while ($row=mysqli_fetch_array($rs))
	{
	extract($row);
		if($artist_media_type=="01website_official"){$artist_media_type_to_show=utf8_decode("Officiële website: ");}
		if($artist_media_type=="02wiki_nl"){$artist_media_type_to_show="Wiki(nl): ";}
		if($artist_media_type=="03facebook_official"){$artist_media_type_to_show=utf8_decode("Facebook officieel: ");}
		if($artist_media_type=="04facebook"){$artist_media_type_to_show="Facebook: ";}
		if($artist_media_type=="05website"){$artist_media_type_to_show="Website: ";}
		if($artist_media_type=="06wiki_en"){$artist_media_type_to_show="Wiki(en): ";}
		if($artist_media_type=="07instagram"){$artist_media_type_to_show="Instagram: ";}
		if($artist_media_type=="08vimeo"){$artist_media_type_to_show="Vimeo: ";}
		if($artist_media_type=="09youtube"){$artist_media_type_to_show="Youtube: ";}
		
$pdf->Ln(14);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,10,$artist_media_type_to_show);	
$pdf->Ln(10);
$pdf->SetTextColor(0,0,255);
$pdf->Write(5,$artist_media_content,$artist_media_content);
$pdf->SetTextColor(0);
//$pdf->Ln(14);		

// if($artist_media_description!="") 
// {
	// $pdf->Write(5,htmlspecialchars_decode($artist_media_description)); 
// }

// if($artist_media_keywords!="")
// {
	// $artist_media_keywords="Keywords: ".$artist_media_keywords;
	// $pdf->Write(5,htmlspecialchars_decode($artist_media_keywords));
// }
		
	}	


$pdf->Output('I',strtolower($artist_folder_name."_".$serie_folder_name).".pdf");
?>
