<?php
include_once("/var/www/inc/dbinfo.inc");

// Connect to the database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = "";
$chatroom_id = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["username"])) {
    $username = $conn->real_escape_string($_POST["username"]);
    
    // Attempt to find an available chatroom or create a new one
    $chatroom_id = findOrCreateChatroom($conn);

    // Insert username into the database along with chatroom_id
    $sql = "INSERT INTO users (username, chatroom_id) VALUES ('$username', '$chatroom_id') ON DUPLICATE KEY UPDATE chatroom_id = VALUES(chatroom_id)";
    if ($conn->query($sql) !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

function findOrCreateChatroom($conn) {
    // Check for an available chatroom (with less than 2 users)
    $sql = "SELECT chatroom_id FROM users GROUP BY chatroom_id HAVING COUNT(*) < 2 LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['chatroom_id']; // Return an existing chatroom ID
    } else {
        // Create a new chatroom ID
        return uniqid('chatroom_');
    }
}


// Display chatroom info and messages
echo "Chatroom ID: $chatroom_id <br>";


$userCount = 0;
if (!empty($username)) {
    // SQL query to count all users
    $sql = "SELECT COUNT(*) AS userCount FROM users";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $userCount = $row['userCount'];
    }
}



?>
<!DOCTYPE html>
<html>
<head>
    <title>Real Time Web Chatting</title>
    <link rel="stylesheet" href="styles_messages.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css"/>
    <script>
        var username = "<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"; // Define username globally

    function set_chat() {
        var element = document.getElementById("chatlog");
        element.scrollTop = element.scrollHeight;
    }

    function AppendText(word, shouldCensor) {
        if (shouldCensor)
            return "*** ";
        return word + " ";
    }

    // This function triggers the filtering process
    function FilterText() {
        const msg = document.getElementById('messageInput').value;
        var words = msg.split(/\s+/); // Split by one or more whitespace
        var filteredText = FilterProfanity(words); // First, filter profanity

        // document.getElementById('output').textContent = `${filteredText}`;
        return filteredText;
    }

    function FilterProfanity(words) {
        const reEscape = s => s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');       
        const badWords = [
        'fuck', 'bitch', 'fk', 'bij', 'cb', 'cheebai', 'jibai', 'knn', 'useless', 'shit', 'lanjiao', 'sohai', 
        'pukimak', 'anjing', 'babi', 'motherfucker', 'motherfker', 'noob', 'n00b', 'stupid', 'stoopid', 'st00pid',
        'dumb', 'asshole', 'ass', '@ss', 'b!tch', 'b!j', '@sshole', '@ssh0le', 'lj', 'dumbfk', 'lampa', 'bullshit',
        'bullsh!t', 'bloody', 'hell', 'bodoh', 'bod0h', 'b0doh', 'b0d0h', 'penis', 'pussy', 'vagina', 'cock', 'chicken',
        'no balls', 'nigger', 'nigg', 'nigga', 'nigg@', 'stfu', 'shut up', 'faggot', 'f@ggot', 'f@gg0t', 'fagg0t', 'cunt',
        ];
        const badWordsRE = new RegExp(badWords.map(reEscape).join('|'), 'i'); // 'i' for case-insensitive

        var filteredText = "";
        for (const w of words) {
            filteredText += AppendText(w, badWordsRE.test(w)); // Check each word against the bad words list
        }
        return filteredText.trim(); // Trim trailing whitespace
    }



    function send_on_click() {
        // var message = document.getElementById("messageInput").value;
        var message = FilterText();
        var username = "<?php echo $username; ?>"; // Ensure you have sanitized this properly

        if (message.trim() === '') return; // Don't send empty messages

        fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message=' + encodeURIComponent(message) + '&username=' + encodeURIComponent(username)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data); // You might want to handle this differently
            document.getElementById("messageInput").value = ''; // Clear input field
            loadMessages(); // Reload messages to display the new one
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }

    // Load messages for the chatroom
    function loadMessages() {
        var chatroom_id = "<?php echo $chatroom_id; ?>";
        fetch('load_messages.php?chatroom_id=' + encodeURIComponent(chatroom_id))
        .then(response => response.json())
        .then(messages => {
            var chatlog = document.getElementById("chatlog");
            chatlog.innerHTML = ''; // Clear existing messages
            messages.forEach(message => {
                var div = document.createElement('div');
                div.className = message.sender === username ? 'chat_right_msg' : 'chat_left_msg';
                var blob = document.createElement('div');
                blob.className = message.sender === username ? 'chat_right_blob' : 'chat_left_blob';
                var p = document.createElement('p');
                p.textContent = message.message;
                blob.appendChild(p);
                div.appendChild(blob);
                chatlog.appendChild(div);
            });
            chatlog.scrollTop = chatlog.scrollHeight; // Scroll to the bottom
        })
        .catch(error => console.error('Error loading messages:', error));
    }

    function checkForChatroomIdUpdate() {
        fetch('get_chatroom_id.php?username=' + encodeURIComponent(username))
        .then(response => response.json())
        .then(data => {
            if (data.chatroom_id && data.chatroom_id !== "<?php echo $chatroom_id; ?>") {
                // If the chatroom_id has changed, update the page accordingly
                console.log("Chatroom ID changed to: " + data.chatroom_id);
                window.location.reload(); // Reload the page or adjust as necessary
            }
        })
        .catch(error => console.error('Error checking chatroom ID:', error));
    }    

    function updateChatPartnerUsername() {
        var username = "<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>";
        var chatroom_id = "<?php echo $chatroom_id; ?>";

        fetch('get_chat_partner.php?username=' + encodeURIComponent(username) + '&chatroom_id=' + encodeURIComponent(chatroom_id))
        .then(response => response.text())
        .then(data => {
            document.getElementById("chatPartnerUsername").innerText = "You are talking to: " + data;
        })
        .catch(error => console.error('Error updating chat partner username:', error));
    }

    document.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent the default action to stop the form from submitting
            send_on_click(); // Trigger the send button functionality
        }
    });


    // document.addEventListener('DOMContentLoaded', loadMessages); // Load messages when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        loadMessages(); // Load messages when the page is fully loaded
        setInterval(loadMessages, 1000); // Refresh messages every 5 seconds
        setInterval(checkForChatroomIdUpdate, 5000);
        setInterval(updateChatPartnerUsername, 1000);
    });

    </script>
