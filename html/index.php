<?php
include_once("/var/www/inc/dbinfo.inc");

// PHP functions for generating random usernames
function getStringFromFile($file_name)
{
    $file_ = fopen($file_name, "r");
    $arr = array();
    $count = 0;
    while(!feof($file_))
    {
        $str = ucfirst(fgets($file_));
        $arr[$count++] = str_replace("\n", "", $str);
    }

    return $arr;
}

$nouns = getStringFromFile("nouns.txt");
$adjs = getStringFromFile("adjectives.txt");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enter Username</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin: auto;
            margin-top: 10vh;
            width: 35vw;
            height: 30vh;
            min-width: 530px;
            min-height: 250px;
            padding: 1rem;
            padding-top: 0.5rem;
            background-color: #3d3d3d;
            letter-spacing: 0.75px;
        }
        .text-username, .text-small {
            margin: auto;
            margin-top: 0;
            font-size: 1.2em;
            font-weight: bold;
            color: #fff;
            text-align: center;
        }
        .center_inputs {
            padding: 1rem;
            margin: auto;
            width: 60%;
            min-width: 250px;
            padding-top: 0.1vh;
        }
        .input-username {
            width: 100%;
            height: 15%;
            font-size: 1em;
            text-align: left;
            line-height: 0;
        }
        .buttons-container {
            width: 100%;
            padding: 5%;
            margin: auto;
        }
        .submit-username-btn, .randomize-username-btn {
            width: 30%;
            min-width: 150px;
            height: 15%;
            min-height: 40px;
            margin: auto;
            margin-top: 1vh;
	    background-image: linear-gradient(45deg, #ff0077, #0f1669);
        }
        .randomize-username-btn {
            background-image: linear-gradient(45deg, #ff0077, #0f1669);
        }
        .btn-text {
            vertical-align: middle;
            text-align: center;
            color: #fff;
        }
    </style>
    <script>
        const nouns = <?php echo '["' . implode('", "', $nouns) . '"]' ?>;
        const adjs = <?php echo '["' . implode('", "', $adjs) . '"]' ?>;
        
        function generateRandomUsername() {
            var num1 = Math.floor(Math.random() * nouns.length);
            var num2 = Math.floor(Math.random() * adjs.length);
            var randomUsername = adjs[num1] + nouns[num2] + Math.floor(Math.random() * 999);
            document.getElementById("username").value = randomUsername;
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            document.getElementById('usernameForm').onsubmit = async function(e) {
                e.preventDefault();
                const formData = new FormData();
                formData.append('username', document.getElementById('username').value);
                console.log("Submitting form...");
                const response = await fetch('check_username.php', {
                    method: 'POST',
                    body: formData,
                });
                const result = (await response.text()).trim();

                if (result === 'duplicate') {
                    alert('Username already exists. Please choose a different username.');
                } else {
                    this.submit();
                }
            };
        });
    </script>
</head>

<body>
    <div class="container">
        <div class="text-username">
            <p>Please Input A Username!</p>
        </div>
        <div class="center_inputs">
            <form id="usernameForm" action="welcome.php" method="POST">
                <div class="text-small">
                    <p>Username:</p>
                </div>
                <input type="text" class="input-username" id="username" name="username" required placeholder="Input Username">
                <div class="buttons-container">
                    <button type="button" class="randomize-username-btn" onclick="generateRandomUsername()">
                        <div class="btn-text">Randomize Username</div>
                    </button>
                    <button type="submit" class="submit-username-btn">
                        <div class="btn-text">Enter</div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
