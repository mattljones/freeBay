<?php include_once("header.php")?>

<div class="container">

<?php

require_once('private/database_credentials.php');
$conn = mysqli_connect(host, username, password, database);
            
if (!$conn) {
  echo '<h2 class="my-3">Creation unsuccessful :(</h2>';
  echo '<p>There was an error connecting to the database.</p><p>Click <a href="create_auction.php">here</a> to return to the auction creation page and try again.</p>';
}

// Contains functions for validating user-inputted create auction form data and preventing SQL injection attacks
require_once('php/create_auction_form_validation.php');

//Functions evaluate to boolean false if there's an issue, so queries using them below will fail (since data types in the database are non-boolean)
$title = validate_title($conn, $_POST["title"]);           
$descript = validate_descript($conn, $_POST['Details']);
$category = validate_category($conn, $_POST['Category']);
$startingPrice = validate_price($conn, $_POST['startingPrice']);
$createDate = date("Y-m-d H:i"); // for record-keeping in the database
$startDateTime = $_POST['Startdate'] . "T" . $_POST['Starttime'];
$startDate = validate_date($conn, $startDateTime);
$endDateTime = $_POST['Enddate'] . "T" . $_POST['Endtime'];
$endDate = validate_date($conn, $endDateTime);

// Assign default values to optional values not entered by the user
if (empty($_POST['ReservePrice'])) { 
  $reservePrice = $startingPrice;
}
else {
  $reservePrice = validate_price($conn, $_POST['ReservePrice']);
}

if (empty($_POST['minIncrement'])) {
  $minIncrement = 0.01;
}
else {
  $minIncrement = validate_minIncrement($conn, $_POST['minIncrement']);
}

// Convert the category name entered by the user into category ID that needs to be inserted
$query1 = "SELECT categoryID FROM Categories WHERE categoryName = '$category'";
$result1 = mysqli_query($conn, $query1);
$row1 = mysqli_fetch_row($result1);
$categoryID = $row1[0];
if (!$result1) {
  echo '<h2 class="my-3">Creation unsuccessful :(</h2><p>There was an error interacting with the database.</p><p>Click <a href="create_auction.php">here</a> to return to the auction creation page and try again.</p>';
  mysqli_close($conn);
  die();
} 

// Inserting required fields
$sellerID = $_SESSION['userID'];
$query2="INSERT INTO Auctions (title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID, categoryID) 
         VALUES ('$title', '$descript', '$createDate', '$startDate', '$endDate', '$startingPrice', '$reservePrice', '$minIncrement', '$sellerID', '$categoryID')";
$result2 = mysqli_query($conn, $query2);

if ($result2) {
  $auctionID = mysqli_insert_id($conn);
  echo "<h2 class='my-3'>Creation successful!</h2><p>Click <a href='listing.php?auctionID=".$auctionID."'>here</a> to view your listing, or wait 10 seconds to be redirected to the homepage.</p>";
  header("refresh:10; url=index.php");
} 
else {
  echo '<h2 class="my-3">Creation unsuccessful :(</h2><p>There was an error interacting with the database.</p><p>Click <a href="create_auction.php">here</a> to return to the auction creation page and try again.</p>';
}

mysqli_close($conn);
die();

?>

</div>

<?php include_once("footer.php")?>