</head>
<body onload="set_chat()">
    <div class="container">
        <div class="chat-header">
            <div class="usernameTitle">
                <p><?php echo "Welcome, $username! Start chatting."; ?></p>
                <p id="chatPartnerUsername">You are talking to: Waiting for someone to join...<</p>

            </div>
        </div>
        <div class="chat-container">
            <div class="messages-container">                    
                <div class="msg-page" id="chatlog">
                     <!-- loadMessages() -->
                    <!-- // Assuming chat_messages table exists and is used
                    // $sql = "SELECT sender, message FROM chat_messages WHERE chatroom_id = 'some_chatroom_id' ORDER BY created_at ASC";
                    // $result = $conn->query($sql);
                    // if ($result) {
                    //     while ($row = $result->fetch_assoc()) {
                    //         if ($row['sender'] != $username) {
                    //             // Message from other user
                    //             echo "<div class='chat_left_msg'><div class='chat_left_blob'><p>".htmlspecialchars($row['message'])."</p></div></div>";
                    //         } else {
                    //             // Message from this user
                    //             echo "<div class='chat_right_msg'><div class='chat_right_blob'><p>".htmlspecialchars($row['message'])."</p></div></div>";
                    //         }
                    //     }
                    // } -->

                </div>             
                <div class="write-message-container">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Write message..." id="messageInput"/>
                        <span class="input-group-text send-icon" onclick="send_on_click()">
                            <i class="bi bi-send"></i>
                        </span>
                    </div>
                </div>            
            </div>   
        </div>
        <!-- <button onclick="window.location='delete_user.php?username=<?php echo htmlspecialchars($username); ?>';">Back</button> -->
    </div>
    <div class="back-button-container">
    <button onclick="window.location='delete_user.php?username=<?php echo htmlspecialchars($username); ?>';">Back</button>
</div>

</body>
</html>
<?php $conn->close(); ?>



