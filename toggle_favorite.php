<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
$product_id = intval($_GET['id'] ?? 0);

if (!$user_id || !$product_id) {
    header('location:login.php');
    exit();
}

$check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'") or die('query failed');

if (mysqli_num_rows($check) > 0) {
    mysqli_query($conn, "DELETE FROM favorites WHERE user_id = '$user_id' AND product_id = '$product_id'") or die('delete failed');
} else {
    mysqli_query($conn, "INSERT INTO favorites(user_id, product_id) VALUES('$user_id', '$product_id')") or die('insert failed');
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
