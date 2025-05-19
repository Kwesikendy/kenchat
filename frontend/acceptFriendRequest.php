<?php
include('../backend/config.php');  // Corrected path to config.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_id'])) {
    $userId = $_SESSION['user_id'];
    $friendId = intval($_POST['friend_id']);  // Ensure it's an integer

    // Check if the connection is successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare SQL query to update friend status
    $sql = "UPDATE friends 
            SET status = 'approved'
            WHERE (f1_id = $friendId AND f2_id = $userId OR f1_id = $userId AND f2_id = $friendId) 
            AND status = 'pending'";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Redirect to the UI page after successful acceptance
        header('Location: ../frontend/ui.php');
        exit();
    } else {
        echo "Failed to accept friend request: " . mysqli_error($conn);  // Display error if query fails
    }
}
?>
