<?php
session_start();

// Database connection details
$servername = "172.20.128.65";
$username = "root";
$password = "Skole123";
$dbname = "login";

// Update money amount in the database before logging out
if (isset($_SESSION['username'])) {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update money amount in the database
    $username = $_SESSION['username'];
    $money = $_SESSION['money'];
    $query = "UPDATE users SET money = $money WHERE username = '$username'";
    if ($conn->query($query) === TRUE) {
        echo "Money amount updated successfully";
    } else {
        echo "Error updating money amount: " . $conn->error;
    }

    // Close database connection
    $conn->close();
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
