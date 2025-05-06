<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  header('location:login.php');
  exit();
}

// Сортировка
$sort = $_GET['sort'] ?? 'latest';
$order_sql = match($sort) {
  'price_asc' => 'ORDER BY price ASC',
  'price_desc' => 'ORDER BY price DESC',
  'alpha' => 'ORDER BY name ASC',
  default => 'ORDER BY id DESC'
};

// Фильтрация по жанру
$selected_genre = $_GET['genre'] ?? '';
$genre_sql = $selected_genre ? "AND genre = '" . mysqli_real_escape_string($conn, $selected_genre) . "'" : '';

// Получение уникальных жанров
$genre_list = mysqli_query($conn, "SELECT DISTINCT genre FROM products WHERE approved = 1 AND genre IS NOT NULL AND genre != ''") or die('query failed');

// Получение избранных
$fav_query = mysqli_query($conn, "SELECT product_id FROM favorites WHERE user_id = '$user_id'");
$fav_ids = [];
while ($row = mysqli_fetch_assoc($fav_query)) {
  $fav_ids[] = $row['product_id'];
}

// Добавление в корзину
if (isset($_POST['add_to_cart'])) {
  $pro_name = $_POST['product_name'];
  $pro_price = $_POST['product_price'];
  $pro_quantity = $_POST['product_quantity'];
  $pro_image = $_POST['product_image'];

  $check = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$pro_name' AND user_id='$user_id'") or die('query failed');
  if (mysqli_num_rows($check) > 0) {
    $message[] = 'Already added to cart!';
  } else {
    mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) 
      VALUES ('$user_id','$pro_name','$pro_price','$pro_quantity','$pro_image')") or die('query2 failed');
    $message[] = 'Product added to cart!';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop Page</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="products_cont">
  <h2 style="text-align:center; margin-bottom:20px;">Browse All Books</h2>

  <div class="container" style="text-align:center; margin-bottom: 20px;">
    <form method="get">
      <label for="sort">Sort by: </label>
      <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="latest" <?= $sort == 'latest' ? 'selected' : '' ?>>Latest</option>
        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        <option value="alpha" <?= $sort == 'alpha' ? 'selected' : '' ?>>Alphabetical</option>
      </select>

      <label for="genre" style="margin-left:20px;">Genre:</label>
      <select name="genre" id="genre" onchange="this.form.submit()">
        <option value="">All Genres</option>
        <?php while ($g = mysqli_fetch_assoc($genre_list)): ?>
          <option value="<?= htmlspecialchars($g['genre']) ?>" <?= $selected_genre == $g['genre'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($g['genre']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>
  </div>

  <div class="pro_box_cont">
    <?php
    $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE approved = 1 $genre_sql $order_sql") or die('query failed');

    if (mysqli_num_rows($select_products) > 0) {
      while ($fetch_products = mysqli_fetch_assoc($select_products)) {
        $is_fav = in_array($fetch_products['id'], $fav_ids);
    ?>
    <form action="" method="post" class="pro_box">
      <img src="./uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
      <h5><?= htmlspecialchars($fetch_products['genre'] ?? '—'); ?></h5>
      <p>Tg. <?= $fetch_products['price']; ?>/-</p>

      <input type="hidden" name="product_name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
      <input type="number" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?= $fetch_products['image']; ?>">

      <input type="submit" value="Add to Cart" name="add_to_cart" class="product_btn">

      <a href="toggle_favorite.php?id=<?= $fetch_products['id'] ?>" class="product_btn" style="display:inline-block; margin-top:10px;">
        <?= $is_fav ? '♥ In Favorites' : '♡ Add to Favorites' ?>
      </a>
    </form>
    <?php
      }
    } else {
      echo '<p class="empty">No Products Found!</p>';
    }
    ?>
  </div>
</section>

<?php include 'footer.php'; ?>
<script src="script.js"></script>
</body>
</html>
