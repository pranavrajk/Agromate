<?php 
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

// include 'includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['title']; ?> - Product Details</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 0.85rem;
            padding-top: 20px;
            padding-bottom: 40px;
        }

        /* Card Styles */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.35rem;
            margin-bottom: 0;
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Button Styles */
        .btn {
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        /* Form Controls */
        .form-control {
            font-size: 0.85rem;
            border-radius: 0.25rem;
            border: 1px solid #d1d3e2;
        }

        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Headers */
        h2 {
            font-weight: 700;
            color: #5a5c69;
            margin-bottom: 1rem;
        }

        h4 {
            font-weight: 700;
            color: #4e73df;
            margin: 1.5rem 0;
        }

        /* Badge for product category */
        .badge {
            font-size: 85%;
            font-weight: 600;
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
        }

        .badge-secondary {
            background-color: var(--secondary-color);
        }

        /* Product image container */
        .product-image-container {
            background-color: #fff;
            border-radius: 0.35rem;
            overflow: hidden;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }

        /* Product details card */
        .product-details-card {
            background-color: #fff;
            border-radius: 0.35rem;
            padding: 1.5rem;
            height: 100%;
        }

        /* Description styling */
        .product-description {
            color: #6e707e;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        /* Price styling */
        .product-price {
            color: #4e73df;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 1rem 0;
        }

        /* Quantity input group */
        .quantity-group {
            width: 150px;
            margin-bottom: 1.5rem;
        }

        /* Back button */
        .back-button {
            margin-bottom: 1.5rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding-left: 0;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            color: #4e73df;
        }

        /* Availability badge */
        .in-stock {
            color: var(--success-color);
            font-weight: 600;
        }

        .out-of-stock {
            color: var(--danger-color);
            font-weight: 600;
        }

        /* Related products */
        .related-products {
            margin-top: 3rem;
        }

        /* Product image */
        .product-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.03);
        }

        /* Add to cart button */
        .add-to-cart-btn {
            width: 100%;
            margin-top: 1rem;
        }

        /* Product features list */
        .features-list {
            padding-left: 1.25rem;
            margin-bottom: 1.5rem;
            color: #6e707e;
        }

        .features-list li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" ><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['title']; ?></li>
        </ol>
    </nav>

    <!-- Back button -->
    <div class="back-button">
        <a href="javascript:history.back()" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Products
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-box-open mr-2 text-primary"></i> Product Details</h5>
                </div>
                <div class="col text-right">
                    <!-- If you have product categories -->
                    <?php if (!empty($product['category'])): ?>
                    <span class="badge badge-secondary">
                        <?php echo $product['category']; ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="product-image-container shadow-sm">
                        <img src="<?php echo $product['image_path']; ?>" class="product-image" alt="<?php echo $product['title']; ?>">
                    </div>
                    <!-- If you have multiple product images, add a gallery here -->
                </div>
                <div class="col-md-7">
                    <div class="product-details-card">
                        <h2><?php echo $product['title']; ?></h2>
                        
                        <!-- Stock status -->
                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                            <p><i class="fas fa-check-circle mr-1 in-stock"></i> <span class="in-stock">In Stock</span> (<?php echo $product['stock']; ?> available)</p>
                        <?php else: ?>
                            <p><span class="out-of-stock"></span></p>
                        <?php endif; ?>
                        
                        <!-- Price -->
                        <div class="product-price">
                            $<?php echo number_format($product['price'], 2); ?>
                        </div>
                        
                        <!-- Description -->
                        <div class="product-description">
                            <?php echo $product['description']; ?>
                        </div>
                        
                        <!-- Features if you have them -->
                        <?php if (isset($product['features']) && !empty($product['features'])): ?>
                        <h6 class="font-weight-bold">Features:</h6>
                        <ul class="features-list">
                            <?php 
                            $features = explode(',', $product['features']);
                            foreach ($features as $feature): 
                            ?>
                            <li><?php echo trim($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                        
                        <!-- Add to cart form -->
                        <form action="add_to_cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="form-group">
                                <label class="font-weight-bold">Quantity:</label>
                                <div class="input-group quantity-group shadow-sm">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="quantity-minus">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="number" name="quantity" id="quantity-input" value="1" min="1" class="form-control text-center">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="quantity-plus">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button style="background-color: green; border-color: green;" type="submit" class="btn btn-primary add-to-cart-btn shadow-sm">
                                <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Required JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Quantity increment/decrement functionality
    $('#quantity-plus').click(function() {
        var value = parseInt($('#quantity-input').val());
        $('#quantity-input').val(value + 1);
    });
    
    $('#quantity-minus').click(function() {
        var value = parseInt($('#quantity-input').val());
        if(value > 1) {
            $('#quantity-input').val(value - 1);
        }
    });
    
    // If you have product image gallery, add the functionality here
    
    // Add hover effects
    $('.card').hover(
        function() {
            $(this).addClass('shadow');
        }, 
        function() {
            $(this).removeClass('shadow');
        }
    );
});
</script>
</body>
</html>