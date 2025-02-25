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
        }

        .container {
            width: 100%; /* Ensure the container width remains consistent */
            max-width: calc(100% - 170px); /* Adjust to prevent shifting */
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
        <div class="profile-pic" id="profilePic">
            <img src="default-profile.png" alt="Profile Picture" id="profileImage">
        </div>
    </div>
</header>
<main>
    <div class="tabs">
        <div class="tab active" onclick="showTabContent('manage')">Manage songs</div>
        <div class="tab" onclick="showTabContent('profile')">Profile</div>
    </div>
    <div class="container">
        <div id="manage" class="tab-content active">
            <!-- Content for Manage songs will be loaded dynamically -->
        </div>
        <div id="profile" class="tab-content">
            <!-- Content for Profile will be loaded dynamically -->
        </div>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profilePic = document.getElementById('profilePic');
        const profileImage = document.getElementById('profileImage');

        profilePic.addEventListener('click', () => {
            const imageInput = document.createElement('input');
            imageInput.type = 'file';
            imageInput.accept = 'image/*';

            imageInput.onchange = (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();

                reader.onloadend = () => {
                    profileImage.src = reader.result;
                    localStorage.setItem('profilePic', reader.result);
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            };

            imageInput.click();
        });

        const savedProfilePic = localStorage.getItem('profilePic');
        if (savedProfilePic) {
            profileImage.src = savedProfilePic;
        }

        const usernameBox = document.getElementById('usernameBox');
        const savedUsername = localStorage.getItem('username');
        if (savedUsername) {
            usernameBox.textContent = savedUsername;
        }

        // Preload content for tabs
        loadTabContent('manage', 'managesongpage.php');
        loadTabContent('profile', 'adminprofilepage.php');
    });

    function loadTabContent(tabId, url) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                const tabContent = document.getElementById(tabId);
                tabContent.innerHTML = data;
            })
            .catch(error => {
                console.error('Error loading content:', error);
            });
    }

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

        const selectedTab = document.getElementById(tabId);
        selectedTab.classList.add('active');
    }
</script>
</body>
</html>
