<?php 
include_once("header.php")?>

<?php
/* (Uncomment this block to redirect people without selling privileges away from this page)
  // If user is not logged in or not a seller, they should not be able to
  // use this page.
  if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] != 'seller') {
    header('Location: browse.php');
  }
*/
?>

<div class="container">

<!-- Create auction form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Create new auction</h2>
  <div class="card">
    <div class="card-body">
      <!-- Note: This form does not do any dynamic / client-side / 
      JavaScript-based validation of data. It only performs checking after 
      the form has been submitted, and only allows users to try once. You 
      can make this fancier using JavaScript to alert users of invalid data
      before they try to send it, but that kind of functionality should be
      extremely low-priority / only done after all database functions are
      complete. -->
      <form method="post" action="create_auction_result.php" onsubmit="return modifysubmit();">
        <div class="form-group row">
          <label for="auctionTitle" class="col-sm-2 col-form-label text-right">Title of auction</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="auctionTitle" name="title" placeholder="e.g. Black mountain bike" required="required">
            <small id="titleHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> A short description of the item you're selling, which will display in listings.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionDetails" class="col-sm-2 col-form-label text-right">Details</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="auctionDetails" name="Details" rows="4" required="required"></textarea>
            <small id="detailsHelp" class="form-text text-muted"><span class="text-danger">* Required.</span>Full details of the listing to help bidders decide if it's what they're looking for.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionCategory" class="col-sm-2 col-form-label text-right">Category</label>
          <div class="col-sm-10">
            <select class="form-control" id="auctionCategory" name='Category' required="required">
              <option selected><option value="Collectables & antiques">Collectables & antiques</option>
          
              <option value="Electronics">Electronics</option>
              <option value="Fashion">Fashion</option>
              <option value="Home & garden">Home & garden</option>
              <option value="Jewellery & watches">Jewellery & watches</option>
              <option value="Motors">Motors</option>
              <option value="Sporting goods">Sporting goods</option>
              <option value="Toys & games">Toys & games</option>
              <option value="Books, comics & magazines">Books, comics & magazines</option>
              <option value="Health & beauty">Health & beauty</option>
              <option value="Musical instruments">Musical instruments</option>
              <option value="Business, office & industrial">Business, office & industrial</option>
            </select>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionStartPrice" class="col-sm-2 col-form-label text-right">Starting price</label>
          <div class="col-sm-10">
	        <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="startingPrice" name="startingPrice" required="required">
            </div>
            <small id="startBidHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Initial bid amount.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionReservePrice" class="col-sm-2 col-form-label text-right">Reserve price</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="ReservePrice" name="ReservePrice">
            </div>
            <small id="reservePriceHelp" class="form-text text-muted">Optional. Auctions that end below this price will not go through. This value is not displayed in the auction listing.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="minIncrement" class="col-sm-2 col-form-label text-right">Minimum increment</label>
          <div class="col-sm-10">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">£</span>
              </div>
              <input type="number" class="form-control" id="minIncrement" name="minIncrement" onblur="minInput_onblur();">
            </div>
            <small id="minIncrementHelp" class="form-text text-muted">Optional.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionStartDate" class="col-sm-2 col-form-label text-right">Start date</label>
          <div class="col-sm-10">
            <!-- <input type="datetime-local" class="form-control" id="Startdate" name="Startdate"  value="
              =(date('Y-m-d')."T".date('H:i'))
              "> -->
            <input type="datetime-local" class="form-control" id="Startdate" name="Startdate"  required="required"/>
            <small id="StartDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span>Day for the auction to Start.</small>
          </div>
        </div>
        <div class="form-group row">
          <label for="auctionEndDate" class="col-sm-2 col-form-label text-right">End date</label>
          <div class="col-sm-10">
            <input type="datetime-local" class="form-control" id="Enddate" name="Enddate" required="required">
            <small id="endDateHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Day for the auction to end.</small>
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control">Create Auction</button>
      </form>
    </div>
  </div>
</div>

</div>


<?php include_once("footer.php")?>

<script>
 document.getElementById('Startdate').value=getDT();
 console.log(getDT());

function getDT(){
    var myDate=new Date();
  var year=myDate.getFullYear();
  var month= myDate.getMonth()+1;       
  var d= myDate.getDate();
  var h=myDate.getHours(); 
  var m=myDate.getMinutes(); 
  return year+"-"+month+"-"+d+"T"+h+":"+m;
}

  

function modifysubmit(){
    var myDate = new Date();

    var d3=myDate.toLocaleString();
   
    var Startdate= document.getElementById("Startdate").value;
    var Enddate= document.getElementById("Enddate").value;
   var startPrice= parseInt(document.getElementById("startingPrice").value);
   var reservePrice= parseInt(document.getElementById("ReservePrice").value);
   var minIncrement= parseInt(document.getElementById("minIncrement").value);
   var category= parseInt(document.getElementById("auctionCategory").value);
    
    var tempDate1=Date.parse(Startdate);
    var tempDate2=Date.parse(Enddate);

    var tempNowDate=Date.parse(getDT());
    
    if(category==""){
      document.getElementById("categoryHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
     document.getElementById("categoryHelp").innerHTML="";
   }
   if(0>= startPrice){
      document.getElementById("startBidHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
     document.getElementById("startBidHelp").innerHTML="";
   }
   if(0>= reservePrice){
      document.getElementById("reservePriceHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
     document.getElementById("reservePriceHelp").innerHTML="";
   }
   if(startPrice>reservePrice){
      document.getElementById("reservePriceHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
     document.getElementById("reservePriceHelp").innerHTML="";
   }
   if(0>= minIncrement){
      document.getElementById("minIncrementHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
     document.getElementById("minIncrementHelp").innerHTML="";
   }
 if(tempDate1<tempNowDate){
      console.log('err2');
      document.getElementById("StartDateHelp").innerHTML="<font color ='red'>Please enter a valid date</font>";
      return false;
   }else{
      document.getElementById("StartDateHelp").innerHTML="";
   }

   if(tempDate1>= tempDate2){
      console.log('err3');
      document.getElementById("endDateHelp").innerHTML="<font color ='red'>Please enter a valid value</font>";
      return false;
   }else{
      document.getElementById("endDateHelp").innerHTML="";
   }


  return true;
}

function minInput_onblur(){
   var startingPrice=0;
   if(document.getElementById("startingPrice").value!='')
   {
      startingPrice= parseInt(document.getElementById("startingPrice").value);
   }
   var ReservePrice=0;
   if(document.getElementById("ReservePrice").value!='')
   {
      ReservePrice= parseInt(document.getElementById("ReservePrice").value);
   }
   var minIncrement=0;
   if(document.getElementById("minIncrement").value!='')
   {
      minIncrement= parseInt(document.getElementById("minIncrement").value);
   }
  
   console.log(startingPrice);
   if((ReservePrice-startingPrice)<minIncrement)
   {
     document.getElementById("minIncrementHelp").innerHTML="<font color ='coral'>Maybe too high</font>";
   }else{
    document.getElementById("minIncrementHelp").innerHTML=""
   }
}

 </script>