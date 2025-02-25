<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // your MySQL root password
$dbname = "test_song_streaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$songId = isset($_GET['song_id']) ? intval($_GET['song_id']) : 0;
$songDetails = null;

if ($songId) {
    // Fetch song details
    $sql = "SELECT * FROM SONG WHERE SONG_ID = $songId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $songDetails = $result->fetch_assoc();
    }
}

$releaseYear = $songDetails ? $songDetails['SONG_RELEASE_YEAR'] : '';
$songTitle = $songDetails ? $songDetails['SONG_TITLE'] : '';
$singerName = $songDetails ? $songDetails['SINGER_ID'] : '';
$langName = $songDetails ? $songDetails['LANG_ID'] : '';

// Fetch all singers and languages for the dropdown
$singerSql = "SELECT * FROM SINGER";
$singerResult = $conn->query($singerSql);

$languageSql = "SELECT * FROM LANGUAGE";
$languageResult = $conn->query($languageSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Song Details</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            width: 95%;
        }

        .containerr {
            max-width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #202020;
            border-radius: 5px;
        }

        h2 {
            color: orange;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: orange;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #ff8c00;
        }

        .error {
            color: red;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>
<div class="containerr">
    <h2><?php echo $songId ? 'Edit Song Details' : 'Add New Song'; ?></h2>
    <form action="save_song.php" method="POST" enctype="multipart/form-data">
        <?php if ($songId): ?>
            <input type="hidden" name="song_id" value="<?php echo $songId; ?>">
        <?php endif; ?>
        <label for="songTitle">Song Title:</label>
        <input type="text" id="songTitle" name="songTitle" value="<?php echo htmlspecialchars($songTitle); ?>" required>

        <label for="releaseYear">Release Year:</label>
        <input type="text" id="releaseYear" name="releaseYear" value="<?php echo htmlspecialchars($releaseYear); ?>" required>

        <label for="singer">Singer:</label>
        <select id="singer" name="singer" required>
            <option value="">Select Singer</option>
            <?php
            if ($singerResult->num_rows > 0) {
                while ($row = $singerResult->fetch_assoc()) {
                    $selected = $row['SINGER_ID'] == $singerName ? 'selected' : '';
                    echo "<option value='{$row['SINGER_ID']}' $selected>{$row['SINGER_NAME']}</option>";
                }
            }
            ?>
        </select>

        <label for="language">Language:</label>
        <select id="language" name="language" required>
            <option value="">Select Language</option>
            <?php
            if ($languageResult->num_rows > 0) {
                while ($row = $languageResult->fetch_assoc()) {
                    $selected = $row['LANG_ID'] == $langName ? 'selected' : '';
                    echo "<option value='{$row['LANG_ID']}' $selected>{$row['LANG_NAME']}</option>";
                }
            }
            ?>
        </select>

        <label for="albumCover">Album Cover:</label>
        <input type="file" id="albumCover" name="albumCover" accept="image/*">

        <label for="songFile">Song File:</label>
        <input type="file" id="songFile" name="songFile" accept="audio/*">

        <button type="submit"><?php echo $songId ? 'Save Changes' : 'Add Song'; ?></button>
    </form>
</div>
</body>
</html>

<?php
$conn->close();
?>
