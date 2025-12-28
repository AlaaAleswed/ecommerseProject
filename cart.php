<?php
session_start();
require_once 'classes/Cart.php';
require_once 'classes/Product.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = new Cart();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $cart_id = $_POST['cart_id'];
        $quantity = $_POST['quantity'];
        
        if ($quantity > 0) {
            $cart->id = $cart_id;
            $cart->quantity = $quantity;
            $cart->update();
        } else {
            $cart->delete($cart_id);
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_id = $_POST['cart_id'];
        $cart->delete($cart_id);
    } elseif (isset($_POST['clear_cart'])) {
        $cart->clear($user_id);
    }
    header('Location: cart.php');
    exit();
}


$cart_items = $cart->readAll($user_id);
$subtotal = 0;
$tax_rate = 0.10;

include 'includes/header.php';
?>

<div class="small-container cart-page">
    <h2>Your Shopping Cart</h2>
    
    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="products.php" class="btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart_items as $item): 
                $price = $item['price'] * (1 - ($item['discount_percent'] / 100));
                $item_total = $price * $item['quantity'];
                $subtotal += $item_total;
            ?>
            <tr>
                <td>
                    <div class="cart-info">
                        <?php if (!empty($item['image'])): ?>
                            <img src="adminDashboard/handlers/assets/<?= htmlspecialchars($item['image']) ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <img src="assets/buy-1.jpg" alt="Product Image">
                        <?php endif; ?>
                        <div>
                            <p><a href="products-details.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a></p>
                            <small>Price: $<?= number_format($price, 2) ?></small>
                            <?php if ($item['discount_percent'] > 0): ?>
                                <br><small style="color: #28a745; font-weight: bold;">Save <?= $item['discount_percent'] ?>%</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <form method="POST" action="" class="quantity-form">
                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                               min="1" max="99">
                        <button type="submit" name="update_quantity" class="btn">Update</button>
                    </form>
                </td>
                <td>$<?= number_format($item_total, 2) ?></td>
                <td>
                    <form method="POST" action="" onsubmit="return confirm('Remove this item?');">
                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                        <button type="submit" name="remove_item" class="btn">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="cart-actions">
            <form method="POST" action="" onsubmit="return confirm('Clear entire cart?');">
                <button type="submit" name="clear_cart" class="btn">Clear Cart</button>
            </form>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </div>

        <div class="total-price">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <tr>
                    <td>Tax (10%)</td>
                    <td>$<?= number_format($subtotal * $tax_rate, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong>$<?= number_format($subtotal * (1 + $tax_rate), 2) ?></strong></td>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>