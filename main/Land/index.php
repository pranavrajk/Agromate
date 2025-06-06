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

// Fetch land listings
$sql = "SELECT * FROM land_listings";
$result = $conn->query($sql);

$listings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Land Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9f4;
        }
        
        /* Header Styles */
        .site-header {
            background: linear-gradient(rgb(23 255 35 / 70%), rgb(6 93 7)), url('https://cdn.jsdelivr.net/gh/placeholder/images@main/farm-landscape.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5px 0;
            margin-bottom: 10px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        
        .site-header h1 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 0;
        }
        
        .site-tagline {
            font-style: italic;
            margin-top: 10px;
            font-size: 1.2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        
        .container-fluid {
            padding: 15px;
        }
        
        .map-container {
            height: calc(100vh - 250px);
            width: 100%;
        }
        
        #map {
            height: 100%;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 3px solid #6a994e;
        }
        
        .detail-panel {
            height: calc(100vh - 250px);
            overflow-y: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #dde5b6;
        }
        
        .land-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #a3b18a;
        }
        
        .card {
            border: none;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 12px;
            background-color: #f8f9fa;
            border-left: 5px solid #588157;
        }
        
        .card-title {
            color: #386641;
            font-weight: 600;
        }
        
        .no-selection-message {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 300px;
            color: #6c757d;
            font-style: italic;
            text-align: center;
            border: 2px dashed #a3b18a;
            border-radius: 12px;
            margin-top: 50px;
            background-color:rgb(245, 250, 247);
        }
        
        .custom-marker {
            text-align: center;
            color: white;
            font-weight: bold;
            background-color: #588157;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        
        .nav-pills .nav-link.active {
            background-color:rgba(14, 158, 6, 0.95);
        }
        
        .nav-pills .nav-link {
            color:rgba(5, 219, 48, 0.93);
            font-weight: 500;
        }
        
        .btn-primary {
            background-color:rgb(35, 174, 11);
            border-color: #588157;
        }
        
        .btn-primary:hover {
            background-color: #386641;
            border-color:rgb(7, 176, 41);
        }
        
        .text-success {
            color: #588157 !important;
        }
        
        /* Agricultural Icons */
        .ag-icon {
            margin-right: 8px;
            color: #588157;
        }
        
        /* Property Type Badge */
        .property-badge {
            background-color: #dde5b6;
            color: #386641;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .logo-image {
            width: auto;
            height: 2.5rem;
            object-fit: cover;
            padding: 1px;
        }
        
        .btn-outline-success {
            background-color: green;
            color: #f9fffc;
            border-radius: 10px;
            border: 3px solid #f8f8f4;
            padding: 6px 15px;
        }
        
        /* New navbar layout styles */
        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .logo-container {
            flex: 1;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }
        
        .title-container {
            flex: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .home-button-container {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
            }
            
            .logo-container, .title-container, .home-button-container {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- New Agricultural Header with updated layout -->
    <header class="site-header">
        <div class="container">
            <div class="navbar-container">
                <!-- Logo on the left -->
                <div class="logo-container">
                <a href="../index.html">
                    <img src="../images/Logo.jpg" class="logo-image" alt="Logo"> </a>
                </div>
                
                <!-- Title in the center -->
                <div class="title-container">
                    <h1><i class="fas fa-tractor"></i> Agricultural Land Marketplace</h1>
                </div>
                
                <!-- Home button on the right -->
                <div class="home-button-container">
                    <a href="../index.html" class="btn btn-outline-success">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                </div>
            </div>
            <p class="site-tagline">Find the perfect farmland, orchard, or ranch for your agricultural needs</p>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <nav>
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php"><i class="fas fa-map-marker-alt"></i> View Map</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        
        <div class="row">
            <!-- Map on the left -->
            <div class="col-md-8">
                <div class="map-container">
                    <div id="map"></div>
                </div>
            </div>
            
            <!-- Details on the right -->
            <div class="col-md-4">
                <div class="detail-panel" id="detailPanel">
                    <div id="landDetails">
                        <div class="no-selection-message">
                            <div>
                                <h4><i class="fas fa-seedling"></i> Land Details</h4>
                                <p>Click on a marker to view agricultural land details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Land listings data from PHP
        const landListings = <?php echo json_encode($listings); ?>;
        
        // Initialize map
        const map = L.map('map').setView([20, 0], 2);
        
        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers for each land listing
        const markers = [];
        landListings.forEach(land => {
            const lat = parseFloat(land.latitude);
            const lng = parseFloat(land.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                // Create custom icon
                const markerIcon = L.divIcon({
                    className: 'custom-marker',
                    html: '<i class="fas fa-leaf"></i>',
                    iconSize: [30, 30]
                });
                
                // Create marker
                const marker = L.marker([lat, lng], {
                    title: land.land_title,
                    icon: markerIcon
                }).addTo(map);
                
                // Add popup with basic info
                marker.bindPopup(`<b>${land.land_title}</b><br>$${parseFloat(land.price).toLocaleString()}`);
                
                // Add click listener
                marker.on('click', () => {
                    showLandDetails(land);
                    
                    // Highlight selected marker
                    markers.forEach(m => {
                        m._icon.style.backgroundColor = '#588157';
                        m._icon.style.zIndex = 1000;
                    });
                    marker._icon.style.backgroundColor = '#386641';
                    marker._icon.style.zIndex = 2000;
                });
                
                markers.push(marker);
            }
        });
        
        // If we have markers, fit the map to show all of them
        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.1));
        }
        
        // Show land details in the panel
        function showLandDetails(land) {
            const landDetails = document.getElementById('landDetails');
            
            // Format price with commas and decimal places
            const formattedPrice = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(land.price);
            
            // Get absolute URL for the image path
            const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
            const imagePath = land.image_path ? baseUrl + land.image_path : baseUrl + 'placeholder.jpg';
            
            // Determine property type (this could be expanded based on real data)
            const propertyType = "Farmland";
            
            // Populate details
            landDetails.innerHTML = `
                <div class="card">
                    <img src="${imagePath}" class="land-image" alt="${land.land_title}" onerror="this.onerror=null; this.src='placeholder.jpg';">
                    <div class="card-body">
                        <div class="property-badge">${propertyType}</div>
                        <h3 class="card-title">${land.land_title}</h3>
                        <h4 class="text-success">${formattedPrice}</h4>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-user ag-icon"></i>Owner:</strong> ${land.owner_name}
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-phone ag-icon"></i>Contact:</strong> ${land.phone_number}
                        </div>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-map-marker-alt ag-icon"></i>Location:</strong> ${parseFloat(land.latitude).toFixed(6)}, ${parseFloat(land.longitude).toFixed(6)}
                        </div>
                        
                        <div class="mb-4">
                            <strong><i class="fas fa-ruler-combined ag-icon"></i>Land Area:</strong>
                            <p>${land.description || 'No description provided.'}</p>
                        </div>
                        
                        <button class="btn btn-primary w-100" onclick="window.location.href='mailto:contact@example.com?subject=Inquiry about ${encodeURIComponent(land.land_title)}'">
                            <i class="fas fa-envelope"></i> Contact Seller
                        </button>
                    </div>
                </div>
            `;
        }
    </script>
</body>
</html>