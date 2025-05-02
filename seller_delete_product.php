<?php
include 'seller_header.php';

if (!isset($_GET['id'])) {
    header('location:seller_products.php');
    exit();
}

$product_id = intval($_GET['id']);

$check_product = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id AND seller_id = '$seller_id'") or die('query failed');

if (mysqli_num_rows($check_product) != 1) {
    header('location:seller_products.php');
    exit();
}

mysqli_query($conn, "DELETE FROM products WHERE id = $product_id") or die('delete failed');

header('location:seller_products.php');
exit();
?>
