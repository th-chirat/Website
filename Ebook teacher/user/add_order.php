<?php
session_start();
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูล eBooks ที่อยู่ในตะกร้าของผู้ใช้
$stmt = $conn->prepare("SELECT cart.id as cart_id, ebooks.ebook_id, cart.quantity, ebooks.price 
                        FROM cart 
                        JOIN ebooks ON cart.ebook_id = ebooks.ebook_id 
                        WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// ตรวจสอบว่ามีสินค้าในตะกร้าหรือไม่
if (count($cart_items) > 0) {
    // คำนวณราคารวมทั้งหมดของคำสั่งซื้อ
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // เพิ่มข้อมูลการสั่งซื้อในตาราง orders (สถานะเริ่มต้นเป็น 'Pending')
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, order_status, created_at) VALUES (?, ?, 'Pending', NOW())");
    $order_stmt->execute([$user_id, $total_price]);
    $order_id = $conn->lastInsertId(); // ดึง order_id ที่เพิ่งสร้าง

    // เพิ่มข้อมูลรายการสั่งซื้อในตาราง order_items
    foreach ($cart_items as $item) {
        $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, ebook_id, quantity, price) VALUES (?, ?, ?, ?)");
        $order_item_stmt->execute([$order_id, $item['ebook_id'], $item['quantity'], $item['price']]);
    }

    // ลบข้อมูลออกจากตะกร้า
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // บันทึกข้อความใน session และเปลี่ยนเส้นทางไปที่หน้า user_dashboard.php
    $_SESSION['success_message'] = "Order placed successfully! Please proceed to payment.";
    header('Location: user_dashboard.php');
    // แสดงข้อความก่อนเปลี่ยนเส้นทาง
    // echo "<script>
    //         alert('Order placed successfully! Please proceed to payment.');
    //         setTimeout(function() {
    //             window.location.href = 'user_dashboard.php';
    //         }, 3000); // หน่วงเวลา 3 วินาที (3000 มิลลิวินาที)
    //       </script>";
    exit();
} else {
    $_SESSION['error_message'] = "Your cart is empty!";
    header('Location: view_cart.php');
    exit();
}
?>
