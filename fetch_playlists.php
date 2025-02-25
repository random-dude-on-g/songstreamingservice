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

// Fetch playlists for the logged-in user
$username = $_SESSION['username'];
$sqlPlaylists = "SELECT P.PLAYLIST_ID, P.PLAYLIST_NAME FROM PLAYLIST P
                 JOIN ACCOUNT A ON P.ACC_ID = A.ACC_ID
                 WHERE A.ACC_NAME = '$username'";
$resultPlaylists = $conn->query($sqlPlaylists);

// Store playlists in an array
$playlists = [];
while ($rowPlaylist = $resultPlaylists->fetch_assoc()) {
    $playlistId = $rowPlaylist['PLAYLIST_ID'];
    $playlistName = $rowPlaylist['PLAYLIST_NAME'];

    // Fetch songs for each playlist
    $sqlSongs = "SELECT S.SONG_ID, S.SONG_TITLE FROM SONG S
                 JOIN STORE ST ON S.SONG_ID = ST.SONG_ID
                 WHERE ST.PLAYLIST_ID = $playlistId";
    $resultSongs = $conn->query($sqlSongs);

    // Store songs in an array
    $songs = [];
    while ($rowSong = $resultSongs->fetch_assoc()) {
        $songId = $rowSong['SONG_ID'];
        $songTitle = $rowSong['SONG_TITLE'];
        $songs[] = ['id' => $songId, 'title' => $songTitle];
    }

    $playlists[] = ['id' => $playlistId, 'name' => $playlistName, 'songs' => $songs];
}

// Close connection
$conn->close();

// Return playlists as JSON
header('Content-Type: application/json');
echo json_encode($playlists);
?>
