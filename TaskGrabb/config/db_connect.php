<?php

    $db = mysqli_connect('localhost', 'spencer', 'silver123', 'taskgrabb');

    //check connection
    if(!$db){
        echo 'Connection error: ' . mysqli_connect_error();
    }

?>