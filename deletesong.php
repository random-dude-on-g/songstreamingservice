<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // your MySQL root password
$dbname = "song_streaming";

// Check if song ID is provided via GET
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $song_id = intval($_GET['id']);

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete from STORE table first
    $delete_store_sql = "DELETE FROM STORE WHERE SONG_ID = $song_id";

    if ($conn->query($delete_store_sql) === TRUE) {
        // Delete from MANAGE table
        $delete_manage_sql = "DELETE FROM MANAGE WHERE SONG_ID = $song_id";

        if ($conn->query($delete_manage_sql) === TRUE) {
            // Proceed to delete from SONG table
            $delete_song_sql = "DELETE FROM SONG WHERE SONG_ID = $song_id";

            if ($conn->query($delete_song_sql) === TRUE) {
                // Redirect back to adminpage.php after successful deletion
                header("Location: adminpage.php");
                exit();
            } else {
                echo "Error deleting song record: " . $conn->error;
            }
        } else {
            echo "Error deleting manage record: " . $conn->error;
        }
    } else {
        echo "Error deleting store record: " . $conn->error;
    }

    // Close the connection
    $conn->close();
} else {
    // If song ID is not provided, redirect back to adminpage.php
    header("Location: adminpage.php");
    exit();
}
?>
