<?php include_once("header.php")?>

<?php // Redirecting users who aren't logged in or are logged in as a buyer away from the page
if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
  header('Location: index.php');
}
?>

<div class="container">

<!-- Create auction form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <div class="card">
    <div class="card-body">
      <form method="post" action="create_auction_result.php" onsubmit="return modifysubmit();">

        <!-- Title -->
        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="auctionTitle" name="title" placeholder="e.g. Black mountain bike" pattern=".{10,80}" required="required">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling. Minimum 10 characters.</small>
          </div>
        </div>

        <!-- Details (description) -->
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="auctionDetails" name="Details" rows="4" minlength="10" maxlength="4000" required="required"></textarea>
            <small id="detailsHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Full details of the listing to help bidders decide if they're interested. Minimum 10 characters.</small>
          </div>
        </div>

        <!-- Category -->
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
          <div class="col-sm-10">
            <select class="form-control" id="auctionCategory" name='Category' required="required">
            <option selected value="" style="display:none" disabled>Choose your category</option>
              <?php // Populating category dropdown from freeBay database
                require_once('private/database_credentials.php');
                $connection = mysqli_connect(host, username, password, database);
                if (!$connection) {
                  echo '</select>';
                  echo '<small class="form-text text-muted"><span class="bg-warning text-dark">&nbspError connecting to the database. Please try refreshing the page (you will lose your form data).&nbsp</span></small>';
                }
                else {
                  $query = 'SELECT categoryName FROM Categories ORDER BY categoryName ASC';
                  $result = mysqli_query($connection, $query);
                  if (!$result) {
                    echo '</select>';
                    echo '<small class="form-text text-muted"><span class="bg-warning text-dark">&nbspError collecting category data from the database. Please try refreshing the page (you will lose your form data).&nbsp</span></small>';
                    mysqli_close($connection);
                  }
                  else {
                    while ($row = mysqli_fetch_array($result)) {
                    echo '<option>'.$row['categoryName'].'</option>';
                    };
                    echo '</select>';
                  
                    mysqli_close($connection);
                  }
                }
              ?>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
          </div>
        </div>

        <!-- Starting price -->
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Start price</label>
          <div class="col-sm-10">
	        <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" step="0.01" min="0" class="form-control" id="startingPrice" name="startingPrice" required="required">
            </div>
            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
          </div>
        </div>

          <!-- Choice to enter reserve price (optional) -->
          <div class="form-group row">
            <label for="chooseInputReserve" class="col-sm-2 col-form-label text-right">Add reserve price?</label>
            <div class="col-sm-10">
              <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="chooseInputReserve" id="inputReserve">
                  <label class="form-check-label" for="inputReserve">Yes</label>
                  <small class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional.</i></span></small>
              </div>
            </div>
          </div>

        <!-- Reserve price -->
        <div class="form-group row" id="reserveAttributes">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" step="0.01" min="0" class="form-control" id="ReservePrice" name="ReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Auctions that end below this price will not sell. This value is not displayed in the listing page.</small>
          </div>
        </div>

        <!-- Choice to enter minimum increment (optional) -->
        <div class="form-group row">
          <label for="chooseInputMinInc" class="col-sm-2 col-form-label text-right">Add minimum increment?</label>
          <div class="col-sm-10">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="chooseInputMinInc" id="inputMinInc">
                <label class="form-check-label" for="inputMinInc">Yes</label>
                <small class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional</i></span></small>
            </div>
          </div>
        </div>

        <!-- Minimum increment -->
        <div class="form-group row" id="minIncAttributes">
          <label for="minIncrement" class="col-sm-2 col-form-label text-right">Minimum increment</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" step="0.01" min="0" class="form-control" id="minIncrement" name="minIncrement" onblur="minInput_onblur()">
            </div>
            <small id="minIncrementHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> The minimum amount one bidder must outbid another bidder by.</small>
          </div>
        </div>

        <!-- Start date -->
        <div class="form-group row">
          <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
          <div class="col-sm-4">
            <input type="date" class="form-control" id="Startdate" name="Startdate"  required="required"/>
			<small id="StartDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Auction start date.</small>
          </div>
		  <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start time</label>
          <div class="col-sm-4">
            <input type="time" class="form-control" id="Starttime" name="Starttime"  required="required"/>
			<small class="form-text text-muted"><span class="text-danger">* Required.</span> Auction start time.</small>
          </div>
		 
        </div>

        <!-- End date -->
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-4">
            <input type="date" class="form-control" id="Enddate" name="Enddate" required="required">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Auction end date.</small>
          </div>
		  <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End time</label>
          <div class="col-sm-4">
            <input type="time" class="form-control" id="Endtime" name="Endtime" required="required">
            <small class="form-text text-muted"><span class="text-danger">* Required.</span> Auction end time.</small>
          </div>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary form-control">Create Auction</button>

      </form>
    </div>
  </div>
