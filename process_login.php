<?php
session_start();

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

$sql = "SELECT ACC_ID FROM ACCOUNT WHERE ACC_NAME = ? AND ACC_PASSWORD = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Login successful, set session variables
    $row = $result->fetch_assoc();
    $acc_id = $row['ACC_ID'];
    $_SESSION['username'] = $user;

    // Check if the user is an Admin and exists in the STAFF table
    if ($user === 'Admin') {
        $sqlStaff = "SELECT STAFF_NAME FROM STAFF WHERE ACC_ID = ?";
        $stmtStaff = $conn->prepare($sqlStaff);
        $stmtStaff->bind_param("i", $acc_id);
        $stmtStaff->execute();
        $resultStaff = $stmtStaff->get_result();

        if ($resultStaff->num_rows > 0) {
            // User is an admin
            $staffRow = $resultStaff->fetch_assoc();
            $staffName = $staffRow['STAFF_NAME'];
            $_SESSION['staff_name'] = $staffName;

            echo '<script>alert("Login successful! Redirecting to admin page.");</script>';
            echo '<script>window.location.href = "adminpage.php";</script>';
            exit(); // Ensure script stops execution after redirection
        }
    }

    echo '<script>alert("Login successful!");</script>';
    echo '<script>window.location.href = "homepage.php";</script>';
    exit(); // Ensure script stops execution after redirection
} else {
    echo '<script>alert("Invalid username or password.");</script>';
    echo '<script>window.location.href = "sp.html";</script>';
    exit(); // Ensure script stops execution after redirection
}

$stmt->close();
$conn->close();
?>
