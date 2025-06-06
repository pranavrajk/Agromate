<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>
<?php
// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'land_marketplace';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';

// Handle deletion
if (isset($_POST['delete_id']) && !empty($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // First, get the image path to delete the file
    $image_query = "SELECT image_path FROM land_listings WHERE id = $delete_id";
    $image_result = $conn->query($image_query);
    
    if ($image_result && $image_result->num_rows > 0) {
        $image_row = $image_result->fetch_assoc();
        $image_path = $image_row['image_path'];
        
        // Delete the image file if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete the record from database
    $delete_query = "DELETE FROM land_listings WHERE id = $delete_id";
    if ($conn->query($delete_query) === TRUE) {
        $message = "Land listing deleted successfully!";
        $messageType = "success";
    } else {
        $message = "Error deleting record: " . $conn->error;
        $messageType = "danger";
    }
}

// Fetch all land listings
$sql = "SELECT * FROM land_listings ORDER BY created_at DESC";
$result = $conn->query($sql);
$listings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Land Listings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .land-image-thumbnail {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .delete-form {
            display: inline;
        }
        .delete-btn {
            color: white;
            background-color: #dc3545;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .empty-message {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        .modal-body img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Remove Land Listings</h1>
                <nav class="mb-4">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link" href="add.php">Add Land</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="remove.php">Remove Land</a>
                        </li>
                    </ul>
                </nav>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($listings)): ?>
                    <div class="empty-message">
                        <h3>No land listings found</h3>
                        <p>There are currently no land listings in the database.</p>
                        <a href="add.php" class="btn btn-primary">Add Land Listing</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Owner</th>
                                    <th>Price</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($listings as $land): ?>
                                    <tr>
                                        <td><?php echo $land['id']; ?></td>
                                        <td>
                                            <?php if (!empty($land['image_path'])): ?>
                                                <img 
                                                    src="<?php echo $land['image_path']; ?>" 
                                                    class="land-image-thumbnail" 
                                                    alt="<?php echo $land['land_title']; ?>"
                                                    onclick="viewLandDetails(<?php echo htmlspecialchars(json_encode($land)); ?>)"
                                                    style="cursor: pointer;"
                                                    onerror="this.onerror=null; this.src='placeholder.jpg';"
                                                >
                                            <?php else: ?>
                                                <img src="placeholder.jpg" class="land-image-thumbnail" alt="No image">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $land['land_title']; ?></td>
                                        <td><?php echo $land['owner_name']; ?></td>
                                        <td>$<?php echo number_format($land['price'], 2); ?></td>
                                        <td>
                                            <?php echo number_format($land['latitude'], 6); ?>, 
                                            <?php echo number_format($land['longitude'], 6); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-info btn-sm me-2" 
                                                    onclick="viewLandDetails(<?php echo htmlspecialchars(json_encode($land)); ?>)"
                                                >
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <form class="delete-form" method="POST" onsubmit="return confirmDelete()">
                                                    <input type="hidden" name="delete_id" value="<?php echo $land['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Land Detail Modal -->
    <div class="modal fade" id="landDetailModal" tabindex="-1" aria-labelledby="landDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="landDetailModalLabel">Land Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="landDetailContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to display land details in modal
        function viewLandDetails(land) {
            const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
            const imagePath = land.image_path ? baseUrl + land.image_path : baseUrl + 'placeholder.jpg';
            
            const formattedPrice = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'INR'
            }).format(land.price);
            
            const content = `
                <div class="row">
                    <div class="col-md-6 text-center">
                        <img src="${imagePath}" class="img-fluid" alt="${land.land_title}" 
                             onerror="this.onerror=null; this.src='placeholder.jpg';">
                    </div>
                    <div class="col-md-6">
                        <h3>${land.land_title}</h3>
                        <h4 class="text-success">${formattedPrice}</h4>
                        
                        <div class="mb-3">
                            <strong>Owner:</strong> ${land.owner_name}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Contact:</strong> ${land.phone_number}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Location:</strong> ${parseFloat(land.latitude).toFixed(6)}, ${parseFloat(land.longitude).toFixed(6)}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Created:</strong> ${new Date(land.created_at).toLocaleString()}
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description:</h5>
                        <p>${land.description || 'No description provided.'}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('landDetailContent').innerHTML = content;
            document.getElementById('landDetailModalLabel').textContent = `Land Details: ${land.land_title}`;
            
            const modal = new bootstrap.Modal(document.getElementById('landDetailModal'));
            modal.show();
        }
        
        // Confirmation dialog for delete action
        function confirmDelete() {
            return confirm('Are you sure you want to delete this land listing? This action cannot be undone.');
        }
    </script>
</body>
</html>