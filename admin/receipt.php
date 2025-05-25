<?php
@include '../config.php';
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin_id'])){
    header('location:../login.php');
    exit;
}

// Get the order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if($order_id <= 0){
    header('location:orders.php');
    exit;
}

// Fetch order details (no user_id restriction for admin)
try {
    $order_query = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $order_query->execute([$order_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if(!$order){
        echo "Order not found";
        exit;
    }
} catch (PDOException $e) {
    error_log("Admin Receipt Error: Error fetching order details for order ID " . $order_id . " - " . $e->getMessage());
    echo "An error occurred while fetching order details. Please try again later.";
    exit;
}
$products_on_receipt = [];
try {
    // Corrected query: we use product_name directly from the order_items table
    $order_items_query = $conn->prepare("
        SELECT
            oi.id,
            oi.order_id,
            oi.product_name,
            oi.size,
            oi.quantity,
            oi.price
        FROM `order_items` oi
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ");
    $order_items_query->execute([$order_id]);
    $items_from_db = $order_items_query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items_from_db as $item_data) {
        $line_item_total = $item_data['quantity'] * $item_data['price'];

        $products_on_receipt[] = [
            'name' => htmlspecialchars($item_data['product_name']),
            'quantity' => $item_data['quantity'],
            'size' => htmlspecialchars($item_data['size'] ?? 'N/A'),
            'unit_price' => $item_data['price'],
            'total_line_price' => $line_item_total,
            'description' => 'Quantity: ' . $item_data['quantity'] . ', Size: ' . htmlspecialchars($item_data['size'] ?? 'N/A')
        ];
    }
} catch (PDOException $e) {
    error_log("Admin Receipt Error: Error fetching order items for order ID " . $order_id . " - " . $e->getMessage());
    $products_on_receipt = [];
    echo "An error occurred while fetching order items. Please contact support.";
    exit;
}
// Set default payment status if not set
if(!isset($order['payment_status'])) {
    $order['payment_status'] = 'paid';
}

// Format date
if(!empty($order['placed_on'])) {
    $timestamp = strtotime($order['placed_on']);
    if($timestamp !== false) {
        $order['placed_on'] = date('M d, Y \a\t g:i A', $timestamp);
    } else {
        $order['placed_on'] = 'Date not available';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Admin Receipt - CLONR</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap");
    body {
      font-family: "Poppins", sans-serif;
      background: #f9f9f9;
    }
    .print-only {
      display: none;
    }
    @media print {
      .no-print {
        display: none;
      }
      .print-only {
        display: block;
      }
      body {
        background: white;
      }
      .receipt-container {
        box-shadow: none;
        border: 1px solid #ddd;
      }
      .red-accent {
        color: #b30000 !important;
      }
    }
    .red-accent {
      color: #b30000;
    }
    .bg-clonr-red {
      background-color: #b30000;
    }
    .border-clonr-red {
      border-color: #b30000;
    }
    .admin-badge {
      background-color: #4CAF50;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
    }
  </style>
</head>
<body class="min-h-screen flex justify-center items-center p-4 md:p-6 bg-gray-100">
  <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg flex flex-col overflow-hidden receipt-container">
   <!-- Header -->
   <header class="bg-black flex items-center px-6 py-4 rounded-t-xl no-print" role="banner">
    <h1 class="flex-grow text-center text-white font-bold text-2xl md:text-3xl tracking-wide">
     Admin Receipt <span class="admin-badge">ADMIN VIEW</span>
    </h1>
    <button onclick="window.print()" class="text-white bg-clonr-red px-4 py-2 rounded-lg hover:bg-red-800 transition">
      <i class="fas fa-print"></i> <span class="hidden md:inline">Print</span>
    </button>
   </header>

   <!-- Print header (only shows when printing) -->
   <div class="print-only p-6 text-center border-b border-clonr-red">
     <h1 class="text-3xl font-bold mb-2 red-accent">CLONR</h1>
     <p class="text-gray-600">Official Receipt (Admin Copy)</p>
     <p class="text-sm text-gray-500 mt-4">Order #<?= htmlspecialchars($order['id']) ?> | <?= htmlspecialchars($order['placed_on']) ?></p>
   </div>

   <!-- Brand Logo and Name -->
   <section class="bg-white flex flex-col items-center gap-4 py-8 px-6 text-center no-print">
    <img alt="CLONR Logo" class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-black" src="https://storage.googleapis.com/a1aa/image/10c90c25-0fec-4ecd-6ca2-279e0ae45274.jpg">
    <h2 class="text-black font-bold text-3xl md:text-4xl tracking-wide">CLONR</h2>
    <div class="admin-badge no-print">ADMIN VIEW</div>
   </section>

   <!-- Order Info -->
   <section class="px-6 md:px-10 py-4 border-b border-gray-200">
     <div class="grid grid-cols-2 gap-3 md:gap-4">
       <div>
         <p class="text-gray-500 text-sm">Order Number</p>
         <p class="font-semibold">#<?= htmlspecialchars($order['id']) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">Date</p>
         <p class="font-semibold"><?= htmlspecialchars($order['placed_on']) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">Payment Method</p>
         <p class="font-semibold"><?= ucfirst(htmlspecialchars($order['method'])) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">Status</p>
         <p class="font-semibold capitalize text-clonr-red"><?= htmlspecialchars($order['payment_status']) ?></p>
       </div>
     </div>
   </section>

   <!-- Receipt Content -->
   <section class="px-6 md:px-10 py-6">
    <div class="mb-6">
     <h3 class="text-black font-bold text-xl md:text-2xl mb-4">
      Items Purchased
     </h3>
     <ul aria-label="List of purchased items" class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
      <?php if(!empty($products_on_receipt)): ?>
        <?php foreach($products_on_receipt as $item): ?>
        <li class="flex justify-between py-3">
          <div>
            <p class="font-semibold text-black"><?= htmlspecialchars($item['name']) ?></p>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($item['description']) ?></p>
            <p class="text-gray-600 text-xs">Unit Price: ₱<?= number_format($item['unit_price'], 2) ?></p>
          </div>
          <p class="font-mono text-black">₱<?= number_format($item['total_line_price'], 2) ?></p>
        </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="py-3 text-gray-500 text-center">No items found for this order.</li>
      <?php endif; ?>
     </ul>
    </div>
    <div aria-live="polite" class="border-t border-gray-200 pt-4 flex justify-between items-center">
     <p class="font-semibold text-black text-lg">
      Subtotal
     </p>
     <p class="font-mono text-black text-lg">
      ₱<?= number_format($order['total_price'], 2) ?>
     </p>
    </div>
    
    <div class="border-t-2 border-clonr-red pt-4 flex justify-between items-center">
     <p class="font-bold text-black text-xl md:text-2xl">
      Total
     </p>
     <p class="font-mono font-bold text-clonr-red text-xl md:text-2xl">
      ₱<?= number_format($order['total_price'], 2) ?>
     </p>
    </div>
   </section>

   <!-- Customer Info -->
   <section class="px-6 md:px-10 py-4 bg-gray-50">
     <h3 class="text-black font-bold text-lg md:text-xl mb-3">Customer Information</h3>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
       <div>
         <p class="text-gray-500 text-sm">Name</p>
         <p class="font-semibold"><?= htmlspecialchars($order['name']) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">Email</p>
         <p class="font-semibold"><?= htmlspecialchars($order['email']) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">Phone</p>
         <p class="font-semibold"><?= htmlspecialchars($order['number']) ?></p>
       </div>
       <div>
         <p class="text-gray-500 text-sm">User ID</p>
         <p class="font-semibold"><?= htmlspecialchars($order['user_id']) ?></p>
       </div>
     </div>
     <div class="mt-3">
       <p class="text-gray-500 text-sm">Shipping Address</p>
       <p class="font-semibold"><?= nl2br(htmlspecialchars($order['address'])) ?></p>
     </div>
   </section>

   <!-- Footer with Thank You -->
   <section class="px-6 md:px-10 py-4 bg-black text-center text-white">
    <p class="text-lg font-light mb-2">
     Order #<?= htmlspecialchars($order['id']) ?> - Admin View
    </p>
    <p class="text-sm text-gray-300">
     Customer: <?= htmlspecialchars($order['name']) ?> | <?= htmlspecialchars($order['placed_on']) ?>
    </p>
    <div class="mt-3 no-print">
      <a href="order.php" class="inline-block bg-clonr-red hover:bg-red-800 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
        Back to Orders
      </a>
    </div>
   </section>
  </div>

  <script>
    // Print functionality
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey && e.key === 'p') {
        e.preventDefault();
        window.print();
      }
    });

    // Add animation when printing is done (for browsers that support it)
    window.addEventListener('afterprint', function() {
      console.log('Printing completed or cancelled');
    });
  </script>
</body>
</html>