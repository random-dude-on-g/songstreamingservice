<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['staff_name'])) {
    // Redirect to login page if not logged in
    header('Location: sp.html');
    exit();
}

$staffName = $_SESSION['staff_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Streaming Website</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            width:100%;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: black;
            width: 95%;
            margin-top: 20px;
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
            width: 50px; /* Adjust width */
            height: 50px; /* Adjust height */
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
            width: 100%;
            justify-content: center;
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
        }

        .container {
            width: 85%;
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
            <?php echo htmlspecialchars($staffName); ?>
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
        <div class="tab active" onclick="showTabContent('manage')">Manage songs</div>
        <div class="tab" onclick="showTabContent('adminprofile')">Profile</div>
    </div>
    <div class="container">
        <div id="manage" class="tab-content active">
            <!-- Content for Manage songs will be loaded dynamically -->
        </div>
        <div id="adminprofile" class="tab-content">
            <!-- Content for Profile will be loaded dynamically -->
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const usernameBox = document.getElementById('usernameBox');
        const savedUsername = localStorage.getItem('username');
        if (savedUsername) {
            usernameBox.textContent = savedUsername;
        }

        // Preload content for tabs
        showTabContent('manage');
    });

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

        const tabContent = document.getElementById(tabId);
        tabContent.classList.add('active');

        fetch(`${tabId}.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                tabContent.innerHTML = data;
                if (tabId === 'manage') {
                    addManagePageInteractions();
                } else if (tabId === 'adminprofile') {
                    addProfilePageInteractions();
                }
            })
            .catch(error => {
                tabContent.innerHTML = `<p>Error loading content: ${error.message}</p>`;
            });
    }

function addManagePageInteractions() {
    // Example: Add event listeners or dynamic behavior here
    const table = document.querySelector('table');
    table.addEventListener('click', function(event) {
        const target = event.target;
        if (target.classList.contains('remove-button')) {
            if (confirm('Are you sure you want to delete this song?')) {
                // Perform AJAX request or form submission to deletesong.php
                const songId = target.dataset.songId;
                fetch(`deletesong.php?id=${songId}`, { method: 'GET' })
                    .then(response => {
                        if (response.ok) {
                            // Reload the page or update the UI accordingly
                            window.location.reload();
                        } else {
                            throw new Error('Failed to delete song');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting song:', error);
                    });
            }
        } else if (target.classList.contains('add-button')) {
            // Change to addsong.php when 'Add' button is clicked
            fetch('addsong.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    const tabContent = document.getElementById('manage');
                    tabContent.innerHTML = data;
                    // Optionally, you can add further interactions or functionalities for the add song page here
                })
                .catch(error => {
                    console.error('Error loading add song page:', error);
                });
        }
    });
}



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

        fetch('admin_profile_picture.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to update profile picture');
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
            alert('Failed to update profile picture');
        });
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

 function loadAddSongPage() {
        fetch('addsong.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('manage').innerHTML = data;
            })
            .catch(error => console.error('Error loading add song page:', error));
    }

 function loadEditSongDetailsPage(songId) {
        // Show the manage detail song UI with the song details
        fetch('managedetailsong.php?song_id=' + songId)
            .then(response => response.text())
            .then(data => {
                const manageContainer = document.getElementById('manage');
                manageContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error loading managedetailsong.php:', error);
            });
    }

</script>

</body>
</html>
