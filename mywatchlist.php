<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
  // This page is for showing a user's watched auctions.
   
  // Create connection
  require_once('private/database_credentials.php');
  $conn = mysqli_connect(host, username, password, database);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }

  // Check if user is logged in
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	  $buyer_id = $_SESSION['userID'];
	  $username = $_SESSION['username'];
	  $usertype = $_SESSION['account_type'];
  }
  
  // If user type is not buyer, redirect
  if ($usertype == "seller") {
	redirect_index();
  }
?>

<div class="container">

<div class="row">
	<div class="col-sm-11">
		<h2 class="my-3">My Watchlist</h2>
		<hr class="rounded">
	</div>
</div>

<div class="row">
  <div class="col-sm-11">
    <div id="productCards" class="row">
      <?php
      $sql_1 = "SELECT w.auctionID, a.title, a.descript, c.categoryName, a.endDate, a.startPrice, a.reservePrice, s.username
				FROM Watching w
				JOIN Auctions a ON w.auctionID = a.auctionID
				LEFT JOIN Categories c ON a.categoryID = c.categoryID
				LEFT JOIN Sellers s ON a.sellerID = s.sellerID
				WHERE buyerID = '$buyer_id';";
      $resultset = mysqli_query($conn, $sql_1) or die("database error:" . mysqli_error($conn));
	  
 	  // Check if the user has any watched auctions
	  if (mysqli_num_rows($resultset) == 0) {
		echo "You haven't added any auctions to your watchlist yet!";
	  }
	  
	  // If the user has watched auctions, continue to show the relevant information
      while ($record = mysqli_fetch_assoc($resultset)) {
		$end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = " Auction ends in " . display_time_remaining($time_to_end);
		  $timeLeftFormat = "bg-success text-white";
        }
        else {
          $productTimeLeft = "Auction ended";
		  $timeLeftFormat = "bg-warning text-dark";
        }
		
		$productTitle = $record['title'];
        $productCategory = $record['categoryName'];
        $productDescript = $record['descript'];
        $productStartPrice = $record['startPrice'];
        $productReservePrice = $record['reservePrice'];
		$sellerUsername = $record['username'];
		
		// Determine the current state of the auction		
		$sql_2 = "SELECT a.auctionID, b.bidAmount, a.reservePrice, b.buyerID, bu.username
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
			$auctionOutcome = 'Winning bid: £' . $result[1];
				// Check who won the auction
				if ($buyer_id == $result[3]) {
					$leadingBidder = "Congrats " . $usernameHighestBidder . "! You won the auction!";
				}
				else {
					$leadingBidder = "Auction won by: " . $usernameHighestBidder;
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
	  
      ?>
        <div class="card" style="width: 20rem; margin-left: 0.5%; margin-right: 0.5%; margin-top: 0.5%; margin-bottom: 0.5%;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-header">
			<h4 class="card-title"><?php echo $productTitle ?></h4>
            <h5 class="card-subtitle">Category: <?php echo $productCategory ?></h5><br>
			<small><span class="<?php echo $timeLeftFormat; ?>"><?php echo $productTimeLeft?></span></small>
		  </div>
		  
		  <div class="card-body">
			<p class="card-text"><?php echo $auctionOutcome ?></p>
			<p class="card-text"><b><?php echo $leadingBidder ?></b></p>
          </div>
		  
          <div class="card-footer">
            <div class="buy d-flex justify-content-between align-items-center">
			  <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
			  <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(<?php echo $productID ?>)">Remove watch</button>
            </div>
			<small>Seller: <?php echo $sellerUsername ?></small>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</div>


<?php $conn->close(); ?>
<?php include_once("footer.php")?>

<script>
// JavaScript function: removeFromWatchlist.

function removeFromWatchlist(auctionID) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [auctionID]},
    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        var objT = obj.trim();
		//alert(auctionID);
		//alert(objT);
        if (objT.includes("success")) {
		  location.reload();
        }
        else {
		  alert("Error: the item was not removed from your watchlist!");
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of removeFromWatchlist func
</script>
