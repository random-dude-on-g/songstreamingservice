<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $artist = htmlspecialchars($_POST['artist']);
    $albumCover = htmlspecialchars($_POST['albumCover']);
    $releaseYear = htmlspecialchars($_POST['releaseYear']);
    $language = htmlspecialchars($_POST['language']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Song Profile</title>
        <style>
            body {
                background-color: black;
                color: white;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
            }

            .container {
                display: flex;
                justify-content: space-between;
            }

            .left-section {
                width: 60%;
                text-align: center;
            }

            .song-picture {
                width: 300px;
                height: 300px;
                border-radius: 50%;
                margin-bottom: 20px;
            }

            .progress-bar-wrapper {
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .progress-bar {
                width: 100%;
                max-width: 600px;
                display: flex;
                align-items: center;
                margin-bottom: 20px;
            }

            .progress-bar-container {
                flex-grow: 1;
                justify-content: center;
                align-items: center;
                height: 5px;
                background-color: #444;
                margin: 0 10px;
            }

            .progress {
                height: 100%;
                background-color: #ffa500;
            }

            .controls {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
            }

            .button {
                margin: 0 10px;
                padding: 10px;
                background-color: #ffa500;
                border: none;
                color: #000;
                cursor: pointer;
                border-radius: 5px;
                text-align: center;
                transition: background-color 0.3s;
            }

            .button:hover {
                background-color: #ff8c00;
            }

            .button i {
                font-size: 20px;
            }

            .right-section {
                width: 35%;
                padding: 20px;
                background-color: #985600;
                border: 2px solid white;
            }

            .song-details {
                color: white;
                margin-bottom: 20px;
            }

            .song-detail-item {
                margin-bottom: 10px;
            }

            .song-detail-item span {
                font-weight: bold;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="left-section">
                <img src="<?php echo $albumCover; ?>" alt="Song Picture" class="song-picture">
                <audio id="audioPlayer" controls style="display: none;">
                    <source id="audioSource" src="" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>

                <div class="progress-bar-wrapper">
                    <div class="progress-bar">
                        <span class="duration-played">0:00</span>
                        <div class="progress-bar-container">
                            <div class="progress" style="width: 0%;"></div>
                        </div>
                        <span class="duration-total">0:00</span>
                    </div>
                </div>

                <div class="controls">
                    <button class="button" id="volumeButton" onclick="toggleMute()"><i class="fas fa-volume-up"></i></button>
                    <button class="button" id="prevButton" onclick="backwardSong()"><i class="fas fa-backward"></i></button>
                    <button class="button" id="playPauseButton" onclick="togglePlayPause()"><i class="fas fa-play"></i></button>
                    <button class="button" id="nextButton" onclick="forwardSong()"><i class="fas fa-forward"></i></button>
                    <button class="button" id="addToPlaylistButton"><i class="fas fa-list"></i></button>
                </div>
            </div>
            
            <div class="right-section">
                <div class="song-details">
                    <div class="song-detail-item"><span>Title:</span> <?php echo $title; ?></div>
                    <div class="song-detail-item"><span>Artist:</span> <?php echo $artist; ?></div>
                    <div class="song-detail-item"><span>Release Year:</span> <?php echo $releaseYear; ?></div>
                    <div class="song-detail-item"><span>Language:</span> <?php echo $language; ?></div>
                </div>
            </div>
        </div>

        <script>
            // Ensure these functions are defined in your script or included JS files
            function toggleMute() {
                // Implementation here
            }

            function togglePlayPause() {
                // Implementation here
            }

            function backwardSong() {
                // Implementation here
            }

            function forwardSong() {
                // Implementation here
            }
        </script>
    </body>
    </html>
    <?php
} else {
    echo "Invalid request method.";
}
?>
