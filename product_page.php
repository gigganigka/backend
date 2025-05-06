<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  header('location:login.php');
  exit();
}

$product_id = intval($_GET['id'] ?? 0);

// Получаем сам продукт
$product_query = mysqli_query($conn, "
  SELECT products.*, register.name AS seller_name 
  FROM products 
  LEFT JOIN register ON products.seller_id = register.id 
  WHERE products.id = '$product_id' AND approved = 1
") or die('query failed');

if (mysqli_num_rows($product_query) != 1) {
  echo "<p style='text-align:center; color:red;'>Sorry, this product is not available.</p>";
  exit();
}

$product = mysqli_fetch_assoc($product_query);

// Избранное
$fav_query = mysqli_query($conn, "SELECT product_id FROM favorites WHERE user_id = '$user_id'");
$fav_ids = [];
while ($row = mysqli_fetch_assoc($fav_query)) {
  $fav_ids[] = $row['product_id'];
}
$is_fav = in_array($product['id'], $fav_ids);

// Добавление в корзину
if (isset($_POST['add_to_cart'])) {
  $pro_name = $_POST['product_name'];
  $pro_price = $_POST['product_price'];
  $pro_quantity = $_POST['product_quantity'];
  $pro_image = $_POST['product_image'];

  $check = mysqli_query($conn, "SELECT * FROM `cart` WHERE name='$pro_name' AND user_id='$user_id'");
  if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) 
      VALUES ('$user_id','$pro_name','$pro_price','$pro_quantity','$pro_image')");
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> - Book Details</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    .slider-container {
      position: relative;
      overflow: hidden;
      margin: 30px auto;
      padding: 0 40px;
    }
    .slider-track {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      scroll-behavior: smooth;
    }
    .slider-track::-webkit-scrollbar {
      display: none;
    }
    .slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: #b00;
      color: white;
      border: none;
      padding: 10px 15px;
      font-size: 20px;
      cursor: pointer;
      z-index: 10;
      border-radius: 50%;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    .slider-btn.left { left: 0; }
    .slider-btn.right { right: 0; }
  </style>
</head>
<body>

<?php include 'user_header.php'; ?>

<section class="product_details" style="padding:30px;">
  <div class="container" style="max-width:800px; margin:auto; text-align:center;">
    <img src="./uploaded_img/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>" style="width:250px; margin-bottom:20px;">
    <h2><?= htmlspecialchars($product['name']); ?></h2>
    <h4>Genre: <?= htmlspecialchars($product['genre']); ?></h4>
    <h4>By: <?= htmlspecialchars($product['seller_name'] ?? '—'); ?></h4>
    <p style="font-size:18px;">Price: <strong><?= $product['price']; ?> ₸</strong></p>

    <form action="" method="post">
      <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
      <input type="number" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_price" value="<?= $product['price']; ?>">
      <input type="hidden" name="product_image" value="<?= $product['image']; ?>">
      <input type="submit" value="Add to Cart" name="add_to_cart" class="product_btn">
    </form>

    <a href="toggle_favorite.php?id=<?= $product['id'] ?>" class="product_btn" style="margin-top:10px;">
      <?= $is_fav ? '♥ In Favorites' : '♡ Add to Favorites' ?>
    </a>
  </div>
</section>

<section class="products_cont">
  <h3 style="text-align:center;">Similar Books in "<?= htmlspecialchars($product['genre']) ?>"</h3>
  <div class="slider-container">
    <button class="slider-btn left" onclick="scrollSlider(this, -1)">‹</button>
    <div class="slider-track">
      <?php
      $similar = mysqli_query($conn, "
        SELECT * FROM products 
        WHERE genre = '".mysqli_real_escape_string($conn, $product['genre'])."' 
          AND id != '$product_id' 
          AND approved = 1 
        LIMIT 10
      ");
      if (mysqli_num_rows($similar) > 0) {
        while ($item = mysqli_fetch_assoc($similar)) {
          $is_fav = in_array($item['id'], $fav_ids);
      ?>
      <div class="pro_box">
        <img src="uploaded_img/<?= $item['image'] ?>" alt="">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p>Tg. <?= $item['price'] ?>/-</p>
        <a href="product_page.php?id=<?= $item['id'] ?>" class="product_btn">View</a>
        <a href="toggle_favorite.php?id=<?= $item['id'] ?>" class="product_btn" style="margin-top:5px;">
          <?= $is_fav ? '♥' : '♡' ?> Favorite
        </a>
      </div>
      <?php }} else { echo "<p class='empty'>No similar books found.</p>"; } ?>
    </div>
    <button class="slider-btn right" onclick="scrollSlider(this, 1)">›</button>
  </div>
</section>

<section class="products_cont">
  <h3 style="text-align:center;">More from <?= htmlspecialchars($product['seller_name']) ?></h3>
  <div class="slider-container">
    <button class="slider-btn left" onclick="scrollSlider(this, -1)">‹</button>
    <div class="slider-track">
      <?php
      $author_books = mysqli_query($conn, "
        SELECT * FROM products 
        WHERE seller_id = '{$product['seller_id']}' 
          AND id != '$product_id' 
          AND approved = 1 
        LIMIT 10
      ");
      if (mysqli_num_rows($author_books) > 0) {
        while ($item = mysqli_fetch_assoc($author_books)) {
          $is_fav = in_array($item['id'], $fav_ids);
      ?>
      <div class="pro_box">
        <img src="uploaded_img/<?= $item['image'] ?>" alt="">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p>Tg. <?= $item['price'] ?>/-</p>
        <a href="product_page.php?id=<?= $item['id'] ?>" class="product_btn">View</a>
        <a href="toggle_favorite.php?id=<?= $item['id'] ?>" class="product_btn" style="margin-top:5px;">
          <?= $is_fav ? '♥' : '♡' ?> Favorite
        </a>
      </div>
      <?php }} else { echo "<p class='empty'>No other books from this author.</p>"; } ?>
    </div>
    <button class="slider-btn right" onclick="scrollSlider(this, 1)">›</button>
  </div>
</section>

<?php include 'footer.php'; ?>

<script>
function scrollSlider(btn, direction) {
  const container = btn.closest('.slider-container');
  const track = container.querySelector('.slider-track');
  const scrollAmount = container.offsetWidth * 0.8;
  track.scrollBy({
    left: scrollAmount * direction,
    behavior: 'smooth'
  });
}
</script>

</body>
</html>
