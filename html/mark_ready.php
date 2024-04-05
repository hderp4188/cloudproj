<?php
include_once("/var/www/inc/dbinfo.inc"); // Your DB connection info
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_POST['username']);

// Update user as ready to chat
$sql = "UPDATE users SET ready_to_chat = TRUE WHERE username = '$username'";
if ($conn->query($sql) === TRUE) {
    echo "Ready";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
