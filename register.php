<?php include_once("header.php")?> 

<div class="container">
<h2 class="my-3">Register a new account</h2>

<!-- Registration form -->
<form method="POST" action="process_registration.php" onsubmit="return checkUniqueSubmit()"> <!-- Check username is unique before form submission -->

  <!-- Account type -->
  <div class="form-group row">
    <label class="col-sm-2 col-form-label text-right">Register as a:</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" checked>
          <label class="form-check-label" for="accountBuyer">Buyer</label>
      </div>
      <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller">
          <label class="form-check-label" for="accountSeller">Seller</label>
      </div>
      <small class="form-text-inline text-muted"><span class="text-danger">* Required</span></small>
    </div>
  </div>

  <!-- Username -->
  <div class="form-group row">
    <label for="username" class="col-sm-2 col-form-label text-right">Username</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" name="username" id="username" placeholder="Enter a username (6 to 20 characters)" pattern=".{6,20}" title="Usernames should be between 6 and 20 characters long." onblur="checkUniqueLive()" required> <!-- Check username is unique in real time -->
      <small class="form-text text-muted"><span class="text-danger" id="usernameHelpText">* Required</span></small>
	  </div>
  </div>

  <!-- Email -->
  <div class="form-group row">
    <label for="emailReg" class="col-sm-2 col-form-label text-right">Email</label>
	  <div class="col-sm-10">
      <input type="email" class="form-control" name="emailReg" id="emailReg" placeholder="Enter your email address" required>
      <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div> 

  <!-- First Name -->
  <div class="form-group row">
    <label for="firstName" class="col-sm-2 col-form-label text-right">First Name</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" name="firstName" id="firstName" placeholder="Enter your first name" pattern=".{1,35}" title="Names should be no longer than 35 characters." required>
      <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div>

  <!-- Family Name -->
  <div class="form-group row">
    <label for="familyName" class="col-sm-2 col-form-label text-right">Family Name</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" name="familyName" id="familyName" placeholder="Enter your family name" pattern=".{1,35}" title="Names should be no longer than 35 characters." required>
      <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div>

  <!-- Password -->
  <div class="form-group row">
    <label for="passwordReg" class="col-sm-2 col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" name="passwordReg" id="passwordReg" placeholder="Enter a password (at least 6 characters)" pattern=".{6,}" onkeyup="checkPassword()" title="Passwords must be at least 6 characters long." onkeyup="checkPassword()" required>
      <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
    </div>
  </div>

  <!-- Password Confirmation -->
  <div class="form-group row">
    <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Verify password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="passwordConfirmation" placeholder="Re-enter your password" onkeyup="checkPassword()" required> <!-- Checks passwords match in real time & disables submission button if they don't -->
      <small class="form-text text-muted"><span id="passwordConfirmationHelpText" class="text-danger">* Required</span></small>
    </div>
  </div>  

  <!-- Choice to enter address (optional) -->
  <div class="form-group row">
    <label for="chooseInputAddress" class="col-sm-2 col-form-label text-right">Add an address?</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="chooseInputAddress" id="inputAddress">
          <label class="form-check-label" for="inputAddress">Yes</label>
          <small class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional</i></span></small>
      </div>
    </div>
  </div>

  <div id="addressAttributes"> <!-- Group all address fields for JS to hide/unhide en masse -->

    <!-- Line 1 -->
    <div class="form-group row">
      <label for="line1" class="col-sm-2 col-form-label text-right">Line 1</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="line1" id="line1" placeholder="Enter the first line of your address" pattern=".{1,35}" title="This should be no longer than 35 characters.">
        <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <!-- City -->
    <div class="form-group row">
      <label for="city" class="col-sm-2 col-form-label text-right">City</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="city" id="city" placeholder="Enter your city" pattern=".{1,35}" title="This should be no longer than 35 characters.">
        <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <!-- Postcode / ZIP Code -->
    <div class="form-group row">
      <label for="postcode" class="col-sm-2 col-form-label text-right">Postcode / ZIP Code</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="postcode" id="postcode" placeholder="Enter your postcode or ZIP code" pattern=".{1,35}" title="This should be no longer than 35 characters.">
        <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <!-- Country -->
    <div class="form-group row">
      <label for="country" class="col-sm-2 col-form-label text-right">Country</label>
      <div class="col-sm-10">
        <select class="form-control" name="country" id="country">
          <option selected value="" style="display:none" disabled>Choose your country</option>
          <?php // Populating country dropdown from freeBay database
            require_once('private/database_credentials.php');
            $connection = mysqli_connect(host, username, password, database);
            if (!$connection) {
              echo '</select>';
              echo '<small class="form-text text-muted"><span class="bg-warning text-dark">&nbspError connecting to the database. Please try refreshing the page (you will lose your form data) or add an address after registration.&nbsp</span></small>';
            }
            else {
              $query = 'SELECT countryName FROM Countries ORDER BY countryName ASC';
              $result = mysqli_query($connection, $query);
              if (!$result) {
                echo '</select>';
                echo '<small class="form-text text-muted"><span class="bg-warning text-dark">&nbspError collecting country data from the database. Please try refreshing the page (you will lose your form data) or add an address after registration.&nbsp</span></small>';
                mysqli_close($connection);
              }
              else {
                while ($row = mysqli_fetch_array($result)) {
                echo '<option>'.$row['countryName'].'</option>';
                };
                echo '</select>';
                echo '<small class="form-text text-muted"><span class="text-danger">* Required</span></small>';
                mysqli_close($connection);
              }
            }
          ?>
      </div>
    </div>

  </div>

  <!-- Choice to enter telephone number (optional) -->
  <div class="form-group row">
    <label for="chooseInputTelNo" class="col-sm-2 col-form-label text-right">Add a phone number?</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="chooseInputTelNo" id="chooseInputTelNo">
          <label class="form-check-label" for="chooseInputTelNo">Yes</label>
          <small class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional</i></span></small>
      </div>
    </div>
  </div>

  <!-- Telephone number -->
  <div class="form-group row" id="telNoAttributes">
    <label for="telephoneNumber" class="col-sm-2 col-form-label text-right">Phone number</label>
    <div class="col-sm-10">
      <input type="tel" class="form-control" name="telephoneNumber" id="telephoneNumber" placeholder="Enter your number incl. country code (i.e. +123456789)" pattern="^\+{1}\d{8,15}" title="Your number should begin with a '+' and be followed by 8 to 15 digits.">
      <small class="form-text text-muted"><span class="text-danger">* Required</span></small>
      <br>
    </div>
  </div>

  <!-- Submit button -->
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control" id="submitButton" disabled>Register</button>
  </div>

