<?php
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

$chatroomId = $conn->real_escape_string($_POST['chatroom_id']);
$sender = $conn->real_escape_string($_POST['sender']);
$message = $conn->real_escape_string($_POST['message']);

$sql = "INSERT INTO chat_messages (chatroom_id, sender, message) VALUES ('$chatroomId', '$sender', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "Message sent successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
