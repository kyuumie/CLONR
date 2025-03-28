<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Verify the script is being called
file_put_contents('debug.log', "[" . date('Y-m-d H:i:s') . "] Script accessed\n", FILE_APPEND);

// Database configuration
$host = 'localhost';
$dbname = 'clonr_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Fixed product details for product 1
    $product = [
        'user_id' => 1, // Temporary user ID
        'pid' => 1,
        'name' => 'CIPHER SPLICED SHORTS - KHAKI/CREAM',
        'price' => 1100.00,
        'image' => 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg',
        'size' => $_POST['size'],
        'quantity' => (int)$_POST['quantity']
    ];

    try {
        $stmt = $conn->prepare("INSERT INTO cart 
            (user_id, pid, name, size, quantity, price, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $product['user_id'],
            $product['pid'],
            $product['name'],
            $product['size'],
            $product['quantity'],
            $product['price'],
            $product['image']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Product added to cart!']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>