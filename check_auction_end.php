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
  
  // Get max bid and buyer who made that bid for each auction
  $sql_0 = "SELECT BidsTemp.auctionID, Bids.buyerID, BidsTemp.maxBid
            FROM Bids
            JOIN (SELECT auctionID, MAX(bidAmount) AS maxBid 
                  FROM Bids
                  GROUP BY auctionID) BidsTemp ON Bids.bidAmount = BidsTemp.maxBid AND Bids.auctionID = BidsTemp.auctionID";
  // Get auctions that have ended in the last 10 minutes and relevant information about them - auction info, seller info, auction outcome, buyer info
  $sql_1 = "SELECT a.auctionID, a.title, a.reservePrice, s.username AS sellerUsername, s.email AS sellerEmail, b1.maxBid
			, CASE WHEN a.reservePrice > IFNULL(b1.MaxBid, 0) THEN 0 ELSE 1 END AS sold, b2.username AS buyerUsername, b2.email AS buyerEmail
			FROM Auctions a
			LEFT JOIN Sellers s ON s.sellerID = a.sellerID
			LEFT JOIN ($sql_0) b1 ON b1.auctionID = a.auctionID
			LEFT JOIN Buyers b2 ON b1.buyerID = b2.buyerID
			WHERE a.endDate < NOW() AND TIMESTAMPDIFF(MINUTE, a.endDate, NOW()) < 10";
  $resultset = mysqli_query($conn, $sql_1) or die("database error:" . mysqli_error($conn));
  
  // If the query returned any results, proceed to send emails
  if (mysqli_num_rows($resultset) > 0) {
	while ($record = mysqli_fetch_assoc($resultset)) {
	  $title = $record['title'];
	  $seller_username = $record['sellerUsername'];
	  $seller_email = $record['sellerEmail'];
	  $sold = $record['sold'];
	  $max_bid = $record['maxBid'];
	  $buyer_username = $record['buyerUsername'];
	  $buyer_email = $record['buyerEmail'];
	  
	  if ($sold == 1) {
		$body = "Congratulations! Your auction <b>$title</b> has been sold to user <b>$buyer_username</b> for <b>£" . number_format($max_bid, 2) . "</b>.";
	  } else {
		$body = "Unfortunately, your auction <b>$title</b> was not sold :( Better luck next time!";
	  }
	  
	  // Create the email to the seller
	  $mail_seller = new PHPMailer(true);
	  try {
		  //Server settings
		  //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
		  $mail_seller->isSMTP();                                            // Send using SMTP
		  $mail_seller->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
		  $mail_seller->SMTPAuth   = true;                                   // Enable SMTP authentication
		  $mail_seller->Username   = 'freebay22@gmail.com';                     // SMTP username
		  $mail_seller->Password   = 'Freebay123!';                               // SMTP password
		  $mail_seller->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
		  $mail_seller->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

		  //Recipients
		  $mail_seller->setFrom('freebay22@gmail.com', 'Freebay');
		  $mail_seller->AddAddress($seller_email, $seller_username);     		// Add a recipient
		 
		  // Content
		  // Get the auction URL so the user can directly access it from the email. Not sure if this will work on other computers.
		  $mail_seller->isHTML(true);      // Set email format to HTML
		  $mail_seller->Subject = "$seller_username, one of your auctions has ended!";
		  $mail_seller->Body    = $body;
		  $mail_seller->send();
	  } catch (Exception $e) {
		  // Do nothing if there's an error
	  }
	  
	  // If the auction was sold, create an email to the buyer
	  if ($sold == 1) {
		  // Create the email to the buyer
		  $mail_buyer = new PHPMailer(true);
		  try {
			  //Server settings
			  //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
			  $mail_buyer->isSMTP();                                            // Send using SMTP
			  $mail_buyer->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
			  $mail_buyer->SMTPAuth   = true;                                   // Enable SMTP authentication
			  $mail_buyer->Username   = 'freebay22@gmail.com';                     // SMTP username
			  $mail_buyer->Password   = 'Freebay123!';                               // SMTP password
			  $mail_buyer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
			  $mail_buyer->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

			  //Recipients
			  $mail_buyer->setFrom('freebay22@gmail.com', 'Freebay');
			  $mail_buyer->AddAddress($buyer_email, $buyer_username);     		// Add a recipient
			 
			  // Content
			  // Get the auction URL so the user can directly access it from the email. Not sure if this will work on other computers.
			  $mail_buyer->isHTML(true);      // Set email format to HTML
			  $mail_buyer->Subject = "$buyer_username, you have won an auction";
			  $mail_buyer->Body    = "Congratulations! You have won auction <b>$title</b> with your bid of <b>£" . number_format($max_bid, 2) . "</b>.";
			  $mail_buyer->send();
		  } catch (Exception $e) {
			  // Do nothing if there's an error
		  }
	  }
	  
	  
	  
	}
  }
  

?>