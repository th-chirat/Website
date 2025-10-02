<?php
session_start();
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ebook_id = $_POST['ebook_id'];
    $user_id = $_SESSION['user_id'];

    // เพิ่ม eBook ลงในตารางรายการสั่งซื้อ (cart หรือ orders)
    $stmt = $conn->prepare("INSERT INTO cart (user_id, ebook_id) VALUES (?, ?)");
    if ($stmt->execute([$user_id, $ebook_id])) {
        $_SESSION['success_message'] = "เพิ่ม eBook ลงในตะกร้าสำเร็จ";
        header('Location: user_dashboard.php');
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มลงในตะกร้า";
    }
}
?>
