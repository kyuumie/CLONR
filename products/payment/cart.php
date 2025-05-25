<?php
// Connect to database
$host = 'localhost';
$dbname = 'clonr_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Get user balance (from `money` column)
$userBalance = 0.00;
if ($user_id) {
    try {
        // OPTIONAL: Set initial balance
        $stmt = $conn->prepare("UPDATE users SET money = 10000000.00 WHERE id = ? AND (money IS NULL OR money = 0)");
        $stmt->execute([$user_id]);

        // Fetch actual money balance
        $stmt = $conn->prepare("SELECT money FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $userBalance = $stmt->fetchColumn();
    } catch(PDOException $e) {
        $error = "Error fetching user balance: " . $e->getMessage();
    }
}

// Get cart items
$cartItems = [];
try {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching cart items: " . $e->getMessage();
}

// Handle remove item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $itemId = $_POST['item_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$itemId, $user_id]);
        header("Location: cart.php");
        exit;
    } catch(PDOException $e) {
        $error = "Error removing item: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CLONR</title>
    <link rel="stylesheet" href="../../global.css">
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
<header>
    <div class="container">
        <a href="../../main.php"><h1 class="title">CLONR</h1></a>
        <nav>
            <ul class="navbar">
                <li><a href="../../main.php">HOME</a></li>
                <li class="dropdown">SHOP
                    <ul class="dropdown-menu">
                        <li><a href="../tshirts.php">T-shirts</a></li>
                        <li><a href="../jackets.php">Jackets</a></li>
                        <li><a href="../pants.php">Pants</a></li>
                        <li><a href="../shorts.php">Shorts</a></li>
                        <li><a href="../accessories.php">Accessories</a></li>
                    </ul>
                </li>
                <li><a href="../../sizechart.php">SIZE CHART</a></li>
                <li><a href="../../contact.php">CONTACT US</a></li>
                <li><a href="../payment/cart.php">CART</a></li>
            </ul>
        </nav>
        <div class="profile-container">
            <?php include '../../header.php'; ?>
        </div>
    </div>
</header>

<main class="cart">
    <h1>Your Shopping Cart</h1>
    <p><strong>Your Balance:</strong> ₱<?php echo number_format($userBalance, 2); ?></p>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($cartItems as $item): 
                    $total = $item['price'] * $item['quantity'];
                    $grandTotal += $total;
                ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['size']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td>₱<?php echo number_format($total, 2); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" name="remove_item">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right;">Grand Total:</td>
                    <td>₱<?php echo number_format($grandTotal, 2); ?></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="cart-actions">
            
            <a href="../../main.php" class="continue-shopping">Continue Shopping</a>
            <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</main>

<hr class="custom-hr">

<footer>
    <div class="footer-content">
        <h2>CLONR - Wear the Movement</h2>
        <p>Email: customerservice.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
        <p>© 2025 CLONR. All Rights Reserved.</p>
    </div>
</footer>
<script src="/CLONR/CLONR-main/js/cart.js"></script>
</body>
</html>
