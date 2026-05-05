<?php
require_once("data/includes/connection_inc.php");

//-------------------------------------------------------------------------------------------------------------------------------------------------
$letter="a-z";
if(isset($_GET["letter"]))
{
    $letter=$_GET["letter"];
}

$abc = array ("a-z","z-a","a", "b", "c", "d", "e","f", "g", "h", "i", "j", "k", "l", "m", "n", "o","p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "afb");

//-------------------------------------------------------------------------------------------------------------------------------------------------
$serie_selected="";
if(isset($_GET["ser_id"]))
{
    $serie_selected=$_GET["ser_id"];
}
//-------------------------------------------------------------------------------------------------------------------------------------------------
echo "<HTML>\n<HEAD>\n<TITLE>FIDK</TITLE>\n";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "</HEAD>\n<BODY>\n";

//-------------------------------------------------------------------------------------------------------------------------------------------------
$abc_string="";
foreach($abc as $val) 
{
    if($val==$letter)
    {
        $abc_string.="<a href='folder_browse.php?letter=".$val."' style='color:red;'>".$val."</a> <span style='color:#0080FF;'>&middot;</span> ";
    }
    else
    {
        $abc_string.="<a href='folder_browse.php?letter=".$val."'>".$val." </a> <span style='color:#0080FF;'>&middot;</span> ";    
    }
}
$abc_string = rtrim($abc_string, "<span style='color:#0080FF;'>&middot;</span>");
$abc_string.="</a>";

echo "<p>".$abc_string."</p>";

if($letter!="a-z" && $letter!="z-a" && $letter!="afb")
{
    $sql1="SELECT * FROM tbl_artists WHERE artist_name LIKE '".$letter."%' ORDER BY artist_name ;";
    echo "<H1>Folder</H1>\n<H2>Bladeren</H2><h3>Fotografen (".$letter.")</h3>\n";
}
else
{
    if($letter=="a-z" || $letter=="afb"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name"; }
    if($letter=="z-a"){ $sql1="SELECT * FROM tbl_artists ORDER BY artist_name DESC"; }
    echo "<H1>Folder</H1>\n<H2>Bladeren</H2><h3>Fotografen (".$letter.")</h3>\n";
}    

        
//-------------------------------------------------------------------------------------------------------------------------------------------------

$rs = mysqli_query($conn1, $sql1);
if($letter!="afb")
{
    echo "<ul>";
}
while ($row=mysqli_fetch_array($rs))
{
    extract($row);
    
    if($letter!="a-z" && $letter!="z-a" && $letter!="afb")
    {
        $artist_name_show=substr_replace(substr($artist_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
    }
    else
    {
        $artist_name_show=$artist_name;
    }

    if($letter!="afb")
    {
        echo "<li>".$artist_name_show;
    }
    
    $sql2="SELECT * FROM tbl_series WHERE serie_artist_fk=".$artist_pk;
    $rs2 = mysqli_query($conn2, $sql2);
        
    if($letter!="afb")
    {
        echo "<br><ul>";
    }

    while ($row2=mysqli_fetch_array($rs2))
    {
        extract($row2);
        
        $url1="?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."&letter=".$letter;
            
        if($letter!="afb")
        {
            echo "<li>";
        }
        
        if($serie_selected==$serie_pk)
        {
            echo "<a style=\"color:red;\" onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.folder.location='folder_browse.php".$url1."';\" href=\"#\">".$serie_name."</a><br clear='all'>\n";    
        }
        else
        {
            if($letter=="afb")
            {
                echo "<p>".$artist_name_show." - <a onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.folder.location='folder_browse.php".$url1."';\" href=\"#\">".$serie_name."</a></p>\n";    
            }
            else
            {
                echo "<a onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.folder.location='folder_browse.php".$url1."';\" href=\"#\">".$serie_name."</a><br clear='all'>\n";    
            }    
        
        }        

        //5 Afbeeldingen tonen
        if($letter=="afb")
        {
            echo "<div class='container'>";
            echo "<div class='row align-items-start'>";
            
            $sql3="SELECT * FROM tbl_images WHERE image_serie_fk=".$serie_pk." LIMIT 5";
            $rs3=mysqli_query($conn3, $sql3);
            while ($row3=mysqli_fetch_array($rs3))
            {
                extract($row3);
                        
                $url="fidk/".$artist_folder_name."/".$serie_folder_name."/thumb/".$image_document_name;
                $im_w=$image_width;
                $im_h=$image_height;
                
                if($im_w>$im_h)
                {
                    $f=$im_h/50;    
                    echo "<img src='".$url."'  width='".$im_w/$f."' height='50'> ";
                }
                else
                {
                    $f=$im_h/50;
                    echo "<img src='".$url."'  width='".$im_w/$f."' height='50'> ";
                }
            }
            echo "</div>";
            echo "</div><hr>";
        }
    }
    
    if($letter!="afb")
    {
        echo "</ul>";                
    }
}

if($letter!="afb")
{
    echo "</ul>";
}
                
//----------------------------------------------------------------------------------------------------------------------------------------------

if($letter!="a-z" && $letter!="z-a" && $letter!="afb")
{
    $sql1="SELECT * FROM tbl_series WHERE serie_name LIKE '".$letter."%' ORDER BY serie_name ;";
    echo "<h3>Reeksen (".$letter.")</h3>\n";
    
    $rs = mysqli_query($conn1, $sql1);
    echo "<ul>";
    while ($row=mysqli_fetch_array($rs))
    {
        extract($row);
        
        $sql2="SELECT * FROM tbl_artists WHERE artist_pk=".$serie_artist_fk;
        $rs2 = mysqli_query($conn2, $sql2);
        while ($row2=mysqli_fetch_array($rs2))
        {
            extract($row2);
            echo "<li>".$artist_name."<br>";
            echo "<ul>";
            
            $url1="?actie=serie&ser_id=".$serie_pk."&art_id=".$artist_pk."&letter=".$letter;
            
            if($letter!="a-z")
            {
                $serie_name=substr_replace(substr($serie_name,1),"<span style='color:red;'>".ucfirst($letter)."</span>",0,0);
            }
            else
            {
                $serie_name_show=$serie_name;
            }

            if($serie_selected==$serie_pk)
            {
                echo "<li><a style=\"color:red;\" onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.folder.location='folder_browse.php".$url1."';\" href=\"#\">".$serie_name."</a>\n";    
            }
            else
            {
                echo "<li><a onclick=\"javascript:parent.preview.location='preview_browse.php".$url1."';parent.folder.location='folder_browse.php".$url1."';\" href=\"#\">".$serie_name."</a>\n";    
            }    
        }
        echo "</ul>";                
    }
    echo "</ul>";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
echo "</BODY>\n</HTML>\n";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>
