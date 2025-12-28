<?php
session_start();
require_once 'classes/Order.php';
require_once 'classes/Address.php';
require_once 'classes/Review.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? $_GET['id'] : 0;

$order = new Order();
$address = new Address();
$review = new Review();

$order_details = $order->readOne($order_id);
if (!$order_details || $order_details['user_id'] != $user_id) {
    header('Location: my-orders.php');
    exit();
}

$order_address = $address->getAddress($order_details['address_id']);
$order_items = $order->getItems($order_id);

$reviewable_items = [];
if ($order_details['status'] === 'delivered') {
    foreach ($order_items as $item) {
        if (!$review->readOneByProductAndUser($item['product_id'], $user_id, $order_id)) {
            $reviewable_items[$item['product_id']] = $item;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $success = true;
    
    if (isset($_POST['ratings']) && is_array($_POST['ratings'])) {
        foreach ($_POST['ratings'] as $product_id => $rating) {
            if ($rating > 0) {
                $review->product_id = $product_id;
                $review->user_id = $user_id;
                $review->order_id = $order_id;
                $review->rating = $rating;
                $review->comment = isset($_POST['comments'][$product_id]) ? $_POST['comments'][$product_id] : '';
                $review->approved = 1;
                
                if (!$review->create()) {
                    $success = false;
                }
            }
        }
    } else {
        $success = false;
        $error = "Please select ratings for at least one product.";
    }
    
    if ($success) {
        header("Location: order.php?id=$order_id&reviewed=1");
        exit();
    }
}

include 'includes/header.php';
?>

<div class="small-container cart-page">
    <div class="order-header">
        <h1>Order #<?= $order_details['order_number'] ?></h1>
        <div class="order-meta">
            <p>Placed on <?= date('F j, Y', strtotime($order_details['created_at'])) ?></p>
            <span class="status-badge status-<?= $order_details['status'] ?>">
                <?= ucfirst($order_details['status']) ?>
            </span>
        </div>
    </div>
    
    <div class="row">
        <div class="col-2">
            <div class="order-section">
                <h3>Order Items</h3>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php 
                    $subtotal = 0;
                    foreach ($order_items as $item): 
                        $item_total = $item['price'] * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                    <tr>
                        <td>
                            <div class="cart-info">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="adminDashboard/handlers/assets/<?= htmlspecialchars($item['image']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>">
                                <?php endif; ?>
                                <div>
                                    <p><?= htmlspecialchars($item['name']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($item_total, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            
            <?php if ($order_address): ?>
            <div class="order-section">
                <h3>Shipping Information</h3>
                <div class="shipping-info">
                    <p><strong><?= htmlspecialchars($order_address['full_name']) ?></strong></p>
                    <p><?= htmlspecialchars($order_address['address_line1']) ?></p>
                    <p><?= htmlspecialchars($order_address['city']) ?>, <?= htmlspecialchars($order_address['country']) ?></p>
                    <p>Phone: <?= htmlspecialchars($order_address['phone']) ?></p>
                    <p><strong>Payment:</strong> <?= ucfirst(str_replace('_', ' ', $order_details['payment_method'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($reviewable_items)): ?>
            <div class="order-section">
                <h3>Review Your Products</h3>
                
                <?php if (isset($error)): ?>
                    <div class="error-message"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="review-form">
                    <?php foreach ($reviewable_items as $item): ?>
                    <div class="review-item">
                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                        
                        <div class="rating-section">
                            <label>Rating (required):</label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="ratings[<?= $item['product_id'] ?>]" 
                                           value="<?= $i ?>" id="rating_<?= $item['product_id'] ?>_<?= $i ?>" required>
                                    <label for="rating_<?= $item['product_id'] ?>_<?= $i ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="comment-section">
                            <label>Comment (optional):</label>
                            <textarea name="comments[<?= $item['product_id'] ?>]" 
                                      placeholder="Share your experience..."></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
            <?php elseif (isset($_GET['reviewed'])): ?>
            <div class="success-message">
                <p>Thank you for your review!</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-2">
            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (10%):</span>
                        <span>$<?= number_format($subtotal * 0.10, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>$<?= number_format($order_details['total_amount'], 2) ?></span>
                    </div>
                </div>
                
                <div class="order-actions">
                    <a href="my-orders.php" class="btn btn-primary">View All Orders</a>
                    <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>