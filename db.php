<!-- db.php -->
<?php
$mysqli = new mysqli("172.20.128.65", "root", "Skole123", "login");

// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
?>
