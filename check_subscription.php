<?php
session_start();

// Function to check subscription status and return JSON response
function checkSubscriptionStatus($servername, $username, $password, $dbname) {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(array('error' => 'Connection failed: ' . $conn->connect_error)));
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

            // Return JSON response
            echo json_encode(array('subscription_status' => $subscription_status));
        } else {
            echo json_encode(array('error' => 'User not found or subscription status not available.'));
        }
    } else {
        echo json_encode(array('error' => 'User session not found.'));
    }

    $conn->close();
}

// Example usage (assuming this file is included via AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Example usage of function
    $servername = "localhost";
    $username = "root";
    $password = ""; // Replace with your MySQL root password
    $dbname = "song_streaming";

    checkSubscriptionStatus($servername, $username, $password, $dbname);
} else {
    echo json_encode(array('error' => 'Invalid request method.'));
}
?>
