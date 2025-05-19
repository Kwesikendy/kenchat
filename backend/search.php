<?php
ob_start();
ini_set('memory_limit', '10G');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('config.php');

header('Content-Type: application/json');

// --- Function to search users ---
function searchUserByEmail($email) {
    global $conn;

    $users = [];

    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT id, name, email FROM user WHERE email LIKE '%$email%'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $userId = $_SESSION['user_id'] ?? 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $friendId = $row['id'];

            if ($userId == $friendId) {
                continue;
            }

            $friendshipSql = "
                SELECT status FROM friends 
                WHERE (f1_id = $userId AND f2_id = $friendId) 
                   OR (f1_id = $friendId AND f2_id = $userId)
                LIMIT 1
            ";
            $friendshipResult = mysqli_query($conn, $friendshipSql);

            if ($friendshipResult && mysqli_num_rows($friendshipResult) > 0) {
                $statusRow = mysqli_fetch_assoc($friendshipResult);
                $row['friend_status'] = $statusRow['status'];
            } else {
                $row['friend_status'] = null;
            }

            $users[] = $row;
        }
    }

    return $users;
}

// --- Function to send friend request ---
function sendFriendRequest($userId, $friendId, $userName, $friendName) {
    global $conn;

    $checkSql = "SELECT * FROM friends 
    WHERE (f1_id = $userId AND f2_id = $friendId) 
       OR (f1_id = $friendId AND f2_id = $userId)";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        return [
            'status' => 'error',
            'message' => 'Friend request already sent or already friends.'
        ];
    }

    $sql = "INSERT INTO friends (f1_id, f1_name, f2_id, f2_name, status)
            VALUES ('$userId', '$userName', '$friendId', '$friendName', 'pending')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        return [
            'status' => 'success',
            'message' => 'Friend request sent successfully.'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to send friend request.'
        ];
    }
}

// --- Main Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // --- Handle Search ---
    if (isset($_GET['email'])) {
        $email = trim($_GET['email']);

        if ($email !== '') {
            $users = searchUserByEmail($email);

            if (!empty($users)) {
                echo json_encode([
                    'status' => 'success',
                    'users' => $users,
                    'message' => 'Search results fetched successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No users found.'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email cannot be empty.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email parameter missing.'
        ]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Handle Friend Request ---
    if (isset($_POST['action']) && $_POST['action'] === 'addFriend' && isset($_POST['friend_id'] )&& isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $userSql = "SELECT name FROM user WHERE id = $userId";
        $userResult = mysqli_query($conn, $userSql);
        $userName = mysqli_fetch_assoc($userResult)['name'];

        $friendId = $_POST['friend_id'];
        $friendSql = "SELECT name FROM user WHERE id = $friendId";
        $friendRResult = mysqli_query($conn, $friendSql);
        $friendName = mysqli_fetch_assoc($friendRResult)['name'];

        $result  = sendFriendRequest($userId, $friendId, $userName, $friendName);
        echo json_encode($result);
        exit();


        if ($userId && $userName && $friendId && $friendName) {
            $response = sendFriendRequest($userId, $friendId, $userName, $friendName);
            echo json_encode($response);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid data provided.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid action.'
        ]);
    }

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>
