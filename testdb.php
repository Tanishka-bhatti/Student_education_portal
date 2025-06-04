<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=info_portal;charset=utf8mb4", "root", "");
    echo "Connected to DB successfully!";
} catch (Exception $e) {
    echo "DB Connection failed: " . $e->getMessage();
}
?>
