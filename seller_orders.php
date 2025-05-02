<?php
include 'seller_header.php';

$query = mysqli_query($conn, "
   SELECT orders.*, order_items.*, products.name AS book_name 
   FROM orders
   JOIN order_items ON orders.id = order_items.order_id
   JOIN products ON order_items.product_id = products.id
   WHERE products.seller_id = '$seller_id'
   ORDER BY orders.id DESC
") or die('query failed');

$grouped_orders = [];

while ($row = mysqli_fetch_assoc($query)) {
   $oid = $row['order_id'];
   $grouped_orders[$oid]['order'] = $row;
   $grouped_orders[$oid]['items'][] = $row;
}
?>

<div class="container my-4">
   <h3>Orders for My Books</h3>
   <?php if (empty($grouped_orders)): ?>
      <p>No orders found.</p>
   <?php else: ?>
      <?php foreach ($grouped_orders as $order_id => $data): ?>
         <div class="card mb-3">
            <div class="card-body">
               <h5>Order #<?= $order_id ?> — <?= htmlspecialchars($data['order']['name']) ?></h5>
               <p><strong>Email:</strong> <?= htmlspecialchars($data['order']['email']) ?></p>
               <p><strong>Books:</strong></p>
               <ul>
                  <?php foreach ($data['items'] as $item): ?>
                     <li><?= htmlspecialchars($item['book_name']) ?> × <?= $item['quantity'] ?> — <?= $item['price'] ?> тг</li>
                  <?php endforeach; ?>
               </ul>
               <p><strong>Total:</strong> <?= $data['order']['total_price'] ?> тг</p>
               <p><strong>Status:</strong> <?= $data['order']['payment_status'] ?></p>
            </div>
         </div>
      <?php endforeach; ?>
   <?php endif; ?>
</div>
