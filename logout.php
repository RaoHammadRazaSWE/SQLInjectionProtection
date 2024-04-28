<?php
// Start session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header("location: index.php");
exit; // Terminate script execution after redirection
?>
