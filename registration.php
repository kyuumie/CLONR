<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $pass = md5(filter_var($_POST['pass'], FILTER_SANITIZE_STRING));
    $cpass = md5(filter_var($_POST['cpass'], FILTER_SANITIZE_STRING));
    
    $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;

    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select->execute([$email]);

    if ($select->rowCount() > 0) {
        $message[] = 'User email already exists!';
    } else {
        if ($pass != $cpass) {
            $message[] = 'Confirm password does not match!';
        } else {
            $insert = $conn->prepare("INSERT INTO `users`(name, email, password, image) VALUES(?,?,?,?)");
            $insert->execute([$name, $email, $pass, $image]);

            if ($insert) {
                if ($image_size > 2000000) {
                    $message[] = 'Image size is too large!';
                } else {
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'Registered successfully!';
                    header('location:login.php');
                }
            }
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
                    <li><a href="products/tshirts.php">T-shirts</a></li>
                    <li><a href="products/jackets.php">Jackets</a></li>
                    <li><a href="products/pants.php">Pants</a></li>
                    <li><a href="products/shorts.php">Shorts</a></li>
                    <li><a href="products/accessories.php">Accessories</a></li>
                  </ul>
                </li>
                <li><a href="sale.php">SALE</a></li>
                <li><a href="sizechart.php">SIZE CHART</a></li>
                <li><a href="contact.php">CONTACT US</a></li>
              </ul>
            </nav>
        </header>
          
        <?php if (isset($message)) {
        foreach ($message as $msg) {
            echo "<div class='message'><span>$msg</span><i class='fas fa-times' onclick='this.parentElement.remove();'></i></div>";
            }
        } ?>

        <section class="form-container">
            <form action="" enctype="multipart/form-data" method="POST">
                <h3>Register Now</h3>
                <input type="text" name="name" class="box" placeholder="Enter your name" required>
                <input type="email" name="email" class="box" placeholder="Enter your email" required>
                <input type="password" name="pass" class="box" placeholder="Enter your password" required>
                <input type="password" name="cpass" class="box" placeholder="Confirm your password" required>
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