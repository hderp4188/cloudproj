<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$chatroom_id = isset($_GET['chatroom_id']) ? $conn->real_escape_string($_GET['chatroom_id']) : '';

// Fetch messages for the chatroom
$sql = "SELECT sender, message, created_at FROM chat_messages WHERE chatroom_id = '$chatroom_id' ORDER BY created_at ASC";
$result = $conn->query($sql);

$messages = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($messages);

$conn->close();
?>
