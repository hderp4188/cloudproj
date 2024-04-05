<?php
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming chatroom_id and username are passed via GET or session
$chatroomId = $conn->real_escape_string($_GET['chatroom_id']);
$username = $conn->real_escape_string($_GET['username']); // Or fetch from session

// Step 2: Delete Chatroom Messages
$sqlDeleteMessages = "DELETE FROM chat_messages WHERE chatroom_id = '$chatroomId'";
if (!$conn->query($sqlDeleteMessages)) {
    echo "Error deleting messages: " . $conn->error;
}

// Step 3: Optionally Re-insert the User into the Available Pool
// This step depends on your application's logic. If you decide to re-insert the user into
// the available users' pool for matching, ensure you have the mechanism in place for it.
// For simplicity, this example will skip re-inserting the user.

$conn->close();

// Redirect or notify the user after successful exit
echo "You have exited the chatroom. Thank you for chatting.";
// Or redirect to another page, like the welcome page or the chat lobby
// header("Location: welcome.php");
// exit;
?>
