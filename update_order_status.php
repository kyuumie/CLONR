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

    // Handle GET request for quick status update (from the cancel button)
    if (isset($_GET['id'])) {
        $order_id = intval($_GET['id']);
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        
        if ($order_id > 0 && in_array($status, ['pending', 'completed', 'cancelled'])) {
            $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            
            // Log the action
            $action = "Changed order #$order_id status to $status";
            $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, timestamp) VALUES (?, ?, NOW())");
            $log_stmt->execute([$admin_id, $action]);
            
            header("Location: order_details.php?id=$order_id&updated=1");
            exit;
        }
    }

    // Handle POST request for full status update (from the order details page)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
        
        if ($order_id > 0 && in_array($status, ['pending', 'completed', 'cancelled'])) {
            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            
            // Add order history record
            $history_stmt = $conn->prepare("INSERT INTO order_history (order_id, status, notes, updated_by, updated_at) 
                                           VALUES (?, ?, ?, ?, NOW())");
            $history_stmt->execute([$order_id, $status, $notes, $admin_id]);
            
            // Log the action
            $action = "Updated order #$order_id status to $status";
            if (!empty($notes)) {
                $action .= " with notes: " . substr($notes, 0, 50) . (strlen($notes) > 50 ? '...' : '');
            }
            $log_stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, timestamp) VALUES (?, ?, NOW())");
            $log_stmt->execute([$admin_id, $action]);
            
            header("Location: order_details.php?id=$order_id&updated=1");
            exit;
        }
    }

    // If no valid action was performed, redirect back
    header("Location: order.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}