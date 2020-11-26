
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
  
 
  // Check if user is logged in
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $has_session = true;
	$user_id = $_SESSION['userID'];
	$username = $_SESSION['username'];
	$usertype = $_SESSION['account_type'];
  } else {
    $has_session = false;
	$usertype = "";
  }

  // Get auctionID from the URL
  $auction_id = $_GET['auctionID'];
  
  
  // Get current price and number of bids
  $sql_1 = "SELECT auctionID, max(bidAmount) AS maxBid, count(bidID) AS numBids 
			FROM Bids 
		    WHERE auctionID = '$auction_id'
			GROUP BY auctionID";
  
  // Get number of watchers
  $sql_2 = "SELECT auctionID, count(buyerID) AS watchers 
			FROM Watching 
		    WHERE auctionID = '$auction_id'
			GROUP BY auctionID";

  // Get all the relevant summary information about the auction
  $sql_main = "SELECT a.title, a.descript, a.startDate, a.endDate, a.startPrice, a.reservePrice, a.minIncrement, c.categoryName, s.username, s.sellerID, p.maxBid, p.numBids, IFNULL(w.watchers, 0) AS watchers
			   FROM Auctions a
			   LEFT JOIN Categories c ON a.categoryID = c.categoryID
			   LEFT JOIN Sellers s ON a.sellerID = s.sellerID
			   LEFT JOIN ($sql_1) p ON p.auctionID = a.auctionID
			   LEFT JOIN ($sql_2) w ON w.auctionID = a.auctionID
			   WHERE a.auctionID = '$auction_id'; ";
  $result = $conn->query($sql_main)->fetch_row() ?? false;
  $title = $result[0];
  $description = $result[1];
  $start_time = new DateTime($result[2]);
  $end_time = new DateTime($result[3]);
  $start_price = $result[4];
  $reserve_price = $result[5];
  $min_increment = $result[6];
  $category = $result[7];
  $seller_username = $result[8];
  $seller_id = $result[9];
  $current_price = floatval($result[10]);
  $num_bids = $result[11];
  $num_watchers = $result[12];  
  
  // If the start date is in the future, only the seller of the item should be able to see it, otherwise redirect
  $now = new DateTime();
  if ($now < $start_time && ($usertype == 'buyer' || $seller_id != $user_id )) {
	redirect_index();
  }
  
  // Get bid history and put it in a table
  $sql_3 = "SELECT bidDate, username, bidAmount 
			FROM Bids, Buyers 
			WHERE bids.buyerID = buyers.buyerID AND auctionID = '$auction_id' 
			ORDER BY bidDate;";
  $result2 = $conn->query($sql_3) ?? false;
  
  $table = '<table class="table table-hover">
			  <thead class="thead-light">
				<th>Bid Date</th>
				<th>Username</th> 
				<th>Bid Amount</th>
			  </thead>';
  foreach($result2 as $val){
    $table .= '<tr>
                <td>'.$val['bidDate'].'</td>
                <td>'.$val['username'].'</td> 
                <td>£'.number_format($val['bidAmount'], 2).'</td>
              </tr>';
	}
  $table .= '</table>'; 
    
  // If the user has a session, determine if the user is already watching this item.
  if ($has_session == true) {
	$sql_4 = "SELECT count(buyerID) 
			  FROM Watching 
			  WHERE auctionID = '$auction_id' AND buyerID = '$user_id';";
	$result3 = $conn->query($sql_4)->fetch_row() ?? false;
	if ($result3[0] == 1) {
		$watching = true;
	} else {
		$watching = false;
	}
  } else {
	  $watching = false;
  }

  
  // Close the connection
  $conn -> close();
  
  //Get the min bid amount, which we use later for input validation
  if ($current_price == 0) {
	  $min_bid = $start_price;
  } else {
	  $min_bid = max($start_price, $current_price) + $min_increment;
  }
   
  // Calculate time to auction end 
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }
  
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
		<hr class="rounded">	
  </div>
  
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php  if ($now < $end_time): ?>
    <div id="watch_nowatch" <?php if (($has_session && $watching) || (!$has_session) || ($usertype == "seller")) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>	
	  <button type="button" class="btn btn-outline-secondary btn-sm" disabled>No. of watchers: <?php echo $num_watchers ?></button>
    </div>
