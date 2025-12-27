<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once "../classes/Database.php";
require_once "../classes/Category.php";

$categoryObj = new Category();
$category = $categoryObj->readOne($_GET['id']);
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Add New Category</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Category Information</h5>
        </div>

        <div class="card-body">
<form method="POST" action="handlers/update_category_handler.php">
    <input type="hidden" name="id" value="<?= $category['id'] ?>">

    <div class="mb-3">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($category['name']) ?>">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($category['description']) ?></textarea>
    </div>

    <button class="btn btn-primary">Update</button>
</form>
</div>
    </div>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
