<?php 

// Contains functions for validating user-inputted form data and preventing SQL injection attacks
require_once('login_register_form_validation.php'); 

require_once('../private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

// Checks whether email is unique across both buyers and sellers
function check_unique($conn, $email) {
    if (!$conn) {
        echo 'failure';
        die();
    }
    else {
        $query = "SELECT email_check('$email')"; // using email_check() function stored in the database
        $result = mysqli_query($conn, $query);
        if (!$result) {
            echo 'failure';
            mysqli_close($conn);
            die();
        }
        else {
            $count = mysqli_fetch_array($result)[0];
            if ($count == 'unique') {
                echo 'unique';
                mysqli_close($conn);
                die();
            }
            else {
                echo 'not_unique';
                mysqli_close($conn);
                die();
            }
        }
    }
}

check_unique($connection, validate_email($connection, $_POST["email"]));

?>