
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
	$buyer_id = $_SESSION['userID'];
	$username = $_SESSION['username'];
	$usertype = $_SESSION['account_type'];
  } else {
    $has_session = false;
  }

  // Get auctionID from the URL
  $auction_id = $_GET['auctionID'];
  
  // Get item description, title, and remaining time
  $sql_1 = "SELECT a.title, a.descript, a.endDate, a.startPrice, a.reservePrice, a.minIncrement, c.categoryName 
			FROM Auctions a
			LEFT JOIN Categories c on a.categoryID = c.categoryID 	
			WHERE auctionID = $auction_id ";
  $result = $conn->query($sql_1)->fetch_row() ?? false;
  $title = $result[0];
  $description = $result[1];
  $end_time = new DateTime($result[2]);
  $start_price = $result[3];
  $reserve_price = $result[4];
  $min_increment = $result[5];
  $category = $result[6];
  
  // Get current price and number of bids
  $sql_2 = "SELECT max(bidAmount), count(bidID) FROM Bids WHERE auctionID = $auction_id ";
  $result = $conn->query($sql_2)->fetch_row() ?? false;
  $current_price = floatval($result[0]);
  $num_bids = $result[1];
    
  // Get bid history and put it in a table
  $sql_3 = "SELECT bidDate, username, bidAmount FROM Bids, Buyers WHERE bids.buyerID = buyers.buyerID AND auctionID = $auction_id ORDER BY bidDate ";
  $result = $conn->query($sql_3) ?? false;
  
  $table = '<table border="1" cellspacing="1" cellpadding="4">
          <tr>
            <th>Bid Date</th>
            <th>Username</th> 
            <th>Bid Amount</th>
          </tr>';
	foreach($result as $val){
    $table .= '<tr>
                <td>'.$val['bidDate'].'</td>
                <td>'.$val['username'].'</td> 
                <td>'.$val['bidAmount'].'</td>
              </tr>';
	}
	$table .= '</table>'; 
  
  // Get number of watchers
  $sql_4 = "SELECT count(buyerID) FROM watching WHERE auctionID = $auction_id ";
  $result = $conn->query($sql_4)->fetch_row() ?? false;
  $num_watchers = $result[0];  
  
  
  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  
  if ($has_session == true) {
	$sql_5 = "SELECT count(buyerID) FROM Watching WHERE auctionID = $auction_id and buyerID = $buyer_id ";
	$result = $conn->query($sql_5)->fetch_row() ?? false;
	if ($result[0] == 1) {
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
  $now = new DateTime();
  
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
	  <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Number of watchers: <?php echo $num_watchers ?></button>
    </div>
<?php endif /* Print nothing otherwise */ ?>

    <div id="watch_watching" <?php if (!$has_session || !$watching || ($usertype == "seller") ) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
	  <button type="button" class="btn btn-outline-secondary btn-sm" disabled>Number of watchers: <?php echo $num_watchers ?></button>
    </div>

  </div>

</div>



<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->
	
	<h5 class="my-3">Category: <?php echo($category); ?></h2>
    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

	<?php if ($now >= $end_time && $current_price >= $reserve_price): ?>
		<p>This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?>.</p>
		<p>The item was sold for £<?php echo(number_format($current_price, 2)) ?>.</p>
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
		<p>This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?>.</p>
		<p>The reserve price was not reached and the item was not sold.</p>
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
		<p>Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p> 
		<hr class="rounded">	
		<p class="lead">Starting price: £<?php echo(number_format($start_price, 2)) ?></p>
		<p class="lead">Minimum increment: £<?php echo(number_format($min_increment, 2)) ?></p>
		<p class="lead">Current bid: £<?php echo(number_format($current_price, 2)) ?></p>
		<?php if ($current_price < $reserve_price): ?>
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
			<input type="number" class="form-control" id="bid" name="bid" min=<?php echo floatval($min_bid); ?> required>
			<div class="input-group-append">
			  <span class="input-group-text">Minimum bid: <?php echo "£" . floatval($min_bid) ?></span>
			</div>
		  </div>
		  <button type="submit" class="btn btn-primary form-control">Place bid</button>
		</form>
		<?php endif ?>
	<?php endif ?>

  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->
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