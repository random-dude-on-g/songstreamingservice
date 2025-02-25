<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song List</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #7f7f7c;
            background-color: #3a2500;
            color: white;
        }

        .left-section {
            width: 60%;
            float: left;
        }

        .song-list {
            width: 100%;
            list-style: none;
            padding: 0;
        }

        .song-item {
            width: 100%;
            display: flex;
            align-items: center;
            background-color: #e96b12;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer; /* Add cursor pointer to indicate clickable */
        }

        .song-item img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .song-info {
            display: flex;
            flex-direction: column;
        }

        .song-title {
            font-weight: bold;
        }

        .right-section {
            width: 35%;
            float: right;
        }

        .filter-box {
            background-color: #3a2500;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #985600;
        }

        .filter-box h3 {
            margin: 0;
            margin-bottom: 10px;
        }

        .filter-button {
            display: flex;
            align-items: center;
            padding: 10px;
            background: linear-gradient(to bottom, black, white);
            color: black;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-button.active {
            background: linear-gradient(to bottom, white, black);
            color: white;
        }

        .player-box {
            clear: both;
            background-color: #9a5800;
            padding: 10px;
            margin-top: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .player-box img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            margin-right: 10px;
        }

        .player-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .player-controls {
            display: flex;
            align-items: center;
        }

        .player-controls button {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .song-details-box {
            background-color: #e96b12;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .song-details-box div {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="left-section">
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search songs...">
        </div>
        <ul class="song-list" id="songList">
            <!-- Songs will be dynamically loaded here -->
        </ul>
    </div>
    <div class="right-section">
        <div class="filter-box">
            <h3>Filter by</h3>
            <div class="filter-button" id="english">English</div>
            <div class="filter-button" id="indonesian">Indonesian</div>
            <div class="filter-button" id="japanese">Japanese</div>
            <div class="filter-button" id="korean">Korean</div>
            <div class="filter-button" id="chinese">Chinese</div>
	    <div class="filter-button" id="french">French</div>
	    <div class="filter-button" id="spanish">Spanish</div>
        </div>

        <div class="player-box" id="playerBox">
            <img src="default-album.png" alt="Album Cover" id="playerAlbumCover">
            <div class="player-info">
                <div id="playerSongTitle">Song Title</div>
                <div id="playerSongArtist">Song Artist</div>
            </div>
            <div class="player-controls">
                <button id="prevButton">⏮</button>
                <button id="playPauseButton">▶</button>
                <button id="nextButton">⏭</button>
            </div>
        </div>

        <div class="song-details-box" id="songDetailsBox">
            <div id="detailSongTitle">Title: </div>
            <div id="detailSongArtist">Artist: </div>
            <div id="detailReleaseYear">Release Year: </div>
            <div id="detailLanguage">Language: </div>
        </div>
    </div>

    <!-- Audio element for music playback -->
    <audio id="audioPlayer" controls style="display: none;">
        <source id="audioSource" src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>


</body>
</html>
