<?php

include '../../config.php'; 

if (!isset($conn) || !$conn) {
     error_log("Database connection is not established via config.php in product display page");
     die("Database connection error. Please check logs.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$loggedInUserId = $_SESSION['user_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
    
    if (!$loggedInUserId) {
        $_SESSION['review_error'] = "You must be logged in to leave a review.";
        
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
    $productId = $_POST['product_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $reviewText = trim($_POST['review_text'] ?? '');
    $postAnonymous = isset($_POST['post_anonymous']); 

    
    
    
    $anonymousUserId = 999; 
    $userIdToStore = $postAnonymous ? $anonymousUserId : $loggedInUserId;


    
    $errors = [];
    if (empty($productId) || !is_numeric($productId)) {
        $errors[] = "Invalid product ID.";
    }
     
    if (!isset($rating) || !is_numeric($rating) || $rating < 1 || $rating > 5) {
        $errors[] = "A rating between 1 and 5 is required.";
    }
     
    if (!$userIdToStore || !is_numeric($userIdToStore)) {
         $errors[] = "Could not determine valid user ID for review submission. Anonymous user ID might be misconfigured.";
    }


    if (empty($errors)) {
        
        $sql = "INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (:product_id, :user_id, :rating, :review_text)";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute([
                ':product_id' => $productId,
                ':user_id' => $userIdToStore,
                ':rating' => $rating,
                ':review_text' => empty($reviewText) ? null : $reviewText 
            ]);

            
            $_SESSION['review_success'] = "Your review has been submitted!";
            
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();

        } catch (\PDOException $e) {
            error_log("Review Submission Error: " . $e->getMessage());
            $_SESSION['review_error'] = "Error submitting review. Please try again.";
            
             header("Location: " . $_SERVER['REQUEST_URI']);
             exit();
        }
    } else {
        
        $_SESSION['review_errors'] = $errors;
         header("Location: " . $_SERVER['REQUEST_URI']);
         exit();
    }
}



$productId = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = (int) $_GET['id']; 
}


$product = null;
$allImages = []; 

if ($productId) {
    try {
        
        $sqlProduct = "SELECT id, name, description, price, category, image_url FROM products WHERE id = :id LIMIT 1";
        $stmtProduct = $conn->prepare($sqlProduct);
        $stmtProduct->execute([':id' => $productId]);
        $product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

        
        if ($product) {
             
             if (!empty($product['image_url'])) {
                 $allImages[] = $product['image_url'];
             }

             
             $sqlImages = "SELECT image_url FROM product_images WHERE product_id = :product_id ORDER BY sort_order ASC, id ASC";
             $stmtImages = $conn->prepare($sqlImages);
             $stmtImages->execute([':product_id' => $productId]);
             $additionalImages = $stmtImages->fetchAll(PDO::FETCH_COLUMN);

             
             $allImages = array_merge($allImages, $additionalImages);

             
             if (empty($allImages)) {
                 
                 
                 
                 $allImages = ['../../path/to/placeholder_image.jpg']; 
             }
        }

    } catch (\PDOException $e) {
        error_log("Database Query Error fetching product/images in product display page: " . $e->getMessage());
        
        
        $product = null;
    }
}


if (!$product) {
    http_response_code(404);
    echo "<h1>Product Not Found</h1>";
    echo "<p>The requested product could not be found.</p>";
    
    
     echo '<p><a href="../jackets.php">Back to Jackets</a></p>'; 
    
    unset($_SESSION['review_success']);
    unset($_SESSION['review_error']);
    unset($_SESSION['review_errors']);
    exit;
}


$productName = htmlspecialchars($product['name']);
$productPrice = number_format($product['price'], 2);
$productDescription = htmlspecialchars($product['description'] ?? ''); 
$productCategory = htmlspecialchars($product['category']);


$mainImageUrl = htmlspecialchars($allImages[0] ?? '../../path/to/placeholder_image.jpg'); 



$reviews = [];
try {
    $sqlReviews = "SELECT r.rating, r.review_text, r.created_at, u.name AS reviewer_name
                   FROM reviews r
                   JOIN users u ON r.user_id = u.id
                   WHERE r.product_id = :product_id
                   ORDER BY r.created_at DESC";
    $stmtReviews = $conn->prepare($sqlReviews);
    $stmtReviews->execute([':product_id' => $productId]);
    $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);
} catch (\PDOException $e) {
     error_log("Error fetching reviews for product ID " . $productId . " in product display page: " . $e->getMessage());
     
     echo "<p>Could not load reviews at this time.</p>";
}



