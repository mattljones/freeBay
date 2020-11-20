<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
  // This page is for showing a user the auctions they've bid on.
   
  // Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  // Check if user is logged in and redirect if not
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	$buyer_id = $_SESSION['userID'];
	$username = $_SESSION['username'];
	$usertype = $_SESSION['account_type'];
  }
  else {
	redirect_index();
  }
  
  // Check if the user type is seller and redirect if yes
  if ($usertype == "seller") {
	redirect_index();
  }
  
?>

<div class="container">

<div class="row">
	<div class="col-sm-11">
		<h2 class="my-3">My Bids</h2>
		<hr class="rounded">
	</div>
</div>

<div class="row">
  <div class="col-sm-11">
    <div id="productCards" class="row">
      <?php
      $sql_1 = "SELECT a.auctionID, s.username, a.title, a.endDate, c.categoryName, MAX(b.bidAmount) AS YourHighestBid  
			  FROM Auctions a 
			  JOIN Bids b ON a.auctionID = b.auctionID 
			  JOIN Categories c ON a.categoryID = c.categoryID
			  JOIN Buyers b2 ON b2.buyerID = b.buyerID
			  JOIN Sellers s ON s.sellerID = a.sellerID
			  WHERE b.buyerID = '$buyer_id' AND b2.username = '$username' AND a.auctionID IN 
			  (SELECT DISTINCT a.auctionID FROM Auctions a JOIN Bids b ON a.auctionID = b.auctionID WHERE b.buyerID = '$buyer_id')
			  GROUP BY a.auctionID, s.username, a.title, a.endDate, c.categoryName;";
      $resultset = mysqli_query($conn, $sql_1) or die("database error:" . mysqli_error($conn));
 	  // Check if the user has placed any bids
	  if (mysqli_num_rows($resultset) == 0) {
		echo "You haven't placed any bids yet!";
	  }
	  
	  // If the user has bids, continue to show the relevant information
      while ($record = mysqli_fetch_assoc($resultset)) {
		$end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = " Auction will end in " . display_time_remaining($time_to_end) . ".";
        }
        else {
          $productTimeLeft = "Auction ended.";
        }
		
		$productTitle = $record['title'];
        $productCategory = $record['categoryName'];
		$sellerUsername = $record['username'];
		
		// Determine the current state of the auction
		$sql_2 = "SELECT a.auctionID,
					b.bidAmount,
					a.reservePrice,
					b.buyerID,
					bu.username
				FROM Auctions a
				JOIN Bids b ON a.auctionID = b.auctionID
				JOIN Buyers bu ON b.buyerID = bu.buyerID
				WHERE a.auctionID = '$productID' AND b.bidAmount = (SELECT MAX(bidAmount) FROM Bids WHERE auctionID = '$productID');";
		$result = $conn->query($sql_2)->fetch_row() ?? false;
		$usernameHighestBidder = $result[4];
		// Check if the auction has ended
		if ($now > $end_time) {
			// Check if there were no bids or the reserve price wasn't reached
			if (mysqli_num_rows($conn->query($sql_2)) == 0 || $result[1] < $result[2]) {
				$auctionOutcome = 'The reserve price was not reached and the item was not sold!';
				$leadingBidder = "";
			}
			
			// Otherwise, the reserve price was reached
			else if ($result[1] >= $result[2]) {
				$auctionOutcome = 'Winning bid: £' . $result[1] . ".";
				// Check who won the auction
				if ($username == $usernameHighestBidder) {
					$leadingBidder = "Congrats " . $usernameHighestBidder . "! You won the auction!";
				}
				else {
					$leadingBidder = "Auction won by: " . $usernameHighestBidder . ".";
				}
			}
		}
		// The auction is still in progress
		else if ($now <= $end_time) {
			// There are no bids yet 
			if (mysqli_num_rows($conn->query($sql_2)) == 0) {
				$auctionOutcome = 'There are no bids yet!';
				$leadingBidder = "";
			}
			// There are bids - show the highest
			else {
				$auctionOutcome = "Current highest bid: £" . $result[1] . ".";
				if ($username == $usernameHighestBidder) {
					$leadingBidder = "You are the highest bidder!";
				}
				else {
					$leadingBidder = "Highest bidder: " . $usernameHighestBidder . ".";
				}
			}
		}    	
			
		// Get the user's bid history for this item and put it in a table
		$sql_3 = "SELECT bidDate, username, bidAmount 
				FROM Bids, Buyers 
				WHERE bids.buyerID = buyers.buyerID AND auctionID = '$productID' and bids.buyerID = '$buyer_id' 
				ORDER BY bidDate ";
		$result = $conn->query($sql_3) ?? false;
		  
		$tableBids = '<table id="bidsTable' . $productID  . '" border="1" cellspacing="1" cellpadding="4" class="collapse">
				<tr>
				<th>Bid Date</th>
				<th>Username</th> 
				<th>Bid Amount</th>
				</tr>';
		foreach($result as $val){
			$tableBids .= '<tr>
					<td>'.$val['bidDate'].'</td>
					<td>'.$val['username'].'</td> 
					<td>'.$val['bidAmount'].'</td>
					 </tr>';
		}
		$tableBids .= '</table>'; 
	  
      ?>
        <div class="card" style="width: 20rem; margin-left: 0.5%; margin-right: 0.5%; margin-top: 0.5%; margin-bottom: 0.5%;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-header">
			<h4 class="card-title"><?php echo $productTitle ?></h4>
            <h5 class="card-subtitle">Category: <?php echo $productCategory ?> </h5><br>
			<h5 class="card-subtitle">Seller: <?php echo $sellerUsername ?></h5>
		  </div>
		  
		  <div class="card-body">
			<p class="card-text"><a href="#" id="bids" onclick="toggleElement('#bidsTable<?php echo $productID ?>')">Your bids <?php echo $tableBids ?></a></p>
			<p class="card-text"><?php echo $productTimeLeft?> <br> <?php echo $auctionOutcome ?></p>
			<p class="card-text"><b><?php echo $leadingBidder ?></b></p>
          </div>
		  
          <div class="card-footer">
            <div class="buy d-flex justify-content-between align-items-center">
              <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
              <!--<a href="#" class="btn btn-danger mt-3"><i class="fas fa-shopping-cart"></i> View Item</a>-->
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>

<?php $conn->close(); ?>
<?php include_once("footer.php")?>


<script>
// Function to show and hide number of bids
function toggleElement(element) {
var element = $(element);
element.toggle();
}
</script>
