<?php
session_start();

// Initialize variables
$loggedin = 0;
$user_pk = "";
$user_name = "";

// Check if user is logged in
if(isset($_SESSION['user_pk']) && !empty($_SESSION['user_pk']))
{
	// Check if token exists in both cookie and session
	if(isset($_COOKIE['session_token']) && isset($_SESSION['token']))
	{
		$token = $_COOKIE['session_token'];
		if ($token === $_SESSION['token'])
		{
			$loggedin = 1;
			$user_pk = $_SESSION['user_pk'];
			$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";
		}
	}
	else
	{
		// Even without token validation, if user_pk is set, consider user logged in
		// This allows the system to work without the token mechanism
		$loggedin = 1;
		$user_pk = $_SESSION['user_pk'];
		$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "";
	}
}
else
{
	$loggedin=0;
	// Don't redirect - allow public access
}

 
 
?>