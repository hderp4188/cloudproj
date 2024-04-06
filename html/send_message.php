<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$chatroom_id = isset($_POST['chatroom_id']) ? $conn->real_escape_string($_POST['chatroom_id']) : '';
$sender = isset($_POST['sender']) ? $conn->real_escape_string($_POST['sender']) : '';
$message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : '';

// Insert the new message into the database
if (!empty($chatroom_id) && !empty($sender) && !empty($message)) {
    $sql = "INSERT INTO chat_messages (chatroom_id, sender, message) VALUES ('$chatroom_id', '$sender', '$message')";
    if ($conn->query($sql) === TRUE) {
        echo "Message sent successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Missing information";
}

$conn->close();
?>
