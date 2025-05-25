<?php
include 'config.php';

// Initialize variables with empty values or previously submitted values
$name = isset($_POST['name']) ? trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING)) : '';
$phone = isset($_POST['phone']) ? trim(filter_var($_POST['phone'], FILTER_SANITIZE_STRING)) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'user';
$message = [];

if (isset($_POST['submit'])) {
    $pass = isset($_POST['pass']) ? trim($_POST['pass']) : '';
    $cpass = isset($_POST['cpass']) ? trim($_POST['cpass']) : '';
    
    $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
    $image_size = isset($_FILES['image']['size']) ? $_FILES['image']['size'] : 0;
    $image_tmp_name = isset($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : '';
    $image_folder = 'uploaded_img/' . basename($image);

    // Validate name
    if (empty($name) || strlen($name) < 3) {
        $message[] = 'Name must be at least 3 characters long!';
        $name = ''; // Reset only if validation fails
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format!';
        $email = ''; // Reset only if validation fails
    } else {
        // Check if user exists only if email is valid
        $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select->execute([$email]);

        if ($select->rowCount() > 0) {
            $message[] = 'User email already exists!';
            $email = ''; // Reset only if validation fails
        }
    }

    // Validate phone number: must be a string of exactly 9 digits
    if (!preg_match('/^\d{10}$/', $phone)) {
        $message[] = 'Phone number must be exactly 10 digits!';
        $phone = ''; // Reset only if validation fails
    } else {
        // Check if phone number already exists only if format is valid
        $check_phone = $conn->prepare("SELECT * FROM `users` WHERE phone = ?");
        $check_phone->execute([$phone]);
        if ($check_phone->rowCount() > 0) {
            $message[] = 'Phone number already exists!';
            $phone = ''; // Reset only if validation fails
        }
    }

    // Validate password
    if (strlen($pass) < 8) {
        $message[] = 'Password must be at least 8 characters long!';
    }

    // Check password match
    if ($pass !== $cpass) {
        $message[] = 'Passwords do not match!';
    }

    // Admin verification process
    if ($user_type === 'admin') {
        echo "<script>
            var adminEmail = prompt('Enter an existing admin email:');
            var adminPass = prompt('Enter the admin password:');
            document.getElementById('admin_email').value = adminEmail;
            document.getElementById('admin_pass').value = adminPass;
        </script>";

        if (!isset($_POST['admin_email']) || !isset($_POST['admin_pass']) || empty($_POST['admin_email']) || empty($_POST['admin_pass'])) {
            $message[] = 'Admin credentials are required to create an admin account!';
            $user_type = 'user'; // Reset to user if admin validation fails
        } else {
            $admin_email = $_POST['admin_email'];
            $admin_pass = md5($_POST['admin_pass']);
            
            $verify_admin = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? AND user_type = 'admin'");
            $verify_admin->execute([$admin_email, $admin_pass]);
            
            if ($verify_admin->rowCount() == 0) {
                $message[] = 'Admin verification failed!';
                $user_type = 'user'; // Reset to user if admin validation fails
            }
        }
    }

    // Image validation
    if (!empty($image_tmp_name)) {
        $allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];
        $image_type = mime_content_type($image_tmp_name);

        if (!in_array($image_type, $allowed_types)) {
            $message[] = 'Invalid image format! Only JPG, JPEG, and PNG allowed.';
            $image = ''; // Reset only if validation fails
        }

        if ($image_size > 2000000) {
            $message[] = 'Image size is too large! Must be under 2MB.';
            $image = ''; // Reset only if validation fails
        }
    } else {
        $message[] = 'Please upload an image!';
    }

    if (empty($message)) {
        // Encrypt password
        $hashed_password = md5($pass);

        // Insert user into database
        $insert = $conn->prepare("INSERT INTO `users`(name, phone,email, password, image, user_type) VALUES(?,?,?,?,?,?)");
        $insert->execute([$name,$phone , $email, $hashed_password, $image, $user_type]);

        if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            header('location:login.php');
            exit();
        } else {
            $message[] = 'Registration failed, please try again!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>CLONR</title>
        <link rel="stylesheet" href="global.css"/>
        <link rel="stylesheet" href="registration.css">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    
    <body>
        <header>
          <div class="container">
            <a href="index.php"><h1 class="title">CLONR</h1></a>
            <nav>
              <ul class="navbar">
                <li><a href="index.php">HOME</a></li>
                <li class="dropdown">SHOP
                  <ul class="dropdown-menu">
                    <li><a href="login.php">T-shirts</a></li>
                    <li><a href="login.php">Jackets</a></li>
                    <li><a href="login.php">Pants</a></li>
                    <li><a href="login.php">Shorts</a></li>
                    <li><a href="login.php">Accessories</a></li>
                  </ul>
                </li>
                <li><a href="sizechart.php">SIZE CHART</a></li>
                <li><a href="contact.php">CONTACT US</a></li>
              </ul>
            </nav>
        </header>
          
        <?php if (!empty($message)) {
            foreach ($message as $msg) {
                echo "<div class='message'><span>$msg</span><i class='fas fa-times' onclick='this.parentElement.remove();'></i></div>";
            }
        } ?>

        <section class="form-container">
            <form action="" enctype="multipart/form-data" method="POST">
                <h3>Register Now</h3>
                <input type="text" name="name" class="box" placeholder="Enter your name" value="<?php echo htmlspecialchars($name); ?>" required>
                
                <input type="text" name="phone" class="box" placeholder="Enter your phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                <input type="email" name="email" class="box" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>
                <input type="password" name="pass" class="box" placeholder="Enter your password (min. 8 characters)" required>
                <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
                <select name="user_type" class="box" required>
                    <option value="user" <?php echo ($user_type === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user_type === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
                <input type="file" name="image" class="box" required accept="image/jpg, image/jpeg, image/png">
                <input type="submit" value="Register Now" class="btn" name="submit">
                <p>Already have an account? <a href="login.php">Login Now</a></p>
            </form>
        </section>

        <hr class="custom-hr">

        <footer>
        <div class="footer-content">
            <h2>CLONR - Wear the Movement</h2>
            <p>Email: customerservice.CLONR@gmail.com | Phone: +63 XXX XXX XXXX</p>
            <p>© 2025 CLONR. All Rights Reserved.</p>
        </div>
        </footer>
    </body>
</html>