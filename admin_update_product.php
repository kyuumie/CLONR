<?php
@include '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!isset($admin_id)) {
    header('location:../login.php');
    exit;
}

// Fetch product details for editing
if (isset($_GET['update'])) {
    $update_id = filter_var($_GET['update'], FILTER_SANITIZE_NUMBER_INT);
    $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $select_product->execute([$update_id]);
    $product = $select_product->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        $_SESSION['message'] = 'Product not found!';
        header('Location: products.php');
        exit;
    }
}

// Handle product update
if (isset($_POST['update_product'])) {
    // Sanitize inputs
    $update_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
    $old_image = filter_var($_POST['old_image'], FILTER_SANITIZE_STRING);
    $old_image_url = filter_var($_POST['old_image_url'] ?? '', FILTER_SANITIZE_URL);
    
    // Initialize variables
    $image = $old_image;
    $new_image_uploaded = false;
    
    // Handle file upload if provided
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_img/' . $image;
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        
        if (!in_array($image_ext, $allowed_ext)) {
            $message[] = 'Invalid image format. Only JPG, JPEG, PNG, WEBP allowed!';
        } elseif ($image_size > 2000000) {
            $message[] = 'Image size is too large! Max 2MB allowed.';
        } else {
            // Delete old image if it exists
            if (!empty($old_image) && file_exists('../uploaded_img/' . $old_image)) {
                unlink('../uploaded_img/' . $old_image);
            }
            
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                $new_image_uploaded = true;
                $message[] = 'Image updated successfully!';
            } else {
                $message[] = 'Failed to upload image!';
                $image = $old_image; // Revert to old image if upload fails
            }
        }
    }
    
    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = 'Product name is required!';
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = 'Valid price is required!';
    if (empty($category)) $errors[] = 'Category is required!';
    if (empty($image) && empty($image_url) && empty($old_image) && empty($old_image_url)) {
        $errors[] = 'Please provide either an image file or URL!';
    }
    
    if (empty($errors)) {
        try {
            $conn->beginTransaction();
            
            // Determine which image to use (priority: new upload > URL > old image)
            $final_image = $new_image_uploaded ? $image : (!empty($image_url) ? '' : $old_image);
            $final_image_url = !empty($image_url) ? $image_url : $old_image_url;
            
            $stmt = $conn->prepare("
                UPDATE products SET 
                name = ?, 
                description = ?, 
                price = ?, 
                category = ?, 
                image = ?, 
                image_url = ? 
                WHERE id = ?
            ");
            $stmt->execute([
                $name, 
                $description, 
                $price, 
                $category, 
                $final_image, 
                $final_image_url, 
                $update_id
            ]);
            
            $conn->commit();
            $_SESSION['message'] = 'Product updated successfully!';
            header('Location: products.php');
            exit;
        } catch (PDOException $e) {
            $conn->rollBack();
            $message[] = 'Database error: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $message = array_merge($message ?? [], $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>CLONR - Update Product</title>
    <link rel="stylesheet" href="../global.css">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div class="container">
            <a href="admindashboard.php">
                <h1 class="title">CLONR</h1>
            </a>
            <nav>
                <ul class="navbar">
                    <li><a href="products.php">Products</a></li>
                    <li><a href="order.php">Orders</a></li>
                    <li><a href="users.php">Users</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="../AdminUpdateProfile.php">Profile</a></li>
                    <li><a href="../logout.php" class="logout-btn">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="update-product">
        <h1 class="title">Update Product</h1>

        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }
        }
        
        if (isset($product)) {
            // Determine current image source
            $current_image_src = !empty($product['image_url']) ? $product['image_url'] : 
                                (!empty($product['image']) ? '../uploaded_img/' . $product['image'] : '');
        ?>
        
       <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
            <input type="hidden" name="old_image" value="<?= $product['image']; ?>">
            <input type="hidden" name="old_image_url" value="<?= $product['image_url']; ?>">
            <div class="form-container">
                <div class="flex">
                    <!-- Left Column - Text Inputs -->
                    <div class="inputBox">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" class="box" required 
                                placeholder="Enter product name" value="<?= htmlspecialchars($product['name']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="box" 
                                    placeholder="Enter product description"><?= htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" class="box" required>
                                <option value="" disabled>Select category</option>
                                <option value="tshirts" <?= $product['category'] == 'tshirts' ? 'selected' : ''; ?>>T-shirts</option>
                                <option value="jackets" <?= $product['category'] == 'jackets' ? 'selected' : ''; ?>>Jackets</option>
                                <option value="shorts" <?= $product['category'] == 'shorts' ? 'selected' : ''; ?>>Shorts</option>
                                <option value="pants" <?= $product['category'] == 'pants' ? 'selected' : ''; ?>>Pants</option>
                                <option value="accessories" <?= $product['category'] == 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Right Column - Image and Price -->
                    <div class="inputBox">
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" min="0" step="0.01" name="price" class="box" required 
                                placeholder="Enter product price" value="<?= htmlspecialchars($product['price']); ?>">
                        </div>
                        
                        <?php if (!empty($current_image_src)): ?>
                        <div class="form-group">
                            <label>Current Image</label>
                            <div class="image-container">
                                <img src="<?= $current_image_src; ?>" alt="Current product image">
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Upload New Image</label>
                            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                        </div>
                        
                        <div class="form-group">
                            <label>Or Enter Image URL</label>
                            <input type="text" name="image_url" placeholder="Enter new image URL" class="box" 
                                value="<?= htmlspecialchars($product['image_url']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <input type="submit" class="btn" value="Update Product" name="update_product">
                    <a href="products.php" class="option-btn">Go Back</a>
                </div>
            </div>
        </form>
        <?php
        } else {
            echo '<p class="empty">No product selected for update!</p>';
            echo '<a href="products.php" class="option-btn">Go Back</a>';
        }
        ?>
    </section>

    <hr class="custom-hr">

    <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.clonr@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>