</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a></div><br>

</div>

<?php include_once("footer.php")?>

<!-- *** JavaScript *** -->

<!-- Script (AJAX) incl. 2 functions for checking username is unique -->
<script>

function checkUniqueLive() { 
  var username = $('#username').val();
  var username_info = $('#usernameHelpText');
  var outcome = '';
  $.ajax({ 
    url: "php/check_username.php", 
    async: false,
    data: {"username": username},
    type: "POST",
    success: function (data) {
      if (data == 'unique') {
        outcome = 'unique';
      }
      else if (data == 'not_unique') {
        outcome = 'not_unique';
      }
      else if (data == 'failure') {
        outcome = 'failure';
      }
    }
  });
  if (outcome == 'unique') {
    username_info.html('* Required &nbsp &nbsp</span><span class="bg-success text-white">&nbspUnique&nbsp</span>');
    return outcome;
  }
  else if (outcome == 'not_unique') {
    username_info.html('* Required &nbsp &nbsp</span><span class="bg-warning text-dark">&nbspUsername already in use&nbsp</span>');
    return outcome;
  }
  else if (outcome == 'failure') {
    username_info.html('* Required &nbsp &nbsp</span><span class="bg-warning text-dark">&nbspError connecting to the database. If this issue persists, please refresh the page.&nbsp</span>');
    return outcome;
  }
}

function checkUniqueSubmit() { 
  var username = $('#username').val();
  var outcome = checkUniqueLive(username);
  if (outcome == 'unique') {
    return true;
  }
  else if (outcome == 'not_unique') {
    alert('That username is already in use.\nPlease pick another.');
    return false;
  }
  else if (outcome == 'failure') {
    alert('There was an error connecting to the database.\nIf this issue persists, please refresh the page.');
    return false;
  }
}

</script>

<!-- Script for checking passwords match -->
<script>
function checkPassword() {
  var first_password = $('#passwordReg').val();
  var second_password = $('#passwordConfirmation').val();
  var password_info = $('#passwordConfirmationHelpText');
  var button = $('#submitButton');
  if (first_password.length < 6 || second_password.length < 6){
    password_info.html('* Required');
  }
  else if (first_password !== second_password) {
    button.prop('disabled', true);
    password_info.html('* Required &nbsp &nbsp</span><span class="bg-warning text-dark">&nbspPasswords do not match&nbsp</span>');
  }
  else {
    button.prop('disabled', false);
    password_info.html('* Required &nbsp &nbsp</span><span class="bg-success text-white">&nbspPasswords match&nbsp</span>');
  }
}
</script>

<!-- Script for hiding/unhiding optional address fields -->
<script>
var box = $('#inputAddress');
var addressField = $('#addressAttributes');
addressField.hide();
box.on('click', function() {
    if($(this).is(':checked')) {
      addressField.show('fast');
      addressField.find('input').attr('required', true);
      addressField.find('select').attr('required', true);
    } else {
      addressField.hide('fast');
      addressField.find('input').attr('required', false);
      addressField.find('select').attr('required', false);
    }
  }
)
</script>

<!-- Script for hiding/unhiding optional telephone number fields -->
<script>
var box = $('#chooseInputTelNo');
var telNoField = $('#telNoAttributes');
telNoField.hide();
box.on('click', function() {
    if($(this).is(':checked')) {
      telNoField.show('fast');
      telNoField.find('input').attr('required', true);
    } else {
      telNoField.hide('fast');
      telNoField.find('input').attr('required', false);
    }
  }
)
</script>