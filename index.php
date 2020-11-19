<?php
// For now, index.php just redirects to browse.php, but you can change this
// if you like.

//header("Location: browse.php");
?>
<?php include_once("header.php"); ?>
<?php require("utilities.php"); ?>
<hr>

<div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
  <form class="form-row justify-content-center">
    <div class="col-8 mb-2 mb-md-0">
      <input class="form-control-lg col-12" type="text" placeholder="Search products" aria-label="Search" id="searchbox">
    </div>
    <div class="col-2">
      <button type="submit" class="btn btn-block btn-lg btn-primary"><i class="fa fa-search" aria-hidden="true"></i>Search</button>
    </div>
  </form>
</div>

<!-- <select name="indication_name" id="update_indication_id" class="form-control" required>
            <option selected="selected" value="">-- Select an option --</option>-->
<?php /*
            include_once("db_connect.php");
            $sql = "SELECT categoryName From Categories";
            $result = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
            $row = mysqli_fetch_array($result, MYSQLI_NUM);
            printf($row)
            while ($row = mysqli_fetch_array($result))
                echo "<option value='" . $row['categoryID'] . "'>" . $row['categoryName'] . "</option>";
            */
?>
<!--    <option value=></option>
        </select> -->

<div class="row" style="margin:0;">
  <div class="col-md-2">
    <h1>Filters</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
      <button id="checkAll" class="btn btn-outline-primary" style="margin-top: 5%;">Reset filters</button>
    <hr>
    <h2>Status</h2>
    <?php
      $activeChecked1 = "checked";
      $soldCompletedChecked = "";
      $unSoldCompletedChecked = "";
      if (isset($_POST['checkedStatus'])) {
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $activeChecked1 = "checked";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "";
        }
        if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $soldCompletedChecked = "checked";
          $unSoldCompletedChecked = "";
        }
        if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
          $activeChecked1 = "";
          $soldCompletedChecked = "";
          $unSoldCompletedChecked = "checked";
        }
      }
    ?>
    <div class="form-group" style="margin-bottom: 1rem">
    <div class="form-check">
        <input class="form-check-input" type="radio" value="checkActive" id="showActive1" name="checkedStatus[]" <?php echo $activeChecked1 ?> >
        <label class="form-check-label" for="showActive1">Show Active Auctions</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" value="checkSoldCompleted" id="showCompleted1" name="checkedStatus[]" <?php echo $soldCompletedChecked ?> >
        <label class="form-check-label" for="showCompleted1">Show Sold & Completed Auctions</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" value="checkUnsoldCompleted" id="showCompleted2" name="checkedStatus[]" <?php echo $unSoldCompletedChecked ?> >
        <label class="form-check-label" for="showCompleted2">Show Unsold & Completed Auctions</label>
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
      if (isset($_POST['checkedOrder'])) {
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $priceChecked1 = "checked";
        }
        if(in_array("checkLowNoBids", $_POST['checkedOrder'])) {
          $bidsChecked1 = "checked";
        }
        if(in_array("checkLowNoWatchers", $_POST['checkedOrder'])) {
          $watchersChecked1 = "checked";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $priceChecked2 = "checked";
        }
        if(in_array("checkHighNoBids", $_POST['checkedOrder'])) {
          $bidsChecked2 = "checked";
        }
        if(in_array("checkHighNoWatchers", $_POST['checkedOrder'])) {
          $watchersChecked2 = "checked";
        }
      }
    ?>
    <div class="form-group" style="margin-bottom: 1rem">
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowPrice" id="sortPriceCheck1" name="checkedOrder[]" <?php echo $priceChecked1 ?> >
          <label class="form-check-label" for="sortPriceCheck1">Sort by Lowest Price</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighPrice" id="sortPriceCheck2" name="checkedOrder[]" <?php echo $priceChecked2 ?> >
          <label class="form-check-label" for="sortPriceCheck2">Sort by Highest Price</label>
        </div>
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowNoBids" id="sortNoBidsCheck1" name="checkedOrder[]" <?php echo $bidsChecked1 ?> >
          <label class="form-check-label" for="sortNoBidsCheck1">Sort by Lowest Number of Bidders</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighNoBids" id="sortNoBidsCheck2" name="checkedOrder[]" <?php echo $bidsChecked2 ?> >
          <label class="form-check-label" for="sortNoBidsCheck2">Sort by Highest Number of Bidders</label>
        </div>
        <hr>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkLowNoWatchers" id="sortNoWatchersCheck1" name="checkedOrder[]" <?php echo $watchersChecked1 ?> >
          <label class="form-check-label" for="sortNoWatchersCheck1">Sort by Lowest Number of Watchers</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" value="checkHighNoWatchers" id="sortNoWatchersCheck2" name="checkedOrder[]" <?php echo $watchersChecked2 ?> >
          <label class="form-check-label" for="sortNoWatchersCheck2">Sort by Highest Number of Watchers</label>
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
      $sqlMaxBid = "SELECT MAX(bidAmount) as maxBid, coalesce(count(bidID), 0) as noOfBidders, auctionID, buyerID FROM Bids GROUP BY auctionID";
      $sqlMainMaxBid = "SELECT mainTable.*, coalesce(maxBid,mainTable.startPrice) as maxBid,  coalesce(noOfBidders,0) as noOfBidders, buyerID as maxBidderID FROM Auctions mainTable LEFT JOIN ($sqlMaxBid) maxBidTable ON mainTable.auctionID = maxBidTable.auctionID";
      $sqlWatchTable = "SELECT COUNT(auctionID) as noOfWatching, auctionID FROM `Watching` GROUP BY auctionID";
      $sqlMainMaxBidWatch = "SELECT mainTable.*, coalesce(noOfWatching,0) as noOfWatching FROM ($sqlMainMaxBid) mainTable LEFT JOIN ($sqlWatchTable) watchTable ON mainTable.auctionID = watchTable.auctionID";
      $sql = "SELECT mainTable.*, categoryName FROM ($sqlMainMaxBidWatch) mainTable INNER JOIN Categories ON mainTable.categoryID = Categories.categoryID";
      if (isset($_POST['checkedCategories'])) {
        $sql .= " WHERE Categories.`categoryID` IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        #print_r($categories);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql .= $categories;
        $sql .= ")";
      }
      if (isset($_POST['checkedStatus'])) {
        $currentTime = new DateTime();
        if (in_array("checkActive", $_POST['checkedStatus'])) {
          $sql .= " AND '2020-11-19 17:55:00' < endDate";
        }
        if (in_array("checkSoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '2020-11-19 17:55:00' > endDate AND (maxBid >= reservePrice AND noOfBidders > 0)";
        }
        if (in_array("checkUnsoldCompleted", $_POST['checkedStatus'])) {
          $sql .= " AND '2020-11-19 17:55:00' > endDate AND (maxBid < reservePrice OR noOfBidders = 0)";
        }
      }
      else {
        $sql .= " AND '2020-11-19 17:55:00' < endDate";
      }
      if (isset($_POST['checkedOrder'])) {
        $sql .= " ORDER BY ";
        if (in_array("checkLowPrice", $_POST['checkedOrder'])) {
          $sql .= "maxBid";
        }
        if(in_array("checkLowNoBids", $_POST['checkedOrder'])) {
          $sql .= "noOfBidders";
        }
        if(in_array("checkLowNoWatchers", $_POST['checkedOrder'])) {
          $sql .= "noOfWatching";
        }
        if (in_array("checkHighPrice", $_POST['checkedOrder'])) {
          $sql .= "maxBid DESC";
        }
        if(in_array("checkHighNoBids", $_POST['checkedOrder'])) {
          $sql .= "noOfBidders DESC";
        }
        if(in_array("checkHighNoWatchers", $_POST['checkedOrder'])) {
          $sql .= "noOfWatching DESC";
        }
      }
      $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
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

        #$result3 = $conn->query($sql3)->fetch_assoc() ?? false;
        $productCurrentPrice = $record['maxBid'];
        #print($productCurrentPrice);
        #print_r($result);
        #$recordBids = mysqli_fetch_assoc($result);
        $end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];


        #Card formatting doesn't change if it has not sold
        $cardStatusFormat = "";
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = ' Auction will end in ' . display_time_remaining($time_to_end) . '';
        } else {
          $productTimeLeft = "Auction Ended";
          if (($productCurrentPrice < $productReservePrice) or ($productBidders == 0))  {
            #Product didnt'sell so format of card will be red
            $cardStatusFormat = "border-danger mb-3";
          }
          else {
            #Product did sell so will become green
            $cardStatusFormat = "border-success mb-3";
          }
        }


        #if ($productCurrentPrice == false) {
        #  $productCurrentPrice = $productStartPrice;
        #}

      ?>
        <div class="card <?php echo $cardStatusFormat?>" style="width: 18rem; margin-left: 0.5%; margin-right: 0.5%; margin-top: 0.5%; margin-bottom: 0.5%;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-body">
            <h5 class="card-title"><?php echo $productTitle ?></h5>
            <p class="card-text">Category: <?php echo $productCategory ?></p>
            <p class="card-text">No of Bidders: <?php echo $productBidders ?></p>
            <p class="card-text">No of Watchers: <?php echo $productWatchers ?></p>
            <p class="card-text"><?php echo $productDescript ?></p>
            <p class="card-text"><?php echo $productTimeLeft ?></p>
            <!-- <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a> -->
          </div>
          <div class="card-footer <?php echo $cardStatusFormat?>">
            <div class="buy d-flex justify-content-between align-items-center">
              <div class="price text-success">
                <h5 class="mt-4">£<?= $productCurrentPrice ?></h5>
              </div>
              <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a>
              <!--<a href="#" class="btn btn-danger mt-3"><i class="fas fa-shopping-cart"></i> View Item</a>-->
            </div>
          </div>
        </div>
      <?php   } ?>
    </div>
  </div>
  </form>


    </body>

    <script>
      function uncheckAll() {
        $("input[type='checkbox']:checked").prop("checked", false)
        $("input[type='radio']:checked").prop("checked", false)
      }
      $('#checkAll').on('click', uncheckAll)

      $(document).ready(function() {
        $('#searchbox').on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $(".card").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });

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
  </div>

  <?php include_once("footer.php") ?>