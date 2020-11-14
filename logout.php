<?php

session_start();

unset($_SESSION['logged_in']);
unset($_SESSION['username']);
unset($_SESSION['account_type']);
unset($_SESSION['userID']);
setcookie(session_name(), "", time() - 360);

session_destroy();

// Refresh the page
header("Location: index.php");

?>