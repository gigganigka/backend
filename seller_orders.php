<?php
include 'config.php';
session_start();

$seller_id = $_SESSION['seller_id'] ?? null;
if (!$seller_id) {
  header('location:login.php');
  exit();
}

// Обработка завершения заказа
if (isset($_GET['complete']) && is_numeric($_GET['complete'])) {
  $order_id = intval($_GET['complete']);
  mysqli_query($conn, "UPDATE orders SET payment_status = 'completed' WHERE id = '$order_id'") or die('query failed');
  header('location:seller_orders.php');
  exit();
}

// Получаем все заказы
$orders_query = mysqli_query($conn, "SELECT * FROM orders ORDER BY placed_on DESC") or die('query failed');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Orders</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'seller_header.php'; ?>

<section class="orders">
  <h2 style="text-align:center; margin: 20px 0;">Your Book Orders</h2>

  <div class="container">
    <?php
    $has_orders = false;

    while ($order = mysqli_fetch_assoc($orders_query)) {
      $products = explode(',', $order['total_products']);
      $matched_books = [];

      foreach ($products as $entry) {
        $parts = explode('(', $entry); // format: Book Name (x2)
        $book_name = trim($parts[0]);

        // Проверка: эта ли книга нашего продавца?
        $book_check = mysqli_query($conn, "SELECT * FROM products WHERE name = '$book_name' AND seller_id = '$seller_id'") or die('query failed');
        if (mysqli_num_rows($book_check) > 0) {
          $matched_books[] = $entry;
        }
      }

      if (!empty($matched_books)) {
        $has_orders = true;
    ?>
    <div class="order_box" style="border:1px solid #ccc; padding:15px; margin-bottom:20px;">
      <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
      <p><strong>Customer:</strong> <?= htmlspecialchars($order['name']) ?> | <?= $order['email'] ?> | <?= $order['number'] ?></p>
      <p><strong>Books:</strong> <?= implode(', ', $matched_books) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
      <p><strong>Payment:</strong> <?= $order['method'] ?> | <strong>Status:</strong> <?= $order['payment_status'] ?></p>
      <p><strong>Date:</strong> <?= $order['placed_on'] ?></p>

      <?php if ($order['payment_status'] != 'completed'): ?>
        <a href="seller_orders.php?complete=<?= $order['id'] ?>" class="product_btn" onclick="return confirm('Mark this order as completed?');">Mark as Completed</a>
      <?php else: ?>
        <span class="product_btn" style="background:gray; cursor:default;">Completed</span>
      <?php endif; ?>
    </div>
    <?php
      }
    }

    if (!$has_orders) {
      echo '<p class="empty" style="text-align:center;">No orders for your books yet.</p>';
    }
    ?>
  </div>
</section>

</body>
</html>
