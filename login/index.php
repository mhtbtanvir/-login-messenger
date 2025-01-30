<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['username'])) {
    header("Location: ../Hotel/index.php");
    die();
}

// Include database configuration and PHPMailer
include 'config.php';
require 'account_lockout.php';

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$msg = "";

// Email verification process
if (isset($_GET['verification'])) {
    $verification_code = mysqli_real_escape_string($conn, $_GET['verification']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE code='{$verification_code}'");

    if (mysqli_num_rows($result) > 0) {
        $query = mysqli_query($conn, "UPDATE users SET code='' WHERE code='{$verification_code}'");

        if ($query) {
            $msg = "<div class='alert alert-success'>Account verification successfully completed.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Invalid verification link or already verified.</div>";
    }
}

// Login process with OTP
if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "<div class='alert alert-danger'>Invalid email format.</div>";
    } else {
        // Check if the account is locked
        if (isAccountLocked($email, $conn)) {
            $msg = "<div class='alert alert-danger'>Your account is locked! Many Failed login attempts! Please try again after 5 minutes.Thank You!</div>";
        } else {
            // Check user credentials
            $sql = "SELECT * FROM users WHERE email='{$email}' AND password='{$password}'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);

                // Check if the account is verified
                if (empty($row['code'])) {
                    resetFailedAttempts($email, $conn); // Reset failed attempts on successful login

                    // Clear any existing OTP
                    $clear_otp_query = "UPDATE users SET otp=NULL, otp_expires=NULL WHERE id='{$row['id']}'";
                    mysqli_query($conn, $clear_otp_query);

                    // Generate new OTP
                    $otp = rand(100000, 999999);
                    $otp_expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

                    // Store OTP in the database
                    $update_query = "UPDATE users SET otp='{$otp}', otp_expires='{$otp_expires}' WHERE id='{$row['id']}'";
                    if (mysqli_query($conn, $update_query)) {
                        // Send OTP to user's email
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'mhtbtanvir@gmail.com'; // Replace with your email
                            $mail->Password = 'jxcsnfadglgvhevt'; // App-specific password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            $mail->setFrom('mhtbtanvir@gmail.com', 'Your Application');
                            $mail->addAddress($email);

                            $mail->isHTML(true);
                            $mail->Subject = 'Your OTP Code';
                            $mail->Body = "Your OTP code is <strong>{$otp}</strong>. It will expire in 5 minutes.";

                            $mail->send();

                            // Save user ID in session for OTP verification
                            $_SESSION['user_id'] = $row['id'];
                            header("Location: verify_otp.php");
                            exit();
                        } catch (Exception $e) {
                            file_put_contents("error_log.txt", "Email error: {$mail->ErrorInfo}\n", FILE_APPEND);
                            $msg = "<div class='alert alert-danger'>Could not send OTP. Please try again later.</div>";
                        }
                    } else {
                        file_put_contents("error_log.txt", "Database error: " . mysqli_error($conn) . "\n", FILE_APPEND);
                        $msg = "<div class='alert alert-danger'>Failed to store OTP. Please try again later.</div>";
                    }
                } else {
                    $msg = "<div class='alert alert-info'>Please verify your account before logging in.</div>";
                }
            } else {
                recordFailedAttempt($email, $conn); // Record failed login attempt
                $msg = "<div class='alert alert-danger'>Email or password do not match.</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>Login Form with 2FA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
    <link href="//fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <script src="https://kit.fontawesome.com/af562a2a63.js" crossorigin="anonymous"></script>
</head>
<body>
    <section class="w3l-mockup-form">
        <div class="container">
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    <div class="alert-close">
                        <span class="fa fa-close"></span>
                    </div>
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="images/image.svg" alt="image">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <h2>Login Now</h2>
                        <p>Welcome! Provide required information below.</p>
                        <?php echo $msg; ?>
                        <form action="" method="post">
                            <input type="email" class="email" name="email" placeholder="Enter Your Email" required>
                            <input type="password" class="password" name="password" placeholder="Enter Your Password" required>
                            <p><a href="forgot-password.php">Forgot Password?</a></p>
                            <button name="submit" class="btn" type="submit">Login</button>
                        </form>
                        <div class="social-icons">
                            <p>Create an account! <a href="register.php">Register</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function (c) {
            $('.alert-close').on('click', function (c) {
                $('.main-mockup').fadeOut('slow', function (c) {
                    $('.main-mockup').remove();
                });
            });
        });
    </script>
</body>
</html>
