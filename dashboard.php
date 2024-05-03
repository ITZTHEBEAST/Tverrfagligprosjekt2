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

    // Check if query was successful
    if (!$result) {
        die("Error: " . $conn->error);
    }
    
    // Check if user data was found
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

// Function to buy an item
function buyItem($itemPrice) {
    if ($_SESSION['money'] >= $itemPrice) {
        $_SESSION['money'] -= $itemPrice;
        return true; // Purchase successful
    } else {
        return false; // Not enough money to purchase
    }
}

// Handle clicking the button
if (isset($_POST['click'])) {
    // Increment money on click
    $_SESSION['money']++;

    // Update money based on cheese count
    $moneyMultiplier = 0.1 * $_SESSION['cheese']; // Money multiplier based on cheese count
    $_SESSION['money'] *= (1 + $moneyMultiplier);

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

// Attempt to buy cheese
if (isset($_POST['buy_cheese'])) {
    $baseCheesePrice = 5; // Base price of cheese
    $multiplier = 0.1; // Multiplier for cheese price
    $cheesePrice = $baseCheesePrice * (1 + $multiplier * $_SESSION['cheese']); // Calculate actual price of cheese
    if (buyItem($cheesePrice)) {
        $_SESSION['cheese']++; // Increment cheese count

        // Connect to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Update cheese count in the database
        $username = $_SESSION['username'];
        $cheeseCount = $_SESSION['cheese'];
        $query = "UPDATE users SET cheese = $cheeseCount WHERE username = '$username'";
        if ($conn->query($query) === TRUE) {
            echo "Cheese count updated successfully";
        } else {
            echo "Error updating cheese count: " . $conn->error;
        }

        // Recalculate the price of cheese after purchase
        $cheesePrice = $baseCheesePrice * (1 + $multiplier * $_SESSION['cheese']);

        // Update money amount in the database
        $money = $_SESSION['money'];
        $query = "UPDATE users SET money = $money WHERE username = '$username'";
        if ($conn->query($query) === TRUE) {
            echo "Money amount updated successfully";
        } else {
            echo "Error updating money amount: " . $conn->error;
        }

        // Close database connection
        $conn->close();
    } else {
        echo "Not enough money to buy cheese!";
    }
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
    <h1>Velkommen, <?php echo $_SESSION['username']; ?>!</h1>
    <p>Penger: <?php echo number_format($_SESSION['money'], 0, '', ''); ?></p>
    <p>Ost: <?php echo $_SESSION['cheese']; ?></p>
    <form method="post">
        <button type="submit" name="click">Klikk meg!</button>
        <?php
            // Recalculate the price of cheese after each purchase
            $baseCheesePrice = 5; // Base price of cheese
            $multiplier = 0.1; // Multiplier for cheese price
            $cheesePrice = $baseCheesePrice * (1 + $multiplier * $_SESSION['cheese']); // Calculate actual price of cheese
        ?>
        <button type="submit" name="buy_cheese">Kjøp ost (<?php echo number_format($cheesePrice, 0, '', ''); ?> penger)</button>
    </form>
    <a href="logout.php">Logout</a>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            color: #666;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>

    <div class="manual">
        Klikk på "klikk" knappen for å få penger, når du har nok penger kan du kjøpe ost
        den vil gi deg 0.1 mer en det du har.(starter på 1)
        <style>
         .manual {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
            margin: 20px 20px 0 auto; /* Adjusted margin */
            text-align: justify;
            position: relative;
        }
        .manual:before {
            content: '?';
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            width: 20px;
            height: 20px;
            background-color: #007bff;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border-radius: 50%;
            line-height: 20px;
            cursor: pointer;
        }
        .manual:hover:before {
            content: attr(title);
        }

        </style>
    </div>
</body>
</html>

