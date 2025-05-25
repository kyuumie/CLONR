<?php
@include '../../config.php';
session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
   exit;
}

// Get current user balance
$get_money = $conn->prepare("SELECT money FROM users WHERE id = ?");
$get_money->execute([$user_id]);
$user_money_row = $get_money->fetch(PDO::FETCH_ASSOC);

if (!$user_money_row) {
    die("User not found.");
}

$user_balance = $user_money_row['money'];
$cart_total = 0;
$cart_products = [];

// Get cart items
$cart_query = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$cart_query->execute([$user_id]);

if($cart_query->rowCount() > 0){
   while($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)){
      $cart_products[] = [
   'name' => $cart_item['name'],
   'price' => $cart_item['price'],
   'quantity' => $cart_item['quantity'],
   'product_id' => $cart_item['pid'],
   'description' => 'Quantity: ' . $cart_item['quantity'],
   'size' => $cart_item['size'],


];
      $cart_total += ($cart_item['price'] * $cart_item['quantity']);
   }
}

// Handle form submission
if(isset($_POST['place_order'])){
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['street'] .' '. $_POST['brgy'] .' '. $_POST['city'] .' '. $_POST['region'] .' '. $_POST['country'], FILTER_SANITIZE_STRING);
   $dt = new DateTime('now', new DateTimeZone('Asia/Manila'));
  
   $total_products = implode(', ', array_map(function($item) {
      return $item['name'] . ' (' . $item['quantity'] . ')' .' (' . $item['size'] . ')' ;
   }, $cart_products));

   // Check for duplicate orders
   $order_query = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ? AND name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ?");
   $order_query->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total]);

   if($cart_total == 0){
      $message[] = 'Your cart is empty.';
   } elseif ($cart_total > $user_balance) {
      $message[] = 'You do not have enough money to place this order.';
   } elseif($order_query->rowCount() > 0) {
      $message[] = 'Order already placed!';
   } else {
      // Start transaction
      $conn->beginTransaction();
      
      try {
         // Insert into orders table first to get the order_id
         $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
         $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total]);
         
         // Get the last inserted order ID
         $order_id = $conn->lastInsertId();
         
         // Insert order items
         foreach ($cart_products as $item) {
            $insert_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price,size) VALUES (?, ?, ?, ?, ?,?)");
            $insert_item->execute([$order_id, $item['product_id'], $item['name'], $item['quantity'], $item['price'], $item['size']]);
         }
         
         // Deduct money from user's balance
         $new_balance = $user_balance - $cart_total;
         $update_balance = $conn->prepare("UPDATE users SET money = ? WHERE id = ?");
         $update_balance->execute([$new_balance, $user_id]);

         // Clear user's cart
         $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
         $delete_cart->execute([$user_id]);
         
         // Commit transaction
         $conn->commit();

         // Redirect to receipt page with order ID
         header('Location: receipt.php?order_id='.$order_id);
         exit;
         
      } catch (Exception $e) {
         // Rollback transaction on error
         $conn->rollBack();
         $message[] = 'Error placing order: ' . $e->getMessage();
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1" name="viewport"/>
  <title>Checkout - CLONR</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap");
    body {
      font-family: "Poppins", sans-serif;
      background: #f9f9f9;
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
    .form-input {
      transition: all 0.3s;
    }
    .form-input:focus {
      border-color: #b30000;
      box-shadow: 0 0 0 3px rgba(179, 0, 0, 0.2);
    }
    .scrollbar::-webkit-scrollbar {
      width: 6px;
    }
    .scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    .scrollbar::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }
    .scrollbar::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }
     .custom-hr {
    width: 100%; 
    border: 1px solid #800020;
    margin-top: 5%;
  }
  </style>
