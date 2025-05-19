<?php

include('config.php'); // include your db connection
session_start();

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// --- Function to get friendship id between two users ---
function getFriendshipId($user1, $user2){
    global $conn;
    $sql = "SELECT id FROM friends WHERE ((f1_id = ? AND f2_id = ?) OR (f1_id = ? AND f2_id = ?)) AND status = 'approved' LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $user1, $user2, $user2, $user1);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(!$result){
        error_log("Error fetching friendship id: " . mysqli_error($conn));
        return null;
    }

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    } else {
        return null;
    }
}

// --- Function to get all messages between users ---
function getMessages($userId, $friendId, $since = null){
    global $conn;

    $roomId = getFriendshipId($userId, $friendId);
    if(!$roomId){
        return [
            'error' => true,
            'message' => 'No friendship found'
        ];
    }

    $sql = "SELECT * FROM messages WHERE room_id = ?";
    if($since){
        $sql .= " AND date > ?";
    }
    $sql .= " ORDER BY date ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if($since){
        mysqli_stmt_bind_param($stmt, "is", $roomId, $since);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $roomId);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(!$result){
        return [
            'error' => true,
            'message' => 'Error fetching messages'
        ];
    }

    $messages = [];
    while($row = mysqli_fetch_assoc($result)){
        $messages[] = $row;
    }

    return [
        'error' => false,
        'messages' => $messages,
        'room_id' => $roomId
    ];
}

// --- Function to send a message ---
function sendMessage($userId, $friendId, $message){
    global $conn;
    $roomId = getFriendshipId($userId, $friendId);
    if(!$roomId){
        return [
            'error' => true,
            'message' => 'No friendship found'
        ];
    }

    $message = mysqli_real_escape_string($conn, $message);

    $sql = "INSERT INTO messages (room_id, sender_id, message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iis", $roomId, $userId, $message);

    if(mysqli_stmt_execute($stmt)){
        return [
            'status' => 'success',
            'error' => false,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => mysqli_insert_id($conn),
                'room_id' => $roomId,
                'sender_id' => $userId,
                'receiver_id' => $friendId,
                'message' => $message,
                'date' => date('Y-m-d H:i:s')
            ]
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Error sending message'
        ];
    }
}

// --- Function to get recent chats ---
function getRecentChats($userId){
    global $conn;
    $sql = "SELECT friends.id, CASE WHEN f1_id = ? THEN f2_id ELSE f1_id END AS friend_id,
            CASE WHEN f1_id = ? THEN f2_name ELSE f1_name END AS friend_name
            FROM friends
            WHERE (f1_id = ? OR f2_id = ?) AND status = 'approved'";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiii", $userId, $userId, $userId, $userId);
    mysqli_stmt_execute($stmt);
    $friendsResult = mysqli_stmt_get_result($stmt);

    if(!$friendsResult){
        return [
            'error' => true,
            'message' => 'Error fetching recent chats'
        ];
    }

    $chats = [];

    while($friend = mysqli_fetch_assoc($friendsResult)){
        $friendId = $friend['friend_id'];
        $roomId = $friend['id'];

        $messageSql = "SELECT * FROM messages WHERE room_id = $roomId ORDER BY date DESC LIMIT 1";
        $messageResult = mysqli_query($conn, $messageSql);

        if(!$messageResult){
            error_log("Error fetching messages: " . mysqli_error($conn));
            continue;
        }

        if(mysqli_num_rows($messageResult) > 0){
            $message = mysqli_fetch_assoc($messageResult);

            $chats[] = [
                'friend_id' => $friendId,
                'friend_name' => $friend['friend_name'],
                'last_message' => $message['message'],
                'message_type' => $message['type'] ?? 'text',
                'time' => $message['date'],
                'is_sender' => $message['sender_id'] == $userId
            ];
        } else {
            // No message yet, just the friend
            $chats[] = [
                'friend_id' => $friendId,
                'friend_name' => $friend['friend_name'],
                'last_message' => 'Start a conversation',
                'message_type' => 'text',
                'time' => null,
                'is_sender' => false
            ];
        }
    }

    // Sort chats by time
    usort($chats, function($a, $b){
        if(!$a['time']) return 1;
        if(!$b['time']) return -1;
        return strtotime($b['time']) - strtotime($a['time']);
    });

    return [
        'status' => 'success',
        'chats' => $chats
    ];
}

// --- Handle GET request ---
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(!isset($_GET['user_id'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Not logged in'
        ]);
        exit();
    }

    $userId = intval($_GET['user_id']);

    if(isset($_GET['action'])){
        if($_GET['action'] === 'getMessages' && isset($_GET['friend_id'])){
            $friendId = intval($_GET['friend_id']);
            $since = isset($_GET['since']) ? $_GET['since'] : null;

            $result = getMessages($userId, $friendId, $since);

            if($result['error']){
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message']
                ]);
            } else {
                echo json_encode([
                    'status' => 'success',
                    'messages' => $result['messages']
                ]);
            }
            exit();
        }

        if($_GET['action'] === 'getRecentChats'){
            $result = getRecentChats($userId);
            echo json_encode($result);
            exit();
        }
    }
}

// --- Handle POST request ---
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!isset($_POST['user_id'])){
        echo json_encode([
            'status' => 'error',
            'message' => 'Not logged in'
        ]);
        exit();
    }

    $userId = intval($_POST['user_id']);

    if(isset($_POST['action'])){
        if($_POST['action'] === 'sendMessage' && isset($_POST['friend_id']) && isset($_POST['message'])){
            $friendId = intval($_POST['friend_id']);
            $message = $_POST['message'];

            $result = sendMessage($userId, $friendId, $message);
            echo json_encode($result);
            exit();
        }
    }
}

// If invalid request
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
]);
?>
