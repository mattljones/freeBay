<?php 

// Contains functions for validating user-inputted form data and preventing SQL injection attacks
require_once('login_register_form_validation.php'); 

require_once('../private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

// Checks whether username is unique across both buyers and sellers
function check_unique($conn, $username) {
    if (!$conn) {
        echo 'failure';
    }
    else {
        $query = "SELECT username_check('$username')";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            echo 'failure';
            mysqli_close($conn);
        }
        else {
            $count = mysqli_fetch_array($result)[0];
            if ($count == 'unique') {
                echo 'unique';
                mysqli_close($conn);
            }
            else {
                echo 'not_unique';
                mysqli_close($conn);
            }
        }
    }
}

check_unique($connection, validate_username($connection, $_POST["username"]));

?>