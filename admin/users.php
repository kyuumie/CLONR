<?php
session_start();

// Database connection
$db_name = "mysql:host=localhost;dbname=clonr_db";
$username = "root";
$password = "";
$conn = new PDO($db_name, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Handle actions (ban, unban, delete, etc.)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $action = $_GET['action'];
    
    try {
        switch ($action) {
            case 'ban':
                $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['message'] = "User has been banned successfully";
                break;
                
            case 'unban':
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->execute([$user_id]);
                $_SESSION['message'] = "User has been unbanned successfully";
                break;
                
            case 'delete':
                // First, check if user has orders or messages
                $check_orders = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                $check_orders->execute([$user_id]);
                $order_count = $check_orders->fetchColumn();
                
                $check_messages = $conn->prepare("SELECT COUNT(*) FROM messages WHERE user_id = ?");
                $check_messages->execute([$user_id]);
                $message_count = $check_messages->fetchColumn();
                
                if ($order_count > 0 || $message_count > 0) {
                    $_SESSION['error'] = "Cannot delete user with associated orders or messages";
                } else {
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $_SESSION['message'] = "User has been deleted successfully";
                }
                break;
                
            case 'toggle_admin':
                // Get current admin status
                $check = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
                $check->execute([$user_id]);
                $current_type = $check->fetchColumn();
                
                $new_type = ($current_type === 'admin') ? 'user' : 'admin';
                $stmt = $conn->prepare("UPDATE users SET user_type = ? WHERE id = ?");
                $stmt->execute([$new_type, $user_id]);
                $_SESSION['message'] = "User admin status updated successfully";
                break;
        }
        
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error performing action: " . $e->getMessage();
        header("Location: users.php");
        exit();
    }
}

