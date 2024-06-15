<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DTR";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="timesheet.txt"');

$sql = "SELECT * FROM timesheet ORDER BY date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Date: " . $row["date"] . "\n";
        echo "Time In: " . $row["time_in"] . "\n";
        echo "Time Out: " . ($row["time_out"] ? $row["time_out"] : "N/A") . "\n";
        echo "----------------------------------------\n";
    }
} else {
    echo "No records found.";
}

$conn->close();
?>
