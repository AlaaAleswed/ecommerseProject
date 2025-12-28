<?php
session_start();
require_once 'classes/Cart.php';
require_once 'classes/Order.php';
require_once 'classes/Address.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}
if ($order_id = $order->create()) {
    
} else {
    echo "Order failed - check order class";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();
$order = new Order();
$address = new Address();


$cart_items = $cart->readAll($user_id);
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}


$subtotal = 0;
foreach ($cart_items as $item) {
    $price = $item['price'] * (1 - $item['discount_percent'] / 100);
    $subtotal += $price * $item['quantity'];
}
$tax = $subtotal * 0.10;
$total = $subtotal + $tax;


$all_addresses = $address->getUserAddresses($user_id);


$shipping_addresses = array_filter($all_addresses, function($a) {
    return in_array($a['address_type'], ['shipping', 'home']);
});


$billing_addresses = array_filter($all_addresses, function($a) {
    return in_array($a['address_type'], ['billing', 'home', 'work']);
});


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_id = $_POST['shipping_address'] ?? null;
    $use_same_billing = isset($_POST['same_billing']);
    $billing_id = $use_same_billing ? $shipping_id : ($_POST['billing_address'] ?? $shipping_id);
    
    if (!$shipping_id) {
        $error = "Please select a shipping address";
    } else {
        $order->user_id = $user_id;
        $order->address_id = $shipping_id;
        $order->total_amount = $total;
        $order->payment_method = $_POST['payment_method'] ?? 'cash_on_delivery';
        
        if ($order_id = $order->create()) {
            foreach ($cart_items as $item) {
                $price = $item['price'] * (1 - $item['discount_percent'] / 100);
                $order->addItem($order_id, $item['product_id'], $item['quantity'], $price);
            }
            
            $cart->clear($user_id);
            header("Location: order.php?id=$order_id");
            exit();
        } else {
            $error = "Failed to create order. Please try again.";
        }
    }
}

include 'includes/header.php';
?>

<div class="small-container cart-page">
    <h2>Checkout</h2>
    
    <?php if (isset($error)): ?>
        <div class="error-message"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" class="checkout-form">
        <div class="row">
            <div class="col-2">
                <!-- Shipping Address -->
                <div class="checkout-section">
                    <h3>Shipping Address</h3>
                    
                    <?php if (!empty($shipping_addresses)): ?>
                        <div class="address-options">
                            <?php foreach ($shipping_addresses as $addr): ?>
                                <div class="address-option">
                                    <input type="radio" name="shipping_address" value="<?= $addr['id'] ?>" 
                                           id="shipping_<?= $addr['id'] ?>" <?= ($addr['is_default']) ? 'checked' : '' ?>
                                           required>
                                    <label for="shipping_<?= $addr['id'] ?>">
                                        <strong><?= htmlspecialchars($addr['full_name']) ?></strong><br>
                                        <?= htmlspecialchars($addr['address_line1']) ?><br>
                                        <?php if (!empty($addr['address_line2'])): ?>
                                            <?= htmlspecialchars($addr['address_line2']) ?><br>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> <?= htmlspecialchars($addr['postal_code']) ?><br>
                                        <?= htmlspecialchars($addr['country']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="or-divider">- OR -</p>
                    <?php endif; ?>
                    
                    <a href="add-address.php?type=shipping" class="btn btn-secondary">Add New Shipping Address</a>
                </div>
                
                <!-- Billing Address -->
                <div class="checkout-section">
                    <h3>Billing Address</h3>
                    
                    <div class="same-address">
                        <input type="checkbox" name="same_billing" id="same_billing" checked>
                        <label for="same_billing">Same as shipping address</label>
                    </div>
                    
                    <div id="billing-section" class="billing-section">
                        <?php if (!empty($billing_addresses)): ?>
                            <div class="address-options">
                                <?php foreach ($billing_addresses as $addr): ?>
                                    <div class="address-option">
                                        <input type="radio" name="billing_address" value="<?= $addr['id'] ?>" 
                                               id="billing_<?= $addr['id'] ?>" <?= ($addr['is_default']) ? 'checked' : '' ?>>
                                        <label for="billing_<?= $addr['id'] ?>">
                                            <strong><?= htmlspecialchars($addr['full_name']) ?></strong><br>
                                            <?= htmlspecialchars($addr['address_line1']) ?><br>
                                            <?php if (!empty($addr['address_line2'])): ?>
                                                <?= htmlspecialchars($addr['address_line2']) ?><br>
                                            <?php endif; ?>
                                            <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['state']) ?> <?= htmlspecialchars($addr['postal_code']) ?><br>
                                            <?= htmlspecialchars($addr['country']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <p class="or-divider">- OR -</p>
                        <?php endif; ?>
                        
                        <a href="add-address.php?type=billing" class="btn">Add New Billing Address</a>
                    </div>
                </div>
                
                <!-- Payment Method -->
                <div class="checkout-section">
                    <h3>Payment Method</h3>
                    
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="cash_on_delivery" id="cod" checked>
                            <label for="cod">Cash on Delivery</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" id="credit_card">
                            <label for="credit_card">Credit/Debit Card</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="paypal" id="paypal">
                            <label for="paypal">PayPal</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-2">
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    
                    <div class="summary-items">
                        <?php foreach ($cart_items as $item): 
                            $price = $item['price'] * (1 - $item['discount_percent'] / 100);
                        ?>
                        <div class="summary-item">
                            <span class="item-name"><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?></span>
                            <span class="item-price">$<?= number_format($price * $item['quantity'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span>$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (10%):</span>
                            <span>$<?= number_format($tax, 2) ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span>$<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-large">Complete Order</button>
                    
                    <p class="terms">
                        By completing your order, you agree to our <a href="#">Terms of Service</a>.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('same_billing').addEventListener('change', function() {
    document.getElementById('billing-section').style.display = this.checked ? 'none' : 'block';
});
</script>

<?php include 'includes/footer.php'; ?>