<?php
// Database connection details
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

// SQL query to retrieve song data with album covers and release year
$sql = "SELECT 
            SONG.SONG_TITLE AS title, 
            SINGER.SINGER_NAME AS artist, 
            CONCAT('album/', SONG.SONG_TITLE, '.jpeg') AS albumCover, 
            SONG.SONG_RELEASE_YEAR AS releaseYear,
            LANGUAGE.LANG_NAME AS language
        FROM SONG
        INNER JOIN SINGER ON SONG.SINGER_ID = SINGER.SINGER_ID
        INNER JOIN LANGUAGE ON SONG.LANG_ID = LANGUAGE.LANG_ID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize an array to store songs
    $songs = array();

    // Fetch data from each row and add to $songs array
    while($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }

    // Convert PHP array to JSON format
    header('Content-Type: application/json');
    echo json_encode($songs);
} else {
    echo "No songs found";
}

// Close connection
$conn->close();
?>
