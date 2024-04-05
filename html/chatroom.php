<?php
include_once("/var/www/inc/dbinfo.inc");
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming chatroom_id is passed as a query parameter
$chatroomId = $conn->real_escape_string($_GET['id']);
$username = ""; // You should determine the current user's username, possibly from session

?>
<!DOCTYPE html>
<html>
<head>
    <title>Chatroom</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php',
                type: 'get',
                data: { 'chatroom_id': '<?php echo $chatroomId; ?>' },
                success: function(response) {
                    $('#messages').html(response);
                }
            });
        }
        
        fetchMessages(); // Fetch messages on page load
        
        $('#sendMessage').click(function(e) {
            e.preventDefault();
            $.ajax({
                url: 'send_message.php',
                type: 'post',
                data: {
                    'chatroom_id': '<?php echo $chatroomId; ?>',
                    'sender': '<?php echo $username; ?>',
                    'message': $('#message').val()
                },
                success: function(response) {
                    $('#message').val(''); // Clear input field
                    fetchMessages(); // Fetch messages again to display the new message
                }
            });
        });

        // Optionally, fetch new messages periodically
        setInterval(fetchMessages, 3000); // Fetch messages every 3 seconds
    });
    </script>
</head>
<body>
    <div id="messages"></div>
    <form>
        <textarea id="message"></textarea>
        <button id="sendMessage">Send</button>
    </form>
</body>
</html>
