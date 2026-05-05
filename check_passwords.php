<?php
require_once("data/includes/connection_inc.php");

echo "<h1>Wachtwoord Check</h1>";

// Controleer databaseverbinding
echo "<h2>Database Verbinding</h2>";
if ($conn1) {
    echo "<p style='color: green;'>✓ Database verbinding succesvol</p>";
} else {
    echo "<p style='color: red;'>✗ Database verbinding mislukt: " . mysqli_connect_error() . "</p>";
    exit;
}

// Toon alle gebruikers en hun wachtwoorden
echo "<h2>Gebruikers en Wachtwoorden</h2>";
$list_users = "SELECT user_pk, user_email, user_pw, user_name, user_role FROM tbl_users";
$users_list = mysqli_query($conn1, $list_users);

if (mysqli_num_rows($users_list) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Email</th><th>Wachtwoord</th><th>Naam</th><th>Rol</th><th>Wachtwoord Type</th></tr>";
    
    while ($user = mysqli_fetch_assoc($users_list)) {
        echo "<tr>";
        echo "<td>" . $user['user_pk'] . "</td>";
        echo "<td>" . $user['user_email'] . "</td>";
        echo "<td>" . $user['user_pw'] . "</td>";
        echo "<td>" . (isset($user['user_name']) ? $user['user_name'] : '') . "</td>";
        echo "<td>" . (isset($user['user_role']) ? $user['user_role'] : '') . "</td>";
        
        // Bepaal het type wachtwoord (gehashed of plain text)
        $password_type = "Platte tekst";
        if (strlen($user['user_pw']) == 32 && ctype_xdigit($user['user_pw'])) {
            $password_type = "MD5 hash";
        } else if (strlen($user['user_pw']) > 40 && strpos($user['user_pw'], '$') === 0) {
            $password_type = "PHP password_hash";
        }
        
        echo "<td>" . $password_type . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Geen gebruikers gevonden</p>";
}

// Close connection
mysqli_close($conn1);
?>
