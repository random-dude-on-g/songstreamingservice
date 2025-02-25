<?php
session_start(); // Make sure this is the very first line

$servername = "localhost";
$username = "root";
$password = ""; // your MySQL root password
$dbname = "song_streaming";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

// Check if username already exists
$sql_check_username = "SELECT * FROM ACCOUNT WHERE ACC_NAME = ?";
$stmt_check_username = $conn->prepare($sql_check_username);
$stmt_check_username->bind_param("s", $user);
$stmt_check_username->execute();
$result_check_username = $stmt_check_username->get_result();

if ($result_check_username->num_rows > 0) {
    // Username already exists
    echo '<script>alert("Sorry, that username is taken");</script>';
    echo '<script>window.location.href = "sp.html";</script>';
    exit();
} else {
    // Insert new user into ACCOUNT table
    $sql_insert_account = "INSERT INTO ACCOUNT (ACC_NAME, ACC_PASSWORD) VALUES (?, ?)";
    $stmt_insert_account = $conn->prepare($sql_insert_account);
    $stmt_insert_account->bind_param("ss", $user, $pass);

    if ($stmt_insert_account->execute()) {
        // Get the inserted account ID
        $accountId = $stmt_insert_account->insert_id;

        // Insert into CUSTOMER table
        $sql_insert_customer = "INSERT INTO CUSTOMER (ACC_ID, SUBSCRIPTION_STATUS) VALUES (?, 0)";
        $stmt_insert_customer = $conn->prepare($sql_insert_customer);
        $stmt_insert_customer->bind_param("i", $accountId);

        if ($stmt_insert_customer->execute()) {
            // Registration successful
            echo '<script>alert("Signup successful!");</script>';
            echo '<script>window.location.href = "sp.html";</script>';
            exit();
        } else {
            echo "Error inserting into CUSTOMER table: " . $conn->error;
        }
    } else {
        echo "Error inserting into ACCOUNT table: " . $conn->error;
    }
}

$stmt_check_username->close();
$stmt_insert_account->close();
$stmt_insert_customer->close();
$conn->close();
?>