$reviewSuccess = $_SESSION['review_success'] ?? null;
$reviewError = $_SESSION['review_error'] ?? null;
$reviewErrors = $_SESSION['review_errors'] ?? null;


unset($_SESSION['review_success']);
unset($_SESSION['review_error']);
unset($_SESSION['review_errors']);




?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title><?php echo $productName; ?> - CLONR</title> <link rel="stylesheet" href="../../global.css"/> <link rel="stylesheet" href="../addtocart.css">
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="product_detail.css">
         <link rel="stylesheet" href="../reviews.css">
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
              <?php include '../../header.php'; ?> </div>
          </div>
      </header>

      <section class="product-section">
        <div>
          <a href="../jackets.php" class="back-button" style="display: inline-flex; align-items: center; padding: 10px 15px; text-decoration: none; color:rgb(0, 0, 0); font-weight: 500; font-size: 14px; border-radius: 6px; transition: all 0.2s ease;">
              <span style="margin-right: 8px; font-size: 16px;">&larr;</span>
              Back 
          </a>
        </div>
        <div class="product-details">
          <h2><?php echo $productCategory; ?></h2> <h1><?php echo $productName; ?></h1> <p>₱<?php echo $productPrice; ?></p> <?php if (!empty($productDescription)): ?>
              <div class="product-description">
                  <h3>Description</h3>
                  <p><?php echo nl2br(htmlspecialchars($productDescription)); ?></p> </div>
          <?php endif; ?>

          <label for="size">Size:</label>
          <select id="size">
            <option value="Small">Small</option>
            <option value="Medium">Medium</option>
            <option value="Large">Large</option>
            <option value="X-Large">X-Large</option>
            <option value="XX-Large">XX-Large</option>
          </select>

          <label for="quantity">Quantity:</label>
          <select id="quantity">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>

          <button id="add-to-cart" data-product-id="<?php echo $productId; ?>">Add to Cart</button>
        </div>

        <div class="product-slider">
          <button class="arrow left" onclick="prevSlide()">&#9664;</button>
          <img id="slider-image" src="<?php echo $mainImageUrl; ?>" alt="<?php echo $productName; ?>">
          <button class="arrow right" onclick="nextSlide()">&#9654;</button>
        </div>
      </section>

      <hr class="custom-hr">

      <section class="reviews-section">
          <h3>Customer Reviews</h3>
          <?php
