<?php
include_once("/var/www/inc/dbinfo.inc");

// Redirect the user if they're already in the database (Optional Step)
// This part requires a session or cookie to identify returning users.

?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter Username</title>
</head>
<body>
    <form action="welcome.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Enter</button>
    </form>
</body>
</html>

