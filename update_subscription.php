<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL root password
$dbname = "song_streaming";

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'];
    $cardNumber = $data['cardNumber'];
    $expiryDate = $data['expiryDate'];
    $ccv = $data['ccv'];

    // Perform any necessary validation on the data (example: sanitize, validate card info)

    // Update subscription status in the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(array('error' => 'Connection failed: ' . $conn->connect_error));
        exit();
    }

    // Ensure username is stored in the session
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];

        // Example query (adjust according to your database schema)
        $sql = "UPDATE CUSTOMER C
                INNER JOIN ACCOUNT A ON A.ACC_ID = C.ACC_ID
                SET C.SUBSCRIPTION_STATUS = 1
                WHERE A.ACC_NAME = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => 'Error updating subscription status: ' . $conn->error));
        }

        $stmt->close();
    } else {
        echo json_encode(array('error' => 'User is not logged in.'));
    }

    $conn->close();
} else {
    echo json_encode(array('error' => 'Invalid request method.'));
}
?>
