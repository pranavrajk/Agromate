<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
    // Get customer details
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $pincode = $_POST['pincode'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'] ?? null;
    
    // Calculate total amount
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll();
    
    $total_amount = 0;
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $total_amount += $product['price'] * $quantity;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    try {
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, email, phone, address, pincode, payment_method, transaction_id, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $address, $pincode, $payment_method, $transaction_id, $total_amount]);
        $order_id = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $stmt->execute([$order_id, $product['id'], $quantity, $product['price']]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Clear cart
        unset($_SESSION['cart']);
        
        // Redirect to success page
        header("Location: order_success.php?id=$order_id");
        exit;
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        die("Error processing your order: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
?>