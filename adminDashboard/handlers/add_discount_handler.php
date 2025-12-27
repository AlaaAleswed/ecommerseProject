<?php
require_once "../../classes/Database.php";
require_once "../../classes/Discount.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $discount = new Discount();
    $discount->category_id = $_POST['category_id'];
    $discount->discount_value = $_POST['discount_value'];
    $discount->create();
}

header("Location: ../categories.php");
exit;
