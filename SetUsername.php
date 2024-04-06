<?php 
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
    <!-- ========== Header ========== -->
    <head>
        <title>Real Time Web Chatting</title>
        <!-- Styles -->
        <link 
            rel = "stylesheet" 
            href = "styles_set_username.css" 
        />
    </head>
    <!-- ========== End Header ========== -->

    <!-- Javascript Stuff -->
    <script type="text/javascript">
        
        const nouns =   <?php echo '["' . implode('", "', $nouns) . '"]' ?>;
        const adjs =    <?php echo '["' . implode('", "', $adjs) . '"]' ?>;

        function randomize_username()
        {
            var input = document.getElementById("input_username");
            
            var num1 = Math.floor(Math.random() * nouns.length);
            var num2 = Math.floor(Math.random() * adjs.length);
            var str = adjs[num1] + nouns[num2] + (num1 * num2);

            input.value = str;
        }
    </script>

    <!-- ========== Body ========== -->
    <body style = "background-color: #33475b">  
        <div class = "container">

            <!-- Input Username Text -->
            <div class = "text-username">
                <p>Please Input A Username!</p>
            </div>

            <!-- Centralize Username Input Stuff -->
            <div class = "center_inputs"> 
            
                <!-- Input Username -->
                <div class = "text-small">
                    <p>Username</p>
                </div>
                <form action = "ChatRoom_New.php">
                    <input
                        type = "text" 
                        class = "input-username"
                        placeholder = "Input Username"
                        name = "username"
                        required = "required"
                        id = "input_username"
                    />

                    <!-- Buttonsss -->
                    <div class = "buttons-container">
                        <!-- Random Name Button -->
                        <button 
                            type = "button" 
                            class = "randomize-username-btn" 
                            onclick = "randomize_username()">
                            <div class = "btn-text"> Randomize Username </div>
                        </button>

                        <!-- Submit Button -->
                        <button 
                            type = "submit" 
                            class = "submit-username-btn" 
                            onclick = "enter_username()">
                            <div class = "btn-text"> Submit </div>
                        </button>
                </div>
                <!-- End Buttonsss -->
                </form>
            </div>
            <!-- End Centralize Username Input Stuff -->

        </div>

    </body>
</html>