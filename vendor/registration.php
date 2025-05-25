<?php
require 'vendor/autoload.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$name = $phone = $email = $pass = '';
$user_type = 'user';
$message = [];

// Check for verification token columns
$check_column = $conn->query("SHOW COLUMNS FROM `users` LIKE 'verification_token'");
if ($check_column->rowCount() == 0) {
    $conn->query("ALTER TABLE `users` ADD COLUMN `verification_token` VARCHAR(255) NULL AFTER `user_type`");
    $conn->query("ALTER TABLE `users` ADD COLUMN `is_verified` TINYINT(1) DEFAULT 0 AFTER `verification_token`");
}

if (isset($_POST['submit'])) {
    // Sanitize inputs
    $name = trim(filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING));
    $phone = trim(filter_var($_POST['phone'] ?? '', FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $pass = trim($_POST['pass'] ?? '');
    $user_type = $_POST['user_type'] ?? 'user';

    // Validate inputs
    if (empty($name)) $message[] = 'Name is required!';
    elseif (strlen($name) < 3) $message[] = 'Name must be at least 3 characters!';

    if (empty($phone)) $message[] = 'Phone number is required!';
    elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) $message[] = 'Invalid phone number!';

    if (empty($email)) $message[] = 'Email is required!';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $message[] = 'Invalid email format!';

    if (empty($pass)) $message[] = 'Password is required!';
    elseif (strlen($pass) < 8) $message[] = 'Password must be at least 8 characters!';
    elseif ($pass != ($_POST['cpass'] ?? '')) $message[] = 'Passwords do not match!';

    // Handle file upload
    $image = '';
    if (empty($_FILES['image']['name'])) {
        $message[] = 'Profile image is required!';
    } else {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_folder = 'uploaded_img/'.$image;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $image_type = mime_content_type($image_tmp_name);
        
        if (!in_array($image_type, $allowed_types)) {
            $message[] = 'Only JPG, JPEG, PNG images allowed!';
        }
        
        if ($image_size > 2000000) {
            $message[] = 'Image must be less than 2MB!';
        }
    }

    if (empty($message)) {
        try {
            // Create upload directory if needed
            if (!file_exists('uploaded_img')) {
                mkdir('uploaded_img', 0777, true);
            }

            // Generate token and hash password
            $verification_token = bin2hex(random_bytes(32));
            $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

            // Insert user into database
            $insert = $conn->prepare("INSERT INTO `users` (name, phone, email, password, image, user_type, verification_token) VALUES (?,?,?,?,?,?,?)");
            $insert->execute([$name, $phone, $email, $hashed_password, $image, $user_type, $verification_token]);

            if ($insert) {
                // Move uploaded file
                if (move_uploaded_file($image_tmp_name, $image_folder)) {
                    // Send verification email using PHPMailer
                    $mail = new PHPMailer(true);
                    
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Use your SMTP server
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'your.email@gmail.com'; // SMTP username
                        $mail->Password   = 'your-email-password'; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('noreply@yourdomain.com', 'CLONR');
                        $mail->addAddress($email, $name);

                        // Content
                        $verification_link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?token=$verification_token";
                        
                        $mail->isHTML(true);
                        $mail->Subject = 'Verify Your Email for CLONR';
                        $mail->Body    = "
                            <h2>Thank you for registering with CLONR!</h2>
                            <p>Please click the link below to verify your email:</p>
                            <p><a href='$verification_link'>Verify Email</a></p>
                            <p>If you didn't request this, please ignore this email.</p>
                        ";

                        $mail->send();
                        $message[] = 'Registration successful! Please check your email to verify your account.';
                    } catch (Exception $e) {
                        $message[] = "Registration successful, but verification email couldn't be sent. Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $message[] = 'Failed to upload profile image!';
                }
            } else {
                $message[] = 'Registration failed, please try again!';
            }
        } catch (PDOException $e) {
            $message[] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!-- Your HTML remains exactly the same -->

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
                <input type="hidden" id="admin_email" name="admin_email">
                <input type="hidden" id="admin_pass" name="admin_pass">
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