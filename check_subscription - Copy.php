<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL root password
$dbname = "test_song_streaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and get username from session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // SQL query to fetch subscription status for the logged-in user by username
    $sql = "SELECT A.ACC_ID, A.ACC_NAME, CASE WHEN C.SUBSCRIPTION_STATUS = 1 THEN 'Subscribed' ELSE 'Not Subscribed' END AS Subscription_Status
            FROM ACCOUNT A
            LEFT JOIN CUSTOMER C ON A.ACC_ID = C.ACC_ID
            WHERE A.ACC_NAME = '$username'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch subscription status for the user
        $row = $result->fetch_assoc();
        $subscription_status = $row["Subscription_Status"];

        // Redirect based on subscription status
        if ($subscription_status === 'Subscribed') {
            header('Location: sub.php');
            exit;
        } elseif ($subscription_status === 'Not Subscribed') {
            header('Location: notsub.php');
            exit;
        } else {
            echo "Invalid subscription status.";
        }
    } else {
        echo "User not found or subscription status not available.";
    }
} else {
    echo "User session not found.";
}

$conn->close();
?>
