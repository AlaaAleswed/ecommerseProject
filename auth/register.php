<?php
session_start();
require_once "../classes/Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            die("Username or email already exists.");
        }

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ]);

        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['username'] = $username;

        header("Location: ../index.php");
        exit;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../account.php");
    exit;
}
