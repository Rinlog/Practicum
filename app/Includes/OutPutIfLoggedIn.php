<?php
//used for outputing session variables
function OutPutIfLoggedIn($output){
    if (!empty($_SESSION[$output])) { 
        echo $_SESSION[$output];
    }
}
?>