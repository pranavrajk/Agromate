<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>
<?php include '../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f9f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #2e7d32;
            margin-bottom: 20px;
            font-size: 28px;
            border-bottom: 2px solid #a5d6a7;
            padding-bottom: 15px;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .add-button {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .add-button:hover {
            background-color: #388e3c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f1f8e9;
        }
        .product-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 4px;
        }
        .price {
            font-weight: bold;
            color: #2e7d32;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .delete-btn {
            background-color: #e53935;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .delete-btn:hover {
            background-color: #c62828;
        }
        .description-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .no-products {
            text-align: center;
            padding: 30px;
            color: #757575;
            font-style: italic;
        }
        .confirmation-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        .dialog-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            text-align: center;
        }
        .dialog-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .dialog-buttons button {
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .confirm-delete {
            background-color: #e53935;
            color: white;
        }
        .cancel-delete {
            background-color: #757575;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h2>Product Dashboard</h2>
            <button onclick="add_product()" class="add-button">Add Item</button>
        </div>
        
        <?php
        // Connect to database
        $conn = new mysqli($host, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Handle delete operation
        if (isset($_POST['delete_id'])) {
            $delete_id = $_POST['delete_id'];
            
            // Get image path before deleting
            $img_query = "SELECT image_path FROM products WHERE id = ?";
            $stmt = $conn->prepare($img_query);
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $image_path = $row['image_path'];
                
                // Delete from database
                $delete_query = "DELETE FROM products WHERE id = ?";
                $delete_stmt = $conn->prepare($delete_query);
                $delete_stmt->bind_param("i", $delete_id);
                
                if ($delete_stmt->execute()) {
                    // If database delete successful, try to delete the image file
                    if (!empty($image_path) && file_exists("../../main/AgroMate_Ecommerce/cart/".$image_path)) {
                        unlink("../../main/AgroMate_Ecommerce/cart/".$image_path);
                    }
                    echo "<div style='background-color: #c8e6c9; color: #2e7d32; padding: 10px; border-radius: 4px; margin-bottom: 15px;'>Product deleted successfully!</div>";
                } else {
                    echo "<div style='background-color: #ffcdd2; color: #c62828; padding: 10px; border-radius: 4px; margin-bottom: 15px;'>Error deleting product: " . $conn->error . "</div>";
                }
            }
        }
        
        // Get all products
        $query = "SELECT * FROM products ORDER BY created_at DESC";
        $result = $conn->query($query);
        ?>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <?php if (!empty($row['image_path'])): ?>
                                    <img src="<?php echo '../../main/AgroMate_Ecommerce/cart/' . $row['image_path']; ?>" alt="<?php echo $row['title']; ?>" class="product-image">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="description-cell" title="<?php echo htmlspecialchars($row['description']); ?>">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td class="price">Rs.<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="actions">
                                    <button class="delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-products">
                <p>No products found. Add some products to see them here.</p>
            </div>
        <?php endif; ?>
        
        <?php $conn->close(); ?>
    </div>
    
    <!-- Delete Confirmation Dialog -->
    <div id="deleteDialog" class="confirmation-dialog">
        <div class="dialog-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this product? This action cannot be undone.</p>
            <div class="dialog-buttons">
                <form id="deleteForm" method="post">
                    <input type="hidden" id="delete_id" name="delete_id" value="">
                    <button type="button" class="cancel-delete" onclick="hideDeleteDialog()">Cancel</button>
                    <button type="submit" class="confirm-delete">Delete</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteDialog').style.display = 'flex';
        }
        
        function hideDeleteDialog() {
            document.getElementById('deleteDialog').style.display = 'none';
        }

        function add_product() {
            window.location.href='add_product.php';
        }
    </script>
</body>
</html>