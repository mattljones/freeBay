
<?php include_once("header.php"); ?>
<?php require("utilities.php"); ?>
<?php
// For now, index.php just redirects to browse.php, but you can change this
// if you like.
$loginIn= $_SESSION['logged_in'];
$username=  $_SESSION['username'];
$sellerID=$_SESSION['userID'];
//header("Location: browse.php");
?>
<hr>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">


<!-- The Listings-->
<div class="row" style="margin:0;">
  <!-- The Filters of the page on the LHS -->
  <div class="col-md-2" style="border-right:1px #ff0000;">
    <h1>Filters</h1>
    
      <button id="checkAll" class="btn btn-outline-primary" style="margin-top: 5%;">Reset filters</button>
      <hr>
      <!-- Filters that check the current status of the Auctions-->
      <h2>Status</h2>
      <?php
      $activeChecked1 = "";
      $activeChecked2 = "";
      $activeChecked3 = "";
      $scheduledChecked = "";
      $soldCompletedChecked = "";
      $unSoldCompletedChecked = "";
      
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $activeChecked1 = "checked";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "";
          $scheduledChecked = "";
        }
        if (in_array("checkActive2", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "checked";
          $activeChecked3 = "";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "";
          $scheduledChecked = "";
        }
        if (in_array("checkActive3", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "checked";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "";
          $scheduledChecked = "";
        }
        if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $soldCompletedChecked = "checked";
          $unSoldCompletedChecked = "";
          $scheduledChecked = "";
        }
        if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "checked";
          $scheduledChecked = "";
        }
        if (in_array("checkScheduled", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $activeChecked2 = "";
          $activeChecked3 = "";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "";
          $scheduledChecked = "checked";
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
          <label class="form-check-label" for="showActive2">Show Active (Will sell)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkActive3" id="showActive3" name="checkedStatus[]" <?php echo $activeChecked3 ?>>
          <label class="form-check-label" for="showActive3">Show Active (Might not sell)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkScheduled" id="showScheduled" name="checkedStatus[]" <?php echo $scheduledChecked ?>>
          <label class="form-check-label" for="showScheduled">Show Scheduled</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkSoldCompleted" id="showCompleted1" name="checkedStatus[]" <?php echo $soldCompletedChecked ?>>
          <label class="form-check-label" for="showCompleted1">Show Completed (Sold)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkUnsoldCompleted" id="showCompleted2" name="checkedStatus[]" <?php echo $unSoldCompletedChecked ?>>
          <label class="form-check-label" for="showCompleted2">Show Completed (Unsold)</label>
        </div>
        <button type="submit" class="btn btn-outline-primary" style="margin-left: 0.5%">Apply</button>
      </div>
      <hr>
      <h2>Categories</h2>
      <div class="list-group">
        <div id="categories-filter">
          <?php
          require_once('private/database_credentials.php');
          $conn = mysqli_connect(host, username, password, database) or die("Connection failed: " . mysqli_connect_error());
          $sql = "SELECT categoryName,categoryID From Categories";
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
      $bidsChecked1 = "";
      $bidsChecked2 = "";
      $watchersChecked1 = "";
      $watchersChecked2 = "";
      $endingSoonChecked ="";
      $endingLaterChecked ="";
      if (isset($_POST['checkedOrder'])) {
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $priceChecked1 = "checked";
        }
        if (in_array("checkLowNoBids", $_POST['checkedOrder'])) {
          $bidsChecked1 = "checked";
        }
        if (in_array("checkLowNoWatchers", $_POST['checkedOrder'])) {
          $watchersChecked1 = "checked";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $priceChecked2 = "checked";
        }
        if (in_array("checkHighNoBids", $_POST['checkedOrder'])) {
          $bidsChecked2 = "checked";
        }
        if (in_array("checkHighNoWatchers", $_POST['checkedOrder'])) {
          $watchersChecked2 = "checked";
        }
        if (in_array("endingSoonChecked", $_POST['checkedOrder'])) {
          $endingSoonChecked = "checked";
        }
        if (in_array("endingLaterChecked", $_POST['checkedOrder'])) {
          $endingLaterChecked = "checked";
        }
        if (in_array("newListedChecked", $_POST['checkedOrder'])) {
          $newListChecked = "checked";
        }
        if (in_array("oldListedChecked", $_POST['checkedOrder'])) {
          $oldListChecked = "checked";
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
    <h1>My Listings</h1>
    <hr>
    <div id="productCards" class="row">
      <?php
      $sqlMaxBid = "SELECT IFNULL(MAX(bidAmount),0) as maxBid,count(DISTINCT buyerID) as noOfBidders,auctionID FROM Bids GROUP BY auctionID";
      $sqlMainMaxBid = "SELECT mainTable.*, coalesce(maxBid,0) as maxBid,  coalesce(noOfBidders,0) as noOfBidders FROM Auctions mainTable LEFT JOIN ($sqlMaxBid) maxBidTable ON mainTable.auctionID = maxBidTable.auctionID";
      $sqlWatchTable = "SELECT COUNT(auctionID) as noOfWatching, auctionID FROM `Watching` GROUP BY auctionID";
      $sqlMainMaxBidWatch = "SELECT mainTable.*, coalesce(noOfWatching,0) as noOfWatching FROM ($sqlMainMaxBid) mainTable LEFT JOIN ($sqlWatchTable) watchTable ON mainTable.auctionID = watchTable.auctionID";
      $sqlMainMaxBidWatchSeller = "SELECT mainTable.*, username as sellerUsername FROM ($sqlMainMaxBidWatch) mainTable INNER JOIN Sellers ON mainTable.sellerID = Sellers.sellerID";
      $sql = "SELECT mainTable.*, categoryName FROM ($sqlMainMaxBidWatchSeller) mainTable INNER JOIN Categories ON mainTable.categoryID = Categories.categoryID";
      if (isset($_POST['checkedCategories'])) {
        $sql .= " WHERE Categories.`categoryID` IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql .= $categories;
        $sql .= ") AND sellerID=$sellerID";
      }
      else {
        $sql .= " WHERE sellerID=$sellerID";
      }
      if (isset($_POST['search'])) {
        $searchToken = $_POST['search'];
        $sql .= " AND title LIKE '%$searchToken%'";
      }
      $currentTime = new DateTime();
      $currentTime = $currentTime->format('Y-m-d H:i:s');
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' < endDate AND '$currentTime' > startDate";
        }
        if (in_array("checkActive2", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' < endDate AND (maxBid >= reservePrice) AND '$currentTime' > startDate";
        }
        if (in_array("checkActive3", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' < endDate AND (maxBid < reservePrice) AND '$currentTime' > startDate";
        }
        if (in_array("checkScheduled", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' < startDate";
        }
        if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' > endDate AND (maxBid >= reservePrice AND noOfBidders > 0)";
        }
        if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' > endDate AND (maxBid < reservePrice OR noOfBidders = 0)";
        }
      } else {
        $sql .= " AND '$currentTime' < endDate";
      }
      if (isset($_POST['checkedOrder'])) {
        $sql .= " ORDER BY ";
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $sql .= "maxBid";
        }
        if (in_array("checkLowNoBids", $_POST['checkedOrder'])) {
          $sql .= "noOfBidders";
        }
        if (in_array("checkLowNoWatchers", $_POST['checkedOrder'])) {
          $sql .= "noOfWatching";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $sql .= "maxBid DESC";
        }
        if (in_array("checkHighNoBids", $_POST['checkedOrder'])) {
          $sql .= "noOfBidders DESC";
        }
        if (in_array("checkHighNoWatchers", $_POST['checkedOrder'])) {
          $sql .= "noOfWatching DESC";
        }
        if (in_array("endingSoonChecked", $_POST['checkedOrder'])) {
          $sql .= "endDate";
        }
        if (in_array("endingLaterChecked", $_POST['checkedOrder'])) {
          $sql .= "endDate DESC";
        }
        if (in_array("newListedChecked", $_POST['checkedOrder'])) {
          $sql .= "createDate DESC";
        }
        if (in_array("oldListedChecked", $_POST['checkedOrder'])) {
          $sql .= "createDate";
        }
      }
      $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
      // Check if the user has placed any listings
	  if (mysqli_num_rows($resultset) == 0) {
      echo "No results found!";
      }
      $num_rows = mysqli_num_rows($resultset);
      while ($record = mysqli_fetch_assoc($resultset)) {
        $productAuctionID = $record['auctionID'];;
       
        $productTitle = $record['title'];
        $productCategory = $record['categoryName'];
        $productDescript = $record['descript'];
        $productStartPrice = $record['startPrice'];
        $productReservePrice = $record['reservePrice'];
        $productBidders = $record['noOfBidders'];
        $productWatchers = $record['noOfWatching'];
        $productSeller = $record['sellerUsername'];

        
        if($record['maxBid']==0){
          $productCurrentPrice = $record['startPrice'];
        }else{
          $productCurrentPrice = $record['maxBid'];
        }
        #print($productCurrentPrice);
        #print_r($result);
        #$recordBids = mysqli_fetch_assoc($result);
        $end_time = new DateTime($record['endDate']);
        $start_time= new DateTime($record['startDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];


        #Card formatting doesn't change if it has not sold
        $cardStatusFormat = "";
        if ($now < $end_time) {
          if($now < $start_time){
          $time_to_end = date_diff($now, $start_time);
          $productTimeLeft = '<span class="badge badge-primary">Auction starts in ' . display_time_remaining($time_to_end) . '</span>';
        }else{
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = '<span class="badge badge-success">Auction ends in ' . display_time_remaining($time_to_end) . '</span>';
        }} else {
          $productTimeLeft = '<span class="badge badge-warning">Auction Ended</span>';
          if (($productCurrentPrice < $productReservePrice) or ($productBidders == 0)) {
            #Product didnt'sell so format of card will be red
            $cardStatusFormat = "border-danger mb-3";
          } else {
            #Product did sell so will become green
            $cardStatusFormat = "border-success mb-3";
          }
        }

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
        $result_1 = $conn->query($sql_2)->fetch_row() ?? false;
        $usernameHighestBidder = $result_1[4];
        // Check if the auction has ended
		    if ($now > $end_time) {
          if (mysqli_num_rows($conn->query($sql_2)) == 0 || $result_1[1] < $result_1[2]){
            $leadingBidder ='The item was not sold';
          }else{
            $leadingBidder = "Auction won by: " . $usernameHighestBidder . ".";
          }
        }
        // The auction is still in progress  
        if ($now <= $end_time && $now > $start_time){
          if (mysqli_num_rows($conn->query($sql_2)) == 0){
            $leadingBidder = "No bidders yet";
          }else{
            $leadingBidder = "Highest bidder: " . $usernameHighestBidder . ".";
          }
        }
        // The auction is still scheduled
        if ($now < $start_time){
          $leadingBidder = "No bidders yet";
        }

        #if ($productCurrentPrice == false) {
        #  $productCurrentPrice = $productStartPrice;
        #}

      ?>
        <div class="card <?php echo $cardStatusFormat ?>" style="width: 18rem; margin-left: 0.5%; margin-right: 0.5%; margin-top: 0.5%; margin-bottom: 0.5%;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-header">
            <div style="float: right;">
              <span class="card-text"><img title="Number of bidders" src="assets/bidderIcon.png">: <?php echo $productBidders ?></span>
              <span class="card-text"><img title="Number of watchers" src="assets/watcherIcon.png">: <?php echo $productWatchers ?></span>
            </div>
            <span style="font-size: 14px;"><?php echo $productCategory ?></span>
          </div>

          <div class="card-body">
            <h5 class="card-title"><?php echo $productTitle ?></h5>
            <!--<p class="card-text">Category: <?php echo $productCategory ?></p>-->
            <p class="card-text"><?php echo $productDescript ?></p>
            <p class="card-text"><?php echo $productTimeLeft ?></p>
            <!-- <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a> -->
          </div>
          <div class="card-footer <?php echo $cardStatusFormat ?>">
            <div class="buy d-flex justify-content-between align-items-center">
              <div class="price text-success">
                <h5 class="mt-4">£<?= number_format($productCurrentPrice,2) ?></h5>
              </div>
              <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
              <!--<a href="#" class="btn btn-danger mt-3"><i class="fas fa-shopping-cart"></i> View Item</a>-->
            </div>
            <span class="text-info"><?= $leadingBidder ?></span>
          </div>
        </div>
      <?php   } ?>
    </div>
  </div>
  </form>
</div>

<script>
  $(document).ready(function() {
    let numResults = <?php echo $num_rows ?>;
    numResults = parseInt(numResults)
    let toastText = "";
    if (numResults === 0) {
      toastText = "No auctions match those filters";
    } else {
      toastText = `The search has found ${numResults} results`;
    }
    let body = document.getElementById("messageHTMLToast");
    body.innerHTML = toastText;
    $("#messageToast").toast('show');
  });

  function uncheckAll() {
    $("input[type='checkbox']:checked").prop("checked", false)
    $("input[type='radio']:checked").prop("checked", false)
  }
  $('#checkAll').on('click', uncheckAll)

  // $(document).ready(function() {
  //   $('#searchbox').on("keyup", function() {
  //     var value = $(this).val().toLowerCase();
  //     $(".card").filter(function() {
  //       $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  //     });
  //   });
  // });

  $(document).ready(function() {
    $('#update_indication_id').on("change", function() {
      var value = $(this).val().toLowerCase();
      $(".card").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });

  $.each($('*'), function() {
    if ($(this).width() > $('body').width()) {
      console.log("Wide Element: ", $(this), "Width: ",
        $(this).width());
    }
  });
</script>


<?php include_once("footer.php") ?>