<?php
session_start();
require_once 'classes/User.php';
require_once 'classes/Address.php';
require_once 'classes/Order.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = new User();
$address = new Address();
$order = new Order();

$user_data = $user->readOne($user_id);
$addresses = $address->getUserAddresses($user_id);
$recent_orders = $order->getByUser($user_id, 5, 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    if (!empty($full_name) || !empty($phone)) {
        $user->full_name = $full_name;
        $user->phone = $phone;
        $user->updateProfile($user_id);
    }
    
    if (!empty($current_password) && !empty($new_password)) {
        $user->changePassword($user_id, $current_password, $new_password);
    }
    
    $user_data = $user->readOne($user_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $new_address = new Address();
    $new_address->user_id = $user_id;
    $new_address->address_type = $_POST['address_type'];
    $new_address->full_name = $_POST['full_name'];
    $new_address->phone = $_POST['phone'];
    $new_address->address_line1 = $_POST['address_line1'];
    $new_address->city = $_POST['city'];
    $new_address->country = $_POST['country'] ?? 'US';
    $new_address->postal_code = $_POST['postal_code'] ?? '';
    $new_address->is_default = isset($_POST['is_default']) ? 1 : 0;
    
    if ($new_address->create()) {
        header("Location: profile.php?success=address_added");
        exit();
    }
}

include 'includes/header.php';
?>

<div class="small-container cart-page">
    <!-- Profile Header -->
    <div class="order-header">
        <h1>My Profile</h1>
        <div class="order-meta">
            <p>Welcome back, <?= htmlspecialchars($user_data['username'] ?? 'User') ?>!</p>
        </div>
    </div>
    
    <div class="row">
        <!-- Left Column - Profile Info & Addresses -->
        <div class="col-2">
            <!-- Personal Information -->
            <div class="order-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Personal Information</h3>
                    <button type="button" onclick="toggleEditProfile()" class="btn btn-small">
                        Edit
                    </button>
                </div>
                
                <div id="profile-view">
                    <div class="shipping-info">
                        <div>
                            <strong>Username:</strong>
                            <p><?= htmlspecialchars($user_data['username']) ?></p>
                        </div>
                        <div>
                            <strong>Email:</strong>
                            <p><?= htmlspecialchars($user_data['email']) ?></p>
                        </div>
                        <?php if (!empty($user_data['full_name'])): ?>
                        <div>
                            <strong>Full Name:</strong>
                            <p><?= htmlspecialchars($user_data['full_name']) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($user_data['phone'])): ?>
                        <div>
                            <strong>Phone:</strong>
                            <p><?= htmlspecialchars($user_data['phone']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <form method="POST" action="" id="profile-edit" style="display: none;" class="address-form">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                    </div>
                    
                    <div style="margin: 20px 0;">
                        <h4 style="color: #555; margin-bottom: 10px;">Change Password</h4>
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password">
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                        <button type="button" onclick="toggleEditProfile()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
            
            <!-- My Addresses -->
            <div class="order-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>My Addresses</h3>
                    <button type="button" onclick="toggleAddAddress()" class="btn btn-small">
                        Add New
                    </button>
                </div>
                
                <?php if (!empty($addresses)): ?>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($addresses as $addr): ?>
                        <div class="address-option">
                            <?php if ($addr['is_default']): ?>
                                <span style="position: absolute; top: 10px; right: 10px; background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                                    Default
                                </span>
                            <?php endif; ?>
                            <div style="display: flex; align-items: flex-start; gap: 10px;">
                                <div style="background: #f0f0f0; padding: 8px 12px; border-radius: 5px; font-weight: bold; color: #555;">
                                    <?= ucfirst($addr['address_type']) ?>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; font-weight: bold;"><?= htmlspecialchars($addr['full_name']) ?></p>
                                    <p style="margin: 0 0 5px 0; color: #666; font-size: 14px;">
                                        <?= htmlspecialchars($addr['address_line1']) ?><br>
                                        <?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['country']) ?> <?= htmlspecialchars($addr['postal_code']) ?>
                                    </p>
                                    <p style="margin: 0; color: #666; font-size: 14px;">Phone: <?= htmlspecialchars($addr['phone']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 20px 0;">No addresses saved yet.</p>
                <?php endif; ?>
                
                <!-- Add Address Form -->
                <form method="POST" action="" id="add-address-form" style="display: none; margin-top: 20px;" class="address-form">
                    <h4 style="color: #555; margin-bottom: 15px;">Add New Address</h4>
                    
                    <div class="form-group">
                        <label>Address Type</label>
                        <select name="address_type">
                            <option value="home">Home</option>
                            <option value="work">Work</option>
                            <option value="shipping">Shipping</option>
                            <option value="billing">Billing</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="text" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Address *</label>
                        <input type="text" name="address_line1" required placeholder="Street address">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" required>
                        </div>
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="postal_code">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" value="US">
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="is_default" id="is_default">
                        <label for="is_default">Set as default address</label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_address" class="btn btn-primary">Save Address</button>
                        <button type="button" onclick="toggleAddAddress()" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Column - Recent Orders -->
        <div class="col-2">
            <div class="order-summary">
                <h3>Recent Orders</h3>
                
                <?php if (!empty($recent_orders)): ?>
                    <div class="orders-list">
                        <?php foreach ($recent_orders as $order_item): ?>
                        <div class="order-card">
                            <div class="order-card-header">
                                <div>
                                    <h4>Order #<?= $order_item['order_number'] ?></h4>
                                    <p class="order-date"><?= date('M d, Y', strtotime($order_item['created_at'])) ?></p>
                                </div>
                                <span class="status-badge status-<?= $order_item['status'] ?>">
                                    <?= ucfirst($order_item['status']) ?>
                                </span>
                            </div>
                            
                            <div class="order-card-body">
                                <div class="order-total">
                                    $<?= number_format($order_item['total_amount'], 2) ?>
                                </div>
                                <div class="order-actions">
                                    <a href="order.php?id=<?= $order_item['id'] ?>" class="btn btn-small">View Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($recent_orders) >= 5): ?>
                        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <a href="my-orders.php" class="btn">View All Orders</a>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <p style="color: #666; text-align: center; padding: 20px 0;">No orders yet.</p>
                    <div style="text-align: center;">
                        <a href="products.php" class="btn">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="order-section" style="margin-top: 20px;">
                <h3>Quick Actions</h3>
                <div style="display: grid; gap: 10px; margin-top: 15px;">
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    <a href="cart.php" class="btn btn-secondary">View Cart</a>
                    <a href="my-orders.php" class="btn btn-success">All Orders</a>
                    <a href="auth/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEditProfile() {
    const profileView = document.getElementById('profile-view');
    const profileEdit = document.getElementById('profile-edit');
    
    if (profileView.style.display === 'none') {
        profileView.style.display = 'block';
        profileEdit.style.display = 'none';
    } else {
        profileView.style.display = 'none';
        profileEdit.style.display = 'block';
    }
}

function toggleAddAddress() {
    const addAddressForm = document.getElementById('add-address-form');
    
    if (addAddressForm.style.display === 'none') {
        addAddressForm.style.display = 'block';
    } else {
        addAddressForm.style.display = 'none';
    }
}
</script>

<?php include 'includes/footer.php'; ?>