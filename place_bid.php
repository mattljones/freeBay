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
  $indexURL = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
  if (!isset($_POST['bid'])) {
	header('Location: ' . $indexURL);
  }
  
  // Check if user is logged in and is a buyer, redirect if not
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && $_SESSION['account_type'] == 'buyer') {
	$buyer_id = $_SESSION['userID'];
	$buyer_username = $_SESSION['username'];
  } else {
	header('Location: ' . $indexURL);
  }
  
  // If user is logged in and bid is posted, continue to insert the bid
  $now = new DateTime();
  $bid_date = $now->format("Y-m-d H:i:s");
  $bid_amount = round($_POST['bid'], 2);
  $auction_id = $_GET['auctionID'];
  
  // Get the ID of the previous highest bidder
  $sql0 = "SELECT b.buyerID
		FROM Auctions a
		JOIN Bids b ON a.auctionID = b.auctionID
		WHERE a.auctionID = '$auction_id' AND b.bidAmount = (SELECT MAX(bidAmount) FROM Bids WHERE auctionID = '$auction_id');";
  $result = $conn->query($sql0)->fetch_row() ?? false;
  $highestBidderID = $result[0];
  
  // Insert the records into the database
  // Not validating bid here since the validation is built into the server with the function bid_check
  $sql = "INSERT INTO Bids (bidDate, bidAmount, buyerID, auctionID) VALUES ('$bid_date', '$bid_amount', '$buyer_id', '$auction_id')";
  
  if ($conn->query($sql) === TRUE) {
  	$bid = 1;
  } else {
	$bid = 0;
  }
  
  // Get the title of the auction to include in the email
  $sql2 = "SELECT title FROM Auctions WHERE auctionID = '$auction_id'";
  $result = $conn->query($sql2)->fetch_row() ?? false;
  $auction_title = $result[0];  
  
  // Get list of buyers who have this item in their watchlist
  $sql3 = "SELECT w.buyerID, b.username, b.email
			FROM Watching w
			JOIN Buyers b ON b.buyerID = w.buyerID
			WHERE auctionID = '$auction_id';";
  $resultset = mysqli_query($conn, $sql3) or die("database error:" . mysqli_error($conn));
  
  // If the bid was successful and this item is on at least one buyer's watchlist, send an email
  if ($bid == 1 && mysqli_num_rows($resultset) > 0) {
	  
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
			  $auctionURL = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/listing.php?auctionID=' . $auction_id;
			  $mail->isHTML(true);                                  // Set email format to HTML
			  $mail->Subject = $username . ', an auction from your watchlist has received a new bid!';
			  $mail->Body    = 'Auction <b><a href =' . $auctionURL . '>' . $auction_title . '</a></b> has received a bid of <b>£' . $bid_amount . '</b> by user <b>' . $buyer_username . '</b>.'
								. '<br>' . $outbid; 

			  $mail->send();
			  //echo 'Message has been sent';
		  } catch (Exception $e) {
			  //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		  }
	  }
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

