<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $action = $_POST['action'];

    // อัปเดตสถานะการชำระเงิน
    if ($action == 'confirm') {
        $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'Confirmed' WHERE payment_id = :payment_id");
        $stmt->execute(['payment_id' => $payment_id]);

        // อัปเดตสถานะคำสั่งซื้อ
        $stmt = $pdo->prepare("UPDATE orders o JOIN payments p ON o.order_id = p.order_id 
                               SET o.order_status = 'Confirmed' WHERE p.payment_id = :payment_id");
        $stmt->execute(['payment_id' => $payment_id]);
    } elseif ($action == 'reject') {
        $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'Rejected' WHERE payment_id = :payment_id");
        $stmt->execute(['payment_id' => $payment_id]);
    }

    header('Location: admin_orders.php');
    exit();
}
?>
