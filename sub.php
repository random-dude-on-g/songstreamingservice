<?php
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header('Location: sp.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player UI</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .left-section, .right-section {
            padding: 20px;
            overflow-y: auto;
        }

        .playlist-item, .add-playlist-button {
            background-color: #333;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            text-align: center;
        }

        .add-playlist-button {
            margin-bottom: 20px; /* Increased margin to separate from playlist items */
        }

        .playlist-item:hover, .add-playlist-button:hover {
            background-color: #9a5800;
            color: #000;
        }

        .song-list {
            border: 1px solid white;
            display: flex;
            flex-direction: column;
        }

        .song-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #fff;
            cursor: move; /* Make song-item draggable */
            user-select: none; /* Prevent text selection during drag */
            background-color: #444; /* Adjust background color for better visibility */
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

        .overlay {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 500;
        }
    </style>
</head>
<body>
    <div class="left-section" id="playlist-container">
        <div class="add-playlist-button" onclick="openNewPlaylistPopup()">Add Playlist</div>
        <!-- Existing playlist items will be appended here -->
    </div>
    <div class="right-section">
        <div class="song-list" id="song-list"></div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="popup" id="new-playlist-popup">
        <h2>New Playlist Name</h2>
        <p>Enter the playlist name</p>
        <input type="text" id="playlist-name">
        <div class="popup-buttons">
            <button class="button-ok" onclick="addNewPlaylist()">OK</button>
            <button class="button-cancel" onclick="closeNewPlaylistPopup()">Cancel</button>
        </div>
    </div>

    <script>
        // Variable to store the current playlistId
        let currentPlaylistId = null;

        // Function to fetch playlists and songs
        function fetchPlaylists() {
            fetch('fetch_playlists.php')
                .then(response => response.json())
                .then(data => {
                    const playlistContainer = document.getElementById('playlist-container');
                    playlistContainer.innerHTML = ''; // Clear existing playlists

                    // Add the "Add Playlist" button
                    const addPlaylistButton = document.createElement('div');
                    addPlaylistButton.className = 'add-playlist-button';
                    addPlaylistButton.textContent = 'Add Playlist';
                    addPlaylistButton.onclick = openNewPlaylistPopup;
                    playlistContainer.appendChild(addPlaylistButton);

                    // Iterate through fetched playlists
                    data.forEach(playlist => {
                        const playlistItem = document.createElement('div');
                        playlistItem.className = 'playlist-item';
                        playlistItem.textContent = playlist.name;
                        playlistItem.setAttribute('data-id', playlist.id);
                        playlistItem.addEventListener('click', () => {
                            currentPlaylistId = playlist.id; // Store current playlist ID
                            displaySongs(playlist);
                        });
                        playlistContainer.appendChild(playlistItem);
                    });
                })
                .catch(error => console.error('Error fetching playlists:', error));
        }

        // Function to display songs for a selected playlist
        function displaySongs(playlist) {
            const songListContainer = document.getElementById('song-list');
            songListContainer.innerHTML = ''; // Clear existing songs

            playlist.songs.forEach(song => {
                const songItem = document.createElement('div');
                songItem.className = 'song-item';
                songItem.textContent = song.title;
                songItem.setAttribute('data-id', song.id);

                // Add move and delete icons and functionality
                const moveIcon = document.createElement('span');
                moveIcon.className = 'move-icon';
                moveIcon.textContent = '☰';
                songItem.appendChild(moveIcon);

                const deleteIcon = document.createElement('span');
                deleteIcon.className = 'delete-icon';
                deleteIcon.textContent = '×';
                deleteIcon.addEventListener('click', () => deleteSongFromPlaylist(song.id)); // Attach delete function for playlist
                songItem.appendChild(deleteIcon);

                songListContainer.appendChild(songItem);

                // Make song-item draggable
                songItem.draggable = true;
                songItem.addEventListener('dragstart', handleDragStart);
                songItem.addEventListener('dragover', handleDragOver);
                songItem.addEventListener('drop', handleDrop);
            });
        }

        // Function to handle drag start
        function handleDragStart(event) {
            event.dataTransfer.setData('text/plain', event.target.dataset.id);
        }

        // Function to handle drag over
        function handleDragOver(event) {
            event.preventDefault();
        }

        // Function to handle drop
        function handleDrop(event) {
            event.preventDefault();
            const draggedItemId = event.dataTransfer.getData('text/plain');
            const droppedItemId = event.currentTarget.dataset.id;

            // Perform reordering in the UI (you might need to update the backend accordingly)
            const songListContainer = document.getElementById('song-list');
            const items = Array.from(songListContainer.children);
            const draggedItemIndex = items.findIndex(item => item.dataset.id === draggedItemId);
            const droppedItemIndex = items.findIndex(item => item.dataset.id === droppedItemId);

            // Swap items in the DOM
            if (draggedItemIndex > -1 && droppedItemIndex > -1) {
                const temp = items[draggedItemIndex];
                songListContainer.insertBefore(items[draggedItemIndex], items[droppedItemIndex]);
                songListContainer.insertBefore(items[droppedItemIndex], temp);
            }
        }

        // Function to open the new playlist popup
        function openNewPlaylistPopup() {
            const overlay = document.getElementById('overlay');
            const popup = document.getElementById('new-playlist-popup');
            overlay.style.display = 'block';
            popup.style.display = 'block';
        }

        // Function to close the new playlist popup
        function closeNewPlaylistPopup() {
            const overlay = document.getElementById('overlay');
            const popup = document.getElementById('new-playlist-popup');
            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        // Function to add a new playlist
        function addNewPlaylist() {
            const playlistName = document.getElementById('playlist-name').value.trim();
            if (playlistName !== '') {
                fetch('add_playlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ name: playlistName }),
                })
                .then(response => {
                    if (response.ok) {
                        closeNewPlaylistPopup();
                        fetchPlaylists(); // Refresh playlists after adding
                    }
                })
                .catch(error => console.error('Error adding playlist:', error));
            } else {
                alert('Please enter a valid playlist name.');
            }
        }

        // Function to delete a song from the current playlist
function deleteSongFromPlaylist(songId) {
    if (currentPlaylistId !== null) {
        fetch('delete_from_playlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                playlist_id: currentPlaylistId,
                song_id: songId
            }),
        })
        .then(response => {
            if (response.ok) {
                return response.json(); // Parse response JSON
            } else {
                throw new Error('Network response was not ok.');
            }
        })
        .then(data => {
            if (data.success) {
                // Remove song item from UI
                const songItem = document.querySelector(`.song-item[data-id="${songId}"]`);
                if (songItem) {
                    songItem.remove();
                }
                console.log(data.message); // Log success message
            } else {
                throw new Error(data.message); // Handle server-side error message
            }
        })
        .catch(error => {
            console.error('Error deleting song from playlist:', error);
            alert('Failed to delete song from playlist. Please try again later.');
        });
    }
}


        // Fetch playlists when the page loads
        fetchPlaylists();
    </script>
</body>
</html>
