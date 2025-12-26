<?php
require_once "db.php";

if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    header("Location: account.php");
    exit;
}

$username = trim($_POST['username']);
$email    = trim($_POST['email']);
$password = $_POST['password'];

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$db = new Database();
$conn = $db->getConnection();

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Email already exists";
    exit;
}

$sql = $conn->prepare(
    "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
);
$sql->bind_param("sss", $username, $email, $hashedPassword);

if ($sql->execute()) {
    header("Location: account.php");
    exit;
} else {
    echo "Something went wrong";
}
