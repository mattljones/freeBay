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

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<hr>
<div class="row" style="margin:0;">
	
  <div class="col-md-2" style="border-right:1px #ff0000;">
    <h1>Filters</h1>

      <button id="checkAll" class="btn btn-outline-primary" style="margin-top: 5%;">Reset filters</button>
      <hr>
      <!-- Filters that check the current status of the Auctions-->
      <h2>Status</h2>
      <?php
      $activeChecked1 = "checked";
      $activeChecked2 = "";
      $activeChecked3 = "";
      $completedWonChecked = "";
      $completedLostChecked = "";
      
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $activeChecked1 = "checked";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $completedWonChecked = "";
          $completedLostChecked = "";
        }
        if (in_array("checkActive2", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "checked";
          $activeChecked3 = "";
		  $completedWonChecked = "";
          $completedLostChecked = "";
        }
        if (in_array("checkActive3", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "checked";
		  $completedWonChecked = "";
          $completedLostChecked = "";
        }
        if (in_array("completedWonChecked", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $completedWonChecked = "checked";
          $completedLostChecked = "";
        }
        if (in_array("completedLostChecked", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $completedWonChecked = "";
          $completedLostChecked = "checked";
        }
      }
      ?>
      <div class="form-group" style="margin-bottom: 1rem">
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkActive" id="showActive1" name="checkedStatus[]" <?php echo $activeChecked1 ?>>
          <label class="form-check-label" for="showActive1">Show Active</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkActive2" id="showActive2" name="checkedStatus[]" <?php echo $activeChecked2 ?>>
          <label class="form-check-label" for="showActive2">Show Active (Winning)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkActive3" id="showActive3" name="checkedStatus[]" <?php echo $activeChecked3 ?>>
          <label class="form-check-label" for="showActive3">Show Active (Not winning)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="completedWonChecked" id="showCompleted1" name="checkedStatus[]" <?php echo $completedWonChecked ?>>
          <label class="form-check-label" for="showCompleted1">Show Completed (Won)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="completedLostChecked" id="showCompleted2" name="checkedStatus[]" <?php echo $completedLostChecked ?>>
          <label class="form-check-label" for="showCompleted2">Show Completed (Lost)</label>
        </div>
        <button type="submit" class="btn btn-outline-primary" style="margin-left: 0.5%">Apply</button>
      </div>
      <hr>
      <h2>Categories</h2>
      <div class="list-group">
        <div id="categories-filter">
          <?php
          $sql = "SELECT categoryName, categoryID From Categories";
          $result = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
          $row = mysqli_fetch_array($result, MYSQLI_NUM);
          $counter = 0;
          while ($row = mysqli_fetch_array($result)) {
            $checked = "";
            $categoryName = $row['categoryName'];
            $categoryID = $row['categoryID'];
            if (isset($_POST['checkedCategories'])) {
              if (in_array($categoryID, $_POST['checkedCategories'])) {
                $checked = "checked";
              }
            }
            echo '
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <div class="input-group-text">
                            <input type="checkbox" value="' . $categoryID . '" name="checkedCategories[]" ' . $checked . '>
                          </div>
                      </div>
                    <div class="form-control"> ' . $categoryName . '</div>
                  </div>';
            $counter++;
          }
          ?>
          <button type="submit" class="btn btn-outline-primary" style="margin-top: 5%;">Apply</button>
          <!--</form> -->
        </div>
      </div>
      <hr>
      <h2>Order by</h2>
      <hr>
      <?php
      $priceChecked1 = "";
      $priceChecked2 = "";
      $endingSoonChecked ="";
      $endingLaterChecked ="";
      if (isset($_POST['checkedOrder'])) {
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $priceChecked1 = "checked";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $priceChecked2 = "checked";
        }
        if (in_array("endingSoonChecked", $_POST['checkedOrder'])) {
          $endingSoonChecked = "checked";
        }
        if (in_array("endingLaterChecked", $_POST['checkedOrder'])) {
          $endingLaterChecked = "checked";
        }
      }
      ?>
      <div class="form-group" style="margin-bottom: 1rem">
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowPrice" id="sortPriceCheck1" name="checkedOrder[]" <?php echo $priceChecked1 ?>>
          <label class="form-check-label" for="sortPriceCheck1">Price (Low to High)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighPrice" id="sortPriceCheck2" name="checkedOrder[]" <?php echo $priceChecked2 ?>>
          <label class="form-check-label" for="sortPriceCheck2">Price (High to Low)</label>
        </div>
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="endingSoonChecked" id="showEndDate1" name="checkedOrder[]" <?php echo $endingSoonChecked ?>>
          <label class="form-check-label" for="showEndDate1">End date (soonest to latest)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="endingLaterChecked" id="showEndDate2" name="checkedOrder[]" <?php echo $endingLaterChecked ?>>
          <label class="form-check-label" for="showEndDate2">End date (latest to soonest)</label>
   
        </div>
        <button type="submit" class="btn btn-outline-primary" style="margin-left: 0.5%">Apply</button>
      </div>
    </form>
  </div>
   
  <div class="col-md-10">
	<h1>My Watchlist</h1>
	<hr class="rounded">
    <div id="productCards" class="row">
      <?php
      $sql_temp = "SELECT w.auctionID, a.title, a.descript, c.categoryID, c.categoryName, a.endDate, a.startPrice, a.reservePrice, s.username, MAX(b.bidAmount) AS MaxBid
				   FROM Watching w
				   JOIN Auctions a ON w.auctionID = a.auctionID
				   LEFT JOIN Categories c ON a.categoryID = c.categoryID
				   LEFT JOIN Sellers s ON a.sellerID = s.sellerID
				   LEFT JOIN Bids b ON w.auctionID = b.auctionID
				   WHERE w.buyerID = '$buyer_id'
				   GROUP BY w.auctionID, a.title, a.descript, c.categoryID, c.categoryName, a.endDate, a.startPrice, a.reservePrice, s.username";
      $sql_temp2 = "SELECT a.auctionID, MAX(b.bidAmount) AS YourHighestBid
					FROM Auctions a
					JOIN Bids b ON a.auctionID = b.auctionID
					WHERE b.buyerID = '$buyer_id'
					GROUP BY a.auctionID";
	  $sql_1 = "SELECT a.* 
				FROM ($sql_temp) AS a
				LEFT JOIN ($sql_temp2) AS b ON a.auctionID = b.auctionID
				WHERE 0=0";
	  // Now we check for filters
	  // First check if any categories are chosen
	  if (isset($_POST['checkedCategories'])) {
        $sql_1 .= " AND categoryID IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql_1 .= $categories;
        $sql_1 .= ")";
      }
	  // Then check which of the status options is selected 
	  $currentTime = new DateTime();
      $currentTime = $currentTime->format('Y-m-d H:i:s');
	  if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $sql_1 .= " AND '$currentTime' < a.endDate";
        }
        if (in_array("checkActive2", $_POST['checkedStatus'])) {
          $sql_1 .= " AND '$currentTime' < a.endDate AND (b.YourHighestBid = a.MaxBid)";
        }
        if (in_array("checkActive3", $_POST['checkedStatus'])) {
          $sql_1 .= " AND '$currentTime' < a.endDate AND (IFNULL(b.YourHighestBid, 0) < IFNULL(a.MaxBid, 0) OR a.MaxBid IS NULL)";
        }
        if (in_array("completedWonChecked", $_POST['checkedStatus'])) {
          $sql_1 .= " AND '$currentTime' > a.endDate AND (b.YourHighestBid = a.MaxBid)";
        }
        if (in_array("completedLostChecked", $_POST['checkedStatus'])) {
          $sql_1 .= " AND '$currentTime' > a.endDate AND (IFNULL(b.YourHighestBid, 0) < IFNULL(a.MaxBid, 0) OR a.MaxBid IS NULL)";
        }
      } else {
        $sql_1 .= " AND '$currentTime' < a.endDate";
      }  
	  // Finally check if the order by filter is selected
	  if (isset($_POST['checkedOrder'])) {
        $sql_1 .= " ORDER BY ";
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $sql_1 .= "maxBid";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $sql_1 .= "maxBid DESC";
        }
        if (in_array("endingSoonChecked", $_POST['checkedOrder'])) {
          $sql_1 .= "endDate";
        }
        if (in_array("endingLaterChecked", $_POST['checkedOrder'])) {
          $sql_1 .= "endDate DESC";
        }
      }	 

	  $resultset = mysqli_query($conn, $sql_1) or die("database error:" . mysqli_error($conn));
	  
 	  // Check if the user has any watched auctions
	  if (mysqli_num_rows($resultset) == 0) {
		echo "No results found!";
	  }
	  
	  // If the user has watched auctions, continue to show the relevant information
      while ($record = mysqli_fetch_assoc($resultset)) {
		$end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = "Auction ends in " . display_time_remaining($time_to_end);
		  $timeLeftFormat = "badge badge-success";
        }
        else {
          $productTimeLeft = "Auction ended";
		  $timeLeftFormat = "badge badge-warning";
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
			$auctionOutcome = 'Winning bid: £' . number_format($result[1], 2);
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
				$auctionOutcome = "Current highest bid: £" . number_format($result[1], 2) . ".";
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
			<div class="buy d-flex justify-content-between align-items-center">
				<span class="<?php echo $timeLeftFormat; ?>"><?php echo $productTimeLeft?></span>
				<button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist(<?php echo $productID ?>)">Remove watch</button>
			</div>
		  </div>
		  
		  <div class="card-body">
			<p class="card-text"><?php echo $auctionOutcome ?></p>
			<p class="card-text"><b><?php echo $leadingBidder ?></b></p>
          </div>
		  
          <div class="card-footer">
            <div class="buy d-flex justify-content-between align-items-center">
			  <span class="text-info">Seller: <?php echo $sellerUsername ?></span>
			  <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
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
