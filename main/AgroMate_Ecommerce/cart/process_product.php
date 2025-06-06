<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    // Handle file upload
    $target_dir = "assets/images/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check === false) {
        die("File is not an image.");
    }
    
    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        die("Sorry, your file is too large.");
    }
    
    // Allow certain file formats
    if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    // Upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert product into database
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $target_file]);
        
        header("Location: ../../../admin/admin/all_products.php");
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>