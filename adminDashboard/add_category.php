<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Add New Category</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Category Information</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="handlers/add_category_handler.php">

                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Category name">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category Description</label>
                    <textarea  name="description" class="form-control" rows="4" placeholder="Category description"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        Save Category
                    </button>
                    <a href="categories.php" class="btn btn-outline-secondary">Back</a>
                </div>

            </form>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
