<?php
// Start session at the very top
// session_start();

// Include database configuration
require 'config.php';

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['id'];

// Fetch order details
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        throw new Exception("Order not found");
    }
    
    // Fetch order items
    $stmt = $pdo->prepare("
        SELECT oi.*, p.title, p.image_path 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
    
} catch (Exception $e) {
    die("Error retrieving order: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .product-img {
            max-height: 60px;
            width: auto;
        }
        .thank-you {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="thank-you">
            <h1 class="text-success">✓</h1>
            <h2>Thank You for Your Order!</h2>
            <p class="lead">Your order has been placed successfully.</p>
            <p>Order ID: <strong>#<?php echo $order['id']; ?></strong></p>
            <a href="index.php" class="btn btn-primary" style="background-color: green;">Continue Shopping</a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="order-card">
                    <h4>Order Details</h4>
                    <hr>
                    <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                    <p><strong>Total:</strong> Rs<?php echo number_format($order['total_amount'], 2); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
                    <?php if (!empty($order['transaction_id'])): ?>
                        <p><strong>Transaction ID:</strong> <?php echo $order['transaction_id']; ?></p>
                    <?php endif; ?>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php 
                            switch($order['status']) {
                                case 'pending': echo 'warning'; break;
                                case 'shipped': echo 'info'; break;
                                case 'delivered': echo 'success'; break;
                                case 'canceled': echo 'danger'; break;
                                default: echo 'secondary';
                            }
                        ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="order-card">
                    <h4>Customer Details</h4>
                    <hr>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                    <p><strong>Pincode:</strong> <?php echo htmlspecialchars($order['pincode']); ?></p>
                </div>
            </div>
        </div>

        <div class="order-card mt-4">
            <h4>Order Items</h4>
            <hr>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" class="product-img me-2">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </td>
                        <td>Rs<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>Rs<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Grand Total:</th>
                        <th>Rs<?php echo number_format($order['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-center mt-4">
            <p>We've sent a confirmation email to <?php echo htmlspecialchars($order['email']); ?></p>
            <p>For any questions, please contact our customer support.</p>
            <a href="index.php" class="btn btn-primary" style="background-color: green;">Continue Shopping</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>