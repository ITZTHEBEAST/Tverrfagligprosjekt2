<?php
session_start();

// Database connection details
$servername = "172.20.128.65";
$username = "root";
$password = "Skole123";
$dbname = "login";

// Function to check if a username already exists in the database
function usernameExists($conn, $username) {
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Function to validate password criteria
function validatePassword($password) {
    return strlen($password) <= 8 && preg_match('/[A-Z]/', $password);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate username uniqueness
    if (usernameExists($conn, $username)) {
        $error = "Username already exists";
    } elseif (!validatePassword($password)) {
        $error = "Password must be maximum 8 characters long and contain at least one uppercase letter";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        if ($conn->query($insert_query) === TRUE) {
            // Registration successful, redirect to login page
            header("Location: login.php");
            exit();
        } else {
            $error = "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }

    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"],
        button {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
        .info {
            cursor: help; /* Change cursor to indicate it's clickable */
            position: relative;
        }
        .info:before {
            content: '?'; /* Display '?' as the icon */
            color: blue; /* Color of the icon */
            font-weight: bold;
            margin-left: 5px; /* Adjust the space between the icon and text */
        }
        .info:hover:before {
            content: attr(title); /* Show the title text as tooltip */
            position: absolute;
            background-color: rgba(0, 0, 0, 0.8); /* Tooltip background color */
            color: white; /* Tooltip text color */
            padding: 5px;
            border-radius: 5px;
            white-space: nowrap; /* Prevent text wrapping */
            z-index: 1; /* Ensure tooltip appears above other elements */
        }
    </style>
</head>
<body>
<div class="container">
        <h2>Register</h2>
        <?php if (isset($error)) : ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <div>
                <label for="username">Brukernavn:</label>
                <input type="text" name="username" required>
            </div>
            <div>
                <label for="password">Passord: <span class="info" title="Passordet må bestå av maksimalt 8 tegn og inneholde minst én stor bokstav"></span></label>
                <input type="password" name="password" required>
            </div>
            <div>
                <button type="submit">Registrer</button>
            </div>
        </form>
        <p class="login-link">Ingen bruker? <a href="login.php">Login her</a></p>
    </div>
</body>
</html>
