<?php
include 'seller_header.php';
if(isset($_POST['add_product'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $genre = mysqli_real_escape_string($conn, $_POST['genre']);
   $image = $_FILES['image']['name'];
   $image_tmp = $_FILES['image']['tmp_name'];
   $image_path = 'uploaded_img/' . $image;
   move_uploaded_file($image_tmp, $image_path);

   mysqli_query($conn, "INSERT INTO products(name, price, image, genre, seller_id, approved) VALUES('$name', '$price', '$image', '$genre', '$seller_id', 0)") or die('query failed');
   header('location:seller_products.php');
}
?>
<div class="container my-4">
   <h3>Add New Book</h3>
   <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
         <label class="form-label">Book Name</label>
         <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
         <label class="form-label">Price</label>
         <input type="number" name="price" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Genre</label>
        <input type="text" name="genre" class="form-control" required>
       </div>
      <div class="mb-3">
         <label class="form-label">Image</label>
         <input type="file" name="image" accept="image/*" class="form-control" required>
      </div>
      
      <button type="submit" name="add_product" class="btn btn-primary">Submit</button>
   </form>
</div>