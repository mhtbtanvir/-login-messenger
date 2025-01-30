<?php
// Start the session
session_start();

// Destroy the session
session_unset();  // Clears all session variables
session_destroy();  // Destroys the session

// Redirect to the login page
header("Location:../Hotel/nonuser-index.php");
exit();
?>
