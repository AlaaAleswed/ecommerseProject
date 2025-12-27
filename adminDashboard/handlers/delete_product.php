<?php
require_once "../../classes/Database.php";
require_once "../../classes/Product.php";

if (!isset($_GET['id'])) {
    header("Location: ../products.php");
    exit;
}

$product = new Product();
$id = (int)$_GET['id'];

if ($product->delete($id)) {
    header("Location: ../products.php");
    exit;
} else {
    echo "Error deleting product.";
}
