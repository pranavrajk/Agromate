<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "agromate";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form data if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST["phone"], FILTER_SANITIZE_STRING);
    $password = $_POST["password"];

    // Validate form data
    if (empty($email) || empty($password) || empty($phone)) {
        // Redirect to index.php with error message if any field is empty
        header("Location: ../index.php?error=" . urlencode("All fields are required"));
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../index.php?error=" . urlencode("Invalid email format"));
        exit();
    }

    // Validate password complexity
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $numeric = preg_match('@[0-9]@', $password);
    $symbol = preg_match('@[^A-Za-z0-9]@', $password);

    if (strlen($password) < 8 || !$uppercase || !$lowercase || !$numeric || !$symbol) {
        header("Location: ../index.php?error=" . urlencode("Password must meet all requirements"));
        exit();
    }

    // Check if the email already exists using a prepared statement
    $check_email_sql = "SELECT * FROM User WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, redirect with error message
        $stmt->close();
        header("Location: ../index.html?error=" . urlencode("Email already exists"));
        exit();
    }
    $stmt->close();

    // Hash the password before storing it in the database
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the User table using a prepared statement
    $insert_sql = "INSERT INTO User (email, password, mobile_number) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $email, $password, $phone);

    if ($stmt->execute()) {
        // Set session variables upon successful registration
        $_SESSION['user_email'] = $email;

        // Redirect to the dashboard after registration
        header("Location: ../main/index.html");
        exit();
    } else {
        // Handle database insertion error
        header("Location: ../index.php?error=" . urlencode("Registration failed: " . $stmt->error));
        exit();
    }
    $stmt->close();
} else {
    // If not a POST request, redirect to the signup page
    header("Location: ../index.php");
    exit();
}

// Close the database connection
$conn->close();
?>