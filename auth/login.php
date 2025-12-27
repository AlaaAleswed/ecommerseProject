<?php
session_start();
require_once "../classes/Database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        die("All fields are required.");
    }

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; 

            if ($user['role'] === 'admin') {
                header("Location: ../adminDashboard/index.php");
                exit;
            } else {
                header("Location: ../index.php");
                exit;
            }

        } else {
            die("Invalid username or password.");
        }

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../account.php");
    exit;
}
