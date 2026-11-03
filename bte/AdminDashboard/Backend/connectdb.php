<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['loggedin'])) {
    // Redirect to login page after 2 seconds
    header('refresh:2;url=../AdminLoginPage');
    echo "You need to login first to view the dashboard!";
    exit();
}

// Database connection parameters (centralized in bte/db_config.php for deployment)
require_once __DIR__ . '/../../db_config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
