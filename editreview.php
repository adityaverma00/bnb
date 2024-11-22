<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Seat Options</title>
</head>
<body>
<?php
//take the details about server and database
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
exit; //stop processing the page further
}
//function to clean input but not validate type and content
function cleanInput($data)
{
return htmlspecialchars(stripslashes(trim($data)));
}
//check if id exists
if ($_SERVER["REQUEST_METHOD"] == "GET") {
$id = $_GET['id'];
if (empty($id) or !is_numeric($id)) {
    echo "<h2>Invalid booking ID</h2>";exit;
}
}
//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {
$review = cleanInput($_POST['room_review']);
$id = cleanInput($_POST['id']);
$upd = "UPDATE `booking` SET room_review=? WHERE Booking_ID=?"; 
    $stmt = mysqli_prepare($DBC, $upd); // Prepare the query
    mysqli_stmt_bind_param($stmt, 'si', $review, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
//print message
echo "<h5>Review updated successfully.</h5>";
$formVisible = false;
}
$query = 'SELECT room_review FROM `booking` WHERE Booking_ID=' . $id; 
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);
?>
<h1>Edit/add room review</h1>
<h2>
<a href='listbooking.php'>[Return to the Bookings listing]</a>
<a href="index.php">[Return to main page]</a>
</h2>
<h2>Review made by Test
</h2>

<div>
<div>
<form method="POST">
        <div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        </div>
        <?php
        if ($rowcount > 0) {
            $row = mysqli_fetch_assoc($result);
        ?>
        <div>
            <label for="room_review">Room Review:</label>
            <input type="text" id="room_review" name="room_review" value="<?php echo $row['room_review']; ?>">
        </div>
        <?php
        } else {
            echo "<h5>No booking found!</h5>"; 
        }
        ?>
        <br><br>
        <div>
            <input type="submit" name="submit" value="Update"> 
        </div>
    </form>
<?php
mysqli_free_result($result);
mysqli_close($DBC);
?>
</body>
</html>