<?php endif /* Print nothing otherwise */ ?>

    <div id="watch_watching" <?php if (!$has_session || !$watching || ($usertype == "seller") ) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
	  <button type="button" class="btn btn-outline-secondary btn-sm" disabled>No. of watchers: <?php echo $num_watchers ?></button>
    </div>

  </div>

</div>



<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->
	
	<h5 class="my-3">Category:<span style="font-weight:normal"> <?php echo($category); ?></span>&nbsp | &nbspSeller:<span style="font-weight:normal"> <?php echo($seller_username); ?></span> </h5>
   <!-- <div class="itemDescription">Description: <?php echo($description); ?></div>-->
    <div class="card">
		<h5 class="card-header">Description</h5>
			<div class="card-body"><?php echo $description ?></div>
	</div>	

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

	<?php if ($now >= $end_time && $current_price >= $reserve_price): ?>
		<span class="badge badge-warning">This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?></span>
		<hr class="rounded">
		<p class="lead">Starting price: £<?php echo(number_format($start_price, 2)) ?></p>
		<p class="lead">The item was sold for £<?php echo(number_format($current_price, 2)) ?>.</p>
		<div class="card">
			<h5 class="card-header">
			<a href="#" id="bids" onclick="toggleElement('#bidsTable')">Number of bids: <?php echo(number_format($num_bids, 0)) ?>
			</a></h5>
			<div id="bidsTable" class="collapse" aria-labelledby="bids">
				<div class="card-body">
				<?php echo $table ?>
				</div>
			</div>
		</div>	
	
	<?php elseif ($now >= $end_time && $current_price < $reserve_price): ?>
		<span class="badge badge-warning">This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?></span>
		<hr class="rounded">
		<p class="lead">Starting price: £<?php echo(number_format($start_price, 2)) ?></p>
		<p class="lead">The reserve price was not reached and the item was not sold.</p>
		<div class="card">
			<h5 class="card-header">
			<a href="#" id="bids" onclick="toggleElement('#bidsTable')">Number of bids: <?php echo(number_format($num_bids, 0)) ?>
			</a></h5>
			<div id="bidsTable" class="collapse" aria-labelledby="bids">
				<div class="card-body">
				<?php echo $table ?>
				</div>
			</div>
		</div>
	 
	<?php else: ?>
		<span class="badge badge-success">Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></span> 
		<hr class="rounded">	
		<p class="lead">Starting price: £<?php echo(number_format($start_price, 2)) ?></p>
		<p class="lead">Minimum increment: £<?php echo(number_format($min_increment, 2)) ?></p>
		<p class="lead">Current price: £<?php echo(number_format($current_price, 2)) ?></p>
		<?php if ($usertype == "seller" && $user_id == $seller_id && $reserve_price > $start_price): ?>
			<p class="lead">Reserve price: £<?php echo(number_format($reserve_price, 2)) ?></p>
		<?php endif ?>
		<?php if ($start_price == $reserve_price): ?>
			<p class="lead">This auction has no reserve price!</p>
		<?php elseif ($current_price < $reserve_price): ?>
			<p class="lead">Reserve price has not been reached!</p>
		<?php else: ?>
			<p class="lead">Reserve price has been reached!</p>
		<?php endif ?>
	
		<div class="card">
			<h5 class="card-header">
			<a href="#" id="bids" onclick="toggleElement('#bidsTable')">Number of bids: <?php echo(number_format($num_bids, 0)) ?>
			</a></h5>
			<div id="bidsTable" class="collapse">
				<div class="card-body">
				<?php echo $table ?>
				</div>
			</div>
		</div><br>
		
    <!-- Bidding form, shown only to buyers -->
		<?php if ($has_session == true): ?>
		<form method="POST" onsubmit="return checkBidSubmit()" action="place_bid.php?auctionID=<?php echo $auction_id ?>" <?php if ($usertype == "seller") echo('style="display: none"');?>>
		  <div class="input-group">
			<div class="input-group-prepend">
			  <span class="input-group-text">£</span>
			</div>
			<input type="number" class="form-control" id="bid" name="bid" min=<?php echo $min_bid; ?> step="0.01" required>
			<div class="input-group-append">
			  <span class="input-group-text">Minimum bid: <?php echo "£" . number_format($min_bid, 2) ?></span>
			</div>
		  </div>
		  <button type="submit" class="btn btn-primary form-control">Place bid</button>
		</form>
		<?php endif ?>
	<?php endif ?>

  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->
