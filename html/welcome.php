<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $conn->real_escape_string($_POST['username'] ?? '');

// Attempt to find an existing chatroom or create a new one
$chatroom_id = findOrCreateChatroom($conn, $username);

function findOrCreateChatroom($conn, $username) {
    // Check for an available chatroom (not full and recent)
    $sql = "SELECT chatroom_id FROM chat_messages GROUP BY chatroom_id HAVING COUNT(DISTINCT sender) < 2";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['chatroom_id']; // Join an existing chatroom
    } else {
        // Create a new chatroom ID
        $newChatroomId = uniqid('chatroom_');
        return $newChatroomId;
    }
}

// HTML and script for the chat functionality will go here
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <script>
	// Poll for new messages every 2 seconds
	setInterval(pollMessages, 2000);

	async function pollMessages() {
    		const chatroom_id = "<?php echo $chatroom_id; ?>";
    		const response = await fetch('poll_messages.php?chatroom_id=' + chatroom_id);
    		const messages = await response.json(); // Assuming the response is JSON
    		displayMessages(messages);
	}

	function displayMessages(messages) {
    	const chatWindow = document.getElementById('chatWindow');
    	chatWindow.innerHTML = ''; // Clear current messages
    	messages.forEach(message => {
        	const messageDiv = document.createElement('div');
        	messageDiv.textContent = `${message.sender}: ${message.message}`;
        	chatWindow.appendChild(messageDiv);
    		});
	}

	async function sendMessage() {
    	const chatroom_id = "<?php echo $chatroom_id; ?>";
    	const message = document.getElementById('messageInput').value;
    	const formData = new FormData();
    	formData.append('chatroom_id', chatroom_id);
    	formData.append('message', message);
    	formData.append('sender', "<?php echo $username; ?>");
    
    	await fetch('send_message.php', {
        	method: 'POST',
        	body: formData,
    	});

    	document.getElementById('messageInput').value = ''; // Clear input
    	pollMessages(); // Immediately poll for the latest messages
	}
</script>

</head>
<body>
    <div id="chatWindow">
        <!-- Messages will be displayed here -->
    </div>
    <input type="text" id="messageInput" placeholder="Type your message here...">
    <button onclick="sendMessage()">Send</button>
    <button onclick="window.location='delete_user.php?username=<?php echo $username; ?>';">Back</button>
</body>
</html>
