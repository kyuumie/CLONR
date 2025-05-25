<?php
include 'config.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists
    $check_token = $conn->prepare("SELECT * FROM `users` WHERE verification_token = ?");
    $check_token->execute([$token]);
    
    if ($check_token->rowCount() > 0) {
        // Mark user as verified
        $verify_user = $conn->prepare("UPDATE `users` SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $verify_user->execute([$token]);
        
        if ($verify_user) {
            $message = "Your email has been verified successfully! You can now login.";
            $success = true;
        } else {
            $message = "Verification failed. Please try again.";
        }
    } else {
        $message = "Invalid or expired verification link.";
    }
} else {
    $message = "No verification token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | CLONR</title>
    <link rel="stylesheet" href="global.css">
    <style>
        .verification-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="verification-container">
        <h2>Email Verification</h2>
        <p class="<?php echo $success ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
        
        <?php if ($success): ?>
            <a href="login.php" class="btn">Login Now</a>
        <?php else: ?>
            <a href="registration.php" class="btn">Register Again</a>
        <?php endif; ?>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>