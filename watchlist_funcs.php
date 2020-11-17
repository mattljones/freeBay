<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
// Check if the data was posted correctly
if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	$buyer_id = $_SESSION['userID'];
} else {
  return;
}

// Create connection
require_once('private/database_credentials.php');
$conn = mysqli_connect(host, username, password, database);
// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Extract arguments from the POST variable:
$post_array = $_POST['arguments'];
$auction_id = $post_array[0];

if ($_POST['functionname'] == "add_to_watchlist") {
  // Update database and return success/failure.
  $sql = "INSERT INTO Watching (auctionID, buyerID) VALUES ('$auction_id', '$buyer_id')";
  if ($conn->query($sql) === TRUE) {
  	$res = "success";
  } else {
	$res = "failure";
  }
  $conn->close();
  
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // Update database and return success/failure.
  $sql = "DELETE FROM Watching WHERE auctionID = $auction_id AND buyerID = $buyer_id;";
  if ($conn->query($sql) === TRUE) {
  	$res = "success";
  } else {
	$res = "failure";
  }
  $conn->close();
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>