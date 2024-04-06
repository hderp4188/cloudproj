<?php
include_once("/var/www/inc/dbinfo.inc");

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["message"]) && !empty($_POST["username"])) {
    $message = $conn->real_escape_string($_POST["message"]);
    $username = $conn->real_escape_string($_POST["username"]);
    // Retrieve chatroom_id for this user
    $sql = "SELECT chatroom_id FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $chatroom_id = $row['chatroom_id'];
        // Insert the message into the chat_messages table
        $insertSql = "INSERT INTO chat_messages (chatroom_id, sender, message) VALUES ('$chatroom_id', '$username', '$message')";
        if (!$conn->query($insertSql)) {
            echo "Error: " . $insertSql . "<br>" . $conn->error;
        }
    } else {
        echo "User not found.";
    }
}

$conn->close();
?>