</head>
<body class="min-h-screen bg-gray-100">
  <div class="w-full max-w-6xl mx-auto p-4 md:p-6">
   <!-- Header -->
   <header class="bg-black flex items-center px-6 py-4 rounded-t-xl" role="banner">
    <a href="../../main.php" class="text-white text-2xl font-light focus:outline-none focus:ring-2 focus:ring-white rounded">
     <i class="fas fa-arrow-left"></i>
    </a>
    <h1 class="flex-grow text-center text-white font-bold text-2xl md:text-3xl tracking-wide">
     Checkout
    </h1>
    <div class="w-8"></div> <!-- Spacer for alignment -->
   </header>

   <!-- Main Content -->
   <div class="flex flex-col lg:flex-row gap-6">
     <!-- Left Column - Shipping Information -->
     <div class="lg:w-2/3">
       <div class="bg-white rounded-b-xl shadow-lg overflow-hidden">
         <!-- Messages -->
         <?php if (!empty($message)): ?>
           <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
             <?php foreach ($message as $msg): ?>
               <p><?= htmlspecialchars($msg) ?></p>
             <?php endforeach; ?>
           </div>
         <?php endif; ?>

         <div class="p-6">
           <h2 class="text-xl font-bold text-black mb-6 border-b border-gray-200 pb-4">Shipping Information</h2>
           
           <form action="" method="POST" class="space-y-4">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                 <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                 <input type="text" id="name" name="name" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="Enter your full name">
               </div>
               <div>
                 <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                 <input type="tel" id="number" name="number" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="Enter your phone number">
               </div>
             </div>
             
             <div>
               <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
               <input type="email" id="email" name="email" required 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                      placeholder="Enter your email">
             </div>
             
             <input type="hidden" name="method" value="OGcash">
             
             <h3 class="text-lg font-bold text-black mt-6 mb-4 border-b border-gray-200 pb-2">Shipping Address</h3>
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                 <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Street Address</label>
                 <input type="text" id="street" name="street" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="House # and Street name">
               </div>
               <div>
                 <label for="brgy" class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                 <input type="text" id="brgy" name="brgy" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="Barangay">
               </div>
             </div>
             
             <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                 <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                 <input type="text" id="city" name="city" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="City">
               </div>
               <div>
                 <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                 <input type="text" id="region" name="region" required 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                        placeholder="Region">
               </div>
             </div>
             
             <div>
               <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
               <input type="text" id="country" name="country" required 
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg form-input focus:outline-none"
                      placeholder="Country">
             </div>
         </div>
       </div>
     </div>

      <!-- Right Column - Order Summary -->
     <div class="lg:w-1/3">
       <div class="bg-white rounded-xl shadow-lg sticky top-6">
         <div class="p-6">
           <h2 class="text-xl font-bold text-black mb-6 border-b border-gray-200 pb-4">Order Summary</h2>
           
           <!-- Payment Method -->
           <div class="mb-6">
             <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Payment Method</h3>
             <div class="flex items-center bg-gray-100 rounded-lg p-3">
               <div class="bg-white p-2 rounded-md shadow-sm mr-3">
                 <img src="https://storage.googleapis.com/a1aa/image/10c90c25-0fec-4ecd-6ca2-279e0ae45274.jpg" alt="GCash" class="h-8 w-auto">
               </div>
               <div>
                 <p class="font-medium text-black">OGcash</p>
                 <p class="text-sm text-gray-500">Pay with your GCash account</p>
               </div>
             </div>
           </div>
           
           <!-- Order Items -->
           <div class="mb-6">
             <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Your Order</h3>
             <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 max-h-64 overflow-y-auto scrollbar">
               <?php if(empty($cart_products)): ?>
                 <div class="p-4 text-center text-gray-500">
                   Your cart is empty
                 </div>
               <?php else: ?>
                 <?php foreach($cart_products as $item): ?>
                 <div class="p-4 flex justify-between items-start">
                   <div>
                     <p class="font-medium text-black"><?= htmlspecialchars($item['name']) ?></p>
                     <p class="text-sm text-gray-500">Qty: <?= htmlspecialchars($item['quantity']) ?></p>
                     <p class="text-sm text-gray-500"> <?= htmlspecialchars($item['size']) ?></p>
                   </div>
                   <p class="font-medium text-black">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                 </div>
                 <?php endforeach; ?>
               <?php endif; ?>
             </div>
           </div>
           
           <!-- Order Totals -->
           <div class="space-y-3 border-t border-gray-200 pt-4">
             <div class="flex justify-between">
               <span class="text-gray-600">Subtotal</span>
               <span class="font-medium text-black">₱<?= number_format($cart_total, 2) ?></span>
             </div>
             <div class="flex justify-between">
               <span class="text-gray-600">Shipping</span>
               <span class="font-medium text-black">Free</span>
             </div>
             <div class="flex justify-between text-lg font-bold pt-2 border-t-2 border-clonr-red">
               <span>Total</span>
               <span class="text-clonr-red">₱<?= number_format($cart_total, 2) ?></span>
             </div>
           </div>
           
           <!-- User Balance -->
           <div class="mt-6 pt-4 border-t border-gray-200">
             <div class="flex justify-between items-center mb-2">
               <span class="text-gray-600">Your Balance</span>
               <span class="font-medium <?= ($user_balance >= $cart_total) ? 'text-green-600' : 'text-red-600' ?>">
                 ₱<?= number_format($user_balance, 2) ?>
               </span>
             </div>
             <?php if ($cart_total > $user_balance): ?>
               <p class="text-sm text-red-600 mt-1">Insufficient balance to place this order</p>
             <?php elseif (empty($cart_products)): ?>
               <p class="text-sm text-red-600 mt-1">Your cart is empty</p>
             <?php endif; ?>
           </div>
         </div>
         
         <!-- Place Order Button -->
         <div class="p-6 bg-gray-50 border-t border-gray-200">
           <button type="submit" name="place_order" 
                   class="w-full bg-clonr-red hover:bg-red-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 <?= ($cart_total == 0 || $cart_total > $user_balance) ? 'opacity-50 cursor-not-allowed' : '' ?>" 
                   <?= ($cart_total == 0 || $cart_total > $user_balance) ? 'disabled' : '' ?>>
             Place Order - ₱<?= number_format($cart_total, 2) ?>
           </button>
           </form>
           
           <p class="text-xs text-gray-500 text-center mt-4">
             By placing your order, you agree to our <a href="#" class="text-clonr-red hover:underline">Terms</a> and <a href="#" class="text-clonr-red hover:underline">Privacy Policy</a>.
           </p>
         </div>
       </div>
     </div>
   </div>
  


  <footer class="py-8 mt-12 rounded-b-xl">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <hr class="custom-hr">
      <br>
     <h2 class="text-2xl font-bold mb-2 text-black">CLONR</h2>
     <p class="mb-1 text-black">Wear the Movement</p>
     <p class="text-black">Email: customerservice.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
     <p class="text-black">© 2025 CLONR. All Rights Reserved.</p>
    </div>
  </footer>
  </div>

  <script>
    // Simple form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const inputs = this.querySelectorAll('input[required]');
      let isValid = true;
      
      inputs.forEach(input => {
        if (!input.value.trim()) {
          input.classList.add('border-red-500');
          isValid = false;
        } else {
          input.classList.remove('border-red-500');
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
      }
    });
  </script>
</body>
</html>