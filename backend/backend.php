<?php

include 'config.php';
if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    $hash_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $present = mysqli_num_rows($result);

    if($present>0){
        $_SESSION['error'] = "Email already exists";
        header('location: ../frontend/register.php');
        exit();
    }
    else{
        $sql = "INSERT INTO user (name, email, password) VALUES ('$name', '$email', '$hash_password')";
        $result = mysqli_query($conn, $sql);
        if($result){
            $_SESSION['success'] = "Registration successful. Please log in.";
            header('location: ../frontend/index.php');
            exit();
        } else {
            $_SESSION['error'] = "Something went wrong. Please try again.";
            header('location: ../frontend/register.php');
            exit();
        }
    }
}
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $present = mysqli_num_rows($result);

    if ($present > 0) {
        $data = mysqli_fetch_array($result);
        error_log("Stored Password: " . $data['password']);
        error_log("Entered Password: " . $password);
        // Verify the password
        if (password_verify($password, $data['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['uid'] = $data['id'];
            header('location: ../frontend/ui.php');
            exit();
        } else {
            // Password is incorrect
            $_SESSION['error'] = "Invalid email or password";
            header('location: ../frontend/index.php');
            exit();
        }
    } else {
        // Email does not exist
        $_SESSION['error'] = "Invalid email or password";
        header('location: ../frontend/index.php');
        exit();
    }
} 
else {
    // Redirect to the login page if the form is not submitted
    header('location: ../frontend/index.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'get_users') {
    $userId = $_SESSION['user_id']; // Current logged-in user ID
    $sql = "SELECT id, name, email FROM users WHERE id != $userId"; // Exclude the current user
    $result = mysqli_query($conn, $sql);

    $users = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
    }

    echo json_encode(['status' => 'success', 'users' => $users]);
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'send_request') {
    $f1_id = $_SESSION['user_id']; // Current logged-in user ID
    $f2_id = $_POST['f2_id']; // ID of the user to whom the request is sent

    // Check if a request already exists
    $checkSql = "SELECT * FROM friends WHERE (f1_id = $f1_id AND f2_id = $f2_id) OR (f1_id = $f2_id AND f2_id = $f1_id)";
    $checkResult = mysqli_query($conn, $checkSql);

    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Friend request already exists.']);
        exit();
    }

    // Insert the friend request
    $sql = "INSERT INTO friends (f1_id, f2_id, status) VALUES ($f1_id, $f2_id, 'pending')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Friend request sent.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send friend request.']);
    }
    exit();
}
?>
