<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: sp.html");
    exit();
}

// Fetch the profile picture from the session
$profilePic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Modification</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }
  
        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            align-items: center;
        }

        .left-section {
            flex: 1;
            display: flex;
            justify-content: center; 
            align-items: center; 
            height: 100%; 
        }

        .profile-wrapper {
            display: flex;
        }
        .profile-info {
            margin-left: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .right-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #ccc;
            margin-bottom: 20px;
        }
        .button {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #fff;
            background-color: #FFA500;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #FF8C00;
        }
        .subscribed-text {
            background-color: #007BFF;
            color: #fff;
            padding: 5px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .big-box {
            width: 80%;
            border: 2px solid #fff;
            padding: 20px;
            border-radius: 10px;
            background-color: #333;
        }
        .header {
            font-size: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .textbox {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #fff;
            border-radius: 5px;
            background-color: #000;
            color: #fff;
        }
        .submit-button {
            width: 100%;
            padding: 10px;
            border: 1px solid #fff;
            background-color: #FFA500;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
        }
        .submit-button:hover {
            background-color: #FF8C00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="profile-wrapper">
                <div>
                    <div class="profile-pic" id="profilePic">
                        <img src="<?php echo $profilePic; ?>" alt="Profile Picture" id="profileImage" style="width: 100%; height: 100%; border-radius: 50%;">
                    </div>
                    <button class="button" onclick="changeProfilePicture()">Change Profile Picture</button>
                    <form id="profilePicForm" action="upload_profile_pic.php" method="post" enctype="multipart/form-data" style="display: none;">
                        <input type="file" name="profile_pic" id="profilePicInput" accept="image/*" onchange="document.getElementById('profilePicForm').submit();">
                    </form>
                </div>
                <div class="profile-info">
                    <div id="subscriptionStatus" class="subscribed-text" style="display: none;">Subscribed</div>
                    <div id="username" class="username"></div>
                    <button class="button" onclick="showChangeUsername()">Change Username</button>
                    <button class="button" onclick="showChangePassword()">Change Password</button>
                </div>
            </div>
        </div>
        <div class="right-section">
            <div class="big-box">
                <div class="header">Modification</div>
                <div id="modificationContent"></div>
                <button class="submit-button" onclick="submitModification()">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Placeholder variables for user's information
        const user = {
            isSubscribed: true,
            username: 'User123'
        };

        // Function to update the UI based on user's subscription status and username
        function updateUI() {
            if (user.isSubscribed) {
                document.getElementById('subscriptionStatus').style.display = 'block';
            } else {
                document.getElementById('subscriptionStatus').style.display = 'none';
            }
            document.getElementById('username').innerText = user.username;
        }

        // Function to handle showing change username form
        function showChangeUsername() {
            document.getElementById('modificationContent').innerHTML = `
                <input type="text" class="textbox" id="currentUsername" placeholder="Current Username" value="${user.username}" readonly>
                <input type="text" class="textbox" id="newUsername" placeholder="New Username">
                <input type="password" class="textbox" id="currentPassword" placeholder="Current Password">
            `;
        }

        // Function to handle showing change password form
        function showChangePassword() {
            document.getElementById('modificationContent').innerHTML = `
                <input type="text" class="textbox" id="currentUsername" placeholder="Current Username" value="${user.username}" readonly>
                <input type="password" class="textbox" id="currentPassword" placeholder="Current Password">
                <input type="password" class="textbox" id="newPassword" placeholder="New Password">
                <input type="password" class="textbox" id="reconfirmNewPassword" placeholder="Reconfirm New Password">
            `;
        }

        // Function to handle changing profile picture
        function changeProfilePicture() {
            document.getElementById('profilePicInput').click();
        }

        // Function to handle form submission based on the selected action
        function submitModification() {
            const currentUsername = document.getElementById('currentUsername').value;
            const currentPassword = document.getElementById('currentPassword') ? document.getElementById('currentPassword').value : '';
            const newUsername = document.getElementById('newUsername') ? document.getElementById('newUsername').value : '';
            const newPassword = document.getElementById('newPassword') ? document.getElementById('newPassword').value : '';
            const reconfirmNewPassword = document.getElementById('reconfirmNewPassword') ? document.getElementById('reconfirmNewPassword').value : '';

            // Logic to handle username change
            if (newUsername) {
                if (currentPassword) {
                    alert(`Username changed from ${currentUsername} to ${newUsername}`);
                    // Implement actual username change logic here
                } else {
                    alert('Please enter current password to change username.');
                }
            }

            // Logic to handle password change
            if (newPassword && reconfirmNewPassword) {
                if (currentPassword && newPassword === reconfirmNewPassword) {
                    alert('Password changed successfully.');
                    // Implement actual password change logic here
                } else {
                    alert('Please ensure all password fields are filled correctly.');
                }
            }
        }

        // Initialize the UI with user's information
        updateUI();
    </script>
</body>
</html>
