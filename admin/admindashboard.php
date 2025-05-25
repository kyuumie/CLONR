<?php
// Start session
session_start();
echo '<pre>';

echo '</pre>';

// Redirect if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Include the database configuration
require_once '../config.php';

try {
    // Get admin information
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

    // Function to count orders by payment status
    function countOrdersByStatus($conn, $status) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE payment_status = :status");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Get counts for each status
    $pending_count = countOrdersByStatus($conn, 'pending');
    $completed_count = countOrdersByStatus($conn, 'completed');
    $cancelled_count = countOrdersByStatus($conn, 'cancelled');

    // Get recent orders (last 5)
    $stmt = $conn->prepare("SELECT o.*, u.name as customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY id  desc LIMIT 5");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total sales
    $total_sales_stmt = $conn->query("SELECT SUM(total_price) FROM orders WHERE payment_status = 'completed'");
    $total_sales = $total_sales_stmt->fetchColumn() ?? 0;

    // Count total users (excluding admins)
    $total_users_stmt = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'user'");
    $total_users = $total_users_stmt->fetchColumn();

    // Count new users this month
    $new_users_stmt = $conn->query("SELECT COUNT(*) FROM users WHERE user_type = 'user' AND created_acc >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
    $new_users = $new_users_stmt->fetchColumn();

    // Get unread messages count
    $unread_messages_stmt = $conn->query("SELECT COUNT(*) FROM messages WHERE is_read = 0");
    $unread_messages = $unread_messages_stmt->fetchColumn();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>CLONR - Admin Dashboard</title>
        <link rel="stylesheet" href="../global.css"/>
        <link rel="stylesheet" href="admin.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            /* Dashboard Specific Styles */
            .admin-main {
                padding: 2rem;
                background-color: #f5f7fa;
            }
            
            .admin-container {
                max-width: 1400px;
                margin: 0 auto;
            }
            
            .admin-title {
                font-size: 2rem;
                color: #333;
                margin-bottom: 1.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #800020;
            }
            
            .admin-welcome {
                font-size: 1.1rem;
                color: #555;
                margin-bottom: 2rem;
                text-align: center;
            }
            
            /* Stats Grid */
            .stats-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }
            
            .stat-card {
                background: white;
                border-radius: 8px;
                padding: 1.5rem;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                transition: transform 0.3s, box-shadow 0.3s;
                border-left: 4px solid;
                position: relative;
                overflow: hidden;
            }
            
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            }
            
            .stat-card h3 {
                font-size: 1rem;
                color: #555;
                margin-bottom: 0.5rem;
            }
            
            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                margin: 0.5rem 0;
            }
            
            .stat-link {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: #800020;
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-size: 0.9rem;
                transition: background 0.3s;
                margin-top: 0.5rem;
            }
            
            .stat-link:hover {
                background: #600018;
            }
            
            /* Status colors */
            .pending {
                border-color: #ffc107;
                background-color: rgba(255, 193, 7, 0.1);
            }
            
            .pending .stat-number {
                color: #ffc107;
            }
            
            .approved {
                border-color: #28a745;
                background-color: rgba(40, 167, 69, 0.1);
            }
            
            .approved .stat-number {
                color: #28a745;
            }
            
            .declined {
                border-color: #dc3545;
                background-color: rgba(220, 53, 69, 0.1);
            }
            
            .declined .stat-number {
                color: #dc3545;
            }
            
            .sales {
                border-color: #17a2b8;
                background-color: rgba(23, 162, 184, 0.1);
            }
            
            .sales .stat-number {
                color: #17a2b8;
            }
            
            .users {
                border-color: #6f42c1;
                background-color: rgba(111, 66, 193, 0.1);
            }
            
            .users .stat-number {
                color: #6f42c1;
            }
            
            /* Recent Orders */
            .recent-orders {
                background: white;
                border-radius: 8px;
                padding: 1.5rem;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                margin-top: 2rem;
            }
            
            .recent-orders h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
                color: #333;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .recent-orders h2 a {
                font-size: 0.9rem;
                color: #800020;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            
            .recent-orders table {
                width: 100%;
                border-collapse: collapse;
            }
            
            .recent-orders th, 
            .recent-orders td {
                padding: 0.75rem 1rem;
                text-align: left;
                border-bottom: 1px solid #eee;
            }
            
            .recent-orders th {
                background: #f8f9fa;
                font-weight: 500;
            }
            
            .recent-orders tr:hover {
                background: rgba(128, 0, 32, 0.03);
            }
            
            .status {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
            }
            
            .status.pending {
                background-color: #fff3cd;
                color: #856404;
            }
            
            .status.completed {
                background-color: #d4edda;
                color: #155724;
            }
            
            .status.cancelled {
                background-color: #f8d7da;
                color: #721c24;
            }
            
            .action-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: #800020;
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-size: 0.8rem;
                transition: background 0.3s;
            }
            
            .action-btn:hover {
                background: #600018;
            }
            
            /* Quick Stats */
            .quick-stats {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-top: 1.5rem;
            }
            
            .quick-stat {
                background: white;
                border-radius: 8px;
                padding: 1rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            
            .quick-stat h4 {
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 0.5rem;
            }
            
            .quick-stat p {
                font-size: 1.2rem;
                font-weight: 600;
                color: #333;
                margin: 0;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .stats-container {
                    grid-template-columns: 1fr 1fr;
                }
                
                .quick-stats {
                    grid-template-columns: 1fr;
                }
            }
            
            @media (max-width: 480px) {
                .stats-container {
                    grid-template-columns: 1fr;
                }
                
                .admin-main {
                    padding: 1rem;
                }
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
        </header>
        <main class="admin-main">
            <div class="admin-container">
                <h1 class="admin-title">Admin Dashboard</h1>
                <p class="admin-welcome">Welcome back, <?= htmlspecialchars($admin['name'] ?? 'Administrator') ?>!</p>
                
                <div class="stats-container">
                    <div class="stat-card pending">
                        <h3>Pending Orders</h3>
                        <p class="stat-number"><?= $pending_count ?></p>
                        <a href="order.php?status=pending&search=&sort=id&order=ASC" class="stat-link">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                    
                    <div class="stat-card approved">
                        <h3>Completed Orders</h3>
                        <p class="stat-number"><?= $completed_count ?></p>
                        <a href="order.php?status=completed&search=&sort=id&order=ASC" class="stat-link">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                    
                    <div class="stat-card declined">
                        <h3>Cancelled Orders</h3>
                        <p class="stat-number"><?= $cancelled_count ?></p>
                        <a href="order.php?status=cancelled&search=&sort=id&order=ASC" class="stat-link">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                    
                    <div class="stat-card sales">
                        <h3>Total Sales</h3>
                        <p class="stat-number">₱<?= number_format($total_sales, 2) ?></p>
                        
                    </div>
                    
                    <div class="stat-card users">
                        <h3>Total Users</h3>
                        <p class="stat-number"><?= $total_users ?></p>
                        <a href="users.php" class="stat-link">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </div>
                </div>
                
                <div class="quick-stats">
                    <div class="quick-stat">
                        <h4>New Users (30 days)</h4>
                        <p><?= $new_users ?></p>
                    </div>
                    <div class="quick-stat">
                        <h4>Unread Messages</h4>
                        <p><?= $unread_messages ?></p>
                    </div>
                </div>
                
                <div class="recent-orders">
                    <h2>
                        Recent Orders
                        <a href="order.php">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td>₱<?= htmlspecialchars(number_format($order['total_price'], 2)) ?></td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($order['placed_on']))) ?></td>
                                <td><span class="status <?= htmlspecialchars($order['payment_status']) ?>"><?= ucfirst(htmlspecialchars($order['payment_status'])) ?></span></td>
                                <td><a href="order_details.php?id=<?= $order['id'] ?>" class="action-btn"><i class="fas fa-eye"></i> View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <hr class="custom-hr">

        <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
        </footer>
    </body>
</html>