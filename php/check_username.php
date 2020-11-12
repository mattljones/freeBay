<?php

// Checks username is unique across both buyers and sellers
function check_unique($username) {
    require_once('../private/database_credentials.php');
    $connection = mysqli_connect(host, username, password, database);
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