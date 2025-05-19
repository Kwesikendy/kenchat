<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$conn = new mysqli("localhost", "root", "", "chatproject");

if(!$conn){
    echo "Connection failed";
}

?>
