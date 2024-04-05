<?php
// Include database configuration file
include_once("/var/www/inc/dbinfo.inc");
// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check for a POST request with 'username'
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"])) {
    $username = $conn->real_escape_string($_POST["username"]);

    // Check if the username already exists
    $checkSql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        echo "duplicate"; // Username exists
    } else {
        echo "unique"; // Username doesn't exist
    }
    // Close statement and connection

    $conn->close();
}
?>

