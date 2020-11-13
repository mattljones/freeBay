<?php 

// Functions for validating user-inputted form data and preventing SQL injection attacks

function validate_user_type($conn, $input) {
    if (preg_match('/^(buyer|seller)$/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_username($conn, $input) {
    if (preg_match('/.{6,20}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_email($conn, $input) {
    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_password($conn, $input) {
    if (preg_match('/.{6,}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}   

function validate_password_plus_hash($conn, $input) {
    if (preg_match('/.{6,}/', $input)) {
        return password_hash($input, PASSWORD_DEFAULT);
    }
    else {
        return false;
    }
}   

function validate_names_addresses($conn, $input) {
    if (preg_match('/.{1,35}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}   

function validate_telephone($conn, $input) {
    if (preg_match('/^\+{1}\d{8,15}/', $input)) {
        return substr(mysqli_real_escape_string($conn, $input), 1);
    }
    else {
        return false;
    }
} 

?>