<?php
$conn = mysqli_connect('localhost', 'root', '', 'AIRPORT', 3307);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>