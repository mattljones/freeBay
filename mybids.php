<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
  // This page is for showing a user the auctions they've bid on.
   
  // Check if user is logged in
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    
	$buyer_id = $_SESSION['userID'];
  } else {
    $buyer_id = null;
  }

  //Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  
  
  
?>

<div class="container">

<div class="row">
	<div class="col-sm-10">
		<h2 class="my-3">My Bids</h2>
		<hr class="rounded">
	</div>
</div>

<div class="row">
  <div class="col-sm-10">
    <div id="productCards" class="row">
      <?php
      $sql = "SELECT a.auctionID, a.title, a.descript, a.endDate, a.startPrice, a.reservePrice, c.categoryName, MAX(b.bidAmount) AS YourHighestBid 
			  FROM auctions a 
			  JOIN bids b ON a.auctionID = b.auctionID 
			  JOIN categories c ON a.categoryID = c.categoryID 
			  WHERE b.buyerID = $buyer_id AND a.auctionID IN 
			  (SELECT DISTINCT a.auctionID FROM auctions a JOIN bids b ON a.auctionID = b.auctionID WHERE b.buyerID = $buyer_id) 
			  GROUP BY a.auctionID, a.title, a.descript, a.endDate, a.startPrice, a.reservePrice, c.categoryName;";
      $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
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
        $productDescript = $record['descript'];
        $productStartPrice = $record['startPrice'];
        $productReservePrice = $record['reservePrice'];
		$yourHighestBid = $record['YourHighestBid'];
			
		$sql2 = "SELECT a.auctionID,
					CASE
						WHEN a.endDate > now() THEN False
						WHEN a.endDate <= now() THEN True END AS AuctionEnd,
					b.bidAmount,
					b.buyerID,
					bu.username
				FROM auctions a
				JOIN bids b ON a.auctionID = b.auctionID
				JOIN buyers bu ON b.buyerID = bu.buyerID
				WHERE a.auctionID = $productID AND b.bidAmount = (SELECT MAX(bidAmount) FROM bids WHERE auctionID = $productID);";
		$result = $conn->query($sql2)->fetch_row() ?? false;
		$username = $result[4];
		// Check if the auction has ended
		if ($result[1] == 1) {
			$auctionOutcome = 'Winning bid: £' . $result[2];
			// Check who won the auction
			if ($buyer_id == $result[3]) {
				$leadingBidder = "Congrats " . $username . "! You won the auction!";
			}
			else {
				$leadingBidder = "Auction won by: " . $username;
			}
        }
        else {
          $auctionOutcome = "Current highest bid: £" . $result[2];
		  $leadingBidder = "Highest bidder: " . $username;
        }
			
		// Get the user's bid history for this item and put it in a table
		$sql3 = "SELECT bidDate, username, bidAmount 
				FROM bids, buyers 
				WHERE bids.buyerID = buyers.buyerID AND auctionID = $productID and bids.buyerID = $buyer_id 
				ORDER BY bidDate ";
		$result = $conn->query($sql3) ?? false;
		  
		$tableBids = '<table id="bidsTable" border="1" cellspacing="1" cellpadding="4">
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
        <div class="card" style="width: 18rem; margin-left: 0.5%; margin-right: 0.5%; margin-top: 0.5%; margin-bottom: 0.5%;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-header">
			<h4 class="card-title"><?php echo $productTitle ?></h4>
            <h5 class="card-subtitle">Category: <?php echo $productCategory ?></h5>
		  </div>
		  
		  <div class="card-body" >
			<p class="card-text">Your bids: <?php echo $tableBids ?></p>
			<p class="card-text"><?php echo $productTimeLeft?> <?php echo $auctionOutcome ?></p>
			<p class="card-text"><b><?php echo $leadingBidder ?></b></p>
          </div>
		  
          <div class="card-footer">
            <div class="buy d-flex justify-content-between align-items-center">
              <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
              <!--<a href="#" class="btn btn-danger mt-3"><i class="fas fa-shopping-cart"></i> View Item</a>-->
            </div>
          </div>
        </div>
      <?php   } ?>
    </div>
  </div>
</div>

<?php $conn->close(); ?>
<?php include_once("footer.php")?>
