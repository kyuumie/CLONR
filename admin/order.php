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

    // Initialize filter variables
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'placed_on';
    $sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 10;

    // Validate sort parameters
    $valid_sort_columns = ['id', 'name', 'placed_on', 'total_price', 'payment_status'];
    $sort_by = in_array($sort_by, $valid_sort_columns) ? $sort_by : 'placed_on';
    $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

    // Build base query
    $query = "SELECT SQL_CALC_FOUND_ROWS * FROM orders WHERE 1=1";
    $count_query = "SELECT COUNT(*) FROM orders WHERE 1=1";
    $params = [];

    // Apply status filter
    if (!empty($status_filter) && in_array($status_filter, ['pending', 'processing', 'shipped', 'completed', 'canceled'])) {
        $query .= " AND payment_status = ?";
        $count_query .= " AND payment_status = ?";
        $params[] = $status_filter;
    }

    // Apply search filter
    if (!empty($search_query)) {
        $query .= " AND (name LIKE ? OR id = ? OR email LIKE ? OR number LIKE ?)";
        $count_query .= " AND (name LIKE ? OR id = ? OR email LIKE ? OR number LIKE ?)";
        $search_param = "%$search_query%";
        $params[] = $search_param;
        
        // Check if search query is numeric (possibly an order ID)
        if (is_numeric($search_query)) {
            $params[] = $search_query;
        } else {
            $params[] = $search_param;
        }
        $params[] = $search_param;
        $params[] = $search_param;
    }

    // Add sorting
    $query .= " ORDER BY $sort_by $sort_order";
    
    // Add pagination
    $offset = ($page - 1) * $per_page;
    $query .= " LIMIT $offset, $per_page";

    // Prepare and execute query
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $total_stmt = $conn->prepare($count_query);
    $total_stmt->execute($params);
    $total_orders = $total_stmt->fetchColumn();
    $total_pages = ceil($total_orders / $per_page);

    // Get order counts for stats
    $counts_query = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(payment_status = 'pending') as pending,
            SUM(payment_status = 'processing') as processing,
            SUM(payment_status = 'shipped') as shipped,
            SUM(payment_status = 'completed') as completed,
            SUM(payment_status = 'canceled') as cancelled
        FROM orders
    ");
    $order_counts = $counts_query->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLONR - Order Management</title>
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-pending { background-color: #FFF3CD; color: #856404; }
        .status-processing { background-color: #CCE5FF; color:rgb(11, 201, 96); }
        .status-shipped { background-color: #D4EDDA; color:rgb(21, 50, 87); }
        .status-completed { background-color: #D4EDDA; color: #155724; }
        .status-cancelled { background-color: #F8D7DA; color: #721C24; }
        
        .stats-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .stat-card {
            flex: 1;
            min-width: 150px;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin-top: 0;
            color: #666;
            font-size: 14px;
        }
        .stat-card p {
            margin-bottom: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .stat-card.total { border-top: 4px solid #6c757d; }
        .stat-card.pending { border-top: 4px solid #FFC107; }
        .stat-card.processing { border-top: 4px solid #17A2B8; }
        .stat-card.shipped { border-top: 4px solid#225979; }
        .stat-card.completed { border-top: 4px solid #28A745; }
        .stat-card.cancelled { border-top: 4px solid #DC3545; }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0;
        }
        .dropdown-content a {
            color: black;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            font-size: 13px;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown {
            position: relative;
            display: inline-block;
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
                    <li><a href="order.php" class="active">Orders</a></li>
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
            <h1 class="admin-title">Order Management</h1>
            
            <!-- Order Stats -->
            <div class="stats-container">
                <div class="stat-card total">
                    <h3>Total Orders</h3>
                    <p><?= $order_counts['total'] ?></p>
                </div>
                <div class="stat-card pending">
                    <h3>Pending</h3>
                    <p><?= $order_counts['pending'] ?></p>
                </div>
                <div class="stat-card processing">
                    <h3>Processing</h3>
                    <p><?= $order_counts['processing'] ?></p>
                </div>
                <div class="stat-card shipped">
                    <h3>Shipped</h3>
                    <p><?= $order_counts['shipped'] ?></p>
                </div>
                <div class="stat-card completed">
                    <h3>Completed</h3>
                    <p><?= $order_counts['completed'] ?></p>
                </div>
                <div class="stat-card cancelled">
                    <h3>Cancelled</h3>
                    <p><?= $order_counts['cancelled'] ?></p>
                </div>
            </div>
            
            <!-- Filter and Search Section -->
            <div class="filter-section">
                <form method="get" class="filter-form">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="">All Orders</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                             <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" placeholder="Order ID, name, email or phone" value="<?= htmlspecialchars($search_query) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sort">Sort By</label>
                        <select name="sort" id="sort">
                            <option value="placed_on" <?= $sort_by === 'placed_on' ? 'selected' : '' ?>>Date</option>
                            <option value="id" <?= $sort_by === 'id' ? 'selected' : '' ?>>Order ID</option>
                            <option value="name" <?= $sort_by === 'name' ? 'selected' : '' ?>>Customer Name</option>
                            <option value="total_price" <?= $sort_by === 'total_price' ? 'selected' : '' ?>>Amount</option>
                            <option value="payment_status" <?= $sort_by === 'payment_status' ? 'selected' : '' ?>>Status</option>
                        </select>
                        <select name="order">
                            <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Descending</option>
                            <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Ascending</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-btn">Apply Filters</button>
                    <a href="order.php" class="reset-btn">Reset</a>
                </form>
            </div>
            
            <!-- Orders Table -->
            <div class="orders-table-container">
                <?php if (empty($orders)): ?>
                    <div class="no-orders">
                        <i class="fas fa-box-open"></i>
                        <p>No orders found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($order['id']) ?></td>
                                    <td>
                                        <div class="customer-info">
                                            <strong><?= htmlspecialchars($order['name']) ?></strong>
                                            <small><?= htmlspecialchars($order['email']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($order['placed_on']))) ?></td>
                                    <td>₱<?= htmlspecialchars(number_format($order['total_price'], 2)) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= htmlspecialchars($order['payment_status']) ?>">
                                            <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="order_details.php?id=<?= $order['id'] ?>" class="action-btn view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="receipt.php?order_id=<?= htmlspecialchars($order['id']) ?>" 
                                            class="receipt-link completed" 
                                            target="_blank"
                                            onclick="return confirm('View receipt for order #<?= htmlspecialchars($order['id']) ?>?')">
                                                <i class="bi bi-receipt-cutoff"></i> View Receipt
                                            </a>
                                                                                
                                        
                                        <!-- <a href="#" class="action-btn print" title="Print Invoice" onclick="printInvoice(<?= $order['id'] ?>)">
                                            <i class="fas fa-print"></i>
                                        </a> -->
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">First</a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Prev</a>
                        <?php endif; ?>
                        
                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        
                        if ($start > 1) echo '<span>...</span>';
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
                        <?php endfor;
                        
                        if ($end < $total_pages) echo '<span>...</span>';
                        ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">Last</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        function updateStatus(orderId, status) {
            if (confirm(`Are you sure you want to change this order status to ${status}?`)) {
                window.location.href = `update_order_status.php?id=${orderId}&status=${status}`;
            }
            return false;
        }
        
        function printInvoice(orderId) {
            window.open(`print_invoice.php?id=${orderId}`, '_blank');
            return false;
        }
        
        // Close dropdowns when clicking elsewhere
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
        
        // Toggle dropdown
        document.querySelectorAll('.dropdown > a').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const dropdown = this.nextElementSibling;
                document.querySelectorAll('.dropdown-content').forEach(d => {
                    if (d !== dropdown) d.style.display = 'none';
                });
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>