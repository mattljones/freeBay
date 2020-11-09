<?php include_once("header.php")?>

<div class="container">
<h2 class="my-3">Register new account</h2>

<!-- Create user form -->
<form id="userDetails" method="POST" action="process_registration.php">

  <div class="form-group row">
    <label for="accountType" class="col-sm-2 col-form-label text-right">Register as a:</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountBuyer" value="buyer" checked>
          <label class="form-check-label" for="accountBuyer">Buyer</label>
      </div>
      <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="accountType" id="accountSeller" value="seller">
          <label class="form-check-label" for="accountSeller">Seller</label>
      </div>
      <small id="accountTypeHelp" class="form-text-inline text-muted"><span class="text-danger">* Required</span></small>
    </div>
  </div>

  <div class="form-group row">
    <label for="username" class="col-sm-2 col-form-label text-right">Username</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" id="username" placeholder="Enter a username (must be unique)" pattern=".{6,20}" title="Usernames should be between 6 and 20 characters long." required>
      <small id="usernameHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div>

  <div class="form-group row">
    <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
	  <div class="col-sm-10">
      <input type="email" class="form-control" id="emailReg" placeholder="Enter your email address" required>
      <small id="emailHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div> 

  <div class="form-group row">
    <label for="firstName" class="col-sm-2 col-form-label text-right">First Name</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" id="firstName" placeholder="Enter your first name" pattern=".{,35}" title="Names should be no longer than 35 characters." required>
      <small id="firstNameHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div>

  <div class="form-group row">
    <label for="familyName" class="col-sm-2 col-form-label text-right">Family Name</label>
	  <div class="col-sm-10">
      <input type="text" class="form-control" id="familyName" placeholder="Enter your family name" pattern=".{,35}" title="Names should be no longer than 35 characters." required>
      <small id="familyNameHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
	  </div>
  </div>

  <div class="form-group row">
    <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="passwordReg" placeholder="Enter a password" pattern=".{6,}" title="Passwords must be at least 6 characters long." required>
      <small id="passwordHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
    </div>
  </div>

  <div class="form-group row">
    <label for="passwordConfirmation" class="col-sm-2 col-form-label text-right">Verify password</label>
    <div class="col-sm-10">
      <input type="password" class="form-control" id="passwordConfirmation" placeholder="Re-enter your password" required>
      <small id="passwordConfirmationHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
    </div>
  </div>

  <div class="form-group row">
    <label for="chooseInputAddress" class="col-sm-2 col-form-label text-right">Add an address?</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="chooseInputAddress" id="inputAddress">
          <label class="form-check-label" for="inputAddress">Yes</label>
          <small id="line1Help" class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional</i></span></small>
      </div>
    </div>
  </div>

  <div id="addressAttributes">

    <div class="form-group row">
      <label for="line1" class="col-sm-2 col-form-label text-right">Line 1</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="line1" placeholder="Enter the first line of your address" pattern=".{,35}" title="This should be no longer than 35 characters.">
        <small id="line1Help" class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <div class="form-group row">
      <label for="city" class="col-sm-2 col-form-label text-right">City</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="city" placeholder="Enter your city" pattern=".{,35}" title="This should be no longer than 35 characters.">
        <small id="cityHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <div class="form-group row">
      <label for="postcode" class="col-sm-2 col-form-label text-right">Postcode / ZIP Code</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="postcode" placeholder="Enter your postcode or ZIP code" pattern=".{,35}" title="This should be no longer than 35 characters.">
        <small id="postcodeHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

    <div class="form-group row">
      <label for="country" class="col-sm-2 col-form-label text-right">Country</label>
      <div class="col-sm-10">
        <select class="form-control" id="country">
          <option selected>Choose...</option>
          <option value="fill">Fill me in</option>
          <option value="with">with options</option>
          <option value="populated">populated from a database?</option>
        </select>
        <small id="countryHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

  </div>

  <div class="form-group row">
    <label for="chooseInputTelNo" class="col-sm-2 col-form-label text-right">Add a phone number?</label>
  	<div class="col-sm-10">
	    <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="chooseInputTelNo" id="inputTelNo">
          <label class="form-check-label" for="inputTelNo">Yes</label>
          <small id="line1Help" class="form-text text-muted"><span class="text-muted"><i>&nbsp Optional</i></span></small>
      </div>
    </div>
  </div>

  <div id="telNoAttributes">

    <div class="form-group row">
      <label for="telephoneNumber" class="col-sm-2 col-form-label text-right">Phone number</label>
      <div class="col-sm-10">
        <input type="tel" class="form-control" id="telephoneNumber" placeholder="Enter your telephone number" pattern="^\+?\d{9,15}" title="A valid international phone number is between 9 and 15 digits long.">
        <small id="telNoHelp" class="form-text text-muted"><span class="text-danger">* Required</span></small>
      </div>
    </div>

  </div>

  <br>
  <div class="form-group row">
    <button type="submit" class="btn btn-primary form-control">Register</button>
  </div>

</form>

<div class="text-center">Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a></div>

</div>

<?php include_once("footer.php")?>

<!-- Script for hiding/unhiding optional address fields -->

<script>
var box = $('#inputAddress'),
    addressField = $('#addressAttributes');

addressField.hide();

box.on('click', function() {
    if($(this).is(':checked')) {
      addressField.show('fast');
      addressField.find('input').attr('required', true);
    } else {
      addressField.hide('fast');
      addressField.find('input').attr('required', false);
    }
})
</script>

<!-- Script for hiding/unhiding optional telephone number fields -->

<script>
var box = $('#inputTelNo'),
    telNoField = $('#telNoAttributes');

telNoField.hide();

box.on('click', function() {
    if($(this).is(':checked')) {
      telNoField.show('fast');
      telNoField.find('input').attr('required', true);
    } else {
      telNoField.hide('fast');
      telNoField.find('input').attr('required', false);
    }
})
</script>