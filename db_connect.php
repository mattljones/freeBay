<?php
$servername = "localhost";
$username = "admin";
$password = "adminpassword";
$dbname = "Freebay";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
?>