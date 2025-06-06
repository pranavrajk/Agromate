<?php 
$host = 'localhost';
$dbname = 'cart';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

session_start();

// Get cart count
$cart_count = !empty($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Fetch a few recommended products
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
$recommended_products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Farm Fresh Market</title>
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
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1590779033100-9f60a05a013d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
        
        .empty-cart-container {
            background-color: white;
            border-radius: 15px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 20px 0 50px;
        }
        
        .empty-cart-icon {
            font-size: 6rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
            opacity: 0.7;
        }
        
        .empty-cart-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-green);
            margin-bottom: 15px;
        }
        
        .empty-cart-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-shop-now {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);
        }
        
        .btn-shop-now:hover {
            background-color: var(--dark-green);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(46, 125, 50, 0.4);
        }
        
        .product-card {
            margin-bottom: 25px;
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }
        
        .product-img-container {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.1);
        }
        
        .organic-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .card-body {
            padding: 1.5rem;
            background-color: white;
        }
        
        .card-title {
            font-weight: 600;
            color: var(--dark-green);
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .price {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 20px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .divider {
            width: 100%;
            height: 2px;
            background-color: #f0f0f0;
            margin: 40px 0;
        }
        
        .footer {
            background-color: var(--dark-green);
            color: white;
            padding: 40px 0;
            margin-top: 60px;
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
        
        .cart-btn {
            color: white;
            background-color: var(--accent-color);
            border: none;
            position: relative;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .logo-image {
            margin: 0 auto;
            width: 100%;
            height: 2.5rem;
            object-fit: cover;
            padding: 1px;
        }
 

        .cart-btn:hover {
            background-color: #e67e00;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
        <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <li class="nav-item">
                <img src="../../images/Logo.jpg" class="logo-image" alt="Blog">
                <div class="nav-links">
                </li>
                </ul>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
           
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
            <h1><i class="fas fa-shopping-basket me-2"></i> Your Shopping Cart</h1>
            <p class="lead">Review your selected fresh produce before checkout</p>
        </div>
    </div>

    <div class="container">
        <div class="empty-cart-container">
            <i class="fas fa-shopping-basket empty-cart-icon"></i>
            <h2 class="empty-cart-title">Your cart is empty</h2>
            <p class="empty-cart-message">Looks like you haven't added any fresh produce to your cart yet. Browse our selection of farm-fresh products and start filling your basket!</p>
            <a href="index.php" class="btn btn-shop-now">
                <i class="fas fa-leaf me-2"></i> Start Shopping
            </a>
        </div>
        
        <div class="divider"></div>
        
        <div class="recommended-products">
            <div class="text-center mb-5">
                <h2 class="section-title">Recommended For You</h2>
                <p class="text-muted">Fresh picks from our farms that you might enjoy</p>
            </div>
            
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php foreach ($recommended_products as $product): ?>
                <div class="col">
                    <div class="product-card">
                        <div class="product-img-container">
                            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <span class="organic-badge"><i class="fas fa-leaf me-1"></i> Organic</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <div class="price">Rs<?php echo number_format($product['price'], 2); ?></div>
                            <form action="add_to_cart.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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