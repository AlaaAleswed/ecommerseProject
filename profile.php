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

// Get user data
$user_data = $user->readOne($user_id);

// Get user addresses
$addresses = $address->getUserAddresses($user_id);

// Get recent orders (last 5)
$recent_orders = $order->getByUser($user_id, 5, 0);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    // Update basic info if provided
    if (!empty($full_name) || !empty($phone)) {
        $user->full_name = $full_name;
        $user->phone = $phone;
        $user->updateProfile($user_id);
    }
    
    // Update password if provided
    if (!empty($current_password) && !empty($new_password)) {
        $user->changePassword($user_id, $current_password, $new_password);
    }
    
    // Refresh user data
    $user_data = $user->readOne($user_id);
}

// Handle add address
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
    <div style="background: radial-gradient(#fff, #ffd6d6); padding: 30px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
        <h1 style="color: #333; margin-bottom: 10px;">My Profile</h1>
        <p style="color: #666;">Welcome back, <?= htmlspecialchars($user_data['username'] ?? 'User') ?>!</p>
    </div>
    
    <div class="row" style="gap: 30px; align-items: flex-start;">
        <!-- Left Column - Profile Info & Addresses -->
        <div class="col-2" style="flex: 1;">
            <!-- Personal Information -->
            <div style="background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="color: #333; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <span>Personal Information</span>
                    <button type="button" onclick="toggleEditProfile()" style="background: #ff523b; color: white; border: none; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-size: 14px;">
                        Edit
                    </button>
                </h3>
                
                <div id="profile-view">
                    <div style="display: grid; gap: 10px;">
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
                
                <form method="POST" action="" id="profile-edit" style="display: none;">
                    <div style="display: grid; gap: 10px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <h4 style="color: #555; margin: 15px 0 10px 0;">Change Password</h4>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Current Password</label>
                            <input type="password" name="current_password" 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                            <input type="password" name="new_password" 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="submit" name="update_profile" class="btn" style="flex: 1;">Save Changes</button>
                            <button type="button" onclick="toggleEditProfile()" 
                                    style="background: #6c757d; color: white; border: none; padding: 10px; border-radius: 30px; cursor: pointer; flex: 1;">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- My Addresses -->
            <div style="background: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="color: #333; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <span>My Addresses</span>
                    <button type="button" onclick="toggleAddAddress()" style="background: #ff523b; color: white; border: none; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-size: 14px;">
                        Add New
                    </button>
                </h3>
                
                <?php if (!empty($addresses)): ?>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($addresses as $addr): ?>
                        <div style="border: 1px solid #ddd; border-radius: 5px; padding: 15px; position: relative;">
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
                <form method="POST" action="" id="add-address-form" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <h4 style="color: #555; margin-bottom: 15px;">Add New Address</h4>
                    <div style="display: grid; gap: 10px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Address Type</label>
                            <select name="address_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="home">Home</option>
                                <option value="work">Work</option>
                                <option value="shipping">Shipping</option>
                                <option value="billing">Billing</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Full Name</label>
                            <input type="text" name="full_name" required 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                            <input type="text" name="phone" required 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Address</label>
                            <input type="text" name="address_line1" required 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Street address">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">City</label>
                                <input type="text" name="city" required 
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: bold;">ZIP Code</label>
                                <input type="text" name="postal_code" 
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Country</label>
                            <input type="text" name="country" value="US" 
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        <label style="display: flex; align-items: center; gap: 5px; margin-top: 5px;">
                            <input type="checkbox" name="is_default">
                            Set as default address
                        </label>
                        <div style="display: flex; gap: 10px; margin-top: 10px;">
                            <button type="submit" name="add_address" class="btn" style="flex: 1;">Save Address</button>
                            <button type="button" onclick="toggleAddAddress()" 
                                    style="background: #6c757d; color: white; border: none; padding: 10px; border-radius: 30px; cursor: pointer; flex: 1;">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Right Column - Recent Orders -->
        <div class="col-2" style="flex: 1;">
            <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="color: #333; margin-bottom: 20px;">Recent Orders</h3>
                
                <?php if (!empty($recent_orders)): ?>
                    <div style="display: grid; gap: 15px;">
                        <?php foreach ($recent_orders as $order_item): ?>
                        <div style="border: 1px solid #ddd; border-radius: 5px; padding: 15px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <div>
                                    <strong style="color: #333;">Order #<?= $order_item['order_number'] ?></strong>
                                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">
                                        <?= date('M d, Y', strtotime($order_item['created_at'])) ?>
                                    </p>
                                </div>
                                <div>
                                    <span style="padding: 3px 10px; border-radius: 15px; font-size: 12px; font-weight: bold;
                                        background: <?= $order_item['status'] == 'delivered' ? '#d4edda' : 
                                                   ($order_item['status'] == 'pending' ? '#fff3cd' : 
                                                   ($order_item['status'] == 'cancelled' ? '#f8d7da' : '#cce5ff')) ?>;
                                        color: <?= $order_item['status'] == 'delivered' ? '#155724' : 
                                                ($order_item['status'] == 'pending' ? '#856404' : 
                                                ($order_item['status'] == 'cancelled' ? '#721c24' : '#004085')) ?>;">
                                        <?= ucfirst($order_item['status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                <span style="font-weight: bold; color: #333;">
                                    $<?= number_format($order_item['total_amount'], 2) ?>
                                </span>
                                <a href="order.php?id=<?= $order_item['id'] ?>" style="color: #ff523b; text-decoration: none; font-size: 14px;">
                                    View Details →
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($recent_orders) >= 5): ?>
                        <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            <a href="my-orders.php" class="btn" style="display: inline-block;">View All Orders</a>
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
            <div style="background: white; padding: 20px; border-radius: 5px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h3 style="color: #333; margin-bottom: 15px;">Quick Actions</h3>
                <div style="display: grid; gap: 10px;">
                    <a href="products.php" class="btn" style="text-align: center;">Continue Shopping</a>
                    <a href="cart.php" class="btn" style="background: #6c757d; text-align: center;">View Cart</a>
                    <a href="my-orders.php" class="btn" style="background: #28a745; text-align: center;">All Orders</a>
                    <a href="auth/logout.php" class="btn" style="background: #dc3545; text-align: center;">Logout</a>
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