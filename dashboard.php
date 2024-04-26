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
        function redirectOnInactivity() {
            setTimeout(function() {
                // Show popup message
                alert("Tiden gikk ut");
                // Redirect to login page
                window.location.href = 'login.php?timeout=true';
            }, 10000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            redirectOnInactivity();
        });

    
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

    <p>Velkommen til din side!.</p>

    
    <form action="logout.php" method="post">
        <button type="submit">Logg ut</button>
        
    </form>
</body>
</html>
