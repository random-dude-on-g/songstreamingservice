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

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$data = json_decode(file_get_contents("php://input"));

if (isset($data->playlist_id) && isset($data->song_id)) {
    $playlistId = $data->playlist_id;
    $songId = $data->song_id;

    // Delete song from playlist in the database
    $sqlDelete = "DELETE FROM STORE WHERE PLAYLIST_ID = ? AND SONG_ID = ?";
    $stmt = $conn->prepare($sqlDelete);
    $stmt->bind_param("ii", $playlistId, $songId);

    if ($stmt->execute()) {
        // Successful deletion
        echo json_encode(array("success" => true, "message" => "Song deleted from playlist successfully."));
    } else {
        // Error in deletion
        http_response_code(500); // Internal Server Error
        echo json_encode(array("success" => false, "message" => "Error deleting song from playlist: " . $conn->error));
    }
} else {
    // Invalid request
    http_response_code(400); // Bad Request
    echo json_encode(array("success" => false, "message" => "Invalid request data."));
}

// Close connection
$stmt->close();
$conn->close();
?>