</div>

<!-- RECOMMENDATIONS (based on which auctions users who are watching this auction are also watching) -->

<br>
<br>
<div class="container">

<?php 

$connection = mysqli_connect(host, username, password, database);

if (!$connection) { 
}

$this_auction_id = $_GET['auctionID'];
$current_time = date("Y-m-d H:i:s");

// Set buyerID to the session variable 'userID' if the individual is logged in as a buyer, and blank otherwise (since used in QUERY 1)
if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
  $buyer_id = $_SESSION['userID'];
}
else {
  $buyer_id = '';
}

// QUERY 1 - Returns list of buyers (or, if logged in as a buyer, other buyers) watching this auction
// NB: unlike in recommendations.php, does not need to ignore buyerID '1' as watch history is deleted when buyer accounts are deleted (see report for details)
$query_buyers = "SELECT buyerID
                 FROM Watching
                 WHERE auctionID = '$this_auction_id'
                 AND buyerID != '$buyer_id'";

// QUERY 2 - Returns a table with two columns:
// 1. A list of active auctions (excluding current auction) which watchers of this auction (excluding current user if logged in as a bidder) are also watching
// 2. A count of the number of buyers watching that auction (its 'score')
$query_auctions = "SELECT auctionID, COUNT(buyerID) as score
                   FROM Watching AS w
                   WHERE buyerID IN ($query_buyers)
                   AND auctionID != '$this_auction_id'
                   AND '$current_time' > (SELECT startDate
                                          FROM Auctions
                                          WHERE auctionID = w.auctionID)
                   AND '$current_time' < (SELECT endDate
                                          FROM Auctions
                                          WHERE auctionID = w.auctionID)
                   GROUP BY auctionID
                   ORDER BY score DESC
                   LIMIT 5";

$recommendations = mysqli_query($connection, $query_auctions);

if (!$recommendations) { // Do not generate the section at all if an error is encountered (since not crucial to the page)
  mysqli_close($connection);
}

