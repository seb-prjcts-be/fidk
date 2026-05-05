<?php
include("cloudservices.inc.php");

$menu="";
if(isset($_GET["menu"])){$menu=$_GET["menu"];}

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>FIDK</title>";
echo "<meta charset=\"UTF-8\">";
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
echo "<link href=\"css/global_custom.css\" rel=\"stylesheet\">";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3' crossorigin='anonymous'>";
echo "</head>";
echo "<body>";

echo "<nav class=\"navbar navbar-expand navbar-light bg-light py-1\">";
echo "  <div class=\"container-fluid\">";

// Left side of navbar - brand/home
echo "    <div class=\"navbar-nav me-auto\">";
echo "      <a class=\"nav-link " . ($menu == "public" ? "active fw-bold" : "") . "\" href='frameset_public.php' target='_top'>Home</a>";
echo "    </div>";

// Right side of navbar - navigation links
echo "    <div class=\"navbar-nav\">";

// Only show admin navigation options if logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==1) {
    echo "      <a class=\"nav-link " . ($menu == "browse" ? "active fw-bold" : "") . "\" href='frameset_browse.php' target='_top'>Admin Bladeren</a>";
    echo "      <a class=\"nav-link " . ($menu == "manage" ? "active fw-bold" : "") . "\" href='frameset_manage.php' target='_top'>Tags beheren</a>";
    echo "      <a class=\"nav-link " . ($menu == "manage_col" ? "active fw-bold" : "") . "\" href='frameset_manage_col.php' target='_top'>Collectie beheren</a>";
}

// Login/logout option
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"]==1) {
    echo "      <a class=\"nav-link text-success\" href='index.php?actie=out' target='_top'>Uitloggen (".$_SESSION['user_name'].")</a>";
} else {
    echo "      <a class=\"nav-link text-primary\" href='index.php?actie=toon_formulier' target='_top'>Inloggen</a>";
}

echo "    </div>";
echo "  </div>";
echo "</nav>";

