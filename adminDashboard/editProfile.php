<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Edit Profile</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Admin Information</h5>
        </div>

        <div class="card-body">
            <form enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Admin Name</label>
                    <input type="text" class="form-control" >
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" >
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" >
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
