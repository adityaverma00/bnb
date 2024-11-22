<?php
// Include database configuration
include "config.php";

// Get search parameters
$fromDate = $_GET['fromDate'];
$toDate = $_GET['toDate'];

// Create a new database connection
$DBC = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

// Check if the connection was successful
if ($DBC->connect_errno) {
    echo "Error: Unable to connect to MySQL. " . $DBC->connect_error;
    exit; // Stop processing the page further
}

// Prepare SQL query to search for available flights
$query = "SELECT roomID, roomname, roomtype, beds 
          FROM room 
          WHERE roomID NOT IN (
              SELECT roomID 
              FROM booking 
              WHERE NOT (
                  checkout_Date < '$fromDate' OR checkin_Date > '$toDate'
              )
          )";

// Prepare the statement
$stmt = mysqli_prepare($DBC, $query);

// Execute the query
mysqli_stmt_execute($stmt);

// Get the result set
$result = mysqli_stmt_get_result($stmt);

// Check if the query was successful
if ($result) {
    // Display search result
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['roomID'] . "</td>";
            echo "<td>" . $row['roomname'] . "</td>";
            echo "<td>" . $row['roomtype'] . "</td>";
            echo "<td>" . $row['beds'] . "</td>";
            echo "</tr>";
        }
    } else {
echo "No available rooms found for the selected date range.";    }
} else {
    // Handle query error
    echo "Error executing the query: " . $DBC->error;
}


// Close the statement
mysqli_stmt_close($stmt);

// Close database connection
mysqli_close($DBC);
?>
