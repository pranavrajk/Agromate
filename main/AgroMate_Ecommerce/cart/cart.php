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

// Check if cart is empty before proceeding
if (empty($_SESSION['cart'])) {
    header("Location: no.php?cart=empty");
    exit;
}

// Get all products in cart
$product_ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($product_ids);
$products = $stmt->fetchAll();
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
        .logo-image {
            margin: 0 auto;
            width: 100%;
            height: 2.5rem;
            object-fit: cover;
            padding: 1px;
        }
 
        
        .cart-table {
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .table thead {
            background-color: var(--light-green);
        }
        
        .table th {
            color: var(--dark-green);
            font-weight: 600;
            border-bottom: none;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
            border: 2px solid var(--light-green);
        }
        
        .product-title {
            font-weight: 500;
            color: var(--dark-green);
        }
        
        .quantity-input {
            max-width: 70px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #ced4da;
            padding: 8px;
        }
        
        .remove-btn {
            background-color: #f44336;
            border: none;
            border-radius: 20px;
            color: white;
            padding: 8px 15px;
            transition: all 0.3s;
        }
        
        .remove-btn:hover {
            background-color: #d32f2f;
            transform: translateY(-2px);
        }
        
        .price {
            color: var(--accent-color);
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .grand-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
        }
        
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        
        .btn-shop {
            background-color: white;
            color: var(--dark-green);
            border: 2px solid var(--dark-green);
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s;
        }


        #cart-btn {
            color: white;
            background-color: var(--accent-color);
            border: none;
            position: relative;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        #cart-btn:hover {
            background-color: #e67e00;
        }


        
        .btn-shop:hover {
            background-color: var(--light-green);
            color: var(--dark-green);
        }
        
        .btn-checkout {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s;
        }
        
        .btn-checkout:hover {
            background-color: var(--dark-green);
            transform: translateY(-3px);
        }
        
        .empty-cart {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 50px 0;
        }
        
        .empty-cart i {
            font-size: 5rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .footer {
            background-color: var(--dark-green);
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
        
        /* Responsive styling */
        @media (max-width: 767px) {
            .product-info {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .product-img {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
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
                    <a href="cart.php" class="btn btn-light position-relative" id="cart-btn">
                        <i class="fas fa-shopping-basket me-2"></i> Cart
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo array_sum($_SESSION['cart']); ?>
                            </span>
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
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Browse our fresh products and add some items to your cart</p>
                <a href="index.php" class="btn btn-checkout mt-3">Shop Now</a>
            </div>
        <?php else: ?>
            <div class="cart-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40%">Product</th>
                            <th width="15%">Price</th>
                            <th width="20%">Quantity</th>
                            <th width="15%">Total</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grand_total = 0;
                        foreach ($products as $product): 
                            $quantity = $_SESSION['cart'][$product['id']];
                            $total = $product['price'] * $quantity;
                            $grand_total += $total;
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($product['image_path']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['title']); ?>">
                                    <span class="product-title"><?php echo htmlspecialchars($product['title']); ?></span>
                                </div>
                            </td>
                            <td class="price">Rs<?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <form action="update_cart.php" method="post" class="d-flex align-items-center">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <p><b><?php echo $quantity; ?></b></p>
                                </form>
                            </td>
                            <td class="price">Rs<?php echo number_format($total, 2); ?></td>
                            <td>
                                <a href="remove_from_cart.php?id=<?php echo $product['id']; ?>" class="remove-btn">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Grand Total</th>
                            <th class="grand-total">Rs<?php echo number_format($grand_total, 2); ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-shop">
                    <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                </a>
                <a href="checkout.php" class="btn btn-checkout">
                    Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
        
        <!-- You might want to add recommended products here -->
        <div class="mt-5">
            <div class="text-center">
                <h3 class="section-title">You Might Also Like</h3>
            </div>
            
            <!-- Recommended products would go here -->
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