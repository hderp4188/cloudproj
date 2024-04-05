<?php
include_once("/var/www/inc/dbinfo.inc"); // Your DB connection info
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_POST['username']);

// Attempt to find a match
$sqlFindMatch = "SELECT username FROM users WHERE username != '$username' AND ready_to_chat = TRUE LIMIT 1";
$matchResult = $conn->query($sqlFindMatch);

if ($matchResult->num_rows > 0) {
    $matchedUser = $matchResult->fetch_assoc()['username'];
    $chatroomId = uniqid('chat_'); // Generate a unique chatroom ID

    // Update both users with the chatroom ID and mark them as not ready (since they're now chatting)
    $sqlUpdateUsers = "
        UPDATE users 
        SET chatroom_id = '$chatroomId', ready_to_chat = FALSE 
        WHERE username IN ('$username', '$matchedUser')
    ";
    if ($conn->query($sqlUpdateUsers)) {
        echo "Matched with $matchedUser. Chatroom ID: $chatroomId";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "No match found";
}

$conn->close();
?>
