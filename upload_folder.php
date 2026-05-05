<?php
// Ref : http://php.net/manual/en/function.ftp-put.php

$name = "test.txt";
$filename = "/home/mine/Desktop/test.txt";

//-- Code to Transfer File on Server Dt: 06-03-2008 by Aditya Bhatt --//
//-- Connection Settings
$ftp_server = "server_url_here"; // Address of FTP server.
$ftp_user_name = "username_here"; // Username
$ftp_user_pass = 'password_here'; // Password
$destination_file = "/absolute/or/relative/path/to/file/here/text.txt"; //where you want to throw the file on the webserver (relative to your login dir)

$conn_id = ftp_connect($ftp_server) or die("<span style='color:#FF0000'><h2>Couldn't connect to $ftp_server</h2></span>");        // set up basic connection

$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass) or die("<span style='color:#FF0000'><h2>You do not have access to this ftp server!</h2></span>");   // login with username and password, or give invalid user message

if ((!$conn_id) || (!$login_result)) {  // check connection
    // wont ever hit this, b/c of the die call on ftp_login
    echo "<span style='color:#FF0000'><h2>FTP connection has failed! <br />";
    echo "Attempted to connect to $ftp_server for user $ftp_user_name</h2></span>";
    exit;
} else {
    //echo "Connected to $ftp_server, for user $ftp_user_name <br />";
}

$upload = ftp_put($conn_id, $destination_file.$name, $filename, FTP_BINARY);  // upload the file
if (!$upload) {  // check upload status
    echo "<span style='color:#FF0000'><h2>FTP upload of $filename has failed!</h2></span> <br />";
} else {
    echo "<span style='color:#339900'><h2>Uploading $name Completed Successfully!</h2></span><br /><br />";
}
ftp_close($conn_id); // close the FTP stream 