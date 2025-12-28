<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once "../classes/Database.php";
require_once "../classes/Category.php";
require_once "../classes/Product.php";

$categoryObj = new Category();
$productObj  = new Product();
$categories = $categoryObj->readAll(100, 0);
$categories = $categoryObj->getAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Categories</h2>
    <!-- Add Sale Form -->
    <div id="addSaleForm" class="card shadow-sm d-none">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Add Sale</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="handlers/add_discount_handler.php">

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Discount Percentage (%)</label>
                    <input type="number" name="discount_value" class="form-control" min="1" max="100" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">Apply Sale</button>
                </div>
            </form>

        </div>
    </div>

    <div id="categoriesTable" class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Category List</h5>
            <a href="add_category.php" class="btn btn-sm btn-light">
                Add New Category
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Number of Products</th>
                            <th>Add Sale</th>
                            <th>Remove Sale</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= $productObj->getTotalCount('', $cat['id']) ?></td> 
                                <td>
                                    <i class="bi bi-plus-circle-fill text-primary" role="button"
                                        onclick="showAddSale(<?= $cat['id'] ?>)">
                                    </i>

                                </td>
                                <td>
                                    <i class="bi bi-dash-circle-fill text-danger" role="button"
                                        onclick="removeSaleConfirm(<?= $cat['id'] ?>)">
                                    </i>

                                </td>
                                <td>
                                    <a href="edit_category.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="handlers/delete_category.php?id=<?= $cat['id'] ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showAddSale(categoryId) {
        document.getElementById('categoriesTable').classList.add('d-none');
        document.getElementById('addSaleForm').classList.remove('d-none');

        document.getElementById('category_id').value = categoryId;
    }

    function backToCategories() {
        document.getElementById('addSaleForm').classList.add('d-none');
        document.getElementById('categoriesTable').classList.remove('d-none');
    }
    function removeSaleConfirm(categoryId) {
        if (confirm("Are you sure you want to remove the sale from this category?")) {
            window.location.href = "handlers/remove_discount.php?category_id=" + categoryId;
        }
    }
</script>
</body>

</html>