<?php
include 'config.php';
include 'seller_header.php';
?>
<div class="container my-4">
   <h3>Welcome, Seller!</h3>
   <div class="row">
      <div class="col-md-4">
         <div class="card p-3 text-center">
            <?php
               $select = mysqli_query($conn, "SELECT * FROM products WHERE seller_id = '$seller_id'");
               $num_products = mysqli_num_rows($select);
            ?>
            <h5>Total Books</h5>
            <h2><?= $num_products; ?></h2>
         </div>
      </div>
   </div>
</div>