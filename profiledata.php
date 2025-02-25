<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SONG_STREAMING";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$acc_id = $_SESSION['acc_id']; // Assuming the user is logged in and acc_id is stored in session

$sql = "SELECT ACC_NAME, ACC_PASSWORD, SUBSCRIPTION_STATUS FROM ACCOUNT a 
        JOIN CUSTOMER c ON a.ACC_ID = c.ACC_ID 
        WHERE a.ACC_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $acc_id);
$stmt->execute();
$stmt->bind_result($acc_name, $acc_password, $subscription_status);
$stmt->fetch();
$stmt->close();

// Convert subscription status to boolean
$isSubscribed = $subscription_status == 1 ? true : false;

$response = array(
    "username" => $acc_name,
    "password" => $acc_password,
    "isSubscribed" => $isSubscribed
);

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
