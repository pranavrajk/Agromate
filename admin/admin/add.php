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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $owner_name = $conn->real_escape_string($_POST['owner_name']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $price = floatval($_POST['price']);
    $land_title = $conn->real_escape_string($_POST['land_title']);
    $description = $conn->real_escape_string($_POST['description']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['land_image']) && $_FILES['land_image']['error'] === 0) {
        $upload_dir = '../../main/Land/uploads/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['land_image']['name']);
        $target_path = $upload_dir . $file_name;
        
        // Move uploaded file to target location
        if (move_uploaded_file($_FILES['land_image']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        } else {
            $message = "Error uploading file.";
        }
    }
    
    // Insert data into database
    $sql = "INSERT INTO land_listings (owner_name, phone_number, price, land_title, description, latitude, longitude, image_path) 
            VALUES ('$owner_name', '$phone_number', $price, '$land_title', '$description', $latitude, $longitude, '$image_path')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Land listing added successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Land Listing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .map-container {
            height: 400px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Add Land Listing</h1>
                <nav>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" href="add.php">Add Land</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="remove.php">Remove Land</a>
        </li>
    </ul>
</nav>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="add.php" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="owner_name" class="form-label">Owner Name</label>
                                <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="land_title" class="form-label">Land Title</label>
                                <input type="text" class="form-control" id="land_title" name="land_title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Land Area</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="land_image" class="form-label">Land Image</label>
                                <input type="file" class="form-control" id="land_image" name="land_image" accept="image/*" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Location on Map</label>
                                <div id="map" class="map-container"></div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude" required readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Land Listing</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css">
    <script>
        // Initialize map
        const map = L.map('map').setView([0, 0], 2);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add marker on click
        let marker;
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            // Update form fields
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Update or add marker
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });
    </script>
</body>
</html>