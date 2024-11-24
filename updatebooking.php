<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit booking</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });

            // Initialize datepickers
            $("#Checkin_Date").datepicker();
            $("#Checkout_date").datepicker();

            // Function to parse date from the input value
            function getDate(element) {
                var date;
                try {
                    // Parse the date using the dateFormat set by datepicker
                    date = $.datepicker.parseDate('yy-mm-dd', element.value);
                } catch (error) {
                    date = null; // Return null if parsing fails
                }
                return date;
            }

            // Form submission validation
            $("form").on("submit", function (e) {
                var checkinDate = getDate($("#Checkin_Date")[0]); // Use the getDate function to parse the date
                var checkoutDate = getDate($("#Checkout_date")[0]);

                // Check if the check-in date is later than the check-out date
                if (checkinDate && checkoutDate && checkinDate > checkoutDate) {
                    alert("Check-in date cannot be later than the check-out date.");
                    e.preventDefault(); // Prevent form submission
                }
            });
        });
    </script>
    


</head>
<body>
<?php
include "config.php";
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL." . mysqli_connect_error();
    exit;
}

function cleanInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$result = NULL; // Initialize to avoid "undefined variable" error
$roomsResult = NULL;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) || !is_numeric($id)) {
        echo "<h2>Invalid ticket ID</h2>";
        exit;
    }

    $query = "SELECT booking.Booking_ID, room.roomID, room.roomname, booking.Checkin_Date, 
                    booking.Checkout_date, booking.Contact_Number, booking.room_review, booking.booking_extras
                    FROM booking
                    INNER JOIN room ON booking.roomID = room.roomID WHERE booking.Booking_ID = $id";

    $result = mysqli_query($DBC, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<h2>No booking found with ID $id</h2>";
        exit;
    }
    $roomsQuery = "SELECT roomID, roomname FROM room";
    $roomsResult = mysqli_query($DBC, $roomsQuery);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit']) && $_POST['submit'] == 'Update') {
    $roomID = cleanInput($_POST['room']);
    $checkin = $_POST['Checkin_Date'];
    $checkout = $_POST['Checkout_date'];
    $contactNumber = cleanInput($_POST['Contact_Number']);
    $bookingExtras = cleanInput($_POST['booking_extras']);
    $roomReview = cleanInput($_POST['room_review']);
    $id = cleanInput($_POST['id']);

    $update = "UPDATE booking SET roomID=?, Checkin_Date=?, Checkout_date=?, Contact_Number=?, booking_extras=?, room_review=? WHERE Booking_ID=?";
    $stmt = mysqli_prepare($DBC, $update);
    mysqli_stmt_bind_param($stmt, 'isssssi', $roomID, $checkin, $checkout, $contactNumber, $bookingExtras, $roomReview, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<h2>Booking updated successfully.</h2>";
    } else {
        echo "<h2>Error: Unable to update booking.</h2>";
    }
    mysqli_stmt_close($stmt);

    $formVisible = false;
} else {
    $formVisible = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit a Booking</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $("#depa").datepicker();
            $("#arr").datepicker();
        });
    </script>
</head>
<body>
    <h1>Update ticket</h1>
    <h2>
        <a href="listbooking.php">[Return to the Bookings listing]</a>
        <a href="index.php">[Return to main page]</a>
    </h2>
    <div>
    <?php if ($formVisible && $_SERVER["REQUEST_METHOD"] == "GET") { ?>
    <form action="updatebooking.php" method="POST">
            <p>
                <label for="room">Room:</label>
                <select name="room" id="room" required>
                    <?php
                    if (isset($roomsResult)) {
                        while ($room = mysqli_fetch_assoc($roomsResult)) {
                            $selected = $room['roomID'] == $row['roomID'] ? 'selected' : '';
                            echo "<option value='{$room['roomID']}' $selected>{$room['roomname']}</option>";
                        }
                    }
                    ?>
                </select>
            </p>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <p>
                <label for="Checkin_Date">Check-in Date:</label>
                <input type="text" id="Checkin_Date" name="Checkin_Date" placeholder="yyyy-mm-dd" required value="<?php echo $row['Checkin_Date'] ?? ''; ?>">
                </p>
            <p>
                <label for="Checkout_date">Check-out Date:</label>
                <input type="text" id="Checkout_date" name="Checkout_date" placeholder="yyyy-mm-dd" required value="<?php echo $row['Checkout_date'] ?? ''; ?>">
                </p>
            <p>
                <label for="Contact_Number">Contact Number:</label>
                <input type="text" id="Contact_Number" name="Contact_Number" pattern="\(\d{3}\) \d{3}-\d{4}" placeholder="(###) ###-####" title="Format: (123) 456-7890" required value="<?php echo $row['Contact_Number'] ?? ''; ?>">
                </p>
            <p>
                <label for="booking_extras">Booking Extras:</label>
                <input type="text" id="booking_extras" name="booking_extras" value="<?php echo $row['booking_extras'] ?? ''; ?>">
                </p>
            <p>
                <label for="room_review">Room Review:</label>
                <input type="text" id="room_review" name="room_review" value="<?php echo $row['room_review'] ?? ''; ?>">
                </p>
            <input type="submit" name="submit" value="Update">
            <a href="listbooking.php">[Cancel]</a>
        </form>
    </div>
    <?php } ?>
    <?php
    if ($result) {
        mysqli_free_result($result);
    }
    if (isset($roomsResult)) {
        mysqli_free_result($roomsResult);
    }
    mysqli_close($DBC);
    ?>
</body>
</html>

