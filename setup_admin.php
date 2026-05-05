<?php
require_once("data/includes/connection_inc.php");

// Check if table exists, if not create it
$check_table = "SHOW TABLES LIKE 'tbl_users'";
$table_exists = mysqli_query($conn1, $check_table);

if (mysqli_num_rows($table_exists) == 0) {
    $create_table = "CREATE TABLE tbl_users (
        user_pk INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL UNIQUE,
        user_pw VARCHAR(255) NOT NULL,
        user_role VARCHAR(50) DEFAULT 'public',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn1, $create_table)) {
        die("Error creating table: " . mysqli_error($conn1));
    }
    echo "Table created successfully<br>";
}

// Check if admin user exists
$check_admin = "SELECT * FROM tbl_users WHERE user_email = 'sebasvanb'";
$admin_exists = mysqli_query($conn1, $check_admin);

if (mysqli_num_rows($admin_exists) == 0) {
    // Create admin user with a secure password
    $admin_email = 'sebasvanb';
    $admin_password = "Admin@" . bin2hex(random_bytes(4)); // Generate a secure password
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    
    $create_admin = "INSERT INTO tbl_users (user_email, user_pw, user_role) VALUES (?, ?, 'admin')";
    $stmt = mysqli_prepare($conn1, $create_admin);
    mysqli_stmt_bind_param($stmt, "ss", $admin_email, $hashed_password);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin account created successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_password . "<br>";
        echo "Please save these credentials and delete this file after use!";
    } else {
        echo "Error creating admin account: " . mysqli_error($conn1);
    }
} else {
    echo "Admin account already exists!";
}
?>
