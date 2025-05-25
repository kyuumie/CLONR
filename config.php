<?php

$db_name = "mysql:host=localhost;dbname=clonr_db";
$username = "root";
$password = "";

$conn = new PDO($db_name, $username, $password);

// Email configuration
define('SMTP_USERNAME', 'noreply.clonr@gmail.com');
define('SMTP_PASSWORD', 'atyfzkdyrpanxojt');
?>