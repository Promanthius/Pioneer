<?php
session_start();
session_destroy(); // Destroys all session data
header("Location: login.php"); // Redirects to the login page
exit();
?>
