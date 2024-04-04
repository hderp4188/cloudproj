<!DOCTYPE html>
<html>
    <!-- ========== Header ========== -->
    <head>
        <title>Real Time Web Chatting</title>
        <!-- Styles -->
        <link 
            rel = "stylesheet" 
            href = "styles_messages.css" 
        />
        <!-- Import this CDN to use icons -->
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css"
        />
    </head>
    <!-- ========== End Header ========== -->


    <!-- ========== Body ========== -->
    <body onload = "set_chat()">
        <!-- Main container -->
        <div class="container">
            <!-- Chat Header, where the other person's username is put -->
            <div class="chat-header">
                <!-- Put Username -->
                <div class="usernameTitle">
                    <p> 
                        <?php 
                            echo "Username!!";
                        ?>
                    </p>
                </div>
            </div>
            <!-- End of the Chat Header -->

            <!-- Chat Container -->
            <div class = "chat-container">
                
                <!-- Messages Container -->
                <div class = "messages-container">                    
                    <div class="msg-page" id = "chatlog"> 

                        <!-- Messages Group -->
<?php
                        // SQL Stuff
                        $host = "localhost";
                        $user = "";
                        $password = "";
                        $db_name = ""
                        $mysqli = new mysqli($host, $user, $password, $db_name);

                        // Query the messages, select from the messages
                        $run = $mysqli->query("SELECT * FROM _____");

                        // Get username from previous
                        $username = "";
                        
                        while($row = $run->fetch_array()) :

                            // Check if this message is from local user
                            if($row['username'] != $username)
                            {   // Not local user
?>
                                <!-- Do HTML message for other-->
                                <div class="chat_left_msg">
                                    <div class="chat_left_blob">
                                        <p>
                                            <?php echo $row["message"]; ?>
                                        </p>
                                    </div>
                                </div>
<?php
                            }
                            else
                            {   // Local user
?>
                                <div class="chat_right_msg">
                                    <div class="chat_right_blob"> 
                                        <p>
                                            <?php echo $row["message"]; ?>
                                        </p>
                                    </div>
                                </div>
<?php 
                            }
                        endwhile;
?>
                        <!-- End Messages Group -->

                    </div>             
                    
                    <!-- Write Message Container -->
                    <div class = "write-message-container">
                        <!-- Input Text -->
                        <div class="input-group">
                        <input
                            type = "text"
                            class = "form-control"
                            placeholder = "Write message..."
                        />

                        <!-- Send Button -->
                        <span class="input-group-text send-icon" onclick = "send_on_click()">
                            <!-- Button icon -->
                            <i class="bi bi-send"></i>
                        </span>
                        </div>
                    </div>            
                    <!-- End Write Message Container -->

                </div>   
                <!-- End Messages Container -->

            </div>
            <!-- End Chat Container-->            
        </div>
    </body>
    <!-- ========== End Body ========== -->
</html>

<script>
    function set_chat() {
        // Find messages
        var element = document.getElementById("chatlog");
        element.scrollTop = element.scrollHeight;
    }

    function send_on_click() {
        // Set what happens when click
    }
</script>