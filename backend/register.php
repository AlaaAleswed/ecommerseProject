<?php
require_once "db.php";

if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    header("Location: account.php");
    exit;
}

$username = $_POST['username'];
$email    = $_POST['email'];
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$check = "SELECT id FROM users WHERE email='$email'";
$result = mysqli_query($conn, $check);

if (mysqli_num_rows($result) > 0) {
    echo "Email already exists";
    exit;
}

$sql = "INSERT INTO users (username, email, password)
        VALUES ('$username', '$email', '$hashedPassword')";

if (mysqli_query($conn, $sql)) {
    header("Location: account.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