// Handle bulk actions
if (isset($_POST['bulk_action']) && isset($_POST['selected_users'])) {
    $selected_users = $_POST['selected_users'];
    $bulk_action = $_POST['bulk_action'];
    
    try {
        $placeholders = implode(',', array_fill(0, count($selected_users), '?'));
        
        switch ($bulk_action) {
            case 'ban':
                $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE id IN ($placeholders)");
                $stmt->execute($selected_users);
                $_SESSION['message'] = "Selected users have been banned successfully";
                break;
                
            case 'unban':
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id IN ($placeholders)");
                $stmt->execute($selected_users);
                $_SESSION['message'] = "Selected users have been unbanned successfully";
                break;
                
            case 'delete':
                // Check if any selected user has orders or messages
                $check = $conn->prepare("SELECT COUNT(*) FROM orders WHERE user_id IN ($placeholders) 
                                        UNION SELECT COUNT(*) FROM messages WHERE user_id IN ($placeholders)");
                $check->execute($selected_users);
                $has_records = false;
                while ($count = $check->fetchColumn()) {
                    if ($count > 0) {
                        $has_records = true;
                        break;
                    }
                }
                
                if ($has_records) {
                    $_SESSION['error'] = "Cannot delete users with associated orders or messages";
                } else {
                    $stmt = $conn->prepare("DELETE FROM users WHERE id IN ($placeholders)");
                    $stmt->execute($selected_users);
                    $_SESSION['message'] = "Selected users have been deleted successfully";
                }
                break;
        }
        
        header("Location: users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error performing bulk action: " . $e->getMessage();
        header("Location: users.php");
        exit();
    }
}

// Search and filter functionality
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Base query
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
          (SELECT COUNT(*) FROM messages WHERE user_id = u.id AND is_read = 0) as unread_messages
          FROM users u";

// Where conditions
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status_filter !== 'all') {
    $conditions[] = "u.status = ?";
    $params[] = $status_filter;
}

// Add conditions to query
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

// Get total users for pagination
$countQuery = "SELECT COUNT(*) FROM ($query) as total";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute($params);
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

// Add sorting
$sort = $_GET['sort'] ?? 'id';
$order = $_GET['order'] ?? 'asc';
$allowed_sort = ['id', 'name', 'email', 'phone', 'created_at', 'order_count'];
$sort = in_array($sort, $allowed_sort) ? $sort : 'id';
$order = $order === 'desc' ? 'desc' : 'asc';

$query .= " ORDER BY $sort $order LIMIT $limit OFFSET $offset";

// Get users
$stmt = $conn->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLONR - User Management</title>
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Enhanced styles for user management */
        .user-management-tools {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
            width: 100%;
        }
        
        .search-filter-container {
             display: flex;
            gap: 15px; /* Adds space between the search and filter boxes */
            align-items: center;
            width: 100%;
        }
        
        .search-box {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 15px;
            padding-left: 40px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
            .search-box input[type="text"] {
                width: 100%;
                padding: 8px 10px 8px 35px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
        
       .search-box i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        
        .filter-box {
            min-width: 150px;
        }

      .filter-box select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }
                
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .bulk-actions {
            margin: 15px 0;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .bulk-actions select, .bulk-actions button {
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .bulk-actions button {
            background-color: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .pagination a:hover {
            background-color: #f0f0f0;
        }
        
        .pagination .current {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .user-actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .action-btn i {
            font-size: 12px;
        }
        
        .view { background-color: #17a2b8; color: white; }
        .edit { background-color: #ffc107; color: black; }
        .message { background-color: #28a745; color: white; }
        .delete { background-color: #dc3545; color: white; }
        .export { background-color: #6c757d; color: white; }
        .add-new { background-color: #007bff; color: white; }
        .admin-toggle { background-color: #6610f2; color: white; }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge.active { background-color: #d4edda; color: #155724; }
        .status-badge.inactive { background-color: #f8d7da; color: #721c24; }
        .status-badge.banned { background-color: #fff3cd; color: #856404; }
        
        .user-avatar {
            border-radius: 50%;
            object-fit: cover;
            height: 40px;
            width: 40px;
        }
        
        .stats-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            background-color: #e9ecef;
            color: #495057;
            font-size: 12px;
            margin-left: 5px;
        }
        
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            background-color: #f8d7da;
            color: #721c24;
        }
        
        th.sortable {
            cursor: pointer;
        }
        
       
        
        @media (max-width: 768px) {
            .users-table table, .users-table thead, .users-table tbody, 
            .users-table th, .users-table td, .users-table tr {
                display: block;
            }
            
            .users-table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            .users-table tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 10px;
            }
            
            .users-table td {
                border: none;
                position: relative;
                padding-left: 50%;
            }
            
            .users-table td:before {
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
            }
            
            .users-table td:nth-of-type(1):before { content: "ID"; }
            .users-table td:nth-of-type(2):before { content: "Profile"; }
            .users-table td:nth-of-type(3):before { content: "name"; }
            .users-table td:nth-of-type(4):before { content: "Email"; }
            .users-table td:nth-of-type(5):before { content: "Phone"; }
            .users-table td:nth-of-type(6):before { content: "Orders"; }
            .users-table td:nth-of-type(7):before { content: "Messages"; }
            .users-table td:nth-of-type(8):before { content: "Status"; }
            .users-table td:nth-of-type(9):before { content: "Actions"; }
            
            .user-actions {
                justify-content: flex-start;
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
        </div>
    </header>

    <main class="admin-main">
        <div class="admin-container">
            <h1 class="admin-title">User Management</h1>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            <div class="user-management-tools">
                <div class="search-filter-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <form method="GET" action="users.php">
                            <input type="text" name="search" placeholder="Search users..." 
                                value="<?= htmlspecialchars($search) ?>">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                        </form>
                    </div>
                    
                    <div class="filter-box">
                        <form method="GET" action="users.php">
                            <select name="status" onchange="this.form.submit()">
                                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status_filter === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="banned" <?= $status_filter === 'banned' ? 'selected' : '' ?>>Banned</option>
                            </select>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        </form>
                    </div>
                </div>
            </div>
                        
            <div class="users-table">
                <?php if (empty($users)): ?>
                    <div class="no-results">
                        <i class="fas fa-user-slash" style="font-size: 24px; margin-bottom: 10px;"></i>
                        <p>No users found matching your criteria.</p>
                        <a href="users.php" class="action-btn">Reset Filters</a>
                    </div>
                <?php else: ?>
                    <form method="post" id="bulkForm">
                        <div class="bulk-actions">
                            <select name="bulk_action" class="bulk-select">
                                <option value="">Bulk Actions</option>
                                <option value="ban">Ban Selected</option>
                                <option value="unban">Unban Selected</option>
                                <option value="delete">Delete Selected</option>
                            </select>
                            <button type="submit" class="bulk-apply">Apply</button>
                        </div>
                    
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th class="sortable" onclick="sortTable('id')">ID <?= $sort === 'id' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th>Profile</th>
                                <th class="sortable" onclick="sortTable('name')">Name <?= $sort === 'name' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th class="sortable" onclick="sortTable('email')">Email <?= $sort === 'email' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th class="sortable" onclick="sortTable('phone')">Phone <?= $sort === 'phone' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th class="sortable" onclick="sortTable('order_count')">Orders <?= $sort === 'order_count' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th>Messages</th>
                                <th class="sortable" onclick="sortTable('status')">Status <?= $sort === 'status' ? ($order === 'asc' ? '↑' : '↓') : '' ?></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><input type="checkbox" name="selected_users[]" value="<?= $user['id'] ?>"></td>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td>
                                    <img src="../uploaded_img/<?= htmlspecialchars($user['image'] ?? 'default.png') ?>" 
                                         alt="<?= htmlspecialchars($user['name']) ?>" class="user-avatar">
                                </td>
                                <td>
                                    <?= htmlspecialchars($user['name']) ?>
                                    <?php if ($user['user_type'] === 'admin'): ?>
                                        <span class="stats-badge" title="Administrator">ADMIN</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td>
                                    <span class="stats-badge"><?= $user['order_count'] ?></span>
                                </td>
                                <td>
                                    <?php if ($user['unread_messages'] > 0): ?>
                                        <span class="stats-badge" style="background-color: #dc3545; color: white;">
                                            <?= $user['unread_messages'] ?> new
                                        </span>
                                    <?php else: ?>
                                        <span class="stats-badge">0</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?= htmlspecialchars($user['Status']) ?>">
                                        <?= ucfirst(htmlspecialchars($user['Status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="user-actions">
                                       
                                        
                                        <a href="message_user.php?id=<?= $user['id'] ?>" class="action-btn message" title="Message">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                        <a href="users.php?action=toggle_admin&id=<?= $user['id'] ?>" class="action-btn admin-toggle" title="Toggle Admin">
                                            <i class="fas fa-user-shield"></i>
                                        </a>
                                        <?php if ($user['Status'] === 'banned'): ?>
                                            <a href="users.php?action=unban&id=<?= $user['id'] ?>" class="action-btn edit" title="Unban">
                                                <i class="fas fa-unlock"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="users.php?action=ban&id=<?= $user['id'] ?>" class="action-btn delete" title="Ban">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="users.php?action=delete&id=<?= $user['id'] ?>" class="action-btn delete" 
                                           title="Delete" onclick="return confirmDelete()">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </form>
                    
                    <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        // Show limited pagination links
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                        
                        if ($start > 1) {
                            echo '<a href="?page=1&search=' . urlencode($search) . '&status=' . urlencode($status_filter) . '&sort=' . $sort . '&order=' . $order . '">1</a>';
                            if ($start > 2) echo '<span>...</span>';
                        }
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($end < $totalPages): ?>
                            <?php if ($end < $totalPages - 1) echo '<span>...</span>'; ?>
                            <a href="?page=<?= $totalPages ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $totalPages ?></a>
                        <?php endif; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&sort=<?= $sort ?>&order=<?= $order ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
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

    <script>
        // Auto-submit search form when typing stops
        let searchTimer;
        const searchInput = document.querySelector('input[name="search"]');
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
        
        // Confirm before important actions
        function confirmDelete() {
            return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.');
        }
        
        function confirmBan() {
            return confirm('Are you sure you want to ban this user? They will no longer be able to access their account.');
        }
        
        // Add click handlers for ban/unban links
        document.querySelectorAll('a[href*="action=ban"], a[href*="action=unban"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const action = this.href.includes('action=ban') ? 'ban' : 'unban';
                const msg = action === 'ban' 
                    ? 'Are you sure you want to ban this user? They will no longer be able to access their account.'
                    : 'Are you sure you want to unban this user? They will regain access to their account.';
                
                if (!confirm(msg)) {
                    e.preventDefault();
                }
            });
        });
        
        // Bulk actions select all
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Sort table
        function sortTable(column) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentOrder = url.searchParams.get('order');
            
            let newOrder = 'asc';
            if (currentSort === column) {
                newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            }
            
            url.searchParams.set('sort', column);
            url.searchParams.set('order', newOrder);
            window.location.href = url.toString();
        }
        
        // Bulk form submission
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const selected = document.querySelectorAll('input[name="selected_users[]"]:checked');
            const action = document.querySelector('select[name="bulk_action"]').value;
            
            if (selected.length === 0) {
                alert('Please select at least one user');
                e.preventDefault();
                return;
            }
            
            if (!action) {
                alert('Please select a bulk action');
                e.preventDefault();
                return;
            }
            
            if (action === 'delete' && !confirm('Are you sure you want to delete the selected users? This cannot be undone.')) {
                e.preventDefault();
                return;
            }
            
            if (action === 'ban' && !confirm('Are you sure you want to ban the selected users?')) {
                e.preventDefault();
                return;
            }
            
            if (action === 'unban' && !confirm('Are you sure you want to unban the selected users?')) {
                e.preventDefault();
                return;
            }
        });
    </script>
</body>
</html>