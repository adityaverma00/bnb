<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse bookings</title>
</head>
<body>
    <?php
    include "config.php";
    include "checksession.php";
    checkUser();
  
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error:unable to connect to Mysql." . mysqli_connect_error();
        exit; //stop processing the page further
    }
    $un = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';


    //prepare a query and send it to the server
   
    $query = 'SELECT booking.Booking_ID, room.roomname, booking.Checkin_date, booking.Checkout_date, customer.firstname, customer.lastname
    FROM booking, room, customer
    WHERE booking.roomID = room.roomID and booking.customerID = customer.customerID
    ORDER BY Booking_ID';

    $result = mysqli_query($DBC, $query);
    $rowcount = mysqli_num_rows($result);
    ?>
    <h6>Logged in as <?php echo $un ?></h6>
    <h1>Current Bookings</h1>
    <h2><a href="makingbooking.php">[Make a Booking]</a><a href="index.php">[Return to main page]</a></h2>

    <table border="1">
        <thead>
            <tr>
                <th>Booking (room, dates)</th>
                <th>Customer</th>
                <th>Action</th>
            </tr>
        </thead>

        <?php

if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['Booking_ID'];	
        echo '<tr><td>' . $row['roomname'] . ', ' . $row['Checkin_date'] . ', ' . $row['Checkout_date'] . '</td>';
        echo '<td>' . $row['firstname'] . ', ' . $row['lastname'] . '</td>';
        echo '<td><a href="viewbookingdetail.php?id=' . $id . '">[view]</a>';
        echo '<a href="updatebooking.php?id=' . $id . '">[edit]</a>';
        echo '<a href="editreview.php?id=' . $id . '">[manage reviews]</a>';
        echo '<a href="deletebooking.php?id=' . $id . '">[delete]</a></td>';
        echo '</tr>';
    }
} else {
    echo "<tr><td colspan='3'><h2>No bookings found!</h2></td></tr>";
}
mysqli_free_result($result);
        mysqli_close($DBC);
        ?>
    </table>
</body>
</html>