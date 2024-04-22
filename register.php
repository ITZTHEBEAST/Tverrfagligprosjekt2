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

// Check if form is submitted
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
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <h2>Register</h2>
    <?php
    if (isset($error)) {
        echo '<p style="color: red;">' . $error . '</p>';
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password"  required>
            <!-- Font Awesome icon for password requirements -->
            <i class="fa fa-info-circle" aria-hidden="true" onclick="showPasswordRequirements()" style="cursor: pointer;"></i>
            <!-- Span to display password requirements -->
            <span id="passwordRequirements" style="display: none;">Password must be at least 8 characters long and contain at least one uppercase letter.</span>
        </div>
        <div>
            <button type="submit">Register</button>
        </div>
    </form>
    <script>
        function showPasswordRequirements() {
            var passwordRequirements = document.getElementById("passwordRequirements");
            if (passwordRequirements.style.display === "none") {
                passwordRequirements.style.display = "inline";
            } else {
                passwordRequirements.style.display = "none";
            }
        }
    </script>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
