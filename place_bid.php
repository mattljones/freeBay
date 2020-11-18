<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php

  // Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  // Check if bid is posted and redirect if not
  $indexURL = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
  if (!isset($_POST['bid'])) {
	header('Location: ' . $indexURL);
  }
  
  // Check if user is logged in and is a buyer, redirect if not
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'buyer') {
	$buyer_id = $_SESSION['userID'];
  } else {
	header('Location: ' . $indexURL);
  }
  
  // If user is logged in and bid is posted, continue to insert the bid
  $now = new DateTime();
  $bid_date = $now->format("Y-m-d H:i:s");
  $bid_amount = round($_POST['bid'], 2);
  $auction_id = $_GET['auctionID'];

  // Insert the records into the database
  // Not validating bid here since the validation is built into the server with the function bid_check
  $sql = "INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('$bid_date', '$bid_amount', '$buyer_id', '$auction_id')";
  
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

