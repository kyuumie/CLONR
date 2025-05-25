<?php
require_once '../config.php';
require_once 'admin_auth.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_GET['id'])) {
        throw new Exception("No message ID provided");
    }

    $message_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$message_id) {
        throw new Exception("Invalid message ID");
    }

    // Start transaction for atomic operation
    $conn->beginTransaction();

    // Get current read status with locking to prevent race conditions
    $stmt = $conn->prepare("SELECT is_read FROM messages WHERE id = :id FOR UPDATE");
    $stmt->bindParam(':id', $message_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$message) {
        throw new Exception("Message not found");
    }

    $new_status = $message['is_read'] ? 0 : 1;
    
    // Update the read status
    $update = $conn->prepare("UPDATE messages SET is_read = :status WHERE id = :id");
    $update->bindParam(':status', $new_status, PDO::PARAM_INT);
    $update->bindParam(':id', $message_id, PDO::PARAM_INT);
    $update->execute();

    // Commit the transaction
    $conn->commit();

    // Store status in session for feedback
    $_SESSION['operation_status'] = [
        'success' => true,
        'message' => 'Message status updated successfully'
    ];

} catch (Exception $e) {
    // Roll back on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $_SESSION['operation_status'] = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
    
    // Log the error for admin review
    error_log("mark_read.php error: " . $e->getMessage());
}

// Redirect back to messages page
header("Location: messages.php");
exit();