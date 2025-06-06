<?php
// Start session at the very top
session_start();

// Check if product ID is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: cart.php?error=no_product_id");
    exit();
}

$product_id = $_GET['id'];

// Validate the product ID
if (!is_numeric($product_id)) {
    header("Location: cart.php?error=invalid_product_id");
    exit();
}

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php?error=cart_empty");
    exit();
}

// Check if the product exists in the cart
if (!isset($_SESSION['cart'][$product_id])) {
    header("Location: cart.php?error=product_not_in_cart");
    exit();
}

// Remove the product from the cart
unset($_SESSION['cart'][$product_id]);

// Check if cart is now empty and clean up
if (empty($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect back to cart with success message
header("Location: cart.php?success=removed&product_id=" . $product_id);
exit();
?>