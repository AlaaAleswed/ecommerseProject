<?php
session_start();
require_once "db.php";

if (!isset($_POST['username'], $_POST['password'])) {
    header("Location: ../account.php");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

$db = new Database();
$conn = $db->getConnection();

$sql = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
$sql->bind_param("s", $username);
$sql->execute();

$result = $sql->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: ../index.php");
        exit;
    } else {
        echo "Wrong password";
    }
} else {
    echo "User not found";
}
