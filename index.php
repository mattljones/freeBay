<?php
// For now, index.php just redirects to browse.php, but you can change this
// if you like.

//header("Location: browse.php");
?>
<?php include_once("header.php") ?>
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
  <div class="col-md-3">
    <h1>Categories</h1>
    <hr>
    <div class="list-group">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <?php
        require_once('private/database_credentials.php');
        $conn = mysqli_connect(host, username, password, database) or die("Connection failed: " . mysqli_connect_error());
        $sql = "SELECT categoryName,categoryID From Categories";
        $result = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        while ($row = mysqli_fetch_array($result)) {
          $category = $row['categoryName'];
          echo '
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                    <input type="checkbox" value="' . $row['categoryID'] . '" name="checkedCategories[]" ' . '>
                        </div>
                    </div>
                    <div class="form-control"> ' . $category . '</div>
                </div>';
        }
        ?>
        <button type="submit" class="btn btn-outline-primary" style="margin-top: 5%;">Apply</button>
      </form>
    </div>


  </div>
  <div class="col-md-9">
    <h1>Our Latest Auctions</h1>
    <hr>
    <div id="productCards" class="row">
      <?php
      include_once("db_connect.php");
      $sql = "SELECT * FROM Auctions JOIN Categories ON Auctions.categoryID = Categories.categoryID";
      if (isset($_POST['checkedCategories'])) {
        $sql .= " WHERE Categories.`categoryID` IN (";
        $categories = implode(',', $_POST['checkedCategories']);
        #print_r($categories);
        $categories = "'" . str_replace(",", "','", $categories) . "'";
        $sql .= $categories;
        $sql .= ")";
      }
      $resultset = mysqli_query($conn, $sql) or die("database error:" . mysqli_error($conn));
      while ($record = mysqli_fetch_assoc($resultset)) {
        $datetime1 = new DateTime($record['endDate']);
        $datetime2 = new DateTime();
        $productID = $record['auctionID'];
        $productTimeLeft = $datetime1->diff($datetime2);
        $productTitle = $record['title'];
        $productCategory = $record['categoryName'];
        $productDescript = $record['descript'];
        $productPrice = $record['startPrice']

      ?>
        <div class="card" style="width: 18rem;">
          <!--<img class="card-img-top" src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($record['image']); ?>" /> -->
          <div class="card-body">
            <h5 class="card-title"><?php echo $productTitle ?></h5>
            <p class="card-text">Category: <?php echo $productCategory ?></p>
            <p class="card-text"><?php echo $productDescript ?><br> Price: <?php echo $productPrice ?></p>
            <p class="card-text">Time Remaining: <br> <?php echo $productTimeLeft->format("%a day(s) %h hr %i min") ?></p>
            <!-- <a href="listing.php?auctionID=<?= $productID ?>" type="submit" class="btn btn-outline-primary text-center">View Item</a> -->
          </div>
          <div class="card-footer">
            <div class="buy d-flex justify-content-between align-items-center">
              <div class="price text-success">
                <h5 class="mt-4">£<?= $productPrice ?></h5>
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