<?php
function requireAdmin() {
    session_start();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('HTTP/1.0 403 Forbidden');
        echo "Access denied. Administrative privileges required.";
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getCurrentUserRole() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'public';
}
?>
