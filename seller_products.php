<?php include 'seller_header.php'; ?>
<div class="container my-4">
   <h3>My Books</h3>
   <a href="seller_add_product.php" class="btn btn-success mb-3">Add New Book</a>
   <table class="table table-bordered">
      <thead>
         <tr>
            <th>Name</th>    
            <th>Genre</th>
            <th>Price</th>           
            <th>Status</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody>
      <?php
         $select = mysqli_query($conn, "SELECT * FROM products WHERE seller_id = '$seller_id'") or die('query failed');
         while($row = mysqli_fetch_assoc($select)):
         ?>
         <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['genre'] ?? '—') ?></td>
            <td><?= $row['price'] ?> тг</td>
            <td><?= $row['approved'] ? 'Approved' : 'Pending' ?></td>
            <td>
               <a href="seller_edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
               <a href="seller_delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
         </tr>
         <?php endwhile; ?>
      </tbody>
   </table>
</div>
