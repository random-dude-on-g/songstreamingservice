<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // your MySQL root password
$dbname = "song_streaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch random songs from SONG table
$sql = "SELECT SONG_ID, SONG_TITLE FROM SONG ORDER BY RAND() LIMIT 7";
$result = $conn->query($sql);

$songs = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $songId = $row['SONG_ID'];
        $songTitle = $row['SONG_TITLE'];
        $mp3FilePath = 'songs/' . $songTitle . '.mp3'; // Path to MP3 file
        $jpegFilePath = 'album/' . $songTitle . '.jpeg'; // Path to JPEG file

        $songs[] = array(
            'songId' => $songId,
            'songTitle' => $songTitle,
            'mp3FilePath' => $mp3FilePath,
            'jpegFilePath' => $jpegFilePath
        );
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($songs);
?>
