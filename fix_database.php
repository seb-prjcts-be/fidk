<?php
require_once("data/includes/connection_inc.php");

echo "<h1>Database Reparatie</h1>";

// Controleer databaseverbinding
echo "<h2>Database Verbinding</h2>";
if ($conn1) {
    echo "<p style='color: green;'>✓ Database verbinding succesvol</p>";
} else {
    echo "<p style='color: red;'>✗ Database verbinding mislukt: " . mysqli_connect_error() . "</p>";
    exit;
}

// Controleer of de tabel bestaat
echo "<h2>Tabel Check</h2>";
$check_table = "SHOW TABLES LIKE 'tbl_users'";
$table_exists = mysqli_query($conn1, $check_table);

if (mysqli_num_rows($table_exists) > 0) {
    echo "<p style='color: green;'>✓ Tabel 'tbl_users' bestaat</p>";
    
    // Controleer de structuur van de tabel
    echo "<h3>Tabel Structuur</h3>";
    $check_structure = "DESCRIBE tbl_users";
    $structure_result = mysqli_query($conn1, $check_structure);
    
    $columns = [];
    while ($column = mysqli_fetch_assoc($structure_result)) {
        $columns[$column['Field']] = $column;
        echo "<p>Kolom: " . $column['Field'] . " - Type: " . $column['Type'] . "</p>";
    }
    
    // Controleer of de user_role kolom bestaat
    if (!isset($columns['user_role'])) {
        echo "<p style='color: red;'>✗ Kolom 'user_role' ontbreekt</p>";
        echo "<h3>Kolom toevoegen...</h3>";
        
        $add_column = "ALTER TABLE tbl_users ADD COLUMN user_role VARCHAR(50) DEFAULT 'admin' AFTER user_pw";
        if (mysqli_query($conn1, $add_column)) {
            echo "<p style='color: green;'>✓ Kolom 'user_role' succesvol toegevoegd</p>";
        } else {
            echo "<p style='color: red;'>✗ Fout bij toevoegen kolom: " . mysqli_error($conn1) . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Kolom 'user_role' bestaat</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Tabel 'tbl_users' bestaat niet</p>";
    
    // Maak de tabel aan als deze niet bestaat
    echo "<h3>Tabel aanmaken...</h3>";
    $create_table = "CREATE TABLE tbl_users (
        user_pk INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL UNIQUE,
        user_pw VARCHAR(255) NOT NULL,
        user_role VARCHAR(50) DEFAULT 'admin',
        user_name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn1, $create_table)) {
        echo "<p style='color: green;'>✓ Tabel 'tbl_users' succesvol aangemaakt</p>";
    } else {
        echo "<p style='color: red;'>✗ Fout bij aanmaken tabel: " . mysqli_error($conn1) . "</p>";
    }
}

// Controleer of er gebruikers zijn
echo "<h2>Gebruikers Check</h2>";
$check_users = "SELECT COUNT(*) as count FROM tbl_users";
$users_result = mysqli_query($conn1, $check_users);
$users_count = mysqli_fetch_assoc($users_result)['count'];

echo "<p>Aantal gebruikers in de database: " . $users_count . "</p>";

if ($users_count == 0) {
    echo "<h3>Testgebruiker aanmaken...</h3>";
    
    // Maak een testgebruiker aan
    $test_email = "admin@example.com";
    $test_password = "admin123";
    $test_name = "Admin User";
    
    // Controleer of de user_role kolom bestaat
    $has_role_column = isset($columns['user_role']);
    
    if ($has_role_column) {
        $create_user = "INSERT INTO tbl_users (user_email, user_pw, user_role, user_name) VALUES (?, ?, 'admin', ?)";
        $stmt = mysqli_prepare($conn1, $create_user);
        mysqli_stmt_bind_param($stmt, "sss", $test_email, $test_password, $test_name);
    } else {
        $create_user = "INSERT INTO tbl_users (user_email, user_pw, user_name) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn1, $create_user);
        mysqli_stmt_bind_param($stmt, "sss", $test_email, $test_password, $test_name);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<p style='color: green;'>✓ Testgebruiker succesvol aangemaakt</p>";
        echo "<p>Email: " . $test_email . "</p>";
        echo "<p>Wachtwoord: " . $test_password . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Fout bij aanmaken testgebruiker: " . mysqli_error($conn1) . "</p>";
    }
}

// Toon alle gebruikers
echo "<h2>Gebruikerslijst</h2>";

// Controleer welke kolommen bestaan
$columns_query = "SHOW COLUMNS FROM tbl_users";
$columns_result = mysqli_query($conn1, $columns_query);
$column_names = [];
while ($column = mysqli_fetch_assoc($columns_result)) {
    $column_names[] = $column['Field'];
}

// Pas de query aan op basis van beschikbare kolommen
$select_fields = "user_pk, user_email";
if (in_array('user_name', $column_names)) {
    $select_fields .= ", user_name";
}
if (in_array('user_role', $column_names)) {
    $select_fields .= ", user_role";
}

$list_users = "SELECT $select_fields FROM tbl_users";
$users_list = mysqli_query($conn1, $list_users);

if (mysqli_num_rows($users_list) > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Email</th>";
    if (in_array('user_name', $column_names)) {
        echo "<th>Naam</th>";
    }
    if (in_array('user_role', $column_names)) {
        echo "<th>Rol</th>";
    }
    echo "</tr>";
    
    while ($user = mysqli_fetch_assoc($users_list)) {
        echo "<tr>";
        echo "<td>" . $user['user_pk'] . "</td>";
        echo "<td>" . $user['user_email'] . "</td>";
        if (in_array('user_name', $column_names) && isset($user['user_name'])) {
            echo "<td>" . $user['user_name'] . "</td>";
        }
        if (in_array('user_role', $column_names) && isset($user['user_role'])) {
            echo "<td>" . $user['user_role'] . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Geen gebruikers gevonden</p>";
}

// Close connection
mysqli_close($conn1);
?>
