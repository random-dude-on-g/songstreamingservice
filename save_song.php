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

$songId = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;
$songTitle = $_POST['songTitle'];
$releaseYear = $_POST['releaseYear'];
$singer = $_POST['singer'];
$language = $_POST['language'];

$albumCover = $_FILES['albumCover'];
$songFile = $_FILES['songFile'];

// Handle file uploads
$albumCoverPath = null;
$songFilePath = null;

if ($albumCover && $albumCover['tmp_name']) {
    $albumCoverPath = 'album/' . $songTitle . '.jpeg';
    move_uploaded_file($albumCover['tmp_name'], $albumCoverPath);
}

if ($songFile && $songFile['tmp_name']) {
    $songFilePath = 'songs/' . $songTitle . '.mp3';
    move_uploaded_file($songFile['tmp_name'], $songFilePath);
}

// Insert or update song details in the database
if ($songId) {
    // Update existing song
    $sql = "UPDATE SONG SET 
            SONG_TITLE = ?, 
            SONG_RELEASE_YEAR = ?, 
            SINGER_ID = ?, 
            LANG_ID = ? 
            WHERE SONG_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $songTitle, $releaseYear, $singer, $language, $songId);
} else {
    // Insert new song
    $sql = "INSERT INTO SONG (SONG_TITLE, SONG_RELEASE_YEAR, SINGER_ID, LANG_ID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $songTitle, $releaseYear, $singer, $language);
}

if ($stmt->execute()) {
    header('Location: adminpage.php');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
