<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once "../classes/Database.php";
require_once '../classes/Product.php';


$product = new Product();
$products = $product->readAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Products</h2>
    <!-- Add Sale Form -->
    <div id="addSaleForm" class="card shadow-sm d-none">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Add Sale</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" placeholder="Product name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Original Price (JD)</label>
                    <input type="number" class="form-control" placeholder="Original price">
                </div>

                <div class="mb-3">
                    <label class="form-label">Discount Percentage (%)</label>
                    <input type="number" class="form-control" placeholder="Discount %">
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success">Apply Sale</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="backToProducts()">Back</button>
                </div>
            </form>
        </div>
    </div>

    <div id="productsTable" class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Product List</h5>
            <a href="add_product.php" class="btn btn-sm btn-light">
                Add New Product
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <!-- <th>Add Sale</th>
                            <th>Remove Sale</th> -->
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>

                            <td>
                                <img src="handlers/assets/<?= $product->getPrimaryImage($p['id']) ?>" width="50">

                            </td>

                            <td><?= htmlspecialchars($p['name']) ?></td>

                            <td><?= htmlspecialchars($p['description']) ?></td>

                            <td>
                                <?= number_format($p['price'], 2) ?> JD
                            </td>

                            <td>
                                <?php if ($p['category_discount'] > 0): ?>
                                    <span class="text-success fw-bold">
                                        <?= number_format($p['discounted_price'], 2) ?> JD
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        (-<?= $p['category_discount'] ?>%)
                                    </small>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($p['category_name']) ?></td>

                            <td>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                <a href="handlers/delete_product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this product?')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </table>
            </div>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- <script>
    function showAddSale() {
        document.getElementById('productsTable').classList.add('d-none');
        document.getElementById('addSaleForm').classList.remove('d-none');
    }

    function backToProducts() {
        document.getElementById('addSaleForm').classList.add('d-none');
        document.getElementById('productsTable').classList.remove('d-none');
    }
    function removeSaleConfirm() {
        if (confirm("Are you sure you want to remove the sale from this product?")) {
            alert("Sale removed ");
        }
    }
</script> -->

</body>

</html>