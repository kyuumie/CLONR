<?php
@include '../config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!isset($admin_id)) {
    header('location:../login.php');
    exit;
}

if (isset($_POST['add_product'])) {
    // Sanitize inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);
    $image = '';

    // Handle file upload if provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_img/' . $image;
        
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    }

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = 'Product name is required!';
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = 'Valid price is required!';
    if (empty($category)) $errors[] = 'Category is required!';
    if (empty($image) && empty($image_url)) $errors[] = 'Please provide either an image file or URL!';
    
    if (empty($errors)) {
        try {
            // Use uploaded image if available, otherwise use URL
            $final_image = !empty($image) ? $image : $image_url;
            
            $stmt = $conn->prepare("
                INSERT INTO products 
                (name, description, price, category, image, image_url) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $category, $final_image, $image_url]);
            
            $message[] = 'Product added successfully! ID: ' . $conn->lastInsertId();
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $message = array_merge($message ?? [], $errors);
    }
}

if (isset($_GET['delete'])) {
    $delete_id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    
    try {
        $conn->beginTransaction();
        
        // Get image info before deletion
        $select_image = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $select_image->execute([$delete_id]);
        $fetch_image = $select_image->fetch(PDO::FETCH_ASSOC);
        
        // Delete from related tables first
        $conn->prepare("DELETE FROM cart WHERE pid = ?")->execute([$delete_id]);
        
        // Then delete the product
        $delete = $conn->prepare("DELETE FROM products WHERE id = ?");
        $delete->execute([$delete_id]);
        
        // Delete the image file if it exists
        if (!empty($fetch_image['image']) && file_exists('../uploaded_img/' . $fetch_image['image'])) {
            unlink('../uploaded_img/' . $fetch_image['image']);
        }
        
        $conn->commit();
        $_SESSION['message'] = 'Product deleted successfully!';
        header('Location: products.php');
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        $message[] = 'Delete failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>CLONR - Admin Products</title>
    <link rel="stylesheet" href="../global.css" />
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
<hr class="custom-hr">
    <section class="add-products">
        <h1 class="title">Add New Product</h1>

        <?php
        if (isset($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }
        }
        ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="flex">
                <div class="inputBox">
                    <input type="text" name="name" class="box" required placeholder="Enter product name">
                    <textarea name="description" class="box" placeholder="Enter product description"></textarea>
                    <select name="category" class="box" required>
                        <option value="" selected disabled>Select category</option>
                        <option value="tshirts">T-shirts</option>
                        <option value="jackets">Jackets</option>
                        <option value="shorts">Shorts</option>
                        <option value="pants">Pants</option>
                        <option value="accessories">Accessories</option>
                    </select>
                </div>
                <div class="inputBox">
                    <input type="number" min="0" step="0.01" name="price" class="box" required placeholder="Enter product price">
                    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
                    <input type="text" name="image_url" placeholder="Or enter image URL" class="box">
                </div>
            </div>
            <input type="submit" class="btn" value="Add Product" name="add_product">
        </form>
    </section>

    <section class="show-products">
        <h1 class="title">Products Added</h1>

        <div class="box-container">
            <?php
            $show_products = $conn->prepare("SELECT * FROM `products`");
            $show_products->execute();
            if ($show_products->rowCount() > 0) {
                while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
                    // Determine image source - use URL if available, otherwise use uploaded image
                    $image_src = !empty($fetch_products['image_url']) ? $fetch_products['image_url'] : '../uploaded_img/' . $fetch_products['image'];
            ?>
                    <div class="box">
                        <div class="price">₱<?= number_format($fetch_products['price'], 2); ?></div>
                        <img src="<?= $image_src ?>" alt="<?= $fetch_products['name']; ?>">
                        <div class="name"><?= $fetch_products['name']; ?></div>
                        <div class="cat"><?= $fetch_products['category']; ?></div>
                        <div class="flex-btn">
                            <a href="admin_update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
                            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">No products added yet!</p>';
            }
            ?>
        </div>
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