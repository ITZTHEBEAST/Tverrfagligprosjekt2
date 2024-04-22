<?php
// Start the session
session_start();

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script>
        // Function to redirect to login page after inactivity
        function redirectOnInactivity() {
            setTimeout(function() {
                // Show popup message
                alert("Tiden gikk ut");
                // Redirect to login page
                window.location.href = 'login.php?timeout=true';
            }, 10000); // 10 seconds (adjust as needed)
        }

        // Start the inactivity timer
        document.addEventListener('DOMContentLoaded', function() {
            redirectOnInactivity();
        });

        // Reset the inactivity timer on user activity
        document.addEventListener('mousemove', function() {
            clearTimeout(timeoutId);
            redirectOnInactivity();
        });

        document.addEventListener('keypress', function() {
            clearTimeout(timeoutId);
            redirectOnInactivity();
        });
    </script>
</head>
<body>
    <h2>Hei, <?php echo $_SESSION['username']; ?>!</h2>
    <!-- Add your dashboard content here -->
    <p>Velkommen til din side!.</p>

    <!-- Add a logout button -->
    <form action="logout.php" method="post">
        <button type="submit">Logg ut</button>
    </form>
</body>
</html>
