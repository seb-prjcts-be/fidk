<?php
session_start();
require_once("data/includes/connection_inc.php");

	$user_email_frm="";
	$user_password_frm="";

	$check=0;
	$actie="";
	
	// Check if actie is set in GET parameters
	if(isset($_GET["actie"])) {
		$actie = $_GET["actie"];
	}

	if(isset($_POST["user_email"]))
	{
		$user_email_frm=$_POST["user_email"];
		if(filter_var($user_email_frm, FILTER_VALIDATE_EMAIL))
		{$check++;}else{$actie="toon_formulier";}
	}
	
	if(isset($_POST["user_pw"]))
	{
		$user_password_frm=$_POST["user_pw"];
		$check++;
	}
	
	if($check==2)
	{
		$actie="try_log";	
	}
	else if($actie=="") {
		// If no action is specified, redirect to public frameset page
		header("location: frameset_public.php");
		exit;
	}
	
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="out")
{
	// Clear session variables
	$_SESSION['loggedin'] = 0;
	$_SESSION['user_name'] = "";
	$_SESSION['user_pk'] = "";
	
	// Redirect to public browsing page
	header("location: frameset_public.php");
	exit;
}	
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="try_log")
{
	// Verwijder debug informatie in productie
	// ini_set('display_errors', 1);
	// error_reporting(E_ALL);
	
	$sql1="SELECT * FROM tbl_users WHERE user_email='".$user_email_frm."'";
	$rs = mysqli_query($conn1,$sql1);
	
	$user_found = false;
	$user_email_db = "";
	$user_password_db = "";
	$user_pk_db = "";
	$user_name_db = "";
	$user_role_db = "admin"; // Standaard admin rol
	
	if ($rs && mysqli_num_rows($rs) > 0) {
		$row = mysqli_fetch_assoc($rs);
		$user_found = true;
		$user_email_db = $row['user_email'];
		$user_password_db = $row['user_pw'];
		$user_pk_db = $row['user_pk'];
		
		// Controleer of user_name bestaat in de resultaatset
		if (isset($row['user_name'])) {
			$user_name_db = $row['user_name'];
		}
		
		// Controleer of user_role bestaat in de resultaatset
		if (isset($row['user_role'])) {
			$user_role_db = $row['user_role'];
		}
	}
	
	// Controleer wachtwoord
	if($user_found && $user_password_frm == $user_password_db)
	{
		// Regenereer sessie ID voor veiligheid
		session_regenerate_id(true); //heel belangrijk tegen hacken! >TWO-STEP FIXATION ATTACKS
		
		// Genereer een veilige token
		$token = bin2hex(random_bytes(32));
		
		// Stel alle sessievariabelen in
		$_SESSION['token'] = $token;
		$_SESSION['loggedin'] = 1;
		$_SESSION['user_name'] = $user_email_db; // Email als gebruikersnaam
		$_SESSION['user_pk'] = $user_pk_db;
		$_SESSION['user_role'] = $user_role_db; // Rol instellen
		
		// Stel een veilige cookie in
		$cookie_options = array(
			'expires' => time() + 60*60*24*30, // 30 dagen
			'path' => '/',
			'domain' => '',
			'secure' => true, // Alleen via HTTPS
			'httponly' => true, // Niet toegankelijk via JavaScript
			'samesite' => 'Strict' // Bescherming tegen CSRF
		);
		
		// Als we op localhost draaien, zet secure op false
		if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'localhost') {
			$cookie_options['secure'] = false;
		}
		
		setcookie('token', $token, $cookie_options);
		
		// Log de succesvolle login
		error_log("Succesvolle login voor gebruiker: " . $user_email_db);
		
		// Redirect naar het dashboard
		header("location: frameset_browse.php");
		exit(); // Zorg ervoor dat de code stopt na de redirect
	}
	else
	{
		// Login mislukt
		$_SESSION['loggedin'] = 0;
		
		// Log de mislukte login poging
		error_log("Mislukte login poging voor email: " . $user_email_frm);
		
		// Redirect terug naar het inlogformulier
		header("location: index.php?actie=toon_formulier&error=1");
		exit(); // Zorg ervoor dat de code stopt na de redirect
	}
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="gegevenskloppenniet")
{
	echo "<!DOCTYPE html>\n<HTML>\n<HEAD>\n<META charset=\"UTF-8\">\n<TITLE>FIDK - Beheerders Login</TITLE>\n<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n<style>\n.login-container {\n  max-width: 400px;\n  margin: 50px auto;\n  padding: 30px;\n  border-radius: 10px;\n  box-shadow: 0 0 20px rgba(0,0,0,0.1);\n  background-color: #fff;\n}\n.login-header {\n  text-align: center;\n  margin-bottom: 30px;\n}\n.login-header h1 {\n  color: #333;\n  font-size: 28px;\n  margin-bottom: 10px;\n}\n.login-header p {\n  color: #666;\n}\n.form-group {\n  margin-bottom: 20px;\n}\n.btn-login {\n  width: 100%;\n  padding: 10px;\n}\n.home-link {\n  text-align: right;\n  padding: 15px;\n}\n.alert-login {\n  text-align: center;\n  margin-bottom: 20px;\n}\n</style>\n</HEAD>\n<BODY class=\"bg-light\">\n";
	echo "<div class=\"home-link\"><a href='frameset_public.php' class=\"btn btn-sm btn-outline-primary\">Home</a></div>";
	echo "<div class=\"container\">\n  <div class=\"login-container\">\n    <div class=\"login-header\">\n      <h1>Beheerders Login</h1>\n    </div>\n    <div class=\"alert alert-danger alert-login\">\n      <strong>Fout!</strong> De ingevoerde gegevens kloppen niet. Probeer het opnieuw.\n    </div>\n    <div class=\"d-grid gap-2\">\n      <a href='index.php?actie=toon_formulier' class=\"btn btn-primary btn-login\">Opnieuw proberen</a>\n    </div>\n  </div>\n</div>\n<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"></script>\n</BODY></HTML>";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
if($actie=="toon_formulier")
{
$_SESSION['user_pk']="";
$_SESSION['user_name']="";

// Controleer of er een foutmelding moet worden getoond
$show_error = isset($_GET['error']) && $_GET['error'] == '1';

echo "<!DOCTYPE html>\n<HTML>\n<HEAD>\n<META charset=\"UTF-8\">\n<TITLE>FIDK - Beheerders Login</TITLE>\n<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n<link href=\"css/global_custom.css\" rel=\"stylesheet\">\n<style>\n.login-container {\n  max-width: 400px;\n  margin: 50px auto;\n  padding: 30px;\n  border-radius: 10px;\n  box-shadow: 0 0 20px rgba(0,0,0,0.1);\n  background-color: #fff;\n}\n.login-header {\n  text-align: center;\n  margin-bottom: 30px;\n}\n.login-header h1 {\n  color: #333;\n  font-size: 28px;\n  margin-bottom: 10px;\n}\n.login-header p {\n  color: #666;\n}\n.form-group {\n  margin-bottom: 20px;\n}\n.btn-login {\n  width: 100%;\n  padding: 10px;\n  background-color: #0d6efd;\n  border: none;\n}\n.home-link {\n  text-align: right;\n  padding: 15px;\n}\n.alert-login {\n  text-align: center;\n  margin-bottom: 20px;\n}\n</style>\n</HEAD>\n<BODY class=\"bg-light\">\n";
echo "<div class=\"home-link\"><a href='frameset_public.php' class=\"btn btn-sm btn-outline-primary\">Home</a></div>";
echo "<div class=\"container\">\n  <div class=\"login-container\">\n    <div class=\"login-header\">\n      <h1>Beheerders Login</h1>\n      <p>Log in om toegang te krijgen tot de beheerdersfuncties.</p>\n    </div>";
    
// Toon foutmelding indien nodig
if ($show_error) {
    echo "<div class=\"alert alert-danger alert-login\">\n      <strong>Fout!</strong> De ingevoerde gegevens kloppen niet. Probeer het opnieuw.\n    </div>";
}

echo "    <form method='post' action='index.php' name='login_fidk'>\n      <div class=\"form-group\">\n        <label for=\"user_email\" class=\"form-label\">E-mail</label>\n        <input type='text' name='user_email' id='user_email' class=\"form-control\" placeholder='Voer uw e-mailadres in' required>\n      </div>\n      <div class=\"form-group\">\n        <label for=\"user_pw\" class=\"form-label\">Wachtwoord</label>\n        <input type='password' name='user_pw' id='user_pw' class=\"form-control\" placeholder='Voer uw wachtwoord in' required>\n      </div>\n      <div class=\"form-group\">\n        <button type='submit' name='submit' class=\"btn btn-primary btn-login\">Inloggen</button>\n      </div>\n    </form>\n  </div>\n</div>\n<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js\"></script>\n</BODY></HTML>";
}
//----------------------------------------------------------------------------------------------------------------------------------------------------
// Close only the main connection since conn2 and conn3 reference the same object
if (isset($conn1) && is_object($conn1)) {
    mysqli_close($conn1);
    // No need to close $conn2 and $conn3 as they reference the same connection
}
?>
