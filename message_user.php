<?php
require_once '../config.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch user details
$user = [];
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? AND user_type = 'user'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (!empty($subject) && !empty($message)) {
        $stmt = $conn->prepare("
            INSERT INTO messages 
            (user_id, sender_id, sender_type, subject, message, is_read, created_at)
            VALUES 
            (:user_id, :sender_id, 'admin', :subject, :message, 0, NOW())
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':sender_id' => $_SESSION['admin_id'],
            ':subject' => $subject,
            ':message' => $message
        ]);
        
        $_SESSION['message_success'] = "Message sent successfully!";
        header("Location: messages.php");
        exit();
    }
}

// Fetch conversation history
$conversation = [];
$stmt = $conn->prepare("
    SELECT m.*, 
           sender.name as sender_name, 
           sender.email as sender_email,
           receiver.name as receiver_name
    FROM messages m
    LEFT JOIN users sender ON m.sender_id = sender.id
    LEFT JOIN users receiver ON m.user_id = receiver.id
    WHERE (m.user_id = :user_id AND m.sender_id = :admin_id) OR 
          (m.sender_id = :user_id AND m.user_id = :admin_id)
    ORDER BY m.created_at ASC
");
$stmt->execute([
    ':user_id' => $user_id,
    ':admin_id' => $_SESSION['admin_id']
]);
$conversation = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CLONR - Message User</title>
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
   
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
            <h1 class="admin-title">Message to <?= htmlspecialchars($user['name']) ?></h1>
            
            <div class="conversation-history">
                <?php if (empty($conversation)): ?>
                    <p class="no-messages">No previous messages with this user</p>
                <?php else: ?>
                    <?php foreach ($conversation as $msg): ?>
                    <div class="message-bubble <?= $msg['sender_type'] === 'admin' ? 'outgoing' : 'incoming' ?>">
                        <div class="message-meta">
                            <span class="sender">
                                <?= $msg['sender_type'] === 'admin' ? 'You' : htmlspecialchars($msg['sender_name']) ?>
                            </span>
                            <span class="time"><?= date('M j, Y g:i a', strtotime($msg['created_at'])) ?></span>
                        </div>
                        <div class="message-subject"><?= htmlspecialchars($msg['subject']) ?></div>
                        <div class="message-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="message-form">
                <form action="message_user.php?id=<?= $user_id ?>" method="post">
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                    <a href="messages.php" class="btn-secondary">Back to All Messages</a>
                </form>
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
</body>
</html>