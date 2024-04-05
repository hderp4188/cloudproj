
<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert username into the database
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"])) {
    $username = $conn->real_escape_string($_POST["username"]);
	// Check if the username already exists
  
 	$sql = "INSERT INTO users (username) VALUES ('$username')";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Welcome, $username! Start chatting.</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
  
}
if(!empty($username)){
// SQL query to count all users
	$sql = "SELECT COUNT(*) AS userCount FROM users";
	$result = $conn->query($sql);

	$userCount = 0;
	if ($result) {
    		$row = $result->fetch_assoc();
    		$userCount = $row['userCount'];
    		echo"<br><p>total active users, $userCount</p>";
	}

}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <button onclick="window.location='delete_user.php?username=<?php echo $username; ?>';">Back</button>
</body>
</html>

