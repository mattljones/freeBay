<?php include_once("header.php")?>  

<div class="container">

<!--
TODO: Extract $_POST variables, check they're OK, and attempt to create
an account. Notify user of success/failure and redirect/give navigation 
options.
-->

<?php
$type = $_POST["accountType"];
$username = $_POST["username"];
$email = $_POST["emailReg"];
$firstName = $_POST["firstName"];
$familyName = $_POST["familyName"];
$password = $_POST["passwordReg"];
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$connection = mysqli_connect('localhost', 'admin', 'adminpassword', 'Freebay');

if ($type == "buyer") {
    // Query B.1 - inserting required fields
    $query_req = "INSERT INTO Buyers (username, email, pass, firstName, familyName) VALUES ('$username', '$email', '$passwordHash', '$firstName', '$familyName')";
    $result_req = mysqli_query($connection, $query_req);
    $success_chk_req = mysqli_affected_rows($connection);
    // Setting as default values
    $success_chk_add = 1;
    $success_chk_tel = 1;
    if ($success_chk_req == 1 && isset($_POST["chooseInputAddress"])) {
        $line1 = $_POST["line1"];
        $city = $_POST["city"];
        $postcode = $_POST["postcode"];
        $country = $_POST["country"];
        // Query B.2.1 - retrieving countryID separately from main query for code readability
        $countryID_query = "SELECT countryID FROM Countries WHERE countryName = '".$country."'";
        $countryID_result = mysqli_query($connection, $countryID_query);
        $countryID = mysqli_fetch_array($countryID_result)[0];
        // Query B.2.2 - retrieving buyerID separately from main query for code readability
        $buyerID_query = "SELECT buyerID FROM Buyers WHERE username = '".$username."'";
        $buyerID_result = mysqli_query($connection, $buyerID_query);
        $buyerID = mysqli_fetch_array($buyerID_result)[0];
        // Query B.3 - addresses query nesting the above two queries
        $query_add = "INSERT INTO BuyerAddresses (line1, city, postcode, countryID, buyerID) VALUES ('$line1', '$city', '$postcode', '$countryID', '$buyerID')";
        $result_add = mysqli_query($connection, $query_add);
        $success_chk_add = mysqli_affected_rows($connection);
    }
    if ($success_chk_req == 1 && $success_chk_add == 1 && isset($_POST["chooseInputTelNo"])) {
        $telNo = substr($_POST["telephoneNumber"], 1);
        // Query S.4 - telNo query incl. buyerID query
        $query_tel = "INSERT INTO BuyerTels (telNo, buyerID) VALUES ('$telNo', '$buyerID')";
        $result_tel = mysqli_query($connection, $query_tel);
        $success_chk_tel = mysqli_affected_rows($connection);
    }
    if ($success_chk_req == 1 && $success_chk_add == 1 && $success_chk_tel == 1) {
        echo '<h2 class="my-3">Registration successful!</h2>';
        echo '<p>Welcome to freeBay.</p>';
    }
    else {
        echo '<h2 class="my-3">Registration unsuccessful :(</h2>';
        echo '<p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
    }
    mysqli_close($connection);
}

else {
    // Query S.1 - inserting required fields
    $query_req = "INSERT INTO Sellers (username, email, pass, firstName, familyName) VALUES ('$username', '$email', '$passwordHash', '$firstName', '$familyName')";
    $result_req = mysqli_query($connection, $query_req);
    $success_chk_req = mysqli_affected_rows($connection);
    // Setting as default values
    $success_chk_add = 1;
    $success_chk_tel = 1;
    if ($success_chk_req == 1 && isset($_POST["chooseInputAddress"])) {
        $line1 = $_POST["line1"];
        $city = $_POST["city"];
        $postcode = $_POST["postcode"];
        $country = $_POST["country"];
        // Query S.2.1 - retrieving countryID separately from main query for code readability
        $countryID_query = "SELECT countryID FROM Countries WHERE countryName = '".$country."'";
        $countryID_result = mysqli_query($connection, $countryID_query);
        $countryID = mysqli_fetch_array($countryID_result)[0];
        // Query S.2.2 - retrieving sellerID separately from main query for code readability
        $sellerID_query = "SELECT sellerID FROM Sellers WHERE username = '".$username."'";
        $sellerID_result = mysqli_query($connection, $sellerID_query);
        $sellerID = mysqli_fetch_array($sellerID_result)[0];
        // Query S.3 - addresses query nesting the above two queries
        $query_add = "INSERT INTO SellerAddresses (line1, city, postcode, countryID, sellerID) VALUES ('$line1', '$city', '$postcode', '$countryID', '$sellerID')";
        $result_add = mysqli_query($connection, $query_add);
        $success_chk_add = mysqli_affected_rows($connection);
    }
    if ($success_chk_req == 1 && $success_chk_add == 1 && isset($_POST["chooseInputTelNo"])) {
        $telNo = substr($_POST["telephoneNumber"], 1);
        // Query S.4 - telNo query incl. sellerID query
        $query_tel = "INSERT INTO SellerTels (telNo, sellerID) VALUES ('$telNo', '$sellerID')";
        $result_tel = mysqli_query($connection, $query_tel);
        $success_chk_tel = mysqli_affected_rows($connection);
    }
    if ($success_chk_req == 1 && $success_chk_add == 1 && $success_chk_tel == 1) {
        echo '<h2 class="my-3">Registration successful!</h2>';
        echo '<p>Welcome to freeBay.</p>';
        echo '<p>Login with your new username and password to get started!';
    }
    else {
        echo '<h2 class="my-3">Registration unsuccessful :(</h2>';
        echo '<p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
    }
    mysqli_close($connection);
}

?>

</div>

<?php include_once("footer.php")?>