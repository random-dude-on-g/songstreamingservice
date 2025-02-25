<?php
session_start();

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

// Function to sanitize input for SQL statements
function sanitize_input($conn, $data) {
    $data = trim($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data when form is submitted
    $songId = $_POST['song_id'];
    $songTitle = $_POST['title']; // Do not sanitize for apostrophes
    $singerName = $_POST['artist'];
    $releaseYear = sanitize_input($conn, $_POST['release-year']);
    $language = sanitize_input($conn, $_POST['language']);

    // Check if the album directory exists, if not, create it
    $albumDir = 'album/';
    if (!is_dir($albumDir)) {
        mkdir($albumDir, 0777, true);
    }

    // Handle album picture upload
    if ($_FILES['album-picture']['name']) {
        $albumTmpName = $_FILES['album-picture']['tmp_name'];
        $albumDestination = $albumDir . $songTitle . '.jpeg';
        move_uploaded_file($albumTmpName, $albumDestination);
    }

    // Check if the songs directory exists, if not, create it
    $songsDir = 'songs/';
    if (!is_dir($songsDir)) {
        mkdir($songsDir, 0777, true);
    }

    // Handle song file upload
    if ($_FILES['song-input']['name']) {
        $fileTmpName = $_FILES['song-input']['tmp_name'];
        $fileExtension = pathinfo($_FILES['song-input']['name'], PATHINFO_EXTENSION);
        $fileDestination = $songsDir . $songTitle . '.' . $fileExtension;
        move_uploaded_file($fileTmpName, $fileDestination);
    }

    // Insert artist if necessary
    $singerId = null;
    if (!empty($singerName)) {
        // Check if the artist already exists
        $checkSingerQuery = "SELECT SINGER_ID FROM SINGER WHERE SINGER_NAME = ?";
        $stmt = $conn->prepare($checkSingerQuery);
        $stmt->bind_param("s", $singerName);
        $stmt->execute();
        $singerResult = $stmt->get_result();

        if ($singerResult->num_rows > 0) {
            // Artist already exists, get the SINGER_ID
            $singerRow = $singerResult->fetch_assoc();
            $singerId = $singerRow['SINGER_ID'];
        } else {
            // Artist does not exist, insert new artist
            $insertSingerQuery = "INSERT INTO SINGER (SINGER_NAME) VALUES (?)";
            $stmt = $conn->prepare($insertSingerQuery);
            $stmt->bind_param("s", $singerName);
            if ($stmt->execute() === TRUE) {
                $singerId = $stmt->insert_id; // Get the new SINGER_ID
            } else {
                echo "Error adding new artist: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // Insert language if necessary
    $languageId = null;
    if (!empty($language)) {
        // Check if the language already exists
        $checkLanguageQuery = "SELECT LANG_ID FROM LANGUAGE WHERE LANG_NAME = ?";
        $stmt = $conn->prepare($checkLanguageQuery);
        $stmt->bind_param("s", $language);
        $stmt->execute();
        $languageResult = $stmt->get_result();

        if ($languageResult->num_rows > 0) {
            // Language already exists, get the LANG_ID
            $languageRow = $languageResult->fetch_assoc();
            $languageId = $languageRow['LANG_ID'];
        } else {
            // Language does not exist, insert new language
            $insertLanguageQuery = "INSERT INTO LANGUAGE (LANG_NAME) VALUES (?)";
            $stmt = $conn->prepare($insertLanguageQuery);
            $stmt->bind_param("s", $language);
            if ($stmt->execute() === TRUE) {
                $languageId = $stmt->insert_id; // Get the new LANG_ID
            } else {
                echo "Error adding new language: " . $conn->error;
            }
        }
        $stmt->close();
    }

    // Insert new song into the SONG table
    $insertSongQuery = "INSERT INTO SONG (SONG_TITLE, SONG_RELEASE_YEAR, SINGER_ID, LANG_ID) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSongQuery);
    $stmt->bind_param("ssii", $songTitle, $releaseYear, $singerId, $languageId);

    // Execute the query
    if ($stmt->execute() === TRUE) {
        echo "New song added successfully";
    } else {
        echo "Error adding new song: " . $stmt->error;
    }
    $stmt->close();

    // Redirect back to song listing page after adding
    header("Location: adminpage.php");
    exit();
}


// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Song</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #000;
            color: #fff;
            margin: 0;
            font-family: Arial, sans-serif;
            height: 100vh;
            justify-content: center;
        }

        h1 {
            color: orange;
            margin-bottom: 20px;
            text-align: left;
            width: 100%;
            max-width: 800px;
        }

        .container {
            display: flex;
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            max-width: 800px;
            width: 100%;
        }

        .left-section, .right-section {
            display: flex;
            flex-direction: column;
            margin: 0 20px;
            flex: 1;
        }

        .left-section {
            margin-right: 40px;
        }

        .left-section label, .left-section input {
            margin-bottom: 15px;
        }

        .left-section label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .left-section input[type="text"], 
        .left-section input[type="file"] {
            padding: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #222;
            color: #fff;
            width: 100%;
            max-width: 250px;
        }

        .input-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .right-section {
            align-items: center;
            flex: 1;
        }

        .right-section img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 2px solid #fff;
        }

        .right-section button {
            padding: 10px 20px;
            margin: 10px;
            background-color: orange;
            border: none;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .right-section button:hover {
            background-color: #ff8c00;
        }

        .buttons {
            display: flex;
            width: 100%;
            justify-content: center;
        }

        .buttons button {
            width: 100px;
            background-color: orange;
        }

        .buttons button.cancel {
            background-color: #555;
            color: #fff;
        }

        .buttons button.cancel:hover {
            background-color: #777;
        }

    </style>
</head>
<body>
    <h1>Add New Song</h1>
    <div class="container">
        <div class="left-section">
            <form id="songForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="input-group">
                    <label for="artist">Artist</label>
                    <input type="text" id="artist" name="artist" required>
                </div>

                <div class="input-group">
                    <label for="release-year">Release Year</label>
                    <input type="text" id="release-year" name="release-year" required>
                </div>

                <div class="input-group">
                    <label for="language">Language</label>
                    <input type="text" id="language" name="language" required>
                </div>

                <div class="input-group">
                    <label for="album-picture">Album Picture</label>
                    <input type="file" id="album-picture" name="album-picture" accept="image/*">
                </div>

                <div class="input-group">
                    <label for="song-input">Song File</label>
                    <input type="file" id="song-input" name="song-input" accept="audio/*">
                </div>

                <div class="buttons">
                    <button type="submit">Save</button>
                    <button type="button" class="cancel" onclick="window.location.href='adminpage.php'">Cancel</button>
                </div>
            </form>
        </div>
        <div class="right-section">
            <img id="albumPreview" src="#" alt="Album Preview">
        </div>
    </div>

    <script>
        document.getElementById('album-picture').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var img = document.getElementById('albumPreview');
                img.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        });
    </script>
</body>
</html>
