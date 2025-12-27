<?php
require_once "../../classes/Database.php";
require_once "../../classes/Product.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = new Product();
    $product->id = $_POST['id'];
    $product->name = $_POST['name'];
    $product->price = $_POST['price'];
    $product->category_id = $_POST['category_id'];
    $product->description = $_POST['description'] ?? '';

    if (!empty($_FILES['image']['name'])) {
        $imageId = $product->uploadImage($_FILES['image']);
        if ($imageId) {
            $product->primary_image_id = $imageId;
        }
    }

    if ($product->update()) {
        header("Location: ../products.php");
        exit;
    } else {
        echo "Error updating product.";
    }
}
