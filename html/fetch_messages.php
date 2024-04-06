<?php
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$chatroom_id = 'main'; // Assuming a single chatroom

$sql = "SELECT sender, message FROM chat_messages WHERE chatroom_id = '$chatroom_id' ORDER BY created_at DESC LIMIT 20";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode($messages);
?>
