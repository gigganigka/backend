<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit();
}


$sort = $_GET['sort'] ?? 'latest';

$order_sql = match($sort) {
    'price_asc' => 'ORDER BY products.price ASC',
    'price_desc' => 'ORDER BY products.price DESC',
    'alpha' => 'ORDER BY products.name ASC',
    default => 'ORDER BY favorites.added_at DESC'
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Favorites</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="products_cont">
  <h2 style="text-align:center; margin-bottom:20px;">My Favorite Books ♥</h2>

  <div class="container" style="text-align:center; margin-bottom: 20px;">
    <form method="get">
      <label for="sort">Sort by: </label>
      <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        <option value="alpha" <?= $sort == 'alpha' ? 'selected' : '' ?>>Alphabetical</option>
   
      </select>
    </form>
  </div>

  <div class="pro_box_cont">
    <?php
    $query = mysqli_query($conn, "
      SELECT products.* FROM favorites 
      JOIN products ON favorites.product_id = products.id 
      WHERE favorites.user_id = '$user_id' AND products.approved = 1
      $order_sql
    ") or die('query failed');

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
    ?>
    <div class="pro_box">
      <img src="uploaded_img/<?= $row['image'] ?>" alt="">
      <h3><?= htmlspecialchars($row['name']) ?></h3>
      <p>Tg. <?= $row['price'] ?>/-</p>

      <a href="product_page.php?id=<?= $row['id'] ?>" class="product_btn">View</a>
      <a href="toggle_favorite.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-2">♥ Remove</a>
    </div>
    <?php
        }
    } else {
        echo '<p class="empty">You have no favorite books yet.</p>';
    }
    ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
