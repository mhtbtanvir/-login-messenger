<?php

$conn = mysqli_connect("localhost", "root", "", "login-messenger");

if (!$conn) {
    echo "Connection Failed";
}