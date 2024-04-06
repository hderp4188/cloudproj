<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : '';

if (!empty($username)) {
    $sql = "SELECT chatroom_id FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['chatroom_id' => $row['chatroom_id']]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} else {
    echo json_encode(['error' => 'Username not provided']);
}

$conn->close();
?>
