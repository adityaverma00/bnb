<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Making a Booking</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <script>
        $(document).ready(function() {
            $.datepicker.setDefaults({
                dateFormat: 'yy-mm-dd'
            });
            $(function() {
                depa = $("#depa").datepicker()
                arr = $("#arr").datepicker()

                function getDate(element) {
                    var date;
                    try {
                        date = $.datepicker.parseDate(dateFormat, element.value);
                    } catch (error) {
                        date = null;
                    }
                    return date;
                }
            });
        });
        
    </script>
</head>
<body>
    
<?php
include "checksession.php";
checkUser();
loginStatus(); 
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);



if (mysqli_connect_errno()) {
  echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
  exit; //stop processing the page further
}


//function to clean input but not validate type and content
function cleanInput($data)
{
  return htmlspecialchars(stripslashes(trim($data)));
}


//on submit check if empty or not string and is submited by POST
if (isset($_POST['submit']) && !empty($_POST['submit']) && ($_POST['submit'] == 'Book')) {
    $room = cleanInput($_POST['rooms']); 
    $customer = cleanInput($_POST['customers']); 
    $checkin = $_POST['depa']; 
    $checkout = $_POST['arr']; 
    $extras = cleanInput($_POST['booking_extras']); 
    $contact_number = cleanInput($_POST['contact_number']);

    $error = 0; 
    $msg = "Error:";

    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
// for the date range 
    $query = "SELECT * FROM booking 
    WHERE roomID = ? 
    AND NOT (checkout_Date < ? OR checkin_Date > ?)";
    $stmt = mysqli_prepare($DBC, $query);
    mysqli_stmt_bind_param($stmt, 'iss', $room, $checkin, $checkout);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result->num_rows > 0) {
        $error++;
        $msg .= " The selected room is already booked for the specified date range.";
    }

    if ($checkinDate >= $checkoutDate) {
        $error++;
        $msg .= " Check-out date must be later than Check-in date.";
        $checkout = ''; 
    }

    if ($error == 0) {
        $query = "INSERT INTO booking (roomID, customerID, checkin_Date, checkout_Date, booking_extras, Contact_Number) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($DBC, $query);
        mysqli_stmt_bind_param($stmt, 'iissss', $room, $customer, $checkin, $checkout, $extras, $contact_number);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<h5>Booking added successfully.</h5>";
    } else {
        echo "<h5>$msg</h5>" . PHP_EOL;
    }
}




$query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
$result = mysqli_query($DBC, $query);
$rowcount = mysqli_num_rows($result);

$query1 = 'SELECT customerID, firstname, lastname FROM customer ORDER BY customerID';
$result1 = mysqli_query($DBC, $query1);
$rowcount1 = mysqli_num_rows($result1);
?>
<h1>Make a Booking</h1>
    <h2>
        <a href='listbooking.php'>[Return to the Bookings listing]</a>
        <a href="index.php">[Return to main page]</a>
    </h2>
    <h3>Booking for test</h3>
    <div>

    <form method="POST">
        <div>
            <label for="rooms">Room:</label>
            <select name="rooms" id="rooms" required>
                <?php
                if ($rowcount > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['roomID']}'>" .
                            "{$row['roomname']} ({$row['roomtype']} - {$row['beds']} beds)</option>";
                    }
                } else {
                    echo "<option>No rooms found</option>";
                }
                mysqli_free_result($result);
                ?>
            </select>
        </div>

        <br>
        <div>
            <label for="customers">Customer:</label>
            <select name="customers" id="customers" required>
                <?php
                if ($rowcount1 > 0) {
                    while ($row = mysqli_fetch_assoc($result1)) {
                        echo "<option value='{$row['customerID']}'>" .
                            "{$row['firstname']} {$row['lastname']}</option>";
                    }
                } else {
                    echo "<option>No customers found</option>";
                }
                mysqli_free_result($result1);
                ?>
            </select>
        </div>
        <br>
        <div>
            <label for="depa">Check-in Date:</label>
            <input type="text" id="depa" name="depa" placeholder="yyyy-mm-dd" required>
        </div>
        <br>
        <div>
            <label for="arr">Check-out Date:</label>
            <input type="text" id="arr" name="arr" placeholder="yyyy-mm-dd" required>
        </div>
        <br>
        <div>
        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" placeholder="(###) ###-####" pattern="\(\d{3}\) \d{3}-\d{4}" title="Format: (123) 456-7890"  required>
        </div>
        <br>
        <div>
            <label for="booking_extras">Booking Extras:</label>
            <input type="text" id="booking_extras" name="booking_extras">
        </div>
        <br>
        <div>
            <input type="submit" name="submit" value="Book">
            <a href='bookingslisting.php'>[Cancel]</a>
        </div>
    </form>
        <hr>
        <h3>Search for room availability</h3>
<div>
<form id="searchForm" method="get" name="searching">
        <label for="start_date">Start Date</label>
        <input type="text" id="fromDate" name="fromDate" required placeholder="From Date">
        <label for="End_date">End Date</label>
        <input type="text" id="toDate" name="toDate" required placeholder="To Date">
        <input type="submit" value="Search" >
    </form>
</div>
<br><br>
<div class="row">
    <table id="tblbookings" border="1">
        <thead>
            <tr>
                <th>Room#</th>
                <th>Room Name</th>
                <th>Room Type</th>
                <th>Beds</th>
            </tr>
        </thead>
        <tbody id="result"></tbody> 
    </table>
</div>

<script>
$(document).ready(function(){
    $("#fromDate, #depa").datepicker({dateFormat:"yy-mm-dd"});
    $("#toDate, #arr").datepicker({dateFormat:"yy-mm-dd"});

    $("#searchForm").submit(function(event) {
    event.preventDefault(); 
    var fromDate = new Date($("#fromDate").val());
    var toDate = new Date($("#toDate").val());
    
    if (fromDate > toDate) {
        alert("From date cannot be later than To date.");
        return false;
    }

    searchTickets(); 
});
});

function searchTickets(){
    var fromDate = $("#fromDate").val();
    var toDate = $("#toDate").val();

    $.ajax({
        url: "bookingsearch.php",
        method: "GET",
        data: {fromDate: fromDate, toDate: toDate},
        success: function(response) {
            $("#result").html(response);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
        }
    });
}
</script>
</body>
</html>
