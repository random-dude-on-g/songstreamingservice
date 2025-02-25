<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // your MySQL root password
$dbname = "song_streaming";

// Function to establish database connection
function connectToDatabase() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to validate current username and password
function validateCredentials($currentUsername, $currentPassword) {
    $conn = connectToDatabase();
    $username = $conn->real_escape_string($currentUsername);
    $password = $conn->real_escape_string($currentPassword);

    $sql = "SELECT * FROM ACCOUNT WHERE ACC_NAME='$username' AND ACC_PASSWORD='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return true; // Credentials are valid
    } else {
        return false; // Credentials are not valid
    }
}

// Handle POST request from JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data sent from frontend
    $data = json_decode(file_get_contents("php://input"));

    // Extract data from JSON
    $action = $data->action;
    $currentUsername = $data->currentUsername;
    $currentPassword = $data->currentPassword;
    $newValue = $data->newValue;

    // Validate current username and password
    if (validateCredentials($currentUsername, $currentPassword)) {
        // Credentials are valid, proceed with update
        $conn = connectToDatabase();
        $currentUsername = $conn->real_escape_string($currentUsername);

        if ($action === 'changeUsername') {
            // Update username in SESSION and database
            $_SESSION['username'] = $newValue;
            $sql = "UPDATE ACCOUNT SET ACC_NAME='$newValue' WHERE ACC_NAME='$currentUsername'";
        } elseif ($action === 'changePassword') {
            // Update password in database
            $newPassword = $conn->real_escape_string($newValue);
            $sql = "UPDATE ACCOUNT SET ACC_PASSWORD='$newPassword' WHERE ACC_NAME='$currentUsername'";
        }

        if ($conn->query($sql) === TRUE) {
            $response = array('success' => true, 'message' => 'Profile updated successfully.');
        } else {
            $response = array('success' => false, 'message' => 'Error updating profile: ' . $conn->error);
        }

        $conn->close();
    } else {
        // Invalid credentials
        $response = array('success' => false, 'message' => 'Invalid current username or password.');
    }

    // Return JSON response to frontend
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
