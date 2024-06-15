<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dtr";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

$checkQuery = "SELECT * FROM timesheet WHERE time_out IS NULL ORDER BY date DESC LIMIT 1";
$result = $conn->query($checkQuery);

if ($result->num_rows > 0) {
    $response['status'] = 'time_in';
} else {
    $response['status'] = 'time_out';
}

$conn->close();

echo json_encode($response);
?>
