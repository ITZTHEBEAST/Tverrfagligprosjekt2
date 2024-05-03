<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Database connection details
$servername = "172.20.128.65";
$username = "root";
$password = "Skole123";
$dbname = "login";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("You are not logged in.");
}

// Check if the user is an admin (you may adjust this condition based on your user roles)
if ($_SESSION['username'] !== 'admin') {
    die("You do not have permission to reset money.");
}

// Check if a username is provided to reset money for a specific user
if (!isset($_GET['username'])) {
    die("Please provide a username.");
}

// Get the username from the query string
$username = $_GET['username'];

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Reset money for the specified user
$query = "UPDATE users SET money = 0 WHERE username = '$username'";
if ($conn->query($query) === TRUE) {
    echo "Money reset successfully for user: $username";
} else {
    echo "Error resetting money: " . $conn->error;
}



// Close database connection
$conn->close();
?>
