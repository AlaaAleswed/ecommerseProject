<?php
session_start();
require_once "db.php";

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: ../account.php");
    exit;
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../index.php"); 
        exit;
    } else {
        echo "Wrong password";
    }
} else {
    echo "User not found";
}
