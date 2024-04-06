<?php
include_once("/var/www/inc/dbinfo.inc");

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_GET['username']);

// Start transaction
$conn->begin_transaction();

try {
    // Retrieve the chatroom_id for the user
    $sql = "SELECT chatroom_id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $chatroom_id = $row['chatroom_id'];

        // Delete the user
        $conn->query("DELETE FROM users WHERE username = '$username'");

        // Check if there's another user in the same chatroom
        $result = $conn->query("SELECT username FROM users WHERE chatroom_id = '$chatroom_id'");
        if ($result->num_rows == 1) {
            // If there's another user, assign them a new chatroom_id
            $newChatroomId = uniqid('chatroom_');
            $otherUser = $result->fetch_assoc()['username'];

            // Update chatroom_id for the remaining user in users table
            $conn->query("UPDATE users SET chatroom_id = '$newChatroomId' WHERE username = '$otherUser'");

            // Update chatroom_id in chat_messages table
            $conn->query("UPDATE chat_messages SET chatroom_id = '$newChatroomId' WHERE chatroom_id = '$chatroom_id'");
        } elseif ($result->num_rows == 0) {
            // If no other user is in the chatroom, delete all messages associated with the chatroom
            $conn->query("DELETE FROM chat_messages WHERE chatroom_id = '$chatroom_id'");
        }

        // Commit transaction
        $conn->commit();
    }
} catch (Exception $e) {
    // An error occurred; rollback the transaction
    $conn->rollback();
    error_log("Error: " . $e->getMessage());
    // Handle error appropriately
}

$conn->close();

// Redirect back to the main page
header("Location: index.php");
exit();
?>
