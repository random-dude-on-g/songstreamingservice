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
            font-family: Arial, sans-serif;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }
  
        .container {
            width: 100%;
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
        .profile-picc {
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
    <div class="left-section">
        <div class="profile-wrapper">
            <div>
                <div class="profile-picc" id="profilePic">
                    <img src="<?php echo $profilePic; ?>" alt="Profile Picture" id="profileImage" style="width: 100%; height: 100%; border-radius: 50%;">
                </div>
                <button class="button" id="changeProfile">Change Profile Picture</button>
                <form id="profilePicForm" action="user_profile_pic.php" method="post" enctype="multipart/form-data" style="display: none;">
                    <input type="file" name="profile_pic" id="profilePicInput" accept="image/*" onchange="document.getElementById('profilePicForm').submit();">
                </form>
            </div>
            <div class="profile-info">
                <div id="subscriptionStatus" class="subscribed-text" style="display: none;">Subscribed</div>
                <div id="username" class="username"><?php echo $_SESSION['username']; ?></div>
                <button class="button" id="showChangeUsername">Change Username</button>
                <button class="button" id="showChangePassword">Change Password</button>
            </div>
        </div>
    </div>
    <div class="right-section">
        <div class="big-box">
            <div class="header">Modification</div>
            <div id="modificationContent"></div>
            <button class="submit-button">OK</button>
        </div>
    </div>

    <script>
        <?php include 'path/to/your/javascript/file.js'; ?>
        // Or directly include the function here if not using a separate file
        addProfilePageInteractions();
    </script>
</body>
</html>
