<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ที่เชื่อมต่อกับฐานข้อมูล
include '../db.php';

// ตรวจสอบว่ามีการส่งค่า ID ของ eBook มาหรือไม่
if (isset($_GET['id'])) {
    $ebook_id = $_GET['id'];

    // เตรียมคำสั่ง SQL สำหรับลบข้อมูล eBook
    $sql = "DELETE FROM ebooks WHERE ebook_id = :ebook_id";

    // เตรียม statement สำหรับการลบ
    $stmt = $conn->prepare($sql);

    // ดำเนินการลบ eBook โดยส่งค่า ebook_id
    if ($stmt->execute(['ebook_id' => $ebook_id])) {
        // ลบสำเร็จ เก็บข้อความสำเร็จใน session
        $_SESSION['success_message'] = "eBook ถูกลบเรียบร้อยแล้ว";
    } else {
        // ลบไม่สำเร็จ เก็บข้อความผิดพลาดใน session
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการลบ eBook";
    }
}

// หลังจากลบเสร็จ กลับไปที่หน้า manage eBooks
header("Location: manage_ebooks.php");
exit();
