<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// ดึงข้อมูลจากตะกร้าสินค้า
$stmt = $pdo->prepare("SELECT e.ebook_id, e.price, c.quantity 
                       FROM carts c 
                       JOIN ebooks e ON c.ebook_id = e.ebook_id 
                       WHERE c.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll();

// คำนวณยอดรวม
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// สร้างคำสั่งซื้อ
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (:user_id, :total_price)");
$stmt->execute(['user_id' => $user_id, 'total_price' => $total_price]);
$order_id = $pdo->lastInsertId();

// เพิ่มสินค้าในคำสั่งซื้อ
foreach ($cart_items as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, ebook_id, quantity, price) VALUES (:order_id, :ebook_id, :quantity, :price)");
    $stmt->execute([
        'order_id' => $order_id,
        'ebook_id' => $item['ebook_id'],
        'quantity' => $item['quantity'],
        'price' => $item['price']
    ]);
}

// ล้างตะกร้าสินค้า
$stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);

header('Location: payment.php?order_id=' . $order_id);
exit();
?>
