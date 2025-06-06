<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: ../../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgroMate Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7f2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, #4a8c24, #2e5e1a);
            color: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-left h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .header-left p {
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 0.3rem;
        }
        
        .logout-btn {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1200px;
            width: 90%;
            margin: 2rem auto;
            flex-grow: 1;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .dashboard-card {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            height: 200px;
            display: flex;
            flex-direction: column;
            border: 1px solid #e0e0e0;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            height: 8px;
        }
        
        .card-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
        }
        
        .card-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 1rem;
            background-color: rgba(74, 140, 36, 0.1);
        }
        
        .card-icon i {
            font-size: 26px;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2e5e1a;
        }
        
        .card-description {
            font-size: 0.9rem;
            color: #666;
        }
        
        .card-users .card-header {
            background-color: #4a8c24; /* Green */
        }
        
        .card-users .card-icon i {
            color: #4a8c24;
        }
        
        .card-products .card-header {
            background-color: #8bc34a; /* Light green */
        }
        
        .card-products .card-icon i {
            color: #8bc34a;
        }
        
        .card-orders .card-header {
            background-color: #ff9800; /* Orange */
        }
        
        .card-orders .card-icon i {
            color: #ff9800;
        }
        
        .card-land .card-header {
            background-color: #795548; /* Brown */
        }
        
        .card-land .card-icon i {
            color: #795548;
        }
        
        .footer {
            text-align: center;
            padding: 1rem;
            margin-top: auto;
            background-color: #fff;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 0.9rem;
            background-color: #f5f7f2;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .header-left h1 {
                font-size: 1.5rem;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .logout-btn {
                align-self: flex-end;
            }
        }
        
        /* Card animation */
        .dashboard-card::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #ddd;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }
        
        .dashboard-card:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }
        
        .card-users:hover::after {
            background-color: #4a8c24;
        }
        
        .card-products:hover::after {
            background-color: #8bc34a;
        }
        
        .card-orders:hover::after {
            background-color: #ff9800;
        }
        
        .card-land:hover::after {
            background-color: #795548;
        }
        
        /* Logout Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 100;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: modalFadeIn 0.3s ease forwards;
            border: 1px solid #e0e0e0;
        }
        
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal h3 {
            margin-bottom: 1rem;
            color: #2e5e1a;
        }
        
        .modal p {
            margin-bottom: 1.5rem;
            color: #666;
        }
        
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-cancel {
            background-color: #e0e0e0;
            color: #333;
        }
        
        .btn-cancel:hover {
            background-color: #d0d0d0;
        }
        
        .btn-logout {
            background-color: #d32f2f;
            color: white;
        }
        
        .btn-logout:hover {
            background-color: #b71c1c;
        }
        
        /* Nature-inspired decorative elements */
        .leaf-decoration {
            position: absolute;
            opacity: 0.1;
            z-index: -1;
        }
        
        .leaf-1 {
            top: 50px;
            right: 50px;
            font-size: 120px;
            color: #4a8c24;
            transform: rotate(30deg);
        }
        
        .leaf-2 {
            bottom: 80px;
            left: 40px;
            font-size: 100px;
            color: #8bc34a;
            transform: rotate(-15deg);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h1><i class="fas fa-leaf"></i> AgroMate Admin</h1>
            <p>Welcome back, Farm Administrator</p>
        </div>
        <button class="logout-btn" onclick="showLogoutModal()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
    
    <div class="container">
        <!-- Decorative leaf elements -->
        <div class="leaf-decoration leaf-1">
            <i class="fas fa-leaf"></i>
        </div>
        <div class="leaf-decoration leaf-2">
            <i class="fas fa-seedling"></i>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card card-users" onclick="redirectTo('../user.php')">
                <div class="card-header"></div>
                <div class="card-content">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="card-title">User Management </h3>
                    <p class="card-description">Manage all registered farmers and users</p>
                </div>
            </div>
            
            <div class="dashboard-card card-products" onclick="redirectTo('all_products.php')">
                <div class="card-header"></div>
                <div class="card-content">
                    <div class="card-icon">
                        <i class="fas fa-apple-alt"></i>
                    </div>
                    <h3 class="card-title">Product Dashboard</h3>
                    <p class="card-description">Manage agricultural products inventory</p>
                </div>
            </div>
            
            <div class="dashboard-card card-orders" onclick="redirectTo('orders.php')">
                <div class="card-header"></div>
                <div class="card-content">
                    <div class="card-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3 class="card-title">Farm Orders</h3>
                    <p class="card-description">Track and process farm product orders</p>
                </div>
            </div>
            
            <div class="dashboard-card card-land" onclick="redirectTo('add.php')">
                <div class="card-header"></div>
                <div class="card-content">
                    <div class="card-icon">
                        <i class="fas fa-tractor"></i>
                    </div>
                    <h3 class="card-title">Land Management</h3>
                    <p class="card-description">Manage farmland and cultivation details</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>© 2025 AgroMate Farm Management System. All rights reserved.</p>
    </div>
    
    <!-- Logout Confirmation Modal -->
    <div class="modal" id="logoutModal">
        <div class="modal-content">
            <h3><i class="fas fa-sign-out-alt"></i> Confirm Logout</h3>
            <form action="../../action/logout.php">
            <p>Are you sure you want to log out of the farm management system?</p>
            <div class="modal-buttons">
                <button type="button" class="btn btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <input type="submit" class="btn btn-logout" value="Logout"></input>
            </div>
            </form>
        </div>
    </div>
    
    <script>
        // Function to redirect to different pages
        function redirectTo(page) {
            
            
            // Simulating page loading delay for smoother transition
            setTimeout(() => {
                window.location.href = page;
            }, 300);
        }
        
        // Update welcome message with current time
        const updateWelcomeMessage = () => {
            const now = new Date();
            const hour = now.getHours();
            let greeting;
            
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }
            
            document.querySelector('.header-left p').innerText = `${greeting}, Farm Administrator`;
        };
        
        // Call the function when page loads
        updateWelcomeMessage();
        
        // Add subtle animation to dashboard cards
        const cards = document.querySelectorAll('.dashboard-card');
        cards.forEach((card, index) => {
            // Add staggered animation delay
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * (index + 1));
        });
        
        // Logout modal functions
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
        }
        
        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }
        
        function logout() {
            // Animation effect before logging out
            document.body.style.opacity = '0.5';
            document.body.style.transition = 'opacity 0.3s ease';
            
            // Redirect to login page
            setTimeout(() => {
                window.location.href = '../../index.html';
            }, 300);
        }
        
        // Close modal if clicked outside
        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) {
                hideLogoutModal();
            }
        }
    </script>
</body>
</html>