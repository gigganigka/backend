<?php
include 'user_header.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit();
}

// Получаем избранные товары пользователя
$favorites = mysqli_query($conn, "
   SELECT products.* 
   FROM favorites 
   JOIN products ON favorites.product_id = products.id 
   WHERE favorites.user_id = '$user_id' AND products.approved = 1
") or die('query failed');
?>

<div class="container my-4">
   <h3>Your Favorites ♥</h3>

   <?php if (mysqli_num_rows($favorites) == 0): ?>
      <p>You have no favorite books yet.</p>
   <?php else: ?>
      <div class="row">
         <?php while($row = mysqli_fetch_assoc($favorites)): ?>
            <div class="col-md-4">
               <div class="card mb-3">
                  <img src="uploaded_img/<?= $row['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($row['name']) ?>">
                  <div class="card-body">
                     <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                     <p class="card-text"><?= $row['price'] ?> тг</p>
                     <a href="product_page.php?id=<?= $row['id'] ?>" class="btn btn-primary">View Book</a>
                     <a href="toggle_favorite.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger">♥ Remove</a>
                  </div>
               </div>
            </div>
         <?php endwhile; ?>
      </div>
   <?php endif; ?>
</div>
