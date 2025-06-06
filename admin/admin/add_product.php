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
    <title>Add New Product</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f9f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            border-bottom: 2px solid #a5d6a7;
            padding-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #1b5e20;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #c8e6c9;
            border-radius: 4px;
            background-color: #f1f8e9;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 16px;
        }
        .form-control:focus {
            border-color: #4caf50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.25);
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn-primary {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 30px auto 0;
        }
        .btn-primary:hover {
            background-color: #388e3c;
        }
        input[type="file"] {
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Product</h2>
        <form action="../../main/Agromate_Ecommerce/cart/process_product.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" required class="form-control">
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Price:</label>
                <input type="number" step="0.01" name="price" required class="form-control">
            </div>
            <div class="form-group">
                <label>Product Image:</label>
                <input type="file" name="image" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</body>
</html>