<?php
require_once "../../classes/Database.php";
require_once "../../classes/Category.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category = new Category();
    $category->name = $_POST['name'] ?? '';
    $category->description = $_POST['description'] ?? '';

    if ($category->create()) {
        header("Location: ../categories.php?success=1");
        exit;
    } else {
        header("Location: ../add_category.php?error=1");
        exit;
    }
}
