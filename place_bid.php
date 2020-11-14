<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php

  //Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  //Determine values to enter into the bids table
  $now = new DateTime();
  $bid_date = $now->format("Y-m-d H:i:s");
  $bid_amount = $_POST['bid'];
  $auction_id = $_GET['auctionID'];
  $buyer_id = $_SESSION['userID'];

  //Insert the records into the database
  $sql = "INSERT INTO bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('$bid_date', '$bid_amount', '$buyer_id', '$auction_id')";
  
  if ($conn->query($sql) === TRUE) {
  	$bid = 1;
  } else {
	$bid = 0;
  }
  $conn->close();
  
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
<div class="col-sm-8"> <!-- Left col -->
<?php
  if ($bid == 1):
?>
    <h2 class="my-3"><?php echo("You successfully placed a bid of £" . $bid_amount . "!") ?></h2>
<?php elseif ($bid == 0): 
?>
    <h2 class="my-3"><?php echo("Error: your bid was not placed!") ?></h2>
</div>
</div>
<?php endif ?>
<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="goBack"> 
		<a href="listing.php?auctionID=<?php echo($auction_id) ?>"> Go back to listing.</a>
    </div>

  </div>
</div>

<?php include_once("footer.php") ?>

