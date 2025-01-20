<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function to check if the user is an admin.
 */
function ensureAdmin() {
    // Check if the user is logged in and has the admin role
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        // Redirect to the login page or an access denied page
        header("Location: login.php");
        exit;
    }
}

// Call the function at the top of your admin-restricted pages
ensureAdmin();
?>
