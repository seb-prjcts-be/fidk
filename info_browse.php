<?php
require_once("data/includes/connection_inc.php");
//----------------------------------------------------------------------------------------------------------------------------------------------------
	echo "<HTML><HEAD><TITLE>FIDK</TITLE><link href=\"css/global_custom.css\" rel=\"stylesheet\">\n";
	echo "<script language=\"javascript\">\n";
	echo "function formulier_acties()\n";
	echo "{\n";
	echo "document.info_browse_bio.action = \"preview_browse.php?actie=search_bio\"\n";    				
	echo "document.info_browse_bio.target = \"preview\";\n";    											
	echo "document.info_browse_bio.submit();\n";        														
	echo "document.info_browse_bio.action = \"info_browse.php\"\n";    									
	echo "document.info_browse_bio.target = \"info\";\n";    													
	echo "document.info_browse_bio.submit(); \n";       														
	echo "return true;\n";
	echo "}\n";
	echo "</script>\n";
	echo "</HEAD><BODY>";
//-------------------------------------------------------------------------------------------------------------------------------------------------
	echo "<hr><p>Bio's doorzoeken op trefwoord.</p>";
	echo "<div style='text-align: right;'><form name='info_browse_bio' id='info_browse_bio' method='post'><input id='search_bio' name='search_bio' style='max-width: 200px;'>&nbsp;&nbsp;<input type='submit' value='Doorzoek bio&rsquo;s' onclick='return formulier_acties();'></form></div>";
//-------------------------------------------------------------------------------------------------------------------------------------------------	
	echo "</BODY></HTML>";
	require("connection_close_inc.php");
?>