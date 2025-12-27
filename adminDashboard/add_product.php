<?php
include 'includes/header.php';
include 'includes/sidebar.php';

require_once '../classes/category.php';
require_once "../classes/Database.php";

$categoryObj = new Category();
$categories = $categoryObj->readAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Add New Product</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Product Information</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="handlers/add_product.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Price (JD)</label>
                    <input type="number" name="price" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Product Category</label>
                    <select name="category_id" class="form-select" required>
                        <option disabled selected>Select category</option>

                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>


                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        Save Product
                    </button>
                    <a href="products.php" class="btn btn-outline-secondary">Back</a>
                </div>

            </form>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>