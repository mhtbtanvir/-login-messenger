<?php
session_start();
include_once 'db.config.php';

$user = $_SESSION["username"];
$admin = 'admin'; // Ensure this matches your admin username in the users table

if(isset($_POST["send"])){
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $sendto = mysqli_real_escape_string($conn, $_POST["sendto"]);
    $msg = mysqli_real_escape_string($conn, $_POST["msg"]);
    $url = mysqli_real_escape_string($conn, $_POST["url"]);

    if(empty($title) || empty($msg)){
        echo "Title and message cannot be empty.";
        die();
    }

    // Insert message logic
    if($_POST['all'] == 'send_all'){
        $sql = "INSERT INTO messages(send_from, send_to, title, message, URL, send_on, public)
                VALUES('$user', 'all', '$title', '$msg', '$url', NOW(), 1)";
        mysqli_query($conn, $sql);
        header('Location:index.php?sent=All users');
    }
    else if($_POST['all'] == 'send_admin'){
        $sql = "INSERT INTO messages(send_from, send_to, title, message, URL, send_on)
                VALUES('$user', '$admin', '$title', '$msg', '$url', NOW())";
        mysqli_query($conn, $sql);
        header('Location:index.php?sent=Admin');
    }
    else{
        $sql = "INSERT INTO messages(send_from, send_to, title, message, URL, send_on)
                VALUES('$user', '$sendto', '$title', '$msg', '$url', NOW())";
        mysqli_query($conn, $sql);
        header('Location:index.php?sent='.$sendto);
    }
}
