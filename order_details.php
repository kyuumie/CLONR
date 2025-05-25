<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config.php';

try {
    // Verify admin
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'admin'");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        session_unset();
        session_destroy();
        header('Location: ../login.php');
        exit;
    }

    // Get order ID from URL
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($order_id <= 0) {
        header('Location: order.php?error=invalid_order_id');
        exit;
    }

    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $allowed_statuses = ['pending', 'completed','processing', 'cancelled', 'shipped'];
        
        if (in_array($new_status, $allowed_statuses)) {
            $update_stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            $update_stmt->execute([$new_status, $order_id]);
            
            // Redirect to prevent form resubmission
            header("Location: order_details.php?id=$order_id&success=status_updated");
            exit;
        }
    }

    // Fetch order details
    $order_query = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $order_query->execute([$order_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: order.php?error=order_not_found');
        exit;
    }

    // Fetch order items
    $items_query = $conn->prepare("SELECT oi.*, p.name as product_name, p.price as unit_price, p.image 
                                 FROM order_items oi 
                                 JOIN products p ON oi.product_id = p.id 
                                 WHERE oi.order_id = ?");
    $items_query->execute([$order_id]);
    $order_items = $items_query->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total from items to verify against order total
    $calculated_total = 0;
    foreach ($order_items as &$item) {
        $item['subtotal'] = $item['unit_price'] * $item['quantity'];
        $calculated_total += $item['subtotal'];
    }
    unset($item); // Break the reference

    // If no order_items table, parse from total_products
    if (empty($order_items)) {
        $products = [];
        $product_names = explode(', ', $order['total_products']);
        foreach($product_names as $product){
            preg_match('/(.*) \((\d+)\)/', $product, $matches);
            if(count($matches) === 3){
                $price = $order['total_price'] / array_sum(array_map(function($p) {
                    preg_match('/(.*) \((\d+)\)/', $p, $m);
                    return $m[2] ?? 1;
                }, $product_names));
                
                $products[] = [
                    'product_name' => $matches[1],
                    'quantity' => $matches[2],
                    'unit_price' => $price,
                    'subtotal' => $price * $matches[2]
                ];
            }
        }
        $order_items = $products;
        $calculated_total = $order['total_price']; 
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?= htmlspecialchars($order['id'] ?? '') ?></title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .price-discrepancy {
            color: #d9534f;
            font-weight: bold;
        }
        
        .status-form {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .status-form select, .status-form button {
            padding: 8px 12px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .status-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .status-form button:hover {
            background-color: #45a049;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            text-transform: capitalize;
        }
        
        .status-pending {
            background-color: #FFC107;
            color: #000;
        }
        
        .status-completed {
            background-color: #28a745;
            color: #fff;
        }
        
        .status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }
        
        .status-shipped {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
     <header>
        <div class="container">
            <a href="admindashboard.php"><h1 class="title">CLONR</h1></a>
            <nav>
                <ul class="navbar">
                    <li><a href="products.php">Products</a></li>
                    <li><a href="order.php">Orders</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="../AdminUpdateProfile.php">Profile</a></li>
                    <li><a href="../logout.php" class="logout-btn">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container">
            <div class="back-button">
                <a href="order.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Orders</a>
            </div>
            
            <h1 class="admin-title">Order Details #<?= htmlspecialchars($order['id'] ?? '') ?></h1>
            
            <?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>
                <div class="success-message">
                    Order status updated successfully!
                </div>
            <?php endif; ?>
            
            <!-- Order Information Section -->
            <div class="order-summary">
                <div class="summary-card">
                    <h3>Order Information</h3>
                    <div class="info-row">
                        <span>Order ID:</span>
                        <span>#<?= htmlspecialchars($order['id'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span>Date:</span>
                        <span><?= isset($order['placed_on']) ? date('M d, Y h:i A', strtotime($order['placed_on'])) : 'N/A' ?></span>
                    </div>
                    <div class="info-row">
                        <span>Status:</span>
                        <span class="status-badge status-<?= htmlspecialchars($order['payment_status'] ?? '') ?>">
                            <?= ucfirst(htmlspecialchars($order['payment_status'] ?? 'N/A')) ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span>Payment Method:</span>
                        <span><?= ucfirst(htmlspecialchars($order['method'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="info-row">
                        <span>Total Amount:</span>
                        <span>₱<?= isset($order['total_price']) ? number_format($order['total_price'], 2) : '0.00' ?></span>
                        <?php if (abs($calculated_total - $order['total_price']) > 0.01): ?>
                            <span class="price-discrepancy">(Calculated: ₱<?= number_format($calculated_total, 2) ?>)</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Status Update Form -->
                    <form class="status-form" method="POST">
                        <label for="status">Update Status:</label>
                        <select name="status" id="status">
                            <option value="pending" <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['payment_status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['payment_status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $order['payment_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['payment_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status">
                            <i class="fas fa-sync-alt"></i> Update
                        </button>
                    </form>
                </div>
                
                <!-- Customer Information Section -->
                <div class="summary-card">
                    <h3>Customer Information</h3>
                    <div class="info-row">
                        <span>Name:</span>
                        <span><?= htmlspecialchars($order['name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span>Email:</span>
                        <span><?= htmlspecialchars($order['email'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-row">
                        <span>Phone:</span>
                        <span><?= htmlspecialchars($order['number'] ?? 'N/A') ?></span>
                    </div>
                </div>
                
                <!-- Shipping Address Section -->
                <div class="summary-card">
                    <h3>Shipping Address</h3>
                    <p><?= isset($order['address']) ? nl2br(htmlspecialchars($order['address'])) : 'N/A' ?></p>
                </div>
            </div>
            
            <!-- Order Items Section -->
            <div class="order-items">
                <h3>Order Items</h3>
                <?php if (!empty($order_items)): ?>
                    <table class="items-table">
                        <thead>
                            <tr class="table-header">
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <?php if (!empty($item['image'])): ?>
                                            <img src="../uploaded_img/<?= htmlspecialchars($item['image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['product_name'] ?? 'Product') ?>" 
                                                 width="50">
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></span>
                                    </div>
                                </td>
                                <td>₱<?= number_format($item['unit_price'] ?? 0, 2) ?></td>
                                <td><?= htmlspecialchars($item['quantity'] ?? 1) ?></td>
                                <td>₱<?= number_format($item['subtotal'] ?? ($item['unit_price'] * $item['quantity']), 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>₱<?= number_format($calculated_total, 2) ?></strong></td>
                            </tr>
                            <?php if (abs($calculated_total - $order['total_price']) > 0.01): ?>
                            <tr class="price-discrepancy">
                                <td colspan="3" class="text-right"><strong>Order Total:</strong></td>
                                <td><strong>₱<?= number_format($order['total_price'], 2) ?></strong></td>
                            </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p>No items found for this order.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>