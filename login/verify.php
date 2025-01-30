<?php
require 'config.php'; // Include your database connection

if (isset($_GET['verification'])) {
    $code = mysqli_real_escape_string($conn, $_GET['verification']);
    
    // Check if the code exists in the database
    $query = "SELECT * FROM users WHERE code='$code'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Update the user's code to null to mark as verified
        $update = "UPDATE users SET code=NULL WHERE code='$code'";
        if (mysqli_query($conn, $update)) {
            echo "<div style='color: green;'>Your account has been verified successfully. You can now <a href='index.php'>login</a>.</div>";
        } else {
            echo "<div style='color: red;'>Verification failed. Please try again later.</div>";
        }
    } else {
        echo "<div style='color: red;'>Invalid or expired verification link.</div>";
    }
} else {
    echo "<div style='color: red;'>No verification code provided.</div>";
}
?>
