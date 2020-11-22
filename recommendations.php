<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container h-100">

<?php   

// Redirecting users who aren't logged in or are logged in as a seller away from the page
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'buyer') {
  header('Location: index.php');
}

require_once('private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

if (!$connection) { 
  echo '<h2 class="my-3">Unexpected error :(</h2>';
  echo '<p>There was an error connecting to the database.</p><p>Please refresh the page or try again later.</p>';
  die();
}

$this_buyer_ID = $_SESSION['userID'];
$current_time = date("Y-m-d H:i:s");

// QUERY 1 - Returns list of auctions the current user has bid on before  
$this_buyer_auctions = "SELECT DISTINCT auctionID FROM Bids WHERE buyerID = '$this_buyer_ID'";

// QUERY 2 - Returns a table with two columns: 
// 1. Bidders who have bid on at least one auction that the current user has also bid on
// 2. 'similarity' covariate (how many auctions that bidder has bid on that the current user has also bid on)
// NB: ignores buyerID '1' as that corresponds to the default value (in case buyers want their accounts removed - more details in the report)
$query_other_bidders = "SELECT buyerID, COUNT(DISTINCT auctionID) AS similarity
                        FROM Bids 
                        WHERE buyerID NOT IN ($this_buyer_ID, '1')
                        AND auctionID IN ($this_buyer_auctions)
                        GROUP BY buyerID";

// QUERY 3 - Returns a table with two columns:
// 1. Auctions which the current user hasn't bid on before but the 'similar' bidders from the previous query *have* bid on
// 2. A count of the associated similarity scores for each auction: increasing in both 1. number of bids received & 2. the 'similarity' of its bidders
// NB: selects a maximum of 10 
$query_auctions = "SELECT auctionID, COUNT(similarity) AS score
                   FROM Bids AS b, ($query_other_bidders) AS q
                   WHERE b.buyerID = q.buyerID
                   AND b.auctionID NOT IN ($this_buyer_auctions)
                   AND '$current_time' < (SELECT endDate
                                          FROM Auctions
                                          WHERE auctionID = b.auctionID)
                   GROUP BY auctionID
                   ORDER BY score DESC
                   LIMIT 10";

$recommendations = mysqli_query($connection, $query_auctions);

if (!$recommendations) {
  echo '<h2 class="my-3">Unexpected error :(</h2>';
  echo '<p>There was an error retrieving your recommendations.</p><p>Please refresh the page or try again later.</p>';
  mysqli_close($connection);
  die();
}

else { // Providing an explanation for a blank page (possible for bidders who haven't bid on many/any auctions); important not to give 'bad' recommendations

  if (mysqli_num_rows($recommendations) == 0) {
    echo '<div class="row"><div class="col-sm-11"><h2 class="my-3">Our top recommendations for you</h2><hr class="rounded"></div></div>';
    echo '<p>We have no recommendations for you at this time.</p>';
  }

  else { // Generate the list of recommendations
    echo '<div class="row"><div class="col-sm-11"><h2 class="my-3">Our top recommendations for you</h2><hr class="rounded"></div></div>';
    $rank = 1;
    while ($row = mysqli_fetch_array($recommendations)) {
      // Retrieving auction title
      $auctionID = $row['auctionID'];
      $query_1 = "SELECT title, endDate, sellerID, categoryID FROM Auctions WHERE auctionID = '$auctionID'";
      $result_1 = mysqli_query($connection, $query_1);
      $result_1_row = mysqli_fetch_array($result_1);
      $title = $result_1_row['title'];
      $endDate = new DateTime($result_1_row['endDate']);
      $sellerID = $result_1_row['sellerID'];
      $categoryID = $result_1_row['categoryID'];
      // Retrieving auction category
      $query_2 = "SELECT categoryName FROM Categories WHERE categoryID = '$categoryID'";
      $result_2 = mysqli_query($connection, $query_2);
      $categoryName = mysqli_fetch_array($result_2)['categoryName'];
      // Retrieving seller username
      $query_3 = "SELECT username FROM Sellers WHERE sellerID = '$sellerID'";
      $result_3 = mysqli_query($connection, $query_3);
      $username = mysqli_fetch_array($result_3)['username'];
      // Retrieving current price
      $query_4 = "SELECT MAX(bidAmount) AS maxBid FROM Bids WHERE auctionID = '$auctionID'"; // Checking to see if there have been any bids yet
      $result_4 = mysqli_query($connection, $query_4);
      $result_4_row = mysqli_fetch_array($result_4);
      $currentPrice = number_format($result_4_row['maxBid'], 2); // Recommended auctions are guaranteed to have at least one bid due to their selection process (above)
      // Calculating time remaining
      $now = new DateTime();
      $timeDelta = date_diff($now, $endDate);
      $timeRemaining = display_time_remaining($timeDelta);

      if (!$result_1 || !$result_2 || !$result_3 || !$result_4) {
        echo '<p>We have no recommendations for you at this time.</p>';
        mysqli_close($connection);
        die();
      }

      else {
        echo '
             <div class="card" style="margin-top: 0.5%; margin-bottom: 0.5%;">

               <div class="row align-items-center h-100 no-gutters">

                 <div class="col-sm-1 text-center">
                   <h4 class="display-4 text-muted">'.$rank.'</h4>
                 </div>

                 <div class="col-sm-5 bg-light">
                   <div style="margin-top: 4%; margin-bottom: 4%; margin-left: 4%; margin-right: 4%;">
                     <h4 class="card-title">'.$title.'</h4>
                       <h6 class="card-subtitle mb-2 text-muted">Category: <span style="font-weight:normal">'.$categoryName.'</span></h6>
                       <h6 class="card-subtitle mb-2 text-muted">Seller: <span style="font-weight:normal">'.$username.'</span></h6>
                   </div>
                 </div>

                 <div class="col-sm-2">
                   <h5 class="card-text text-center">Current price:</h5>
                   <h5 class="card-text text-center text-success">£'.$currentPrice.'</h5>
                 </div>

                 <div class="col-sm-2">
                   <h5 class="card-text text-center">Ending in:</h5>
                   <h5 class="card-text text-center text-warning">'.$timeRemaining.'</h5>
                 </div>

                 <div class="col-sm-2 text-center">
                   <a href="listing.php?auctionID='.$auctionID.'" class="btn btn-outline-primary text-center">View Item</a>
                 </div>

               </div>
  
             </div>';
      $rank += 1;
      }

    }

  }
  mysqli_close($connection);
  die();
}

?>

</div>

<?php include_once("footer.php")?>