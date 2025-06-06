<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>
<?php
include '../config.php';

// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: ../index.php");
//     exit;
// }

if (isset($_GET['id']) && isset($_GET['status'])) {
    $order_id = $_GET['id'];
    $status = $_GET['status'];
    
    $valid_statuses = ['pending', 'shipped', 'delivered', 'canceled'];
    if (!in_array($status, $valid_statuses)) {
        die("Invalid status");
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $order_id]);
    
    header("Location: orders.php");
    exit;
} else {
    header("Location: orders.php");
    exit;
}
?>