?>
          <?php if (isset($reviewSuccess) && $reviewSuccess): ?>
              <div class="review-message success"><?php echo htmlspecialchars($reviewSuccess); ?></div>
          <?php endif; ?>
          <?php if (isset($reviewError) && $reviewError): ?>
              <div class="review-message error"><?php echo htmlspecialchars($reviewError); ?></div>
          <?php endif; ?>
           <?php if (isset($reviewErrors) && $reviewErrors): ?>
               <div class="review-message validation-errors">
                   <p>Please fix the following errors:</p>
                   <ul>
                       <?php foreach ($reviewErrors as $error): ?>
                           <li><?php echo htmlspecialchars($error); ?></li>
                       <?php endforeach; ?>
                   </ul>
               </div>
           <?php endif; ?>
          <?php // --- End PHP to display session messages --- ?>


          <?php
          // --- PHP to check login status and display button/message ---
          // You need the PHP variable $loggedInUserId populated from session
          // in the PHP block at the top of the file.
          ?>
          <?php if (isset($loggedInUserId) && $loggedInUserId): ?>
              <button id="toggle-review-form">Leave a Review</button>
          <?php else: ?>
              <p>Please <a href="../../login.php">log in</a> to leave a review.</p> <?php endif; ?>
          <?php // --- End PHP for login check --- ?>


          <div id="review-form-container" style="display: none;">
              <h4>Submit Your Review</h4>
              <form action="" method="POST"> <?php // You need the PHP variable $productId populated from $_GET ?>
                  <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productId ?? ''); ?>">

                  <div class="form-group">
                      <label for="rating">Rating:</label>
                      <select id="rating" name="rating" required>
                          <option value="">Select a rating</option>
                          <option value="5">5 Stars</option>
                          <option value="4">4 Stars</option>
                          <option value="3">3 Stars</option>
                          <option value="2">2 Stars</option>
                          <option value="1">1 Star</option>
                      </select>
                  </div>

                  <div class="form-group">
                      <label for="review_text">Your Review:</label>
                      <textarea id="review_text" name="review_text" rows="4"></textarea>
                  </div>

                  <div class="form-group">
                      <input type="checkbox" id="post_anonymous" name="post_anonymous">
                      <label for="post_anonymous">Post Anonymously</label>
                  </div>

                  <button type="submit" name="submit_review">Submit Review</button>
              </form>
          </div>

          <div class="existing-reviews">
              <?php
              // --- PHP to fetch and display existing reviews ---
              // You need the PHP variable $reviews populated from the database query
              // in the PHP block at the top of the file.
              ?>
              <?php if (!empty($reviews)): ?>
                  <?php foreach ($reviews as $review): ?>
                      <div class="review-item">
                          <p>
                              <strong><?php echo htmlspecialchars($review['reviewer_name'] ?? 'Anonymous'); ?></strong> rated it <?php echo htmlspecialchars($review['rating']); ?>/5 stars on <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                          </p>
                          <?php if (!empty($review['review_text'])): ?>
                              <p>&ldquo;<?php echo nl2br(htmlspecialchars($review['review_text'])); ?>&rdquo;</p> <?php endif; ?>
                      </div>
                       <hr class="review-item-separator"> <?php endforeach; ?>
              <?php else: ?>
                  <p>No reviews for this product yet.</p> <?php endif; ?>
              <?php // --- End PHP for displaying reviews --- ?>
          </div>

      </section>


      <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.CLONR@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
      </footer>

    <script>
        
        
        const images = <?php echo json_encode($allImages ?? []); ?>; 


        let currentIndex = 0;
        const sliderImage = document.getElementById('slider-image');

         
        if (images.length > 0) {
            sliderImage.src = images[currentIndex];
        } else {
             
             
             sliderImage.src = '../../path/to/placeholder_image.jpg'; 
        }


        function prevSlide() {
          if (images.length > 1) { 
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            sliderImage.src = images[currentIndex];
          }
        }

        function nextSlide() {
          if (images.length > 1) { 
            currentIndex = (currentIndex + 1) % images.length;
            sliderImage.src = images[currentIndex];
          }
        }

        
        const toggleButton = document.getElementById('toggle-review-form');
        const reviewFormContainer = document.getElementById('review-form-container');

        if (toggleButton && reviewFormContainer) {
            toggleButton.addEventListener('click', function() {
                const isHidden = reviewFormContainer.style.display === 'none';
                reviewFormContainer.style.display = isHidden ? 'block' : 'none';
                toggleButton.textContent = isHidden ? 'Hide Review Form' : 'Leave a Review';
            });
        }

      </script>

      <script src="../../js/cart.js"></script>

      <?php
      
      
      $conn = null;
      ?>
    </body>
</html>
