<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    // Add logging for debugging
    error_log('Unauthorized access attempt - admin_id not set in session');
    header("Location: ../login.php");
    exit();
}

// Optional: Verify admin still exists in database
$check_admin = $conn->prepare("SELECT id FROM users WHERE id = ? AND user_type = 'admin'");
$check_admin->execute([$_SESSION['admin_id']]);
if ($check_admin->rowCount() === 0) {
    // Admin no longer exists in database
    session_destroy();
    error_log('Admin account not found in database - ID: ' . $_SESSION['admin_id']);
    header("Location: ../login.php");
    exit();
}
?>