</div>

</div>

<?php include_once("footer.php")?>

<!-- *** JavaScript *** -->

<script>

function getDT(){
  var myDate = new Date();
  var year = myDate.getFullYear();
  var month = myDate.getMonth()+1;       
  var d = myDate.getDate();
  var h = myDate.getHours(); 
  var m = myDate.getMinutes(); 
  return year+"-"+month+"-"+d+"T"+h+":"+m;
}

</script>

<!-- Script for checking data integrity of dates and prices -->
<script>
  
function modifysubmit() {
  var myDate = new Date();
  var d3 = myDate.toLocaleString();
  var Startdate = document.getElementById("Startdate").value;
  var Starttime = document.getElementById("Starttime").value;
  var Startdatetime = Startdate + 'T' + Starttime; 
  var Enddate = document.getElementById("Enddate").value;
  var Endtime = document.getElementById("Endtime").value;
  var Enddatetime = Enddate + 'T' + Endtime; 
  var startPrice = parseInt(document.getElementById("startingPrice").value);
  var reservePrice = parseInt(document.getElementById("ReservePrice").value);
  var minIncrement = parseInt(document.getElementById("minIncrement").value);
  var tempDate1 = Date.parse(Startdatetime);
  var tempDate2 = Date.parse(Enddatetime);
  var tempNowDate = Date.parse(getDT());

  if (startPrice > reservePrice) {
    document.getElementById("reservePriceHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span><span class='bg-warning text-dark'>&nbspYour reserve price must be greater than your start price.&nbsp</span>";
  }
  else {
    document.getElementById("reservePriceHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span>";
  }
  if (tempDate1 < tempNowDate) {
    document.getElementById("StartDateHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span><span class='bg-warning text-dark'>&nbspYour auction start date cannot be in the past.&nbsp</span>";
  }
  else {
    document.getElementById("StartDateHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span>";
  }
  if (tempDate1 >= tempDate2) {
    document.getElementById("endDateHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span><span class='bg-warning text-dark'>&nbspYour end date must be later than your start date.&nbsp</span>";
  }
  else {
    document.getElementById("endDateHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span>";
  }
  if (startPrice > reservePrice || tempDate1 < tempNowDate || tempDate1 >= tempDate2) {
    return false;
  }
  else {
    return true;
  }
}
</script>

<!-- Script for checking a bidder's minimum increment makes sense compared to their reserve and start prices-->
<script>

function minInput_onblur() {  
   var startingPrice = 0;
   if ($('#startingPrice').val() != '')
   {
      startingPrice = $('#startingPrice').val();
   }
   var ReservePrice = 0;
   if ($('#ReservePrice').val() != '')
   {
      ReservePrice = $('#ReservePrice').val();
   }
   var minIncrement = 0;
   if ($('#minIncrement').val() != '')
   {
      minIncrement = $('#minIncrement').val();
   }
   if ((ReservePrice - startingPrice) <= minIncrement)
   {    
      document.getElementById("minIncrementHelp").innerHTML="<span class='text-danger'>* Required.&nbsp</span><span class='bg-warning text-dark'>&nbspThis seems high compared to your reserve price.&nbsp</span>";
   } 
   else
   {
      document.getElementById("minIncrementHelp").innerHTML="<span class='text-danger'>* Required.</span> The minimum amount one bidder must outbid another bidder by.";
   }
}

 </script>

<!-- Script for hiding/unhiding optional reserve price fields -->
<script>
var box = $('#inputReserve');
var reserveField = $('#reserveAttributes');
reserveField.hide();
box.on('click', function() {
    if($(this).is(':checked')) {
      reserveField.show('fast');
      reserveField.find('input').attr('required', true);
    } else {
      reserveField.hide('fast');
      reserveField.find('input').attr('required', false);
    }
  }
)
</script>

<!-- Script for hiding/unhiding optional minimum increment fields -->
<script>
var box = $('#inputMinInc');
var minIncField = $('#minIncAttributes');
minIncField.hide();
box.on('click', function() {
    if($(this).is(':checked')) {
      minIncField.show('fast');
      minIncField.find('input').attr('required', true);
    } else {
      minIncField.hide('fast');
      minIncField.find('input').attr('required', false);
    }
  }
)
</script>