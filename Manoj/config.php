<?php
// Database configuration
$host = 'localhost';
$db_name = 'scholarnest'; // Replace with your actual database name
$username = 'root'; // Default XAMPP MySQL username
$password = ''; // Default XAMPP MySQL password (leave empty for root user)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>