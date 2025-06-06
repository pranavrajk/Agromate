<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root'; // Replace with your database username
$db_pass = '';     // Replace with your database password
$db_name = 'agromate';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    
    $sql = "DELETE FROM user WHERE id = $delete_id";
    
    if ($conn->query($sql)) {
        $message = "User deleted successfully!";
    } else {
        $message = "Error deleting user: " . $conn->error;
    }
}

// Fetch all users
$sql = "SELECT id, email, mobile_number, created_at FROM user ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agromate - User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --dark-green: #2E7D32;
            --light-green: #81C784;
            --accent-color: #FFC107;
            --error-color: #e74c3c;
            --bg-color: #F1F8E9;
            --text-color: #33691E;
            --light-text: #689F38;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color);
            color: #333;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234caf50' fill-opacity='0.07'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .header {
            background-color: var(--dark-green);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath fill='%23ffffff' fill-opacity='0.05' d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z'%3E%3C/path%3E%3C/svg%3E");
            z-index: 0;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            z-index: 1;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .logout-btn {
            background-color: var(--accent-color);
            color: var(--dark-green);
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            z-index: 1;
        }
        
        .logout-btn i {
            margin-right: 6px;
        }
        
        .logout-btn:hover {
            background-color: #FFD54F;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,0.08);
            position: relative;
            border: 1px solid #E8F5E9;
        }
        
        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background-color: var(--primary-color);
            border-radius: 12px 12px 0 0;
        }
        
        h1 {
            text-align: center;
            color: var(--text-color);
            margin-top: 10px;
            margin-bottom: 30px;
            font-weight: 600;
            padding-bottom: 15px;
            position: relative;
        }
        
        h1::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-color);
        }
        
        .message {
            padding: 12px 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .message i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .success {
            background-color: #E8F5E9;
            color: var(--dark-green);
            border: 1px solid #C8E6C9;
        }
        
        .error {
            background-color: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.03);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #E8F5E9;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: #F9FFF9;
        }
        
        tr:hover {
            background-color: #E8F5E9;
        }
        
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .delete-btn {
            background-color: var(--error-color);
        }
        
        .delete-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .delete-btn i {
            margin-right: 5px;
        }
        
        .no-users {
            padding: 30px;
            text-align: center;
            color: var(--light-text);
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-leaf"></i> AGROMATE
        </div>
        <a href="admin/index.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
    
    <div class="container">
        <h1>User Management Dashboard</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php if (strpos($message, 'Error') !== false): ?>
                    <i class="fas fa-exclamation-circle"></i>
                <?php else: ?>
                    <i class="fas fa-check-circle"></i>
                <?php endif; ?>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email (Username)</th>
                    <th>Mobile Number</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['mobile_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="?delete_id=<?php echo $row['id']; ?>" 
                                   class="action-btn delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-users">
                            <i class="fas fa-users-slash"></i> No users found in the system
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>