else { // Do not generate the section at all if there are no recommendations to give (possible for newer auctions with few or no watchers)

  if (mysqli_num_rows($recommendations) == 0) {
    mysqli_close($connection);
  }

  else { // Generate the carousel of recommendations
    echo '<h5 class="my-3 text-center"><span style="font-weight:normal"><i>You might also be interested in...</i></span></h5>';
    echo '
         <div id="carousel" class="carousel slide w-50 mx-auto" data-interval="false" data-ride="carousel">

           <a class="carousel-control-prev w-auto" href="#carousel" role="button" data-slide="prev">
             <span class="carousel-control-prev-icon bg-dark" aria-hidden="true"></span>
             <span class="sr-only">Previous</span>
           </a>

           <a class="carousel-control-next w-auto" href="#carousel" role="button" data-slide="next">
             <span class="carousel-control-next-icon bg-dark" aria-hidden="true"></span>
             <span class="sr-only">Next</span>
           </a>
         
           <div class="carousel-inner w-80" role="listbox">';
    $rank = 1;
    while ($row = mysqli_fetch_array($recommendations)) {
      // Retrieving auction title
      $auctionID_rec = $row['auctionID'];
      $query_1 = "SELECT title, endDate, startPrice, sellerID, categoryID FROM Auctions WHERE auctionID = '$auctionID_rec'";
      $result_1 = mysqli_query($connection, $query_1);
      $result_1_row = mysqli_fetch_array($result_1);
      $title_rec = $result_1_row['title'];
      $endDate = new DateTime($result_1_row['endDate']);
      $startPrice = $result_1_row['startPrice'];
      $sellerID = $result_1_row['sellerID'];
      $categoryID = $result_1_row['categoryID'];
      // Retrieving auction category
      $query_2 = "SELECT categoryName FROM Categories WHERE categoryID = '$categoryID'";
      $result_2 = mysqli_query($connection, $query_2);
      $categoryName = mysqli_fetch_array($result_2)['categoryName'];
      // Retrieving seller username
      $query_3 = "SELECT username FROM Sellers WHERE sellerID = '$sellerID'";
      $result_3 = mysqli_query($connection, $query_3);
      $username_seller = mysqli_fetch_array($result_3)['username'];
      // Retrieving current price
      $query_4 = "SELECT MAX(bidAmount) AS maxBid FROM Bids WHERE auctionID = '$auctionID_rec'"; // Checking to see if there have been any bids yet
      $result_4 = mysqli_query($connection, $query_4);
      $result_4_row = mysqli_fetch_array($result_4);
      if (is_null($result_4_row['maxBid'])) {
        $currentPrice = number_format($startPrice, 2); // Recommended auctions are not guaranteed to have a bid (unlike in recommendations.php)
      }
      else {
        $currentPrice = number_format($result_4_row['maxBid'], 2);  
      }
      // Calculating time remaining
      $time_now = new DateTime();
      $timeDelta = date_diff($time_now, $endDate);
      $timeRemaining = display_time_remaining($timeDelta);

      if (!$result_1 || !$result_2 || !$result_3 || !$result_4) {
        echo '<p>Recommendations are currently unavailable for this auction.</p>';
        mysqli_close($connection);
      }

      else {
        if ($rank == 1) {
          echo '
               <div class="carousel-item active">
          
                 <div class="card" style="margin-left: 10%; margin-right: 10%; margin-top: 0.5%; margin-bottom: 0.5%">
         
                   <div class="card-header">
                     <span style="font-size: 14px;">'.$categoryName.'</span>
                   </div>

                   <div class="card-body">
                     <h5 class="card-title">'.$title_rec.'</h5>
                     <p class="badge badge-success">Auction ends in '.$timeRemaining.'</p>
                   </div>

                   <div class="card-footer">
                     <div class="buy d-flex justify-content-between align-items-center">
                       <div class="price text-success">
                         <h5 class="mt-4">£'.$currentPrice.'</h5>
                       </div>
                     <a href="listing.php?auctionID='.$auctionID_rec.'" class="btn btn-outline-primary text-center">View Item</a>
                     </div>
                     <span class="text-info"><b>Seller: </b>'.$username_seller.'</span>
                   </div>

                 </div>
                 
               </div>';
          $rank += 1;
        }

        else {
        echo '      
             <div class="carousel-item">
      
               <div class="card" style="margin-left: 10%; margin-right: 10%; margin-top: 0.5%; margin-bottom: 0.5%">
      
                 <div class="card-header">
                   <span style="font-size: 14px;">'.$categoryName.'</span>
                 </div>

                 <div class="card-body">
                   <h5 class="card-title">'.$title_rec.'</h5>
                   <p class="badge badge-success">Auction ends in '.$timeRemaining.'</p>
                 </div>

                 <div class="card-footer">
                   <div class="buy d-flex justify-content-between align-items-center">
                     <div class="price text-success">
                       <h5 class="mt-4">£'.$currentPrice.'</h5>
                     </div>
                   <a href="listing.php?auctionID='.$auctionID_rec.'" class="btn btn-outline-primary text-center">View Item</a>
                   </div>
                   <span class="text-info"><b>Seller: </b>'.$username_seller.'</span>
                 </div>

               </div>

            </div>';
        }
      }
    }
  echo '</div></div>';
  mysqli_close($connection);
  }
}

?>

<br>
<br>
</div>

<?php include_once("footer.php")?>

<script> 
// Function to validate the bid amount input

function checkBidSubmit() { 
  var bid = $('#bid').val();
  var auctionID = String([<?php echo($auction_id); ?>]);
  var outcome = '';  
  $.ajax({ 
    url: "php/check_bid.php", 
    async: false,
	data: {"bid": bid, "auctionID": auctionID},
	type: "POST",
    success: function (data) {
	  var s1 = !data.includes("invalid");
	  var s2 = data.includes("valid");
	  if (s1 == false) {
        outcome = false;
		alert("The bid you entered is invalid! Please check the amount.")
      }
      else if (s1 == true && s2 == true) {
		outcome = true;
      }
    }
  });
  return outcome;
}

</script>

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
		  location.reload();
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

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($auction_id);?>]},
    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        var objT = obj.trim();
 
        if (objT.includes("success")) {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
		  location.reload();
        }
        else {
		  alert(objT);
		  var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of removeFromWatchlist func
</script>

<script>
// Function to toggle an element
function toggleElement(element) {
var element = $(element);
element.toggle();
}
</script>