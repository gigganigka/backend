<?php
include 'seller_header.php';

if (!isset($_GET['id'])) {
    header('location:seller_products.php');
    exit();
}

$product_id = intval($_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM products WHERE id = $product_id AND seller_id = '$seller_id'") or die('query failed');

if (mysqli_num_rows($query) != 1) {
    header('location:seller_products.php');
    exit();
}

$product = mysqli_fetch_assoc($query);

if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = intval($_POST['price']);
    $image = $product['image'];
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);


    if (!empty($_FILES['image']['name'])) {
        $new_image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, 'uploaded_img/' . $new_image);
        $image = $new_image;
    }

    mysqli_query($conn, "UPDATE products SET 
        name = '$name', 
        genre = '$genre', 
        price = '$price',   
        image = '$image', 
        approved = 0 
        WHERE id = $product_id AND seller_id = '$seller_id'
    ") or die('update failed');

    header('location:seller_products.php');
    exit();
}
?>

<div class="container my-4">
    <h3>Edit Book</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Book Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" value="<?= $product['price'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Genre</label>
            <input type="text" name="genre" value="<?= htmlspecialchars($product['genre']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Current Image</label><br>
            <img src="uploaded_img/<?= $product['image'] ?>" width="150">
        </div>
        <div class="mb-3">
            <label class="form-label">New Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" name="update_product" class="btn btn-primary">Update</button>
    </form>
</div>
