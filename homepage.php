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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: black;
        }

        .logo {
            height: 50px;
            margin-right: 20px;
        }

        header h1 {
            color: white;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .username-box {
            padding: 10px;
            background-color: orange;
            border-radius: 5px;
            margin-right: 10px;
        }

        .profile-pic {
            position: relative;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
        }

        .profile-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        main {
            display: flex;
            padding: 20px;
        }

        .tabs {
            width: 10%;
            display: flex;
            flex-direction: column;
            margin-right: -2px;
        }

        .tab {
            width: 100%;
            background-color: #202020;
            color: white;
            padding: 15px;
            margin-bottom: 4px;
            cursor: pointer;
            border: 2px solid #919395;
            border-right: none;
        }

        .tab.active {
            background-color: #e96b12;
            color: black;
            border: 2px solid #e96b12;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: calc(100% - 170px);
            background-color: #16140b;
            padding: 20px;
            border: 2px solid #985600;
            border-left: 10px solid #985600;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
<header>
    <img src="SSS.png" alt="Logo" class="logo">
    <h1>Song Streaming Website</h1>
    <div class="user-info">
        <div class="username-box" id="usernameBox">
            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>
        </div>
        <div class="profile-pic" id="profilePic">
            <?php
            // Assuming profile picture URL is stored in session or retrieved from DB
            $profilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.png';
            ?>
            <img src="<?php echo $profilePic; ?>" alt="Profile Picture" id="profileImage">
        </div>
    </div></header>

<main>
    <div class="tabs">
        <div class="tab active" onclick="showTabContent('home')">Home</div>
        <div class="tab" onclick="showTabContent('songpage')">Songs</div>
        <div class="tab" onclick="showTabContent('profile')">Profile</div>
        <div class="tab" onclick="showTabContent('check_subscription')">Playlist</div>
    </div>
    <div class="container">
        <div id="tabContent" class="tab-content active">
            <!-- Content will be loaded here -->
        </div>
    </div>
</main>
<audio id="audioPlayer" controls style="display: none;">
    <source id="audioSource" src="" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<audio id="audioPlayer"></audio> <!-- Audio player added outside of tab content -->

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
	
        const profilePic = document.getElementById('profilePic');
        const profileImage = document.getElementById('profileImage');

        const usernameBox = document.getElementById('usernameBox');
        const savedUsername = localStorage.getItem('username');
        if (savedUsername) {
            usernameBox.textContent = savedUsername;
        }


        // Load default tab content
        showTabContent('home');
    });

    let audioPlayer = document.getElementById('audioPlayer');
    let currentSongIndex = -1; // Variable to track the current song index

    function showTabContent(tabId) {
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
        });

        const activeTab = document.querySelector(`.tab[onclick="showTabContent('${tabId}')"]`);
        activeTab.classList.add('active');

        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        const tabContent = document.getElementById('tabContent');
        tabContent.classList.add('active');

        if (tabId === 'check_subscription') {
        checkSubscription();
    } else {
        fetch(`${tabId}.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                tabContent.innerHTML = data;
                if (tabId === 'home') {
                    addRecomPageInteractions();
                } else if (tabId === 'songpage') {
                    addSongPageInteractions();
                } else if (tabId === 'profile') {
                    addProfilePageInteractions();
                }
            })
            .catch(error => {
                tabContent.innerHTML = `<p>Error loading content: ${error.message}</p>`;
            });
    }
    }




function addSongPageInteractions() {
    const searchInput = document.getElementById('searchInput');
    const songList = document.getElementById('songList');
    const playPauseButton = document.getElementById('playPauseButton');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const playerSongTitle = document.getElementById('playerSongTitle');
    const playerSongArtist = document.getElementById('playerSongArtist');
    const playerAlbumCover = document.getElementById('playerAlbumCover');
    const audioPlayer = new Audio(); // Assuming you have an audio player setup

    const songDetailsBox = document.getElementById('songDetailsBox'); // Added songDetailsBox

    let songs = []; // Placeholder for songs data
    let currentSongIndex = 0;
    let currentLanguageFilter = ''; // Placeholder for current language filter

    function fetchSongs() {
        fetch('songdata.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Assuming server returns JSON data
            })
            .then(data => {
                songs = data; // Store fetched songs data
                renderSongs(songs); // Display songs initially
            })
            .catch(error => {
                console.error('Error fetching songs:', error);
            });
    }

    function renderSongs(songListData) {
        songList.innerHTML = '';
        songListData.forEach((song, index) => {
            const songItem = document.createElement('li');
            songItem.className = 'song-item';
            songItem.innerHTML = `
                <img src="${song.albumCover}" alt="${song.title} Cover">
                <div class="song-info">
                    <div class="song-title">${song.title}</div>
                    <div class="song-artist">${song.artist}</div>
                </div>
            `;
            songItem.addEventListener('click', () => playSong(index));
            songList.appendChild(songItem);
        });
    }

    function playSong(index) {
        if (currentSongIndex !== index) {
            const song = songs[index];
            playerSongTitle.textContent = song.title;
            playerSongArtist.textContent = song.artist;
            playerAlbumCover.src = song.albumCover;
            audioPlayer.src = `songs/${song.title}.mp3`; // Adjust audio source path
            audioPlayer.play();
            playPauseButton.textContent = '❚❚'; // Pause icon
            currentSongIndex = index;
            loadSongDetails(song); // Load song details when playing new song
        } else {
            togglePlayPause();
        }
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            playPauseButton.textContent = '❚❚';
        } else {
            audioPlayer.pause();
            playPauseButton.textContent = '▶';
        }
    }

    function loadSongDetails(song) {
        // Example of loading details into the songDetailsBox
        songDetailsBox.innerHTML = `
            <div id="detailSongTitle">Title: ${song.title}</div>
            <div id="detailSongArtist">Artist: ${song.artist}</div>
            <div id="detailReleaseYear">Release Year: ${song.releaseYear}</div>
            <div id="detailLanguage">Language: ${song.language}</div>
        `;
    }

    function prevSong() {
        if (audioPlayer.currentTime > 10) {
            audioPlayer.currentTime -= 10; // Go back 10 seconds
        } else {
            // If less than 10 seconds elapsed or at start of song, go to previous song if available
            if (currentSongIndex > 0) {
                currentSongIndex--;
            } else {
                currentSongIndex = 0; // Stay at the beginning of the current song
            }
            playSong(currentSongIndex);
        }
    }

    function nextSong() {
        if (audioPlayer.currentTime + 10 < audioPlayer.duration) {
            audioPlayer.currentTime += 10; // Forward 10 seconds
        } else {
            // If more than 10 seconds remaining or end of song reached, go to next song if available
            if (currentSongIndex < songs.length - 1) {
                currentSongIndex++;
                playSong(currentSongIndex);
            }
            // No action needed if there are no next songs
        }
    }

    function filterSongs() {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredSongs = songs.filter(song => 
            (song.title.toLowerCase().includes(searchTerm) || song.artist.toLowerCase().includes(searchTerm)) &&
            (currentLanguageFilter === '' || song.language.toLowerCase() === currentLanguageFilter.toLowerCase())
        );
        renderSongs(filteredSongs);
    }

    function toggleFilter(button, language) {
        currentLanguageFilter = language; // Update current language filter
        filterSongs(); // Re-filter songs based on new filter
        // Optionally, you can add visual feedback to indicate the active filter
        document.querySelectorAll('.filter-button').forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    }

    // Event listeners for filter buttons
    document.getElementById('english').addEventListener('click', function() { toggleFilter(this, 'English'); });
    document.getElementById('indonesian').addEventListener('click', function() { toggleFilter(this, 'Indonesian'); });
    document.getElementById('japanese').addEventListener('click', function() { toggleFilter(this, 'Japanese'); });
    document.getElementById('korean').addEventListener('click', function() { toggleFilter(this, 'Korean'); });
    document.getElementById('chinese').addEventListener('click', function() { toggleFilter(this, 'Chinese'); });
    document.getElementById('french').addEventListener('click', function() { toggleFilter(this, 'French'); });
    document.getElementById('spanish').addEventListener('click', function() { toggleFilter(this, 'Spanish'); });


    // Event listeners for play/pause and navigation buttons
    playPauseButton.addEventListener('click', togglePlayPause);
    prevButton.addEventListener('click', prevSong);
    nextButton.addEventListener('click', nextSong);
    searchInput.addEventListener('input', filterSongs);

    // Initial fetch and render
    fetchSongs();

    // Automatically play next song when current song ends
    audioPlayer.addEventListener('ended', () => {
        currentSongIndex++;
        if (currentSongIndex < songs.length) {
            playSong(currentSongIndex);
        }
    });
}

    function addRecomPageInteractions() {
    const coverFlow = document.getElementById('coverFlow');
    const audioPlayer = document.getElementById('audioPlayer');
    const audioSource = document.getElementById('audioSource');
    const playerSongTitle = document.getElementById('playerSongTitle');
    const playPauseButton = document.getElementById('playPauseButton');
    const volumeButton = document.getElementById('volumeButton');
    const progressBar = document.querySelector('.progress-bar');
    const progressBarContainer = document.querySelector('.progress-bar-container');
    const progress = document.querySelector('.progress');
    const durationPlayed = document.querySelector('.duration-played');
    const durationTotal = document.querySelector('.duration-total');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const addToPlaylistButton = document.getElementById('addToPlaylistButton');
    const playlistPopup = document.getElementById('playlistPopup');
    const playlistContainer = document.getElementById('playlistContainer');
    const confirmButton = document.getElementById('confirmButton');
    const cancelButton = document.getElementById('cancelButton');

    audioPlayer.addEventListener('timeupdate', updateProgress);

    function updateProgress() {
        var percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progress.style.width = percent + '%';
        durationPlayed.textContent = formatTime(audioPlayer.currentTime);
        durationTotal.textContent = formatTime(audioPlayer.duration);
    }

    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var secs = Math.floor(seconds % 60);
        if (secs < 10) {
            secs = '0' + secs;
        }
        return minutes + ':' + secs;
    }

    function playSong(mp3FilePath, songTitle) {
        // Update audio player with new source
        audioSource.src = mp3FilePath;
        audioPlayer.load();
        audioPlayer.play();

        // Update player UI with song details
        playerSongTitle.textContent = songTitle;
        playPauseButton.innerHTML = '<i class="fas fa-pause"></i>';
    }

    function togglePlayPause() {
        if (audioPlayer.paused) {
            audioPlayer.play();
            playPauseButton.innerHTML = '<i class="fas fa-pause"></i>';
        } else {
            audioPlayer.pause();
            playPauseButton.innerHTML = '<i class="fas fa-play"></i>';
        }
    }

    function toggleVolume() {
        if (audioPlayer.muted) {
            audioPlayer.muted = false;
            volumeButton.innerHTML = '<i class="fas fa-volume-up"></i>';
        } else {
            audioPlayer.muted = true;
            volumeButton.innerHTML = '<i class="fas fa-volume-mute"></i>';
        }
    }

    // Update progress bar when clicked
    progressBarContainer.addEventListener('click', (e) => {
        const containerWidth = progressBarContainer.offsetWidth;
        const clickPosition = e.offsetX;
        const percentage = clickPosition / containerWidth;
        const newTime = percentage * audioPlayer.duration;
        audioPlayer.currentTime = newTime;
    });

    // Add event listeners to play/pause and volume buttons
    playPauseButton.addEventListener('click', togglePlayPause);
    volumeButton.addEventListener('click', toggleVolume);
    prevButton.addEventListener('click', backwardSong);
    nextButton.addEventListener('click', forwardSong);

    function backwardSong() {
        audioPlayer.currentTime -= 10; // Rewind 10 seconds
    }

    function forwardSong() {
        audioPlayer.currentTime += 10; // Forward 10 seconds
    }

    fetch('recomdata.php')
        .then(response => response.json())
        .then(songs => {
            let index = 1;
            songs.forEach(song => {
                const songElement = document.createElement('div');
                songElement.classList.add(index === 4 ? 'big-square' : 'side-square');
                songElement.innerHTML = `<img src="${song.jpegFilePath}" alt="${song.songTitle}">`;
                songElement.onclick = () => playSong(song.mp3FilePath, song.songTitle);
                coverFlow.appendChild(songElement);
                index++;
            });
        });

    addToPlaylistButton.addEventListener('click', () => {
        // Fetch the current user's playlists
        fetch('getplaylists.php')
            .then(response => response.json())
            .then(playlists => {
                playlistContainer.innerHTML = '';
                playlists.forEach(playlist => {
                    const playlistElement = document.createElement('div');
                    playlistElement.innerHTML = `
                        <input type="checkbox" id="playlist_${playlist.PLAYLIST_ID}" value="${playlist.PLAYLIST_ID}">
                        <label for="playlist_${playlist.PLAYLIST_ID}">${playlist.PLAYLIST_NAME}</label>
                    `;
                    playlistContainer.appendChild(playlistElement);
                });
                playlistPopup.style.display = 'block';
            });
    });

    confirmButton.addEventListener('click', () => {
        const selectedPlaylists = [];
        document.querySelectorAll('#playlistContainer input:checked').forEach(checkbox => {
            selectedPlaylists.push(checkbox.value);
        });

        const currentSongTitle = playerSongTitle.textContent;

        // Make a request to add the current song to the selected playlists
        fetch('addtoplaylist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                songTitle: currentSongTitle,
                playlists: selectedPlaylists
            })
        })
        .then(response => response.json())
        .then(result => {
            // Handle the result
            console.log(result);
        });

        playlistPopup.style.display = 'none';
    });

    cancelButton.addEventListener('click', () => {
        playlistPopup.style.display = 'none';
    });
}





    function addProfilePageInteractions() {
    // Change Profile Picture
    document.getElementById('changeProfile').addEventListener('click', function() {
        document.getElementById('profilePicInput').click();
    });

    // Show Change Username Form
    document.getElementById('showChangeUsername').addEventListener('click', function() {
        document.getElementById('modificationContent').innerHTML = `
            <form id="changeUsernameForm">
                <label for="currentUsername">Current Username:</label>
                <input type="text" id="currentUsername" name="currentUsername" value="<?php echo $_SESSION['username']; ?>" readonly>
                <label for="newUsername">New Username:</label>
                <input type="text" id="newUsername" name="newUsername" required>
                <label for="currentPassword">Current Password:</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </form>
        `;
        document.querySelector('.submit-button').setAttribute('data-action', 'changeUsername');
    });

    // Show Change Password Form
    document.getElementById('showChangePassword').addEventListener('click', function() {
        document.getElementById('modificationContent').innerHTML = `
            <form id="changePasswordForm">
                <label for="currentUsername">Current Username:</label>
                <input type="text" id="currentUsername" name="currentUsername" value="<?php echo $_SESSION['username']; ?>" readonly>
                <label for="currentPassword">Current Password:</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword" name="newPassword" required>
                <label for="reconfirmNewPassword">Reconfirm New Password:</label>
                <input type="password" id="reconfirmNewPassword" name="reconfirmNewPassword" required>
            </form>
        `;
        document.querySelector('.submit-button').setAttribute('data-action', 'changePassword');
    });

    // Submit Modification
    document.querySelector('.submit-button').addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const currentUsername = "<?php echo $_SESSION['username']; ?>";
        const currentPassword = document.querySelector('#modificationContent input[name="currentPassword"]').value;
        let newValue = '';

        if (action === 'changeUsername') {
            newValue = document.querySelector('#modificationContent input[name="newUsername"]').value;
        } else if (action === 'changePassword') {
            const newPassword = document.querySelector('#modificationContent input[name="newPassword"]').value;
            const reconfirmNewPassword = document.querySelector('#modificationContent input[name="reconfirmNewPassword"]').value;

            if (newPassword !== reconfirmNewPassword) {
                alert('New passwords do not match!');
                return;
            }
            newValue = newPassword;
        }

        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                currentUsername: currentUsername,
                currentPassword: currentPassword,
                newValue: newValue
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (action === 'changeUsername') {
                    document.getElementById('username').textContent = newValue;
                }
                document.getElementById('modificationContent').innerHTML = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
}

// Call the function to add interactions
function addProfilePageInteractions() {
    // Change Profile Picture
    document.getElementById('changeProfile').addEventListener('click', function() {
        document.getElementById('profilePicInput').click();
    });

    // Handle Profile Picture Input Change
    document.getElementById('profilePicInput').addEventListener('change', function(event) {
        const file = event.target.files[0]; // Assuming single file selection
        const formData = new FormData();
        formData.append('profilePic', file);

        fetch('update_profile_picture.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Profile picture updated');
            }
            return response.text();
        })
        .then(newProfilePic => {
            const profileImage = document.getElementById('profileImage');
            profileImage.src = newProfilePic; // Assuming server returns the updated image path
            localStorage.setItem('profilePic', newProfilePic); // Optionally save in localStorage
            alert('Profile picture updated successfully!');
        })
        .catch(error => {
            console.error('Error updating profile picture:', error);
            alert('Profile picture updated');
        });
    });

    // Show Change Username Form
    document.getElementById('showChangeUsername').addEventListener('click', function() {
        document.getElementById('modificationContent').innerHTML = `
            <form id="changeUsernameForm">
                <label for="currentUsername">Current Username:</label>
                <input type="text" id="currentUsername" name="currentUsername" value="<?php echo $_SESSION['username']; ?>" readonly>
                <label for="newUsername">New Username:</label>
                <input type="text" id="newUsername" name="newUsername" required>
                <label for="currentPassword">Current Password:</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
            </form>
        `;
        document.querySelector('.submit-button').setAttribute('data-action', 'changeUsername');
    });

    // Show Change Password Form
    document.getElementById('showChangePassword').addEventListener('click', function() {
        document.getElementById('modificationContent').innerHTML = `
            <form id="changePasswordForm">
                <label for="currentUsername">Current Username:</label>
                <input type="text" id="currentUsername" name="currentUsername" value="<?php echo $_SESSION['username']; ?>" readonly>
                <label for="currentPassword">Current Password:</label>
                <input type="password" id="currentPassword" name="currentPassword" required>
                <label for="newPassword">New Password:</label>
                <input type="password" id="newPassword" name="newPassword" required>
                <label for="reconfirmNewPassword">Reconfirm New Password:</label>
                <input type="password" id="reconfirmNewPassword" name="reconfirmNewPassword" required>
            </form>
        `;
        document.querySelector('.submit-button').setAttribute('data-action', 'changePassword');
    });

    // Submit Modification
    document.querySelector('.submit-button').addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const currentUsername = "<?php echo $_SESSION['username']; ?>";
        const currentPassword = document.querySelector('#modificationContent input[name="currentPassword"]').value;
        let newValue = '';

        if (action === 'changeUsername') {
            newValue = document.querySelector('#modificationContent input[name="newUsername"]').value;
        } else if (action === 'changePassword') {
            const newPassword = document.querySelector('#modificationContent input[name="newPassword"]').value;
            const reconfirmNewPassword = document.querySelector('#modificationContent input[name="reconfirmNewPassword"]').value;

            if (newPassword !== reconfirmNewPassword) {
                alert('New passwords do not match!');
                return;
            }
            newValue = newPassword;
        }

        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                currentUsername: currentUsername,
                currentPassword: currentPassword,
                newValue: newValue
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (action === 'changeUsername') {
                    document.getElementById('username').textContent = newValue;
                }
                document.getElementById('modificationContent').innerHTML = '';
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
}


// Call the function to add interactions
addProfilePageInteractions();



// Function to check subscription and load content into the tab
function checkSubscription() {
    const url = 'check_subscription.php';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        const tabContent = document.getElementById('tabContent');
        if (data.subscription_status === 'Subscribed') {
            fetch('sub.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    tabContent.innerHTML = data;
                    addPlaylistPageInteractions();
                })
                .catch(error => {
                    tabContent.innerHTML = `<p>Error loading content: ${error.message}</p>`;
                });
        } else if (data.subscription_status === 'Not Subscribed') {
            window.location.href = 'notsub.php';
        } else {
            console.error('Invalid subscription status.');
        }
    })
    .catch(error => {
        console.error('Error checking subscription:', error);
    });
}




// Function to handle interactions on the playlist page
function addPlaylistPageInteractions() {
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
                addPlaylistButton.addEventListener('click', openNewPlaylistPopup);
                playlistContainer.appendChild(addPlaylistButton);

                // Iterate through fetched playlists
                data.forEach(playlist => {
                    const playlistItem = createPlaylistItem(playlist);
                    playlistContainer.appendChild(playlistItem);
                });
            })
            .catch(error => console.error('Error fetching playlists:', error));
    }

    // Function to create a playlist item
    function createPlaylistItem(playlist) {
        const playlistItem = document.createElement('div');
        playlistItem.className = 'playlist-item';
        playlistItem.textContent = playlist.name;
        playlistItem.setAttribute('data-id', playlist.id);
        playlistItem.addEventListener('click', () => {
            currentPlaylistId = playlist.id; // Store current playlist ID
            displaySongs(playlist);
        });
        return playlistItem;
    }

    // Function to display songs for a selected playlist
    function displaySongs(playlist) {
        const songListContainer = document.getElementById('song-list');
        songListContainer.innerHTML = ''; // Clear existing songs

        playlist.songs.forEach(song => {
            const songItem = createSongItem(song);
            songListContainer.appendChild(songItem);
        });
    }

    // Function to create a song item
    function createSongItem(song) {
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
        deleteIcon.addEventListener('click', () => deleteSongFromPlaylist(song.id));
        songItem.appendChild(deleteIcon);

        // Make song-item draggable
        songItem.draggable = true;
        songItem.addEventListener('dragstart', handleDragStart);
        songItem.addEventListener('dragover', handleDragOver);
        songItem.addEventListener('drop', handleDrop);

        // Play song when clicked
        songItem.addEventListener('click', () => playSong(song.title));

        return songItem;
    }

    // Function to play a song
    function playSong(songTitle) {
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSource = document.getElementById('audioSource');
        audioSource.src = `songs/${songTitle}.mp3`;
        audioPlayer.load();
        audioPlayer.play();
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

    // Event listeners for popup buttons
    document.querySelector('.button-ok').addEventListener('click', addNewPlaylist);
    document.querySelector('.button-cancel').addEventListener('click', closeNewPlaylistPopup);

    // Fetch playlists when the page loads
    fetchPlaylists();
}
// Call the function when the page is loaded
addPlaylistPageInteractions();


</script>
</body>
</html>
