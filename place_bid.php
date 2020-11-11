<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php

  //Connection parameters
  $server_name = "localhost";
  $username = "root";
  $password = "";
  $db_name = "freebay";
  
  //Create connection
  $conn = new mysqli($server_name, $username, $password, $db_name);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  //Determine values to enter into the bids table
  $now = new DateTime();
  $bid_date = $now->format("Y-m-d H:i:s");
  $bid_amount = $_POST['bid'];
  //TODO: get the actual buyer ID
  $buyer_id = 2;
  $auction_id = $_GET['auctionID'];
  
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
    <?php echo("Go back to listing."); ?> <!--TODO: add link to go back to the auction -->
    </div>

  </div>
</div>

<?php include_once("footer.php") ?>

