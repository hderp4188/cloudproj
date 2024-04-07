<?php
include_once("/var/www/inc/dbinfo.inc");

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : '';
$chatroom_id = isset($_GET['chatroom_id']) ? $conn->real_escape_string($_GET['chatroom_id']) : '';

$partnerUsername = "Waiting for someone to join..."; // Default message

if (!empty($username) && !empty($chatroom_id)) {
    $sql = "SELECT username FROM users WHERE chatroom_id = '$chatroom_id' AND username != '$username' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $partnerUsername = $row['username']; // Get the chat partner's username
    }
}

echo $partnerUsername;

$conn->close();
?>
