<?php
$server = "127.0.0.1";
$username = "root";
$password = "";
$database = "hostel_db";

// Try to connect with utf8mb4 character set
$conn = mysqli_connect($server, $username, $password, $database);
if ($conn) {
    // Set character set for robust international character support
    mysqli_set_charset($conn, 'utf8mb4');
} else {
    die("Error: ". mysqli_connect_error());
}
?>
