<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Booking Details</title>
</head>
<body>
<?php
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
if (mysqli_connect_errno()) {
echo "Error:Unable to connect to MySql." . mysqli_connect_error();
exit; //stop processing the page further.
}
//check if id exists
if ($_SERVER["REQUEST_METHOD"] == "GET") {
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
echo "<h2>Invalid booking id</h2>";
exit;
}
}
$query = 'SELECT booking.Booking_ID, room.roomname,
booking.room_review, booking.Checkin_date, booking.Checkout_date, booking.Contact_Number, booking.booking_extras
FROM `booking`
INNER JOIN `room` ON booking.roomID=room.roomID WHERE Booking_ID=' . $id;
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>

<!-- We can add a menu bar here to go back -->
<h1>Booking Details View</h1>
<h2><a href="listbooking.php">[Return to the Bookings listing]</a>
<a href="index.php">[Return to the main page]</a>
</h2>
<?php
if ($rowcount > 0) {
    echo "<fieldset><legend>Booking detail #$id</legend><dl>";
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd><br>" . PHP_EOL;
    echo "<dt>Checkin date: </dt><dd>" . $row['Checkin_date'] . "</dd><br>" . PHP_EOL;
    echo "<dt>Checkout date: </dt><dd>" . $row['Checkout_date'] . "</dd><br>" . PHP_EOL;
    echo "<dt>Contact number: </dt><dd>" . $row['Contact_Number'] . "</dd><br>" . PHP_EOL;
    echo "<dt>Booking extras: </dt><dd>" . $row['booking_extras'] . "</dd>" . PHP_EOL;
    echo "<dt>Room review: </dt><dd>" . $row['room_review'] . "</dd>" . PHP_EOL;
    echo '</dl></fieldset>' . PHP_EOL;
} else {
    echo "<h5>No booking found! Possibly deleted!</h5>";
}

mysqli_free_result($result);
mysqli_close($DBC);
?>
</body>
</html>



