<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
  // PHPMailer classes required to send emails
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
  
  require("PHPMailer\PHPMailer.php");
  require("PHPMailer\SMTP.php");
  require("PHPMailer\Exception.php");
  
  // Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  // Check if bid is posted and redirect if not
  if (!isset($_POST['bid'])) {
	redirect_index();
  }
  
  // Check if user is logged in and is a buyer, redirect if not
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'buyer') {
	$buyer_id = $_SESSION['userID'];
	$buyer_username = $_SESSION['username'];
  } else {
	redirect_index();
  }
  
  // If user is logged in and bid is posted, continue to insert the bid
  $now = new DateTime();
  $bid_date = $now->format("Y-m-d H:i:s");
  $bid_amount = mysqli_real_escape_string($conn, round($_POST['bid'], 2));  
  $auction_id = $_GET['auctionID'];
  
  // Get the ID of the highest bidder before we place the new one, auction title to include in email later, and endDate of the auction
  $sql_0 = "SELECT b.buyerID, a.title, a.endDate
			FROM Auctions a
			JOIN Bids b ON a.auctionID = b.auctionID
			WHERE a.auctionID = '$auction_id' AND b.bidAmount = (SELECT MAX(bidAmount) FROM Bids WHERE auctionID = '$auction_id');";
  $result = $conn->query($sql_0)->fetch_row() ?? false;
  $highestBidderID = $result[0];
  $auction_title = $result[1];  
  $end_date = new DateTime($result[2]);
  
  // Check if bid was placed before endDate and reject it otherwise
  // This can happen if the user loads the page before endDate and then submits the bid after endDate
  if ($now > $end_date) {
	$bid_success = 0;
  }
  else {
    // Insert the records into the database
    // Not validating bid here since the validation is built into the server with the function bid_check
	$sql_1 = "INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('$bid_date', '$bid_amount', '$buyer_id', '$auction_id')";
	if ($conn->query($sql_1) === TRUE) {
		$bid_success = 1;
	} else {
		$bid_success = 0;
	}
  }
  
  // Get list of buyers who have this item in their watchlist
  $sql_2 = "SELECT w.buyerID, b.username, b.email
			FROM Watching w
			JOIN Buyers b ON b.buyerID = w.buyerID
			WHERE auctionID = '$auction_id';";
  $resultset = mysqli_query($conn, $sql_2) or die("database error:" . mysqli_error($conn));
  
  // If bid was succesful, check if the user has this item on their watchlist 
  if ($bid_success == 1) {
	$sql_3 = "SELECT count(buyerID) 
			  FROM Watching 
			  WHERE auctionID = '$auction_id' AND buyerID = '$buyer_id';";
	$result = $conn->query($sql_3)->fetch_row() ?? false;
	if ($result[0] == 1) {
		$watching = true;
	} else {
		$watching = false;
	}
  }
  
  // If the bid was successful and this item is on at least one buyer's watchlist, send an email
  if ($bid_success == 1 && mysqli_num_rows($resultset) > 0) {
	  //echo urlencode($_SERVER['HTTP_HOST']);
	  //echo urlencode(dirname($_SERVER['PHP_SELF']));
	  //echo htmlentities(urlencode(dirname($_SERVER['PHP_SELF'])));
	  // Go through each buyer and send an email. Sending separate emails allows for customization.  
	  while ($record = mysqli_fetch_assoc($resultset)) {
		  $userID = $record['buyerID'];
		  $username = $record['username'];
		  $email = $record['email'];
		  
		  // If the previous highest bidder was outbid by another user and they had the auction on their watchlist, notify them
		  $outbid = '';
		  if ($userID == $highestBidderID AND $userID != $buyer_id) {
			$outbid = 'You were outbid and you are no longer the highest bidder :(';
		  }
		  
		  // Create the email object
		  $mail = new PHPMailer(true);
		  try {
			  //Server settings
			  //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
			  $mail->isSMTP();                                            // Send using SMTP
			  $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
			  $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			  $mail->Username   = 'freebay22@gmail.com';                     // SMTP username
			  $mail->Password   = 'Freebay123!';                               // SMTP password
			  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			  $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

			  //Recipients
			  $mail->setFrom('freebay22@gmail.com', 'Freebay');
			  $mail->AddAddress($email, $username);     		// Add a recipient
			 
			  // Content
			  // Get the auction URL so the user can directly access it from the email. Not sure if this will work on other computers.
			  //$auctionURL = 'http://' . htmlentities($_SERVER['HTTP_HOST']) . htmlentities(dirname($_SERVER['PHP_SELF'])) . '/listing.php?auctionID=' . $auction_id;
			  $mail->isHTML(true);      // Set email format to HTML
			  $mail->Subject = "$username, an auction from your watchlist has received a new bid!";
			  $mail->Body    = "Auction <b>$auction_title</b> has received a bid of <b>£$bid_amount</b> by user <b>$buyer_username</b><br>$outbid"; 
			  $mail->send();

		  } catch (Exception $e) {
			  // Do nothing if there's an error
		  }
	  }
  }
  
  $conn->close();
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
<div class="col-sm-8">
<?php if ($bid_success == 1):?>
    <h2 class="my-3"><?php echo("You successfully placed a bid of £" . number_format($bid_amount, 2) . "!") ?></h2>
	<?php if (!$watching):?>
		<div id="watch_nowatch">
		<p>You don't have this auction on your watchlist, would you like to add it and receive bid notifications?</p>
			<button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>	
		</div>
		<div id="watch_watching" style="display: none">
			<h5>Auction added to watchlist successfully!</h5>	
		</div>
	<?php endif ?>
<?php elseif ($bid_success == 0): ?>
    <h2 class="my-3"><?php echo("Error: your bid was not placed!") ?></h2>
</div>
</div>
<?php endif ?>
<br>
<div class="row"> <!-- Row #2 with auction description + bidding info -->
<div class="col-sm-8">
	<h5><a href="listing.php?auctionID=<?php echo($auction_id) ?>"> Go back to listing.</a></h5>
</div>
</div>


<?php include_once("footer.php") ?>

<script>
// JavaScript functions: addToWatchlist and removeFromWatchlist.
function addToWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($auction_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        var objT = obj.trim();
 
        if (objT.includes("success")) {
		  $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func
</script>
