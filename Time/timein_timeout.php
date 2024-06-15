<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dtr";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'];
$date = $data['date'];
$time = $data['time'];

$response = array();

if ($action === 'TimeIn') {
    $sql = "INSERT INTO timesheet (date, time_in) VALUES ('$date', '$time')";
    if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
        $response['message'] = "Time in recorded successfully.";
    } else {
        $response['success'] = false;
        $response['error'] = "Error recording time in: " . $conn->error;
    }
} else if ($action === 'TimeOut') {
    $sql = "UPDATE timesheet SET time_out='$time' WHERE time_out IS NULL ORDER BY date DESC LIMIT 1";
    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "Time out recorded successfully.";
        } else {
            $response['success'] = false;
            $response['error'] = "You have not timed in yet.";
        }
    } else {
        $response['success'] = false;
        $response['error'] = "Error recording time out: " . $conn->error;
    }
}

$conn->close();

echo json_encode($response);
?>
