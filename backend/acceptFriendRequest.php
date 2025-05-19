<?php
include('config.php'); // include your db connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_id'])) {
    $userId = $_SESSION['user_id'];
    $friendId = intval($_POST['friend_id']);

    $sql = "UPDATE friends 
            SET status = 'approved'
            WHERE f1_id = $friendId AND f2_id = $userId AND status = 'pending'";

    if (mysqli_query($conn, $sql)) {
        header('Location: ../frontend/ui.php'); // redirect back to ui.php after accepting
        exit();
    } else {
        echo "Failed to accept friend request.";
    }
}
?>