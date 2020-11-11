<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php

  // Get info from the URL:
  $auction_id = $_GET['auctionID'];
  //$item_id = 8;
  
  //Connection parameters
  $server_name = "localhost";
  $username = "root";
  $password = "";
  $db_name = "freebay";
  
  //Create connection
  $conn = new mysqli($server_name, $username, $password, $db_name);
  // Check connection
  if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
  }
  
  // Get item description, title, and remaining time
  $sql_1 = "SELECT title, descript, endDate, startPrice, reservePrice, minIncrement FROM auctions WHERE auctionID = $auction_id ";
  $result = $conn->query($sql_1)->fetch_row() ?? false;
  $title = $result[0];
  $description = $result[1];
  $end_time = new DateTime($result[2]);
  $start_price = $result[3];
  $reserve_price = $result[4];
  $min_increment = $result[5];
  
  //Get current price and number of bids
  $sql2 = "SELECT max(bidAmount), count(bidID) FROM bids WHERE auctionID = $auction_id ";
  $result = $conn->query($sql2)->fetch_row() ?? false;
  $current_price = floatval($result[0]);
  $num_bids = $result[1];
  
  $conn -> close();
  
  //Get the min bid amount, which we use later for input validation
  if ($current_price == 0) {
	  $min_bid = $start_price;
  } else {
	  $min_bid = max($start_price, $current_price) + $min_increment;
  }
   
  // Calculate time to auction end:
  $now = new DateTime();
  
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }
  
  // Determine whether the auction has ended
  if ($now >= $end_time || $current_price == $reserve_price) {
	  $end_auction = 1;
  } else {
	  $end_auction = 0;
  }
  
  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  $has_session = true;
  $watching = false;
  
?>


<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time):
?>
    <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
    </div>
    <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
    </div>
<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
<?php if ($end_auction == 1 && $current_price >= $start_price): ?>
     <b>This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?></b><br> <!--$end_time needs to be changed to either end_time or last bid time-->
	 <b>Auction outcome: <?php echo("Sold for £" . number_format($current_price, 2)) ?></b>

<?php elseif ($end_auction == 1 && $current_price < $start_price): ?>
	 <b>This auction ended on <?php echo(date_format($end_time, 'j M H:i')) ?></b><br>
	 <b>Auction outcome: <?php echo("The item was not sold") ?></b>
	 
<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>  
    <p class="lead">Starting price: £<?php echo(number_format($start_price, 2)) ?></p>
	<p class="lead">Current bid: £<?php echo(number_format($current_price, 2)) ?></p>
	<p class="lead">Minimum increment: £<?php echo(number_format($min_increment, 2)) ?></p>
	<p class="lead">Reserve price: £<?php echo(number_format($reserve_price, 2)) ?></p>

    <!-- Bidding form -->
    <form method="POST" action="place_bid.php?auctionID=<?php echo $auction_id ?>" >
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">£</span>
        </div>
	    <input type="number" class="form-control" id="bid" name = "bid" min=<?php echo $min_bid; ?> max=<?php echo $reserve_price; ?> step=<?php echo $min_increment; ?> required>
      </div>
      <button type="submit" class="btn btn-primary form-control">Place bid</button>
    </form>
<?php endif ?>

  
  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->



<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
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

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
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

} // End of addToWatchlist func
</script>