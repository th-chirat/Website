<?php
session_start();
include '../db.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // ลบผู้ใช้จากฐานข้อมูล
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute([$user_id])) {
        $_SESSION['success_message'] = "User deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting user.";
    }
}

// หลังจากลบเสร็จ กลับไปที่หน้า manage_users.php
header("Location: manage_users.php");
exit();
