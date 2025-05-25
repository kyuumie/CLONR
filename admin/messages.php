<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
// Display operation status if set
if (isset($_SESSION['operation_status'])) {
    $status = $_SESSION['operation_status'];
    if ($status['success']) {
        echo '<div class="alert success">' . htmlspecialchars($status['message']) . '</div>';
    } else {
        echo '<div class="alert error">' . htmlspecialchars($status['message']) . '</div>';
    }
    unset($_SESSION['operation_status']); // Clear the status
}

// Fetch all users for the dropdown
$users = [];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE user_type = 'user'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch both received AND sent messages
$messages = [];
$stmt = $conn->prepare("
    SELECT m.*, 
           sender.name as sender_name, 
           sender.email as sender_email,
           receiver.name as receiver_name,
           receiver.email as receiver_email
    FROM messages m
    LEFT JOIN users sender ON m.sender_id = sender.id
    LEFT JOIN users receiver ON m.user_id = receiver.id
    WHERE m.sender_type = 'user' OR (m.sender_type = 'admin' AND m.sender_id = :admin_id)
    ORDER BY m.created_at DESC
");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLONR - Messages</title>
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        
        /* Improved message styling */
        .message-card {
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .outgoing {
            background-color: #EFF8FF;
            border-left: 4px solid #3B82F6;
        }
        
        .incoming {
            background-color: #F0FDF4;
            border-left: 4px solid #10B981;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .message-header h3 {
            margin: 0;
            color: #1F2937;
            font-size: 1.1rem;
        }
        
        .message-date {
            color: #6B7280;
            font-size: 0.85rem;
        }
        
        .message-direction {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }
        
        .direction-icon {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }
        
        .outgoing .direction-icon {
            color: #3B82F6;
        }
        
        .incoming .direction-icon {
            color: #10B981;
        }
        
        .message-content {
            padding: 0.75rem;
            background: white;
            border-radius: 6px;
            margin-bottom: 1rem;
            border: 1px solid #E5E7EB;
        }
        
        .message-actions {
            display: flex;
            gap: 0.75rem;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .reply {
            background-color: #3B82F6;
            color: white;
        }
        
        .reply:hover {
            background-color: #2563EB;
        }
        
        .delete {
            background-color: #EF4444;
            color: white;
        }
        
        .delete:hover {
            background-color: #DC2626;
        }
        
        .action-btn:not(.reply):not(.delete) {
            background-color: #E5E7EB;
            color: #4B5563;
        }
        
        .action-btn:not(.reply):not(.delete):hover {
            background-color: #D1D5DB;
        }
        
        .no-messages {
            text-align: center;
            padding: 2rem;
            color: #6B7280;
            font-style: italic;
        }
        
        /* Message form styling */
        .message-form {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        textarea.form-control {
            min-height: 150px;
        }
        
        .btn-primary {
            background-color: #3B82F6;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.2s;
        }
        
        .btn-primary:hover {
            background-color: #2563EB;
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
            <h1 class="admin-title">Message Center</h1>
            
            <!-- Admin Message Form -->
            <div class="message-form">
                <h2>Send Message to User</h2>
                <form action="send_admin_message.php" method="post">
                    <div class="form-group">
                        <label for="user_id">Select User:</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']) ?>">
                                    <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="messages-list">
    <?php if (empty($messages)): ?>
        <p class="no-messages">No messages found.</p>
    <?php else: ?>
        <?php foreach ($messages as $message): ?>
        <div class="message-card <?= $message['is_read'] ? 'read' : 'unread' ?> <?= $message['sender_type'] === 'admin' ? 'outgoing' : 'incoming' ?>">
            <div class="message-header">
                <h3>
                    <?php if ($message['sender_type'] === 'admin'): ?>
                        <a href="message_user.php?id=<?= $message['user_id'] ?>"><?= htmlspecialchars($message['subject']) ?></a>
                    <?php else: ?>
                        <a href="message_user.php?id=<?= $message['sender_id'] ?>"><?= htmlspecialchars($message['subject']) ?></a>
                    <?php endif; ?>
                </h3>
                <span class="message-date"><?= htmlspecialchars(date('M d, Y h:i A', strtotime($message['created_at']))) ?></span>
            </div>
            
            <div class="message-direction">
                <?php if ($message['sender_type'] === 'admin'): ?>
                    <span class="direction-icon">↗</span>
                    <span class="direction-text">
                        You sent to 
                        <a href="message_user.php?id=<?= $message['user_id'] ?>">
                            <strong><?= htmlspecialchars($message['receiver_name']) ?></strong>
                        </a>
                        (<?= htmlspecialchars($message['receiver_email']) ?>)
                    </span>
                <?php else: ?>
                    <span class="direction-icon">↙</span>
                    <span class="direction-text">
                        Received from 
                        <a href="message_user.php?id=<?= $message['sender_id'] ?>">
                            <strong><?= htmlspecialchars($message['sender_name']) ?></strong>
                        </a>
                        (<?= htmlspecialchars($message['sender_email']) ?>)
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="message-content">
                <?= nl2br(htmlspecialchars($message['message'])) ?>
            </div>
            
            <div class="message-actions">
                <?php if ($message['sender_type'] !== 'admin'): ?>
                    <a href="message_user.php?id=<?= $message['sender_id'] ?>" class="action-btn reply">Reply</a>
                <?php endif; ?>
                <a href="mark_read.php?id=<?= $message['id'] ?>" class="action-btn">Mark as <?= $message['is_read'] ? 'Unread' : 'Read' ?></a>
                <a href="delete_message.php?id=<?= $message['id'] ?>" class="action-btn delete">Delete</a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: noreply.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>