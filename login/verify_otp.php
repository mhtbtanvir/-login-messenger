<?php
session_start();
require 'config.php'; // Database connection

// Ensure session exists
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?error=session_expired");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']); // Sanitize input
    $user_id = $_SESSION['user_id'];

    // Validate OTP input
    if (!ctype_digit($otp) || strlen($otp) !== 6) {
        header("Location: verify_otp.php?error=invalid_otp_format");
        exit();
    }

    // Fetch OTP and expiry from the database
    $query = $conn->prepare("SELECT otp, otp_expires FROM users WHERE id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Validate OTP and expiry
        if ($otp === $user['otp'] && new DateTime() < new DateTime($user['otp_expires'])) {
            // Clear OTP after successful verification
            $clear_otp_query = $conn->prepare("UPDATE users SET otp = NULL, otp_expires = NULL WHERE id = ?");
            $clear_otp_query->bind_param("i", $user_id);

            if ($clear_otp_query->execute()) {
                header("Location: welcome.php"); // Redirect to welcome page after successful OTP verification
                exit();
            } else {
                // Log error and show server error message
                file_put_contents("error_log.txt", "Failed to clear OTP for user_id {$user_id}: " . $conn->error . "\n", FILE_APPEND);
                header("Location: verify_otp.php?error=server_error");
                exit();
            }
        } else {
            // Handle invalid or expired OTP
            if ($otp !== $user['otp']) {
                header("Location: verify_otp.php?error=invalid_otp");
            } elseif (new DateTime() >= new DateTime($user['otp_expires'])) {
                header("Location: verify_otp.php?error=otp_expired");
            }
            exit();
        }
    } else {
        // Log error and redirect user not found
        file_put_contents("error_log.txt", "User not found during OTP verification for user_id {$user_id}\n", FILE_APPEND);
        header("Location: index.php?error=user_not_found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <form method="POST" action="verify_otp.php">
        <h2>Verify OTP</h2>
        <?php
        if (isset($_GET['error'])) {
            echo "<p style='color:red;'>Error: " . htmlspecialchars($_GET['error']) . "</p>";
        }
        ?>
        <label for="otp">Enter OTP:</label>
        <input type="text" name="otp" id="otp" maxlength="6" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
