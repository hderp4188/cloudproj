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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    function updateUserCount() {
        $.get("get_user_count.php", function(data) {
            $("#userCount").text(data + " users online");
            setTimeout(updateUserCount, 5000); // Update every 5 seconds
        });
    }
    $(document).ready(function() {
        updateUserCount(); // Initial call
        $("#startChat").click(function() {
            // Mark the user as ready to chat
            $.post("mark_ready.php", { username: "<?php echo $username; ?>" }, function(data) {
                console.log(data); // Log response
                alert("Waiting for a match...");
                checkForMatch(); // Begin checking for a match
            });
        });

        function checkForMatch() {
            $.post("check_for_match.php", { username: "<?php echo $username; ?>" }, function(data) {
                console.log(data); // Log response
                if (data.includes("Matched")) {
                    var chatroomId = data.split(": ")[2]; // Assuming the format is "Matched with USERNAME. Chatroom ID: ID"
                    window.location.href = `chatroom.php?id=${chatroomId}`; // Redirect to chatroom
                    // alert(data); // Placeholder action
                } else {
                    setTimeout(checkForMatch, 3000); // Re-check every 3 seconds
                }
            });
        }
    });
</script>

</head>
<body>
    <p>Welcome, <?php echo $username; ?>! Start chatting.</p>
    <!-- Ensure the button has the ID 'startChat' for the jQuery selector -->
    <div id="userCount">Checking users online...</div>
    <button id="startChat">Start Chatting</button>
    <!-- Other HTML content -->
    <button onclick="window.location='delete_user.php?username=<?php echo $username; ?>';">Back</button>
</body>
</html>
