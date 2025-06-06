<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>
<?php 
include '../config.php';

// Simple admin authentication (in a real app, use proper authentication)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: ../index.php");
//     exit;
// }

// Filter settings
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$startDate = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : '';
$search = isset($_GET['search']) && !empty($_GET['search']) ? $_GET['search'] : '';

// Build the query with filters
$query = "SELECT * FROM orders WHERE 1=1";
$params = [];

// Apply status filter
if (!empty($statusFilter)) {
    $query .= " AND status = :status";
    $params[':status'] = $statusFilter;
}

// Apply date range filter
if (!empty($startDate)) {
    $query .= " AND DATE(created_at) >= :start_date";
    $params[':start_date'] = $startDate;
}

if (!empty($endDate)) {
    $query .= " AND DATE(created_at) <= :end_date";
    $params[':end_date'] = $endDate;
}

// Apply search filter
if (!empty($search)) {
    $query .= " AND (id LIKE :search OR customer_name LIKE :search_name OR transaction_id LIKE :search_tx)";
    $params[':search'] = "%$search%";
    $params[':search_name'] = "%$search%";
    $params[':search_tx'] = "%$search%";
}

// Add sorting
$query .= " ORDER BY created_at DESC";

// Prepare and execute the query with filters
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Pagination settings
$itemsPerPage = 10;
$totalOrders = count($orders);
$totalPages = ceil($totalOrders / $itemsPerPage);
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$ordersOnPage = array_slice($orders, $offset, $itemsPerPage);

