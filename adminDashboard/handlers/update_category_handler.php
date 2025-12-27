<?php
require_once "../../classes/Database.php";
require_once "../../classes/Category.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = new Category();
    $category->id = $_POST['id'];
    $category->name = $_POST['name'];
    $category->description = $_POST['description'];

    $category->update();
}

header("Location: ../categories.php");
exit;
