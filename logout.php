<?php
session_start();
include "./Admin/database/db.php";

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id']; 

    $query = "UPDATE users SET  last_activity = NOW(), status = 'offline' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    setcookie('email', '', time() - 3600, '/');
    setcookie('password', '', time() - 3600, '/');
    setcookie('type', '', time() - 3600, '/');

    header("Location: login.php");
    exit;
} else {
    echo "No user session found.";
}

$conn->close();
?>
