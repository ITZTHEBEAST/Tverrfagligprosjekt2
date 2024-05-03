<?php
session_start();

// Database connection details
$servername = "172.20.128.65";
$username = "root";
$password = "Skole123";
$dbname = "login";

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve money amount and cheese count from the database upon login
if (!isset($_SESSION['money']) || !isset($_SESSION['cheese'])) {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve money amount and cheese count from the database
    $username = $_SESSION['username'];
    $query = "SELECT money, cheese FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['money'] = $row['money'];
        $_SESSION['cheese'] = $row['cheese'];
    } else {
        echo "Error: Data not found for user $username";
    }

    // Close database connection
    $conn->close();
}

// Handle clicking the button
if (isset($_POST['click'])) {
    // Multiply money by 1.1 on click
    $_SESSION['money'] *= 1.1;

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

// Function to buy cheese and update money
function buyCheese($cheesePrice, $servername, $username, $password, $dbname) {
    if ($_SESSION['money'] >= $cheesePrice) {
        $_SESSION['cheese']++; // Increment cheese count
        $_SESSION['money'] -= $cheesePrice; // Deduct cheese price from money

        // Connect to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update cheese count and money in the database
        $username = $_SESSION['username'];
        $cheeseCount = $_SESSION['cheese'];
        $moneyAmount = $_SESSION['money'];
        $query = "UPDATE users SET cheese = $cheeseCount, money = $moneyAmount WHERE username = '$username'";
        if ($conn->query($query) === TRUE) {
            echo "Cheese count and money updated successfully";
        } else {
            echo "Error updating cheese count and money: " . $conn->error;
        }

        // Close database connection
        $conn->close();
    } else {
        echo "Not enough money to buy cheese!";
    }
}

// Attempt to buy cheese for 5 money
if (isset($_POST['buy_cheese'])) {
    $cheesePrice = 5;
    buyCheese($cheesePrice, $servername, $username, $password, $dbname);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <p>Money: <?php echo number_format($_SESSION['money'], 0, '', ''); ?></p>
    <p>Cheese: <?php echo number_format($_SESSION['cheese'], 0, '', ''); ?></p>
    <form method="post">
        <button type="submit" name="click">Click</button>
        <button type="submit" name="buy_cheese">Buy Cheese (5 money)</button>
    </form>
    <a href="logout.php">Logout</a>
</body>
</html>
