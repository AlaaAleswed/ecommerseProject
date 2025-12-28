<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once '../classes/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id, username, email FROM users WHERE role = 'admin'");
$stmt->execute();
$admins = $stmt->fetchAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <h2 class="mb-4">Admins</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admin Accounts</h5>
            <a href="add_admin.php" class="btn btn-sm btn-light">
                Add New Admin
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?= htmlspecialchars($admin['id']) ?></td>
                                <td><?= htmlspecialchars($admin['username']) ?></td>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
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
</body>

</html>