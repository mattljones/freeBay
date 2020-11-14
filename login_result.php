<?php include_once("header.php")?> 

<div class="container">

<?php

require_once('private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

if (!$connection) {
    echo '<h2 class="my-3">Login unsuccessful :(</h2>';
    echo '<p>There was an error connecting to the database. Please try again.</p>';
    die();
}

// Contains functions for validating user-inputted form data and preventing SQL injection attacks
require_once('php/login_register_form_validation.php'); 

$username = validate_username($connection, $_POST["user_name"]);
$password = $_POST["password"]; // Don't need to validate since not used in any queries

// STEP 1 - Check if login credentials are correct and store in variable creds_OK
$query_creds = "SELECT pass FROM Buyers WHERE username = '".$username."' UNION ALL SELECT pass FROM Sellers WHERE username = '".$username."'";
$result_creds = mysqli_query($connection, $query_creds);
if (!$result_creds) {
    echo '<h2 class="my-3">Login unsuccessful :(</h2>';
    echo '<p>There was an error connecting to the database. Please try again.</p>';
    mysqli_close($connection);
    die();
}
elseif (password_verify($password, mysqli_fetch_array($result_creds)[0])) {
    $creds_OK = true;
}
else {
    $creds_OK = false;
}

// STEP 2 - Collect user ID
$query_id = "SELECT buyerID AS userID FROM (SELECT buyerID, username FROM Buyers UNION ALL SELECT sellerID, username FROM Sellers) AS Combined WHERE username = '".$username."'";
$result_id = mysqli_query($connection, $query_id);
if (!$result_id) {
    echo '<h2 class="my-3">Login unsuccessful :(</h2>';
    echo '<p>There was an error connecting to the database. Please try again.</p>';
    mysqli_close($connection);
    die();
}
else {
    $userID = mysqli_fetch_array($result_id)[0];
}

// STEP 3 - Collect user type
$query_type_buyer = "SELECT 1 FROM Buyers WHERE username = '".$username."'";
$result_buyer = mysqli_query($connection, $query_type_buyer);
$query_type_seller = "SELECT 1 FROM Sellers WHERE username = '".$username."'";
$result_seller = mysqli_query($connection, $query_type_seller);
if (!$result_buyer || !$result_seller) {
    echo '<h2 class="my-3">Login unsuccessful :(</h2>';
    echo '<p>There was an error connecting to the database. Please try again.</p>';
    mysqli_close($connection);
    die();
}
elseif (mysqli_fetch_array($result_buyer)[0] == '1') {
    $type = 'buyer';
}
else {
    $type = 'seller';
}

// STEP 4 - Create session if steps 1-3 successful (script will have been exited automatically) and credentials are valid
if ($creds_OK == true) {
    header("refresh:5; url=browse.php");
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $_POST['user_name'];
    $_SESSION['account_type'] = $type;
    $_SESSION['userID'] = $userID;
    echo '<h2 class="my-3">Login successful!</h2>';
    echo '<p><i>You will be redirected shortly.</i></p>';
    die();
}
else {
    echo '<h2 class="my-3">Login unsuccessful :(</h2>';
    echo mysqli_fetch_array($result_creds)[0];
    echo '<p>That username-password combination is invalid. Please try again.</p>';
    die();
}

?>

</div>

<?php include_once("footer.php")?>