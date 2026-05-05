<?php
require_once("data/includes/connection_inc.php");

if(isset($_GET["actie"])){$actie=$_GET["actie"];}else{$actie="nieuwe_tag";}
if(isset($_GET["tag"])){$old_tag_name=$_GET["tag"];}

echo "<HTML><HEAD><TITLE>FIDK</TITLE><link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
echo "<script language=\"javascript\">\n";
echo "function formulier_acties()\n";
echo "{\n";
echo "document.tag_rename.action = \"tags_manage.php?actie=tag_rename&old_tag_name=".$old_tag_name."\"\n";    	
echo "document.tag_rename.target = \"tags\";\n";    																		
echo "document.tag_rename.submit();\n";        																			
echo "document.tag_rename.action = \"info_manage.php?actie=nieuwe_tag\"\n";    										
echo "document.tag_rename.target = \"info\";\n";    																		
echo "document.tag_rename.submit(); \n";       																			
echo "return true;\n";
echo "}\n";
echo "</script>\n";
echo "</HEAD><BODY>";
//-------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="rename_tag")
{	
	echo "<hr><p>Hernoem de tag <b>\"".$old_tag_name."\"</b>.</p>";
	echo "<table><tr><td><form name='tag_rename' id='tag_rename' method='post'><input id='tag_name' name='tag_name' value='".$old_tag_name."'><input type='submit' value='Hernoem' onclick='return formulier_acties() ;'></form></td></tr></table>";
}	
//-------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="nieuwe_tag")
{	
	echo "<hr><p>Maak een <b>nieuwe tag</b> aan:</p>";
	echo "<table><tr><td><form id='tag_insert' method='post' action='tags_manage.php?actie=new_tag_name_no_clicks&post_action=toon_tags' target='tags'><input id='new_tag_name' name='new_tag_name'><input type='submit' value='Nieuwe tag'></form></td></tr></table>";
}
//-------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="nieuwe_tag_aan_afbeelding")
{
echo "<br><hr><p>Of koppel een <b>nieuwe tag aan deze afbeelding</b>:</p>";
echo "<table><tr><td><form id='tag_insert' method='post' action='tags_manage.php?actie=new_tag_name&post_action=toon_tags&img_id=".$img_id."&image_document_name=".$image_document_name."&ser_id=".$ser_id."&art_id=".$art_id."'><input id='new_tag_name' name='new_tag_name'><input type='submit' value='tag afbeelding'></form>	</td></tr></table><br><hr>";
}
//-------------------------------------------------------------------------------------------------------------------------------------------------
echo "</BODY></HTML>";

// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}

?>