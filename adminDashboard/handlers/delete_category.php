<?php
require_once "../../classes/Database.php";
require_once "../../classes/category.php";

if (isset($_GET['id'])) {
    $category = new Category();
    $category->delete($_GET['id']);
}

header("Location: ../categories.php");
exit;