// include '../header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom Styles -->
    <style>
        /* Your existing styles */
        /* ... */
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <!-- Display success or error messages -->
    <?php if (isset($_GET['status_updated']) && $_GET['status_updated'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i> Order #<?php echo htmlspecialchars($_GET['order_id']); ?> status has been updated successfully.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i> 
        <?php 
            switch($_GET['error']) {
                case 'missing_parameters':
                    echo 'Missing required parameters to update order status.';
                    break;
                case 'invalid_status':
                    echo 'Invalid status value provided.';
                    break;
                case 'database_error':
                    echo 'A database error occurred. Please try again.';
                    break;
                default:
                    echo 'An error occurred. Please try again.';
            }
        ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-shopping-cart text-primary mr-2"></i> Order Management</h2>
        </div>
        
    </div>
    
    <!-- Filters -->
    <div class="card filter-card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter mr-2"></i> Filters</h5>
        </div>
        <div class="card-body">
            <form class="row" method="GET">
                <div class="col-md-3 form-group">
                    <label class="text-sm font-weight-bold text-gray-600">Order Status</label>
                    <select name="status" class="form-control shadow-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="shipped" <?php echo $statusFilter == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $statusFilter == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="canceled" <?php echo $statusFilter == 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label class="text-sm font-weight-bold text-gray-600">Date Range</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control shadow-sm" placeholder="From" 
                               value="<?php echo htmlspecialchars($startDate); ?>">
                        <input type="date" name="end_date" class="form-control shadow-sm" placeholder="To"
                               value="<?php echo htmlspecialchars($endDate); ?>">
                    </div>
                </div>
                <div class="col-md-3 form-group">
                    <label class="text-sm font-weight-bold text-gray-600">Search</label>
                    <input type="text" name="search" class="form-control shadow-sm" 
                           placeholder="Order ID, Customer, Transaction ID"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3 form-group">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Apply Filters
                        </button>
                        <a href="orders.php" class="btn btn-outline-secondary shadow-sm ml-2">
                            <i class="fas fa-redo mr-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="fas fa-list mr-2"></i> Orders List</h5>
                </div>
                <div class="col text-right">
                    <span class="badge badge-pill badge-primary">
                        <?php echo $totalOrders; ?> Orders
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Pincode</th>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ordersOnPage) == 0): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle mr-2"></i> No orders found matching your criteria.
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php foreach ($ordersOnPage as $order): ?>
                        <tr class="transition-all">
                            <td><span class="order-id">#<?php echo $order['id']; ?></span></td>
                            <td><?php echo $order['customer_name']; ?></td>
                            <td>
                                <?php if (!empty($order['address'])): ?>
                                    <?php if (strlen($order['address']) > 30): ?>
                                        <span data-toggle="tooltip" title="<?php echo htmlspecialchars($order['address']); ?>">
                                            <?php echo substr($order['address'], 0, 30) . '...'; ?>
                                        </span>
                                    <?php else: ?>
                                        <?php echo $order['address']; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not provided</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo !empty($order['pincode']) ? $order['pincode'] : '<span class="text-muted">N/A</span>'; ?></td>
                            <td>
                                <?php 
                                    echo !empty($order['transaction_id']) ? 
                                        '<span class="text-sm font-weight-bold">' . $order['transaction_id'] . '</span>' : 
                                        '<span class="text-muted">N/A</span>';
                                ?>
                            </td>
                            <td>
                                <?php 
                                    $date = new DateTime($order['created_at']);
                                    echo '<span class="font-weight-bold">' . $date->format('M d, Y') . '</span>';
                                    echo '<br><small class="text-muted">' . $date->format('h:i A') . '</small>';
                                ?>
                            </td>
                            <td>
                                <span class="order-amount">$<?php echo number_format($order['total_amount'], 2); ?></span>
                            </td>
                            <td>
                                <span class="badge badge-pill status-badge badge-<?php 
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="view.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info shadow-sm mr-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm btn-secondary shadow-sm dropdown-toggle status-update-btn" data-toggle="dropdown" data-order-id="<?php echo $order['id']; ?>">
                                        <i class="fas fa-edit"></i> Status
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                        <h6 class="dropdown-header">Update Status</h6>
                                        <a class="dropdown-item <?php echo $order['status'] == 'pending' ? 'active' : ''; ?>" 
                                           href="update_status.php?id=<?php echo $order['id']; ?>&status=pending">
                                           <i class="fas fa-clock text-warning mr-2"></i> Pending
                                        </a>
                                        <a class="dropdown-item <?php echo $order['status'] == 'shipped' ? 'active' : ''; ?>" 
                                           href="update_status.php?id=<?php echo $order['id']; ?>&status=shipped">
                                           <i class="fas fa-shipping-fast text-info mr-2"></i> Shipped
                                        </a>
                                        <a class="dropdown-item <?php echo $order['status'] == 'delivered' ? 'active' : ''; ?>" 
                                           href="update_status.php?id=<?php echo $order['id']; ?>&status=delivered">
                                           <i class="fas fa-check-circle text-success mr-2"></i> Delivered
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item <?php echo $order['status'] == 'canceled' ? 'active' : ''; ?>" 
                                           href="update_status.php?id=<?php echo $order['id']; ?>&status=canceled">
                                           <i class="fas fa-ban text-danger mr-2"></i> Canceled
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row align-items-center">
                <div class="col-md-6 text-muted text-sm">
                    Showing <?php echo min($offset + 1, $totalOrders); ?> to <?php echo min($offset + $itemsPerPage, $totalOrders); ?> of <?php echo $totalOrders; ?> entries
                </div>
                <div class="col-md-6">
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&status=<?php echo $statusFilter; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&search=<?php echo $search; ?>">
                                    <i class="fas fa-angle-left"></i> Previous
                                </a>
                            </li>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&search=<?php echo $search; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&status=<?php echo $statusFilter; ?>&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&search=<?php echo $search; ?>">
                                    Next <i class="fas fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
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
    // Open status modal with the right order ID
    $('.status-update-btn').on('click', function(e) {
        e.preventDefault(); // Prevent the dropdown from opening
        var orderId = $(this).data('order-id');
        $('#order_id_input').val(orderId);
        $('#statusModal').modal('show');
    });
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Add shadow effect on table row hover
    $('.table-hover tr').hover(
        function() {
            $(this).addClass('shadow-sm');
        }, 
        function() {
            $(this).removeClass('shadow-sm');
        }
    );
    
    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut(500);
});
</script>
</body>
</html>