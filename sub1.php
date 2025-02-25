<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription</title>
    <style>
        body {
            display: flex;
            height: 100vh;
            background-color: #000;
            color: #fff;
            margin: 0;
            font-family: Arial, sans-serif;
            width: 100%;
        }

        .left-section, .right-section {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .playlist-item {
            background-color: #333;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            text-align: center;
        }

        .playlist-item:hover {
            background-color: #9a5800;
            color: #000;
        }

        .song-list {
            border: 1px solid white;
            display: none; /* Initially hide all song lists */
            flex-direction: column;
        }

        .song-list.active {
            display: flex; /* Display only the active song list */
        }

        .song-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #fff;
            cursor: grab; /* Make the song items draggable */
            user-select: none; /* Prevent text selection during drag */
        }

        .song-item:last-child {
            border-bottom: none;
        }

        .song-item .move-icon,
        .song-item .delete-icon {
            cursor: pointer;
        }

        .song-item .move-icon {
            margin-right: 10px;
        }

        .song-item .delete-icon {
            margin-left: auto;
            background-color: red;
            border-radius: 50%;
            padding: 5px;
        }

        .popup {
            display: none; /* Hidden by default */
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: #333;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .popup h2 {
            margin-top: 0;
        }

        .popup p {
            margin: 10px 0;
        }

        .popup input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .popup-buttons {
            display: flex;
            justify-content: space-between;
        }

        .popup-buttons .button-ok {
            background-color: #ffa500;
            border: none;
            padding: 10px 20px;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
        }

        .popup-buttons .button-cancel {
            background-color: #fff;
            border: none;
            padding: 10px 20px;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <div class="left-section" id="playlist-section">
        <div class="playlist-item add-playlist" id="addPlaylist">+ New Playlist</div>
        <!-- Placeholder for playlists added dynamically -->
    </div>

    <div class="right-section" id="song-list-section">
        <!-- Placeholder for song lists added dynamically -->
    </div>

    <div id="new-playlist-popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeNewPlaylistPopup()">&times;</span>
            <h2>New Playlist</h2>
            <input type="text" id="playlist-name" placeholder="Playlist Name">
            <div class="popup-buttons">
                <button class="button-ok" id="okButton">Add Playlist</button>
                <button class="button-cancel" id="cancelButton" onclick="closeNewPlaylistPopup()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
