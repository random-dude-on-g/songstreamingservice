<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in']);
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
            max-width: 100%;
            justify-content: center;
            align-items: center;
            width: 100%;
            background-color: #000;
            color: #fff;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .subscription-section {
            text-align: center;
            margin-top: 20px;
            width: 100%;
            max-width: 100%;
        }

        .subscription-section img {
            width: 20%;
            margin-bottom: 20px;
        }

        .subscription-section p {
            margin: 5px 0;
        }

        .subscription-section .subscribe-button {
            margin-top: 10px;
            padding: 40px 50px;
            background-color: #ffa500;
            border: 15px, white;
            color: #000;
            cursor: pointer;
            border-radius: 15px;
            font-size: 50px;
            transition: background-color 0.3s;
        }

        .subscription-section .subscribe-button:hover {
            background-color: #ff8c00;
        }

        .popup {
            display: none; /* Hidden by default */
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 50%;
            padding: 20px;
            background-color: #333;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .popup h2 {
            margin-top: 0;
            font-size: 30px;
        }

        .popup label {
            display: block;
            margin: 10px 0 5px;
            font-size: 20px;
        }

        .popup input {
            width: 90%;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .popup .popup-buttons {
            display: flex;
            justify-content: space-between;
            font-size: 30px;
        }

        .popup .popup-buttons .button-confirm {
            background-color: #ffa500;
            border: none;
            padding: 10px 20px;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
        }

        .popup .popup-buttons .button-confirm:hover {
            background-color: #ff8c00;
        }

        .popup .popup-buttons .button-cancel {
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

<div class="subscription-section">
    <img src="SSS.png" alt="SSS Logo">
    <p style="font-size: 45px;">You are not subscribed to the system :(</p>
    <p style="font-size: 40px;">We offer playlists for subscribed members only</p>
    <button class="subscribe-button" onclick="openPopup()">Subscribe now RMXX</button>
</div>

<div class="overlay" id="overlay"></div>

<div class="popup" id="popup">
    <h2>Payment Window</h2>
    <label for="email">Email</label>
    <input type="email" id="email" required>
    <label for="card-number">Card number</label>
    <input type="text" id="card-number" required>
    <label for="expiry-date">Expiry date</label>
    <input type="text" id="expiry-date" required style="width: 60%;">
    <label for="ccv">CCV</label>
    <input type="text" id="ccv" required style="width: 30%;">
    <div class="popup-buttons">
        <button class="button-confirm" onclick="confirmSubscription()">Confirm</button>
        <button class="button-cancel" onclick="closePopup()">Cancel</button>
    </div>
</div>

<script>
    function openPopup() {
        document.getElementById('popup').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function closePopup() {
        document.getElementById('popup').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
        window.location.href = 'homepage.php';
    }

    function confirmSubscription() {
        const email = document.getElementById('email').value.trim();
        const cardNumber = document.getElementById('card-number').value.trim();
        const expiryDate = document.getElementById('expiry-date').value.trim();
        const ccv = document.getElementById('ccv').value.trim();

        // Validate inputs
        if (!email || !cardNumber || !expiryDate || !ccv) {
            alert('Please fill in all fields.');
            return;
        }

        fetch('update_subscription.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                cardNumber: cardNumber,
                expiryDate: expiryDate,
                ccv: ccv
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Subscription confirmed!');
                window.location.href = 'homepage.php'; // Redirect to homepage after successful subscription
            } else {
                alert('Failed to confirm subscription. Please try again later.');
            }
        })
        .catch(error => {
            console.error('Error confirming subscription:', error);
            alert('Failed to confirm subscription. Please try again later.');
        });
    }
</script>
</body>
</html>

