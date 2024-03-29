<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Assuming you're identifying the user in some way to delete (e.g., session, cookie, or a passed username)
// For demonstration, let's assume a username is passed back for simplicity, but consider using sessions or cookies.
$username = $conn->real_escape_string($_GET['username']); // Make sure to get the username safely

$sql = "DELETE FROM users WHERE username = '$username'";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php"); // Redirect back to the first page
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>

