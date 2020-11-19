<?php   

// Functions for validating user-inputted create auction form data and preventing SQL injection attacks

function validate_title($conn, $input) {
    if (preg_match('/.{10,80}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_descript($conn, $input) {
    if (preg_match('/.{10,4000}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_category($conn, $input) {
    if (preg_match('/.{1,35}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_price($conn, $input) {
    if (preg_match('/\d{1,9}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}   

function validate_minIncrement($conn, $input) {
    if (preg_match('/\d{1,8}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
} 

function validate_date($conn, $input) {
    if (preg_match('/.{16}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
} 

?>