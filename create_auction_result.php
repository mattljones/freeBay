<?php 

include_once("header.php")?>

<!-- <div class="container my-5"> -->

<?php

// This function takes the form data and adds the new auction to the database.

// Connect to MySQL database 

require_once('private/database_credentials.php');
            
$conn = mysqli_connect(host, username, password, database)or die ('Error connecting to MySQL server.' . mysql_error());


// Contains functions for validating user-inputted form data and preventing SQL injection attacks
require_once('php/create_auction_form_validation.php');

//Functions evaluate to boolean false if there's an issue, so queries using them below will fail (since data types in the database are non-boolean)
$title=validate_title($conn, $_POST["title"]);           
$descript=validate_descript($conn,$_POST['Details']);

$category=$_POST['Category'];

$startingPrice=validate_price($conn,$_POST['startingPrice']);

$reservePrice=validate_price($conn,$_POST['ReservePrice']);
$minIncrement=validate_minIncrement($conn,$_POST['minIncrement']);
$createDate=date("Y-m-d H:i:s");
$startDate=validate_date($conn,$_POST['Startdate']);
$endDate=validate_date($conn,$_POST['Enddate']);

$sellerID=$_SESSION['userID'];

//Assign default values to optional value not entered by the user
if(empty($_POST['ReservePrice'])){ 
  $reservePrice = $startingPrice;
}else{
  $reservePrice=$_POST['ReservePrice'];
}

if(empty($_POST['minIncrement'])){
  $minIncrement=0.01;
}else{
  $minIncrement=$_POST['minIncrement'];
}

if(empty($_POST['Startdate'])){
  $startDate=date("Y-m-d H:i:s");
}else{
  $startDate=$_POST['Startdate'];
}


//Convert the category name entered by the user into category ID that needs to be inserted
$query1 = "SELECT categoryID FROM Categories WHERE categoryName = '$category'";
$result1 = mysqli_query($conn,$query1);
$row1 = mysqli_fetch_row($result1);
$categoryID = $row1[0];
if (!$result1) {
  echo "Select category unsuccessful. Error:". $sql . "<br>" . mysqli_error($conn);
  mysqli_close($connection);
} 




//Inserting required fields
$query3="INSERT INTO Auctions(title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID,categoryID) VALUES
('$title','$descript','$createDate','$startDate','$endDate','$startingPrice','$reservePrice','$minIncrement','$sellerID','$categoryID') ";


if (mysqli_query($conn, $query3)) {
  echo "Insert successfully!";
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
  mysqli_close($conn);
}




echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>
