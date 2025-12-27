<?php
require_once "../../classes/Database.php";
require_once "../../classes/product.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = new Product();
    $product->name = $_POST['name'];
    $product->description = $_POST['description'];
    $product->price = $_POST['price'];
    $product->category_id = $_POST['category_id'];
    $product->featured = isset($_POST['featured']) ? 1 : 0;

    if ($product->create()) {
        if (!empty($_FILES['image']['name'])) {
            $product->uploadImage($_FILES['image']);
        }
        header("Location: ../products.php?success=1");
        exit;
    } else {
        header("Location: ../add_product.php?error=1");
        exit;
    }
}


header("Location: ../products.php");
exit;
