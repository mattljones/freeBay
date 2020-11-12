<?php 

include_once("header.php")?>

<!-- <div class="container my-5"> -->

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */

$conn = mysqli_connect("localhost","root","root","freebay") or die ('Error connecting to MySQL server.' . mysql_error());


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
$createDate=$_POST['Createdate'];
$startDate=$_POST['Startdate'];
$endDate=$_POST['Enddate'];

//need username to replace this default value
$seller='rulesuper';


// /* TODO #3: If everything looks good, make the appropriate call to insert
//             data into the database. */
            

if(!$title ||!$descript || !$category || !$startingPrice   || !$reservePrice  || !$minIncrement  || !$createDate || !$startDate || !$endDate){
    echo('Error:There is lack of some data');
    exit;
}

if($startingPrice < 0 or $reservePrice <0 or $minIncrement < 0){
    echo('Error:Please enter correct price');
    exit;
}

if(strtotime($startDate)>strtotime($endDate)){
  
	echo('Error:Please enter correct startDate,startdate should be later to endDate');
    exit;
 }

$query1 = "SELECT categoryID FROM Categories WHERE categoryName = '$category'";
$result1 = mysqli_query($conn,$query1);
$row1 = mysqli_fetch_row($result1);
$categoryID = $row1[0];



$query2 = "SELECT sellerID FROM sellers WHERE username = '$seller'";
$result2 = mysqli_query($conn,$query2);
$row2 = mysqli_fetch_row($result2);
$sellerID = $row2[0];


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
