<?php
// For now, index.php just redirects to browse.php, but you can change this
// if you like.

//header("Location: browse.php");
?>
<?php include_once("header.php"); ?>
<?php require("utilities.php"); ?>
<hr>

<!-- The Search bar at the top of the listings -->
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
  <div class="form-row justify-content-center">
    <div class="col-8 mb-2 mb-md-0">
      <input class="form-control-lg col-12" type="text" placeholder="Search products" aria-label=" Search" id="searchbox" name="search">
    </div>
    <div class="col-2">
      <button type="submit" class="btn btn-block btn-lg btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
    </div>
  </div>
</div>


<!-- BootStrap Toastie that notifies the search results-->
<div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 25px;">
  <div class="toast hide" id="messageToast" style="position: absolute; top: 0; right: 0;" data-autohide="false">
    <div class="toast-header">
      <img src="assets/favicon.png" class="rounded mr-2" alt="fB">
      <strong class="mr-auto">Search Results</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body" id="messageHTMLToast">
      The search has found <?php echo $num_rows ?> results.
    </div>
  </div>
</div>

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
      //Radio boxes default ticked status values when the page is first loaded
      $activeChecked1 = "checked";
      $allCompletedChecked = "";
      $soldCompletedChecked = "";
      $unSoldCompletedChecked = "";
      //Checks when a certain radio box is ticked so it is retained when searching
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $activeChecked1 = "checked";
        } else {
          $activeChecked1 = "";
          if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
            $soldCompletedChecked = "checked";
          }
          if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
            $unSoldCompletedChecked = "checked";
          }
          if (in_array("checkAllCompleted", $_POST['checkedStatus'])) {
            $allCompletedChecked = "checked";
          }
        }
      }
      ?>
      <!-- The radio boxes for the active/completed listings -->
      <div class="form-group" style="margin-bottom: 1rem">
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkActive" id="showActive1" name="checkedStatus[]" <?php echo $activeChecked1 ?>>
          <label class="form-check-label" for="showActive1">Show Active</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkAllCompleted" id="showCompleted3" name="checkedStatus[]" <?php echo $allCompletedChecked ?>>
          <label class="form-check-label" for="showCompleted3">Show Completed (All)</label>
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
      <!-- The categories section to allow the user to filter -->
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
          // Loops through the SQL database to populate the different types of categories
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
      $endingSoonChecked = "";
      $endingLaterChecked = "";
      $newListChecked = "";
      $oldListChecked = "";
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
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="newListedChecked" id="showNewList1" name="checkedOrder[]" <?php echo $newListChecked ?>>
          <label class="form-check-label" for="showNewList1">List date (newest to oldest)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="oldListedChecked" id="showNewList2" name="checkedOrder[]" <?php echo $oldListChecked ?>>
          <label class="form-check-label" for="showNewList2">List date (oldest to newest)</label>
        </div>
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowNoBids" id="sortNoBidsCheck1" name="checkedOrder[]" <?php echo $bidsChecked1 ?>>
          <label class="form-check-label" for="sortNoBidsCheck1">Number of Bidders (Low to High)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighNoBids" id="sortNoBidsCheck2" name="checkedOrder[]" <?php echo $bidsChecked2 ?>>
          <label class="form-check-label" for="sortNoBidsCheck2">Number of Bidders (High to Low)</label>
        </div>
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowNoWatchers" id="sortNoWatchersCheck1" name="checkedOrder[]" <?php echo $watchersChecked1 ?>>
          <label class="form-check-label" for="sortNoWatchersCheck1">Number of Watchers (Low to High)</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighNoWatchers" id="sortNoWatchersCheck2" name="checkedOrder[]" <?php echo $watchersChecked2 ?>>
          <label class="form-check-label" for="sortNoWatchersCheck2">Number of Watchers (High to Low)</label>
        </div>
        <button type="submit" class="btn btn-outline-primary" style="margin-left: 0.5%">Apply</button>
      </div>
    </form>

  </div>
  <div class="col-md-10">
    <h1>Our Latest Auctions</h1>
    <hr>
    <div id="productCards" class="row">
      <?php
      //This section displays the Auctions and their relevant information within a card
      $sqlMaxBid = "SELECT MAX(bidAmount) as maxBid, coalesce(count(DISTINCT buyerID), 0) as noOfBidders, auctionID, buyerID FROM Bids GROUP BY auctionID";
      $sqlMainMaxBid = "SELECT mainTable.*, coalesce(maxBid,mainTable.startPrice) as maxBid,  coalesce(noOfBidders,0) as noOfBidders, buyerID as maxBidderID FROM Auctions mainTable LEFT JOIN ($sqlMaxBid) maxBidTable ON mainTable.auctionID = maxBidTable.auctionID";
      $sqlWatchTable = "SELECT COUNT(auctionID) as noOfWatching, auctionID FROM `Watching` GROUP BY auctionID";
      $sqlMainMaxBidWatch = "SELECT mainTable.*, coalesce(noOfWatching,0) as noOfWatching FROM ($sqlMainMaxBid) mainTable LEFT JOIN ($sqlWatchTable) watchTable ON mainTable.auctionID = watchTable.auctionID";
      $sqlMainMaxBidWatchSeller = "SELECT mainTable.*, username as sellerUsername FROM ($sqlMainMaxBidWatch) mainTable INNER JOIN Sellers ON mainTable.sellerID = Sellers.sellerID";
      $sql = "SELECT mainTable.*, categoryName FROM ($sqlMainMaxBidWatchSeller) mainTable INNER JOIN Categories ON mainTable.categoryID = Categories.categoryID";
      if (isset($_POST['checkedCategories'])) {
        $sql .= " WHERE Categories.`categoryID` IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql .= $categories;
        $sql .= ")";
      }
      if (isset($_POST['search'])) {
        $searchToken = $_POST['search'];
        $sql .= " AND title LIKE '%$searchToken%'";
      }
      $currentTime = new DateTime();
      $currentTime = $currentTime->format('Y-m-d H:i:s');
      // This checks the logic of the ordering filters
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' < endDate AND '$currentTime' > startDate";
        }
        if (in_array("checkAllCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' > endDate";
        }
        if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' > endDate AND (maxBid >= reservePrice AND noOfBidders > 0)";
        }
        if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '$currentTime' > endDate AND (maxBid < reservePrice OR noOfBidders = 0)";
        }
      } else {
        $sql .= " AND '$currentTime' < endDate AND '$currentTime' > startDate";
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
      $num_rows = mysqli_num_rows($resultset);
      while ($record = mysqli_fetch_assoc($resultset)) {
        $productAuctionID = $record['auctionID'];;
        $sql2 = "SELECT max(bidAmount) as currentPrice, count(bidID) FROM Bids WHERE auctionID = $productAuctionID ";
        $sql3 = "SELECT count(auctionID) as noOfWatchers FROM Watching WHERE auctionID = $productAuctionID ";
        $result = $conn->query($sql2)->fetch_assoc() ?? false;
        $productTitle = $record['title'];
        $productCategory = $record['categoryName'];
        $productDescript = $record['descript'];
        $productStartPrice = $record['startPrice'];
        $productReservePrice = $record['reservePrice'];
        $productBidders = $record['noOfBidders'];
        $productWatchers = $record['noOfWatching'];
        $productSeller = $record['sellerUsername'];
        $productCurrentPrice = $record['maxBid'];
        $productStartDate = new DateTime($record['startDate']);
        $end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];
        

        #Card formatting doesn't change if it has not sold
        $cardStatusFormat = "";
        $numberOfDaysSinceListed = $now->diff($productStartDate)->days;
        if ($numberOfDaysSinceListed <= 1) {
          $checkNewListing = True;
          $badgeNewListing = '<span class="badge badge-secondary">New</span>';
        }
        #Checks if it is a new Listing
        else {
          $checkNewListing = False;
          $badgeNewListing = '';
        }
        #Calculates the remaning time of the Auction
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft ='<span class="badge badge-success">Auction ends in ' . display_time_remaining($time_to_end) . '</span>' . " " . $badgeNewListing;
        } else {
          $productTimeLeft = '<span class="badge badge-warning">Auction Ended</span>';
          if (($productCurrentPrice < $productReservePrice) or ($productBidders == 0)) {
            #Product didnt'sell so format of card will be red
            $cardStatusFormat = "border-danger mb-3";
          } else {
            #Product did sell so will become green
            $cardStatusFormat = "border-success mb-3";
          }
        }
        #If the title or description is too long it will concat "..." at the end
        if (strlen($productTitle) > 79) {
          $titleSuffix = "...";
        }
        else {
          $titleSuffix = "";
        }
        if (strlen($productDescript) > 300) {
          $descriptSuffix = "...";
        }
        else {
          $descriptSuffix = "";
        }

      ?>
      <!-- An image can be added later on if that is required -->
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
            <h5 class="card-title"><?php echo substr($productTitle, 0, 80) . $titleSuffix?></h5>
            <p class="card-text"><?php echo substr($productDescript,0,300) . $descriptSuffix ?></p>
            <p class="card-text"><?php echo $productTimeLeft ?></p>
          </div>
          <div class="card-footer <?php echo $cardStatusFormat ?>">
            <div class="buy d-flex justify-content-between align-items-center">
              <div class="price text-success">
                <h5 class="mt-4">£<?= number_format($productCurrentPrice,2) ?></h5>
              </div>
              <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
            </div>
            <span class="text-info"><b>Seller: </b><?= $productSeller ?></span>
          </div>
        </div>
      <?php   } ?>
    </div>
  </div>
  </form>
</div>

<script>
  // This function displays the message on the search result toastie at the top
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

  //This checks the radio or checkboxes when someone searches
  function uncheckAll() {
    $("input[type='checkbox']:checked").prop("checked", false)
    $("input[type='radio']:checked").prop("checked", false)
  }
  $('#checkAll').on('click', uncheckAll)


  //This function hides any cards if required
  $(document).ready(function() {
    $('#update_indication_id').on("change", function() {
      var value = $(this).val().toLowerCase();
      $(".card").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
  });

  //Helper formatting function to check which element is wider
  $.each($('*'), function() {
    if ($(this).width() > $('body').width()) {
      console.log("Wide Element: ", $(this), "Width: ",
        $(this).width());
    }
  });
</script>


<?php include_once("footer.php") ?>