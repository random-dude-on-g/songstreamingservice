<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player UI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            align-items: center;
            justify-content: center;
        }

        .cover-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            perspective: 1000px;
            width:100%;
        }

        .side-square {
            width: 200px;
            height: 200px;
            background-color: #333;
            transform-style: preserve-3d;
        }

        .side-square:nth-child(1) {
            transform: rotateY(50deg) translateX(250px);
        }
        .side-square:nth-child(2) {
            transform: rotateY(50deg) translateX(150px);
        }
        .side-square:nth-child(3) {
            transform: rotateY(50deg) translateX(50px);
        }
        .side-square:nth-child(5) {
            transform: rotateY(-50deg) translateX(-50px);
            z-index: 2;
        }
        .side-square:nth-child(6) {
            transform: rotateY(-50deg) translateX(-150px);
            z-index: 1;
        }
        .side-square:nth-child(7) {
            transform: rotateY(-50deg) translateX(-250px);
            z-index: 0;
        }

        .side-square img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            transform-style: preserve-3d;
        }

        .big-square {
            width: 250px;
            height: 250px;
            background-color: #333;
            transform-style: preserve-3d;
            margin-bottom: 20px;
            z-index: 3;
        }

        .big-square img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
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

        .song-name {
            font-size: 20px;
            text-align: center;
        }

        #playlistPopup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #222;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        #playlistPopup h2 {
            margin-top: 0;
        }

        #playlistContainer div {
            margin-bottom: 10px;
        }

        #confirmButton, #cancelButton {
            background-color: #ffa500;
            border: none;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            transition: background-color 0.3s;
        }

        #confirmButton:hover, #cancelButton:hover {
            background-color: #ff8c00;
        }
    </style>
</head>
<body>
    <div class="cover-flow" id="coverFlow">
        <!-- Song covers will be inserted here by JavaScript -->
    </div>
    
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

    <div id="playlistPopup" style="display:none;">
        <h2>Select Playlist</h2>
        <div id="playlistContainer"></div>
        <button id="confirmButton">Confirm</button>
        <button id="cancelButton">Cancel</button>
    </div>
    
    <div class="song-name" id="playerSongTitle"></div>

    <script src="recomFunctions.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', addRecomPageInteractions);
    </script>
</body>
</html>
