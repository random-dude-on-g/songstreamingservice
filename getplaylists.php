<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: sp.html');
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL root password
$dbname = "song_streaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$username = $_SESSION['username'];

// Get the ACC_ID from the username
$query = "SELECT ACC_ID FROM ACCOUNT WHERE ACC_NAME = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$acc_id = $user['ACC_ID'];

$query = "SELECT PLAYLIST_ID, PLAYLIST_NAME FROM PLAYLIST WHERE ACC_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $acc_id);
$stmt->execute();
$result = $stmt->get_result();

$playlists = [];
while ($row = $result->fetch_assoc()) {
    $playlists[] = $row;
}

echo json_encode($playlists);
?>