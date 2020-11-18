<?php 

// Functions for validating user-inputted form data and preventing SQL injection attacks



function validate_title($conn, $input) {
    if (preg_match('/.{1,80}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_descript($conn, $input) {
    if (preg_match('/.{1,4000}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}

function validate_price($conn, $input) {
    if (preg_match('/\d{1,7}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
}   

function validate_minIncrement($conn, $input) {
    if (preg_match('/\d{1,6}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
} 

function validate_date($conn, $input) {
    if (preg_match('/\d{0,12}/', $input)) {
        return mysqli_real_escape_string($conn, $input);
    }
    else {
        return false;
    }
} 



?>