<?php
session_start();
require_once 'classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order = new Order();

$user_orders = $order->getByUser($user_id);

include 'includes/header.php';
?>

<div class="small-container cart-page">
    <h2>My Orders</h2>
    
    <?php if (empty($user_orders)): ?>
        <div class="empty-orders">
            <p>You haven't placed any orders yet.</p>
            <a href="products.php" class="btn">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($user_orders as $order_item): ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <h3>Order #<?= $order_item['order_number'] ?></h3>
                        <p class="order-date"><?= date('F j, Y', strtotime($order_item['created_at'])) ?></p>
                    </div>
                    <span class="status-badge status-<?= $order_item['status'] ?>">
                        <?= ucfirst($order_item['status']) ?>
                    </span>
                </div>
                
                <div class="order-card-body">
                    <div class="order-total">
                        Total: <strong>$<?= number_format($order_item['total_amount'], 2) ?></strong>
                    </div>
                    <div class="order-actions">
                        <a href="order.php?id=<?= $order_item['id'] ?>" class="btn btn-primary">View Details</a>
                        <?php if ($order_item['status'] === 'delivered'): ?>
                            <a href="order.php?id=<?= $order_item['id'] ?>" class="btn btn-success">Leave Review</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>