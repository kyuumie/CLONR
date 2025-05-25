<?php
require_once '../config.php';

// Start session FIRST
session_start();

// Debugging - log session status
error_log('Delete action - Session ID: ' . session_id());
error_log('Delete action - Admin ID: ' . ($_SESSION['admin_id'] ?? 'NOT SET'));

require_once 'admin_auth.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception("No message ID provided");
    }

    $message_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if (!$message_id || $message_id <= 0) {
        throw new Exception("Invalid message ID");
    }

    // Verify message exists and belongs to admin (either as sender or recipient)
    $check_message = $conn->prepare("
        SELECT id FROM messages 
        WHERE id = ? 
        AND (sender_id = ? OR user_id = ?)
    ");
    $check_message->execute([$message_id, $_SESSION['admin_id'], $_SESSION['admin_id']]);

    if ($check_message->rowCount() === 0) {
        throw new Exception("Message not found or unauthorized access");
    }

    // Delete the message
    $delete_stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $delete_stmt->execute([$message_id]);

    $_SESSION['operation_status'] = [
        'success' => true,
        'message' => 'Message status deleted successfully'
    ];

} catch (Exception $e) {
    error_log("Delete error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
}

header("Location: messages.php");
exit();
?>