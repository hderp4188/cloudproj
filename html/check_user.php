<?php
// Include database configuration file
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Check for a POST request with 'username'
if(isset($_POST['username'])) {
    
    $username = $conn->real_escape_string($_POST["username"]);
    // Prepare a SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username); // 's' specifies the variable type => 'string'

    // Execute the prepared statement
    $stmt->execute();

    // Store the result so we can check the row count
    $checkSql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($checkSql);

    if($result->num_rows > 0) {
	    // Username exi
	    //sts
	
        echo "duplicate";
    } else {
        // Username doesn't exist
	    echo "unique";
	    
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

