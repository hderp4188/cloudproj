<?php
include_once("/var/www/inc/dbinfo.inc");

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) AS userCount FROM users WHERE ready_to_chat = TRUE";
$result = $conn->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    echo $row['userCount'];
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
