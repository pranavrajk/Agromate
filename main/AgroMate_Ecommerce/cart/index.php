<?php
// Start session at the very top
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'cart'; // Make sure this is your correct database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Fetch all products from database
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// Check if a product was added to cart (from the URL)
if (isset($_GET['added'])) {
    $message = $_GET['added'] == 'success' ? 
        '<div class="alert alert-success">Product added to cart!</div>' : 
        '<div class="alert alert-danger">Failed to add product to cart.</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Fresh Market</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
            position: sticky;
            top:0;
            z-index:10;
            background-color: var(--primary-color) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            
        }
        
        .navbar-brand {
            font-family: 'Satisfy', cursive;
            font-size: 1.8rem;
            color: white !important;
            
        }
        
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            text-align: center;
            border-radius: 0 0 15px 15px;
            
            
        }
        .logo-image {
            margin: 0 auto;
            width: 100%;
            height: 2.5rem;
            object-fit: cover;
            padding: 1px;
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
            height: 200px;
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
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        
        .card-text {
            color: #666;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .price {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
            transform: translateY(-3px);
        }
        
        .btn-outline-secondary {
            color: #f3f8f3;
            border-color: var(--dark-green);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--light-green);
            color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .cart-btn {
            color: #0d0e0d;;
            background-color: var(--accent-color);
            border: none;
            position: relative;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s;
            margin-right: 10px; 
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
        
        .quantity-input {
            border-radius: 20px 0 0 20px;
            text-align: center;
            border-color: #ced4da;
        }
        
        .add-to-cart-btn {
            border-radius: 0 20px 20px 0;
        }
        
        .alert {
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .footer {
            background-color: var(--dark-green);
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
       
        .btn {
            background-color: #086e08;
            border-radius: 10px;
            border: 3px solid #086e08;
            color: #0d0e0d;;
        }
        
        .search-container {
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .search-input {
            border-radius: 30px 0 0 30px;
            border-right: none;
            padding-left: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .search-button {
            border-radius: 0 30px 30px 0;
            background-color: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .search-button:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
        }
        
        .no-results {
            text-align: center;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 1.1rem;
            color: #666;
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
                    <a href="../../index.html">
                <img src="../../images/Logo.jpg" class="logo-image" alt="Blog">
                </a>
                <div class="nav-links">
                </li>
                  
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="cart-btn position-relative">
                        <i class="fas fa-shopping-basket me-2"></i> Cart
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
            
            <a href="../../../main/index.html" class="btn btn-outline-success"><i class="fas fa-home mr-1"></i> Home</a>
        </div>
        

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon">
              <i class="fas fa-bars" style="color:#2e7d32; font-size:28px;"></i>
          </span>
      </button>
    </nav>

    <div class="page-header">
        <div class="container">
            <h1>Farm Fresh Organic Products</h1>
            <p class="lead">Directly from our farms to your table</p>
        </div>
    </div>

    <div class="container">
        <?php if (isset($message)) echo $message; ?>

        <!-- Search Bar -->
        <div class="search-container">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Search products...">
                <button class="btn search-button" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <div class="text-center mb-5">
            <h2 class="section-title">Our Fresh Products</h2>
            <p class="text-muted">Handpicked from local farms, guaranteed fresh and organic</p>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="productsContainer">
            <?php foreach ($products as $product): ?>
            <div class="col product-item" data-title="<?php echo strtolower(htmlspecialchars($product['title'])); ?>" data-description="<?php echo strtolower(htmlspecialchars($product['description'])); ?>">
                <div class="product-card">
                    <div class="product-img-container">
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <span class="organic-badge"><i class="fas fa-leaf me-1"></i> Organic</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...</p>
                        <div class="price">Rs<?php echo number_format($product['price'], 2); ?></div>
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="input-group mb-3">
                                <input type="number" name="quantity" value="1" min="1" class="form-control quantity-input">
                                <button type="submit" class="btn btn-primary add-to-cart-btn">
                                    <i class="fas fa-cart-plus me-1"></i> Add
                                </button>
                            </div>
                        </form>
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- No results message (hidden by default) -->
        <div id="noResults" class="no-results" style="display: none;">
            <i class="fas fa-search fa-2x mb-3"></i>
            <p>No products found matching your search. Please try a different search term.</p>
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
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt me-2"></i>Vivekandha,Puttur</p>
                    <p><i class="fas fa-phone me-2"></i>9447659630</p>
                    <p><i class="fas fa-envelope me-2"></i> Agromate@farmfreshmarket.com</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; 2025 Farm Fresh Market. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Search Functionality Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const productItems = document.querySelectorAll('.product-item');
            const noResultsDiv = document.getElementById('noResults');
            
            // Function to perform search
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                let resultsFound = false;
                
                productItems.forEach(item => {
                    const title = item.getAttribute('data-title');
                    const description = item.getAttribute('data-description');
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm) || searchTerm === '') {
                        item.style.display = '';
                        resultsFound = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide no results message
                noResultsDiv.style.display = resultsFound ? 'none' : 'block';
            }
            
            // Search on button click
            searchButton.addEventListener('click', performSearch);
            
            // Search as you type
            searchInput.addEventListener('input', performSearch);
            
            // Search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        });
    </script>
</body>
</html>