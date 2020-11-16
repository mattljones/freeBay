<?php 

include_once("header.php")?>

<!-- <div class="container my-5"> -->

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */

            require_once('private/database_credentials.php');
            
            $conn = mysqli_connect(host, username, password, database)or die ('Error connecting to MySQL server.' . mysql_error());


/* TODO #2: Extract form data into variables. Because the form was a 'post'
form, its data can be accessed via $POST['auctionTitle'], $POST['auctionDetails'], etc. Perform checking on the data to
make sure it can be inserted into the database. If there is an
issue, give some semi-helpful feedback to user. */



$title=$_POST['title'];           
$descript=$_POST['Details'];

$category=$_POST['Category'];

$startingPrice=$_POST['startingPrice'];

$reservePrice=$_POST['ReservePrice'];
$minIncrement=$_POST['minIncrement'];
$createDate=date("Y-m-d H:i:s");
$startDate=$_POST['Startdate'];
$endDate=$_POST['Enddate'];

$sellerID=$_SESSION['userID'];

// /* TODO #3: If everything looks good, make the appropriate call to insert
//             data into the database. */
            

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





if(!$title ||!$descript || !$category || !$startingPrice   || !$reservePrice  || !$minIncrement  || !$createDate || !$startDate || !$endDate){
    echo('Error:There is lack of some data');
    exit;
}













$query1 = "SELECT categoryID FROM Categories WHERE categoryName = '$category'";
$result1 = mysqli_query($conn,$query1);
$row1 = mysqli_fetch_row($result1);
$categoryID = $row1[0];






$query3="INSERT INTO Auctions(title, descript, createDate, startDate, endDate, startPrice, reservePrice, minIncrement, sellerID,categoryID) VALUES
('$title','$descript','$createDate','$startDate','$endDate','$startingPrice','$reservePrice','$minIncrement','$sellerID','$categoryID') ";


if (mysqli_query($conn, $query3)) {
  echo "Insert successfully!";
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}




echo('<div class="text-center">Auction successfully created! <a href="FIXME">View your new listing.</a></div>');


?>

</div>


<?php include_once("footer.php")?>
