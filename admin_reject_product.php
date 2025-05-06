<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "UPDATE products SET approved = -1 WHERE id = $id") or die('query failed');
}

header('location:admin_products.php');
exit();
