<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dtr";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT date, time_in, time_out FROM timesheet ORDER BY date DESC";
$result = $conn->query($sql);

$timesheet = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $timesheet[] = $row;
    }
}

$conn->close();

echo json_encode($timesheet);
?>
