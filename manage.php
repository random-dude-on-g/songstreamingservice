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

// Query to fetch songs with related details from SONG, SINGER, and LANGUAGE tables
$sql = "SELECT s.SONG_ID, s.SONG_TITLE, s.SONG_RELEASE_YEAR, si.SINGER_NAME, l.LANG_NAME
        FROM SONG s
        LEFT JOIN SINGER si ON s.SINGER_ID = si.SINGER_ID
        LEFT JOIN LANGUAGE l ON s.LANG_ID = l.LANG_ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Songs</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: fit-content;
            background-color: #000;
            font-family: Arial, sans-serif;
            margin: 0;
            color: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #555;
        }

        th {
            color: orange;
        }

        td img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
        }

        .button-container {
            text-align: right;
        }

        .button {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .add-button {
            background-color: #ffa500;
            color: #000;
        }

        .add-button:hover {
            background-color: #ff8c00;
        }

        .edit-button {
            background-color: #007bff;
            color: #fff;
        }

        .edit-button:hover {
            background-color: #0056b3;
        }

        .remove-button {
            background-color: #ff1493;
            color: #fff;
        }

        .remove-button:hover {
            background-color: #c71585;
        }

    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th> </th>
                <th>Title</th>
                <th>Artist</th>
                <th>Release Year</th>
                <th>Language</th>
                <th><button class="button add-button" onclick="loadAddSongPage()">Add New Song Entry</button></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    // Fetch album image if exists, otherwise display default message
                    $albumImage = file_exists("album/{$row['SONG_TITLE']}.jpeg") ? "album/{$row['SONG_TITLE']}.jpeg" : "no-image.jpeg";
                    echo "<td><img src='{$albumImage}' alt='Album {$row['SONG_TITLE']}'></td>";
                    echo "<td>{$row['SONG_TITLE']}</td>";
                    echo "<td>{$row['SINGER_NAME']}</td>";
                    echo "<td>{$row['SONG_RELEASE_YEAR']}</td>";
                    echo "<td>{$row['LANG_NAME']}</td>";
                    echo "<td>";
                    echo "<button class='button edit-button' onclick=\"loadEditSongDetailsPage({$row['SONG_ID']})\">Edit</button>";
                    echo "<a href='deletesong.php?id={$row['SONG_ID']}'><button class='button remove-button'>Remove</button></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No songs found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
