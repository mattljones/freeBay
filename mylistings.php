<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<div class="col-md-9">
    <h1>My listings</h1>
    <hr>
    <div id="productCards" class="row">

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up their auctions.
  
  // TODO: Loop through results and print them out as list items.
   $loginIn= $_SESSION['logged_in'];
   $username=  $_SESSION['username'];
   $sellerID=$_SESSION['userID']
?>

<div class="row" style="margin:0;">
  <div class="col-md-3">
    <h1>Categories</h1>
    <hr>
    <div class="list-group">
      <div id="categories-filter">
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
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
        </form>
        <button id="checkAll" class="btn btn-outline-primary" style="margin-top: 5%;">Reset filters</button>
      </div>
    </div>


  </div>
  <div class="col-md-9">
   
    <hr>
    <div id="productCards" class="row">
      <?php
      $sql = "SELECT * FROM Auctions JOIN Categories ON Auctions.categoryID = Categories.categoryID";
      if (isset($_POST['checkedCategories'])) {
        $sql .= " WHERE Categories.`categoryID` IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        #print_r($categories);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql .= $categories;
        $sql .= ") AND sellerID=$sellerID";
      }
      else {
        $sql .= " WHERE sellerID=$sellerID";
      }
      $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
      while ($record = mysqli_fetch_assoc($resultset)) {
        $productAuctionID = $record['auctionID'];;
        $sql2 = "SELECT max(bidAmount) as currentPrice, count(bidID) FROM Bids WHERE auctionID = $productAuctionID ";
        $sql3 = "SELECT count(auctionID) as noOfWatchers FROM Watching WHERE auctionID = $productAuctionID ";
        $result = $conn->query($sql2)->fetch_assoc() ?? false;
        $result3 = $conn->query($sql3)->fetch_assoc() ?? false;
        $productCurrentPrice = $result['currentPrice'];
        #print($productCurrentPrice);
        #print_r($result);
        #$recordBids = mysqli_fetch_assoc($result);
        $end_time = new DateTime($record['endDate']);
        $now = new DateTime();
        $productID = $record['auctionID'];
        if ($now < $end_time) {
          $time_to_end = date_diff($now, $end_time);
          $productTimeLeft = ' Auction will end in ' . display_time_remaining($time_to_end) . '';
        }
        else {
          $productTimeLeft = "Auction Ended";
        }
        $productTitle = $record['title'];
        $productCategory = $record['categoryName'];
        $productDescript = $record['descript'];
        $productStartPrice = $record['startPrice'];
        $productReservePrice = $record['reservePrice'];
        $productBidders = $result['count(bidID)'];
        $productWatchers = $result3['noOfWatchers'];

        if ($productCurrentPrice == false) {
          $productCurrentPrice = $productStartPrice;
        }

      ?>
        <div class="card" style="width: 18rem;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-body">
            <h5 class="card-title"><?php echo $productTitle ?></h5>
            <p class="card-text">Category: <?php echo $productCategory ?></p>
            <p class="card-text">No of Bidders: <?php echo $productBidders ?></p>
            <p class="card-text">No of Watchers: <?php echo $productWatchers ?></p>
            <p class="card-text"><?php echo $productDescript ?><br> Start Price: <?php echo $productStartPrice ?></p>
            <p class="card-text"><?php echo $productTimeLeft?></p>
            <!-- <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a> -->
          </div>
          <div class="card-footer">
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
  </body>

  <script>
    function uncheckAll() {
      $("input[type='checkbox']:checked").prop("checked", false)
      <?php 
        $_POST['checkedCategories'] = array();
      ?>
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
<?php include_once("footer.php")?>