<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once "../classes/Database.php";
require_once "../classes/Product.php";
require_once '../classes/Category.php';

$productObj = new Product();

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$id = (int)$_GET['id'];
$product = $productObj->readOne($id);
if (!$product) {
    echo "Product not found.";
    exit;
}

$categoryObj = new Category();
$categories = $categoryObj->readAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2>Edit Product</h2>
    <form method="POST" action="handlers/update_product.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Price (JD)</label>
            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Current Image</label><br>
            <img src="assets/<?= $productObj->getPrimaryImage($product['id']) ?>" width="100">
        </div>

        <div class="mb-3">
            <label>Upload New Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select" required>
                <option disabled>Select category</option>
                    <?php foreach ($categories as $category): ?>
                <option 
                    value="<?= $category['id'] ?>"
                    <?= ($product['category_id'] == $category['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>
                    <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="products.php" class="btn btn-outline-secondary">Back</a>
        </div>
    </form>
</main>
