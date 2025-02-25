<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: sp.html');
    exit();
}

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

$data = json_decode(file_get_contents('php://input'), true);
$songTitle = $data['songTitle'];
$playlists = $data['playlists'];

$username = $_SESSION['username'];

// Get the ACC_ID from the username
$query = "SELECT ACC_ID FROM ACCOUNT WHERE ACC_NAME = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$acc_id = $user['ACC_ID'];

$query = "SELECT SONG_ID FROM SONG WHERE SONG_TITLE = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $songTitle);
$stmt->execute();
$result = $stmt->get_result();
$song = $result->fetch_assoc();
$song_id = $song['SONG_ID'];

foreach ($playlists as $playlist_id) {
    $query = "INSERT INTO STORE (PLAYLIST_ID, SONG_ID) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $playlist_id, $song_id);
    $stmt->execute();
    if ($stmt->error) {
        die("Execute failed: " . $stmt->error);
    }
}

echo json_encode(["status" => "success"]);

$conn->close();
?>
