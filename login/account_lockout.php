<?php
function isAccountLocked($email, $conn, $max_attempts = 5, $lockout_duration = 300) {
    $query = "SELECT failed_attempts, last_attempt_time FROM users WHERE email='{$email}'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['failed_attempts'] >= $max_attempts) {
            $last_attempt_time = strtotime($user['last_attempt_time']);
            if (time() - $last_attempt_time < $lockout_duration) {
                return true; // Account is locked
            }
        }
    }
    return false; // Account is not locked
}

function recordFailedAttempt($email, $conn) {
    $query = "UPDATE users SET failed_attempts = failed_attempts + 1, last_attempt_time = NOW() WHERE email='{$email}'";
    mysqli_query($conn, $query);
}

function resetFailedAttempts($email, $conn) {
    $query = "UPDATE users SET failed_attempts = 0, last_attempt_time = NULL WHERE email='{$email}'";
    mysqli_query($conn, $query);
}
?>
