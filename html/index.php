<?php
include_once("/var/www/inc/dbinfo.inc");

// Redirect the user if they're already in the database (Optional Step)
// This part requires a session or cookie to identify returning users.
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter Username</title>
<script>
        // Function to generate a random username
        function generateRandomUsername() {
            var randomUsername = "User" + Math.floor(Math.random() * 10000); // Example random username
            document.getElementById("username").value = randomUsername; // Set the random username to the input field
	
	}

//duplicate checking
	document.addEventListener('DOMContentLoaded', (event) => {
            document.getElementById('usernameForm').onsubmit = async function(e) {
                e.preventDefault(); // Prevent the default form submission
                const formData = new FormData();
                formData.append('username', document.getElementById('username').value);
		console.log("Submitting form...");
                // Fetch API to POST data and wait for the response
                const response = await fetch('check_username.php', {
                    method: 'POST',
                    body: formData,
                });
                const result = (await response.text()).trim();

                // Conditional actions based on the response
                if (result === 'duplicate') {
                    alert('Username already exists. Please choose a different username.');
		} else {	
                    // If unique, submit the form programmatically
                    this.submit();
                }
            };
        });
    </script>
</head>
<body>
    <form id = "usernameForm" action="welcome.php" method="POST" >
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
	<button type="submit" >Enter</button>
	<button type="button" id="random" onclick="generateRandomUsername()">Random</button>
    </form>
	

</body>
</html>

