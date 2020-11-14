<?php include_once("header.php")?> 

<div class="container">

<?php   

require_once('private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

if (!$connection) {
    echo '<h2 class="my-3">Registration unsuccessful :(</h2>';
    echo '<p>There was an error connecting to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
    die();
}

// Contains functions for validating user-inputted form data and preventing SQL injection attacks
require_once('php/login_register_form_validation.php'); 

// Functions evaluate to boolean false if there's an issue, so queries using them below will fail (since data types in the database are non-boolean)
$type = validate_user_type($connection, $_POST["accountType"]);
$username = validate_username($connection, $_POST["username"]);
$email = validate_email($connection, $_POST["emailReg"]);
$firstName = validate_names_addresses($connection, $_POST["firstName"]);
$familyName = validate_names_addresses($connection, $_POST["familyName"]);
$password = $_POST["passwordReg"];
$passwordHash = validate_password_plus_hash($connection, $password);

// BUYER BRANCH
if ($type == "buyer") {
    // Query B.1 - inserting required fields
    $query_req = "INSERT INTO Buyers (username, email, pass, firstName, familyName) VALUES ('$username', '$email', '$passwordHash', '$firstName', '$familyName')";
    $result_req = mysqli_query($connection, $query_req);
    if (!$result_req) {
        echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
        mysqli_close($connection);
        die();
    }  // Query B.2 - inserting address fields (if chosen)
    if (isset($_POST["chooseInputAddress"])) {
        $line1 = validate_names_addresses($connection, $_POST["line1"]);
        $city = validate_names_addresses($connection, $_POST["city"]);
        $postcode = validate_names_addresses($connection, $_POST["postcode"]);
        $country = validate_names_addresses($connection, $_POST["country"]);
        $query_add = "INSERT INTO BuyerAddresses (line1, city, postcode, countryID, buyerID) 
                      VALUES ('$line1', 
                              '$city', 
                              '$postcode', 
                              (SELECT countryID FROM Countries WHERE countryName = '".$country."'), 
                              (SELECT buyerID FROM Buyers WHERE username = '".$username."'))";
        $result_add = mysqli_query($connection, $query_add);
        if (!$result_add) {
            echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
            mysqli_query($connection, "DELETE FROM Buyers WHERE username = '".$username."'");
            mysqli_close($connection);
            die();
        }
    }  // Query B.3 - inserting telephone fields (if chosen)
    if (isset($_POST["chooseInputTelNo"])) {
        $telNo = validate_telephone($connection, $_POST["telephoneNumber"]);
        $query_tel = "INSERT INTO BuyerTels (telNo, buyerID) 
                      VALUES ('$telNo', 
                              (SELECT buyerID FROM Buyers WHERE username = '".$username."'))";
        $result_tel = mysqli_query($connection, $query_tel);
        if (!$result_tel) {
            echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
            mysqli_query($connection, "DELETE FROM Buyers WHERE username = '".$username."'");
            if (isset($_POST["chooseInputAddress"])) {
                mysqli_query($connection, "DELETE FROM BuyerAddresses WHERE buyerID = (SELECT buyerID FROM Buyers WHERE username = '".$username."'");
            }
            mysqli_close($connection);
            die();
        }
    }
    // Success message & re-direct
    echo '<h2 class="my-3">Registration successful!</h2>';
    echo '<p>Welcome to freeBay.</p>';
    echo '<p><i>You have been logged in automatically, and will be redirected shortly.</i></p>';
    header("refresh:5; url=browse.php");
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['account_type'] = $type;
    $_SESSION['userID'] = mysqli_insert_id($connection);      
    die();
}

// SELLER BRANCH
if ($type == "seller") {
    // Query S.1 - inserting required fields
    $query_req = "INSERT INTO Sellers (username, email, pass, firstName, familyName) VALUES ('$username', '$email', '$passwordHash', '$firstName', '$familyName')";
    $result_req = mysqli_query($connection, $query_req);
    if (!$result_req) {
        echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
        mysqli_close($connection);
        die();
    }  // Query S.2 - inserting address fields (if chosen)
    if (isset($_POST["chooseInputAddress"])) {
        $line1 = validate_names_addresses($connection, $_POST["line1"]);
        $city = validate_names_addresses($connection, $_POST["city"]);
        $postcode = validate_names_addresses($connection, $_POST["postcode"]);
        $country = validate_names_addresses($connection, $_POST["country"]);
        $query_add = "INSERT INTO SellerAddresses (line1, city, postcode, countryID, sellerID) 
                      VALUES ('$line1', 
                              '$city', 
                              '$postcode', 
                              (SELECT countryID FROM Countries WHERE countryName = '".$country."'), 
                              (SELECT sellerID FROM Sellers WHERE username = '".$username."'))";
        $result_add = mysqli_query($connection, $query_add);
        if (!$result_add) {
            echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
            mysqli_query($connection, "DELETE FROM Sellers WHERE username = '".$username."'");
            mysqli_close($connection);
            die();
        }
    }  // Query S.3 - inserting telephone fields (if chosen)
    if (isset($_POST["chooseInputTelNo"])) {
        $telNo = validate_telephone($connection, $_POST["telephoneNumber"]);
        $query_tel = "INSERT INTO SellerTels (telNo, sellerID) 
                      VALUES ('$telNo', 
                              (SELECT sellerID FROM Sellers WHERE username = '".$username."'))";
        $result_tel = mysqli_query($connection, $query_tel);
        if (!$result_tel) {
            echo '<h2 class="my-3">Registration unsuccessful :(</h2><p>There was an error adding your details to the database.</p><p>Click <a href="register.php">here</a> to return to the registration page and try again.</p>';
            mysqli_query($connection, "DELETE FROM Sellers WHERE username = '".$username."'");
            if (isset($_POST["chooseInputAddress"])) {
                mysqli_query($connection, "DELETE FROM SellerAddresses WHERE sellerID = (SELECT sellerID FROM Sellers WHERE username = '".$username."'");
            }
            mysqli_close($connection);
            die();
        }
    }
    // Success message & re-direct
    echo '<h2 class="my-3">Registration successful!</h2>';
    echo '<p>Welcome to freeBay.</p>';
    echo '<p><i>You have been logged in automatically, and will be redirected shortly.</i></p>';
    header("refresh:5; url=browse.php");
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['account_type'] = $type;
    $_SESSION['userID'] = mysqli_insert_id($connection);   
    die();
}   

?>

</div>

<?php include_once("footer.php")?>