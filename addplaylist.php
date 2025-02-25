<?php
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: sp.html');
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL root password
$dbname = "song_streaming";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Playlist name not provided']);
    exit();
}

$playlistName = htmlspecialchars($data['name']);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new playlist
$username = $_SESSION['username'];
$sql = "INSERT INTO PLAYLIST (ACC_ID, PLAYLIST_NAME) VALUES (
    (SELECT ACC_ID FROM ACCOUNT WHERE ACC_NAME = '$username'),
    '$playlistName'
)";

if ($conn->query($sql) === TRUE) {
    // Return the newly created playlist ID if needed
    $last_id = $conn->insert_id;
    echo json_encode(['id' => $last_id, 'name' => $playlistName]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error adding playlist: ' . $conn->error]);
}

// Close connection
$conn->close();
?>
