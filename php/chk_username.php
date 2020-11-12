<?php

function check_unique($username) {
    $connection = mysqli_connect('localhost', 'admin', 'adminpassword', 'Freebay');
    $query = "SELECT username_check('$username')";
    $result = mysqli_query($connection, $query);
    $count = mysqli_fetch_array($result)[0];
    if ($count == 'unique') {
        echo 'unique';
    }
    else {
        echo 'not_unique';
    }
}

check_unique($_POST["username"]);

?>