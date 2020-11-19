<?php 

var_dump($_POST);

require_once('../private/database_credentials.php');
$connection = mysqli_connect(host, username, password, database);

// Checks whether bid is valid
function check_bid($conn, $bid, $auctionID) {
    if (!$conn) {
        echo 'failure';
        die();
    }
    else {
        $query = "SELECT bid_check('$bid', '$auctionID')"; // using bid_check() function stored in the database
        $result = mysqli_query($conn, $query);
        $outcome = mysqli_fetch_array($result)[0];
		if ($outcome == 'valid') {
			echo 'valid';
			mysqli_close($conn);
			die();
		}
		else {
			echo 'invalid';
			mysqli_close($conn);
			die();
		}
    }
}

check_bid($connection, $_POST['bid'], $_POST['auctionID']);

?>