<?php 
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$order_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

include 'includes/header.php'; 
?>

<div class="container">
    <div class="alert alert-success">
        <h2>Order Placed Successfully!</h2>
        <p>Thank you for your order. Your order ID is: <strong>#<?php echo $order['id']; ?></strong></p>
        <p>We'll process your order shortly.</p>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <h4>Order Details</h4>
            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Date:</strong> <?php echo $order['created_at']; ?></p>
            <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Payment Method:</strong> <?php echo ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
        </div>
        <div class="col-md-6">
            <h4>Customer Details</h4>
            <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
            <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
            <p><strong>Address:</strong> <?php echo nl2br($order['address']); ?></p>
            <p><strong>Pincode:</strong> <?php echo $order['pincode']; ?></p>
        </div>
    </div>
    
    <h4>Order Items</h4>
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
            <?php
            $stmt = $pdo->prepare("
                SELECT oi.*, p.title, p.image_path 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$order_id]);
            $items = $stmt->fetchAll();
            
            foreach ($items as $item):
            ?>
            <tr>
                <td>
                    <img src="<?php echo $item['image_path']; ?>" width="50">
                    <?php echo $item['title']; ?>
                </td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="index.php" class="btn btn-primary">Continue Shopping</a>
</div>

<!-- <?php include 'includes/footer.php'; ?> -->