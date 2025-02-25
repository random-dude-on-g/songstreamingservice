<?php
session_start(); // Start session to access session variables

$servername = "localhost";
$username = "root";
$password = ""; // Your MySQL root password
$dbname = "song_streaming";

// Check if user is logged in
if (!isset($_SESSION['logged_in_user'])) {
    header("HTTP/1.1 401 Unauthorized");
    exit();
}

$acc_name = $_SESSION['logged_in_user'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement
$sql = "
    SELECT 
        p.PLAYLIST_NAME, 
        s.SONG_TITLE 
    FROM 
        ACCOUNT a
    JOIN 
        PLAYLIST p ON a.ACC_ID = p.ACC_ID
    JOIN 
        STORE st ON p.PLAYLIST_ID = st.PLAYLIST_ID
    JOIN 
        SONG s ON st.SONG_ID = s.SONG_ID
    WHERE 
        a.ACC_NAME = ?
";

// Prepare statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $acc_name); // Bind the account name parameter

// Execute statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

$playlists = [];
while ($row = $result->fetch_assoc()) {
    $playlists[$row['PLAYLIST_NAME']][] = $row['SONG_TITLE'];
}

// Transform the playlists array to the desired format
$formattedPlaylists = [];
foreach ($playlists as $playlistName => $songs) {
    $formattedPlaylists[] = [
        'PLAYLIST_NAME' => $playlistName,
        'songs' => array_map(function ($songTitle) {
            return ['SONG_TITLE' => $songTitle];
        }, $songs)
    ];
}

// Output the result as JSON
header('Content-Type: application/json');
echo json_encode($formattedPlaylists);

// Close connection
$stmt->close();
$conn->close();
?>
