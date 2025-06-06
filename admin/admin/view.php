<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>
<?php
include '../config.php';

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: orders.php?error=missing_order_id");
    exit;
}

$order_id = intval($_GET['id']);

try {
    // Fetch order information
    $orderQuery = "SELECT * FROM orders WHERE id = :order_id";
    $orderStmt = $pdo->prepare($orderQuery);
    $orderStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $orderStmt->execute();
    
    if ($orderStmt->rowCount() === 0) {
        header("Location: orders.php?error=order_not_found");
        exit;
    }
    
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch order items with product details
    $itemsQuery = "SELECT oi.*, p.title, p.image_path 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
    $itemsStmt = $pdo->prepare($itemsQuery);
    $itemsStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $itemsStmt->execute();
    $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    header("Location: orders.php?error=database_error");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order_id; ?> Details</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f9f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .order-header {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #2e7d32;
        }
        
        .order-card {
            background-color: #ffffff;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: #e8f5e9;
            padding: 15px 20px;
            border-bottom: 1px solid #c8e6c9;
        }
        
        .card-header h5 {
            margin: 0;
            color: #2e7d32;
            font-weight: 600;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .section-title {
            color: #2e7d32;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row {
            margin-bottom: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #616161;
        }
        
        .info-value {
            color: #212121;
        }
        
        .status-badge {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 50px;
        }
        
        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-shipped {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .badge-delivered {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-canceled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .product-item {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .product-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 4px;
            background-color: #f5f5f5;
            margin-right: 15px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .product-details {
            flex-grow: 1;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2e7d32;
        }
        
        .product-price {
            color: #616161;
            margin-bottom: 5px;
        }
        
        .product-quantity {
            color: #616161;
        }
        
        .product-total {
            text-align: right;
            font-weight: 600;
            color: #2e7d32;
            font-size: 1.1rem;
            flex-shrink: 0;
            padding-left: 15px;
            margin-left: auto;
        }
        
        .order-summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-label {
            font-weight: 600;
            color: #616161;
        }
        
        .summary-value {
            color: #212121;
        }
        
        .summary-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2e7d32;
        }
        
        .btn-primary {
            background-color: #2e7d32;
            border-color: #2e7d32;
        }
        
        .btn-primary:hover {
            background-color: #1b5e20;
            border-color: #1b5e20;
        }
        
        .back-btn {
            margin-bottom: 20px;
        }
        
        @media (max-width: 767px) {
            .product-item {
                flex-direction: column;
            }
            
            .product-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .product-total {
                text-align: left;
                padding-left: 0;
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="orders.php" class="btn btn-primary back-btn">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
        
        <div class="order-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Order #<?php echo $order_id; ?></h2>
                    <div class="text-muted">
                        <?php echo date('F d, Y \a\t h:i A', strtotime($order['created_at'])); ?>
                    </div>
                </div>
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <?php
                    $statusClass = '';
                    $statusIcon = '';
                    
                    switch($order['status']) {
                        case 'pending':
                            $statusClass = 'badge-pending';
                            $statusIcon = 'clock';
                            break;
                        case 'shipped':
                            $statusClass = 'badge-shipped';
                            $statusIcon = 'shipping-fast';
                            break;
                        case 'delivered':
                            $statusClass = 'badge-delivered';
                            $statusIcon = 'check-circle';
                            break;
                        case 'canceled':
                            $statusClass = 'badge-canceled';
                            $statusIcon = 'ban';
                            break;
                    }
                    ?>
                    <span class="badge status-badge <?php echo $statusClass; ?>">
                        <i class="fas fa-<?php echo $statusIcon; ?> mr-2"></i><?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Order Items -->
                <div class="order-card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart mr-2"></i>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($orderItems) > 0): ?>
                            <?php foreach ($orderItems as $item): ?>
                                <div class="product-item">
                                    <div class="product-image">
                                        <?php if ($item['image_path']): ?>
                                            <img src="<?php echo '../../main/AgroMate_Ecommerce/cart/' . $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-details">
                                        <div class="product-title"><?php echo htmlspecialchars($item['title']); ?></div>
                                        <div class="product-price">Price: $<?php echo number_format($item['price'], 2); ?></div>
                                        <div class="product-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                                    </div>
                                    <div class="product-total">
                                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="order-summary">
                                <?php
                                $itemCount = 0;
                                foreach ($orderItems as $item) {
                                    $itemCount += $item['quantity'];
                                }
                                ?>
                                <div class="summary-row">
                                    <span class="summary-label">Items:</span>
                                    <span class="summary-value"><?php echo $itemCount; ?></span>
                                </div>
                                <div class="summary-row">
                                    <span class="summary-label">Total:</span>
                                    <span class="summary-value summary-total">$<?php echo number_format($order['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>No items found for this order.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="order-card">
                    <div class="card-header">
                        <h5><i class="fas fa-credit-card mr-2"></i>Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Payment Method</div>
                                    <div class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <div class="info-label">Transaction ID</div>
                                    <div class="info-value">
                                        <?php echo $order['transaction_id'] ? htmlspecialchars($order['transaction_id']) : '<span class="text-muted">N/A</span>'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Customer Information -->
                <div class="order-card">
                    <div class="card-header">
                        <h5><i class="fas fa-user mr-2"></i>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['email']); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['phone']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Information -->
                <div class="order-card">
                    <div class="card-header">
                        <h5><i class="fas fa-shipping-fast mr-2"></i>Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($order['address'])); ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Pincode</div>
                            <div class="info-value"><?php echo htmlspecialchars($order['pincode']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Actions -->
                <div class="order-card">
                    <div class="card-header">
                        <h5><i class="fas fa-cog mr-2"></i>Order Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column">
                            <a href="update_status.php?id=<?php echo $order_id; ?>&status=pending" class="btn <?php echo $order['status'] == 'pending' ? 'btn-warning disabled' : 'btn-outline-warning'; ?> mb-2">
                                <i class="fas fa-clock mr-2"></i>Mark as Pending
                            </a>
                            <a href="update_status.php?id=<?php echo $order_id; ?>&status=shipped" class="btn <?php echo $order['status'] == 'shipped' ? 'btn-info disabled' : 'btn-outline-info'; ?> mb-2">
                                <i class="fas fa-shipping-fast mr-2"></i>Mark as Shipped
                            </a>
                            <a href="update_status.php?id=<?php echo $order_id; ?>&status=delivered" class="btn <?php echo $order['status'] == 'delivered' ? 'btn-success disabled' : 'btn-outline-success'; ?> mb-2">
                                <i class="fas fa-check-circle mr-2"></i>Mark as Delivered
                            </a>
                            <a href="update_status.php?id=<?php echo $order_id; ?>&status=canceled" class="btn <?php echo $order['status'] == 'canceled' ? 'btn-danger disabled' : 'btn-outline-danger'; ?>">
                                <i class="fas fa-ban mr-2"></i>Cancel Order
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>