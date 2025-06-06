<?php 
include 'config.php';

if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Get cart count
$cart_count = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Get products in cart
$product_ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($product_ids);
$products = $stmt->fetchAll();

// Calculate total
$grand_total = 0;
foreach ($products as $product) {
    $quantity = $_SESSION['cart'][$product['id']];
    $total = $product['price'] * $quantity;
    $grand_total += $total;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Farm Fresh Market</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Satisfy&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4caf50;
            --secondary-color: #8bc34a;
            --accent-color: #ff9800;
            --light-green: #e8f5e9;
            --dark-green: #2e7d32;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-family: 'Satisfy', cursive;
            font-size: 1.8rem;
            color: white !important;
        }
        
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1556767576-5ec41e3239ea?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 40px 0;
            margin-bottom: 40px;
            text-align: center;
            border-radius: 0 0 15px 15px;
        }
        
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: 600;
            color: var(--dark-green);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
            height: 3px;
            background-color: var(--accent-color);
        }
        
        .checkout-container {
            margin-bottom: 60px;
        }
        
        .checkout-form {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        .logo-image {
            margin: 0 auto;
            width: 100%;
            height: 2.5rem;
            object-fit: cover;
            padding: 1px;
        }
        
        .form-section {
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .form-section.billing {
            background-color: var(--light-green);
            border-left: 5px solid var(--primary-color);
        }
        
        .form-section.payment {
            background-color: #fff8e1;
            border-left: 5px solid var(--accent-color);
        }
        
        .form-section.summary {
            background-color: #f5f5f5;
            border-left: 5px solid #9e9e9e;
        }
        
        .section-header {
            color: var(--dark-green);
            font-weight: 600;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .section-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        
        .order-summary {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-item-name {
            font-weight: 500;
        }
        
        .order-item-details {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-item-price {
            font-weight: 600;
            color: var(--accent-color);
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #eee;
            margin-top: 10px;
        }
        
        .order-total-label {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .order-total-amount {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--accent-color);
        }
        
        .btn-place-order {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 20px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-place-order:hover {
            background-color: var(--dark-green);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3);
        }
        
        .footer {
            background-color: var(--dark-green);
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
        
        .payment-methods-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .payment-icon {
            font-size: 2rem;
            color: #555;
        }
        
        .secure-payment {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .secure-payment i {
            color: var(--primary-color);
            margin-right: 5px;
        }
        
        .cart-btn {
            color: white;
            background-color: var(--accent-color);
            border: none;
            position: relative;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .cart-btn:hover {
            background-color: #e67e00;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--dark-green);
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }
        
        @media (max-width: 767px) {
            .form-section {
                padding: 15px;
            }
            
            .section-header {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
        
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <li class="nav-item">
                <img src="../../images/Logo.jpg" class="logo-image" alt="Blog">
                <div class="nav-links">
                </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="cart-btn position-relative">
                        <i class="fas fa-shopping-basket me-2"></i> Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-clipboard-check me-2"></i> Checkout</h1>
            <p class="lead">Complete your purchase and get ready for farm fresh goodness</p>
        </div>
    </div>

    <div class="container checkout-container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="checkout-form">
                    <form action="process_payment.php" method="post">
                        <div class="form-section billing">
                            <h3 class="section-header"><i class="fas fa-user"></i> Billing Details</h3>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" name="name" id="name" required class="form-control" placeholder="John Doe">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" id="email" required class="form-control" placeholder="john@example.com">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" required class="form-control" placeholder="+1 234 567 8901">
                                </div>
                                <div class="col-md-6">
                                    <label for="pincode" class="form-label">Pincode</label>
                                    <input type="text" name="pincode" id="pincode" required class="form-control" placeholder="123456">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Delivery Address</label>
                                <textarea name="address" id="address" required class="form-control" rows="3" placeholder="Enter your complete delivery address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Order Notes (optional)</label>
                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Special notes for delivery"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section payment">
                            <h3 class="section-header"><i class="fas fa-credit-card"></i> Payment Information</h3>
                            <center><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/QR_Code_Example.svg/1200px-QR_Code_Example.svg.png" alt="OR Code" style="height:200px;"></center>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="credit_card">Credit/Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="cashondelivery">Cash on Delivery</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="transaction_id" class="form-label">Transaction Number (if applicable)</label>
                                <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter transaction ID if you have one">
                            </div>
                            
                            <div class="payment-methods-icons">
                                <i class="fab fa-cc-visa payment-icon"></i>
                                <i class="fab fa-cc-mastercard payment-icon"></i>
                                <i class="fab fa-cc-amex payment-icon"></i>
                                <i class="fab fa-paypal payment-icon"></i>
                                <i class="fas fa-money-bill-wave payment-icon"></i>
                            </div>
                            
                            <div class="secure-payment">
                                <i class="fas fa-lock"></i> Your payment information is secure
                            </div>
                        </div>
                        
                        <div class="form-section summary">
                            <h3 class="section-header"><i class="fas fa-shopping-basket"></i> Order Summary</h3>
                            <div class="order-summary">
                                <?php foreach ($products as $product): 
                                    $quantity = $_SESSION['cart'][$product['id']];
                                    $total = $product['price'] * $quantity;
                                ?>
                                <div class="order-item">
                                    <div>
                                        <div class="order-item-name"><?php echo htmlspecialchars($product['title']); ?></div>
                                        <div class="order-item-details">Quantity: <?php echo $quantity; ?></div>
                                    </div>
                                    <div class="order-item-price">Rs<?php echo number_format($total, 2); ?></div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="order-total">
                                    <div class="order-total-label">Total Amount</div>
                                    <div class="order-total-amount">Rs<?php echo number_format($grand_total, 2); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-place-order">
                            <i class="fas fa-leaf me-2"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>Farm Fresh Market</h3>
                    <p>Bringing the best organic produce directly from farms to your home.</p>
                </div>
                <div class="col-md-4">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="#" class="text-white">Products</a></li>
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Farm Road, Green Valley</p>
                    <p><i class="fas fa-phone me-2"></i> +1 234 567 8901</p>
                    <p><i class="fas fa-envelope me-2"></i> info@farmfreshmarket.com</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; 2025 Farm Fresh Market. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>