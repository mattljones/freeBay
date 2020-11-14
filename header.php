<!doctype html>

<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!-- Bootstrap, FontAwesome CSS & jQuery -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">

  <link rel="icon" href="assets/favicon.png" type="image/x-icon"/>
  <title>freeBay</title>

  <?php session_start(); ?> <!-- Accessing session variables -->

</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-white mx-2">
  <a class="navbar-brand" href="index.php"><img src="assets/logo.png" class="img-fluid" height="30%" width="30%"></a>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
    
<?php
  // Displays either login or logout on the right, depending on user's
  // current status (session).
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    echo '<a class="nav-link text-info" href="logout.php"><b>Logout</b></a>';
  }
  else {
    echo '<button type="button" class="btn nav-link text-success" data-toggle="modal" data-target="#loginModal"><b>Login</b></button>';
    echo '<a class="nav-link" href="register.php"><i>Register</i></a>';
  }
?>
    </li>
  </ul>
</nav>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <ul class="navbar-nav align-middle">
<?php
  if (!isset($_SESSION['account_type'])) {
    echo('
  <li class="nav-item mx-1">
      <a class="nav-link" href="index.php">Home</a>
    </li>');
    }
  elseif (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
  echo('
  <li class="nav-item mx-1">
      <a class="nav-link text-white"><b>Logged in as:</b><i>&nbsp&nbsp'.$_SESSION['username'].'</i></a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="index.php">Home</a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="mybids.php">My Bids</a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="mywatchlist.php">My Watchlist</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="recommendations.php">Recommended</a>
    </li>');
  }
  elseif (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'seller') {
  echo('
  <li class="nav-item mx-1">
      <a class="nav-link text-white"><b>Logged in as:</b><i>&nbsp&nbsp'.$_SESSION['username'].'</i></a>
    </li>
  <li class="nav-item mx-1">
      <a class="nav-link" href="index.php">Home</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="mylistings.php">My Listings</a>
    </li>
	<li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>');
  }
?>
  </ul>
</nav>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form method="POST" action="login_result.php">
          <div class="form-group">
            <label for="user_name">Username</label>
            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Username">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-primary form-control">Sign in</button>
        </form>
      </div>

    </div>
  </div>
</div> <!-- End modal -->