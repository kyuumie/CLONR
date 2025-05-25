<?php
// Start session and check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    die("Please login first");
}
$db_name = "mysql:host=localhost;dbname=clonr_db";
$username = "root";
$password = "";
// Connect to database (simple way)
$conn = new PDO($db_name, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get form data
$user_id = $_POST['user_id'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$admin_id = $_SESSION['admin_id'];

// Simple validation
if (empty($user_id) || empty($subject) || empty($message)) {
    die("Please fill all fields");
}

// Insert message
try {
    $stmt = $conn->prepare("
        INSERT INTO messages
        (user_id, sender_id, sender_type, subject, message, is_read, created_at)
        VALUES 
        (:user_id, :sender_id, 'admin', :subject, :message, 0, NOW())
    ");
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':sender_id' => $admin_id,
        ':subject' => $subject,
        ':message' => $message
    ]);
    
    // Simple success message
    echo "Message sent! <a href='messages.php'>Go back</a>";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>