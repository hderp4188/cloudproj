<?php
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$chatroomId = $conn->real_escape_string($_GET['chatroom_id']);

$sql = "SELECT * FROM chat_messages WHERE chatroom_id = '$chatroomId' ORDER BY timestamp ASC";
$result = $conn->query($sql);

$messages = "";
while($row = $result->fetch_assoc()) {
    $side = ($row['sender'] === $username) ? 'right' : 'left';
    $messages .= "<div style='text-align: $side;'>{$row['message']}</div>";
}

echo $messages;
$conn->close();
?>
