<?php
session_start();
include 'db.php';

$order_id = $_GET['order_id'];

// ตรวจสอบสิทธิ์ Admin
if ($_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

// ดึงข้อมูลคำสั่งซื้อ
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch();

// ดึงรายการสินค้าในคำสั่งซื้อ
$stmt = $pdo->prepare("SELECT e.name, oi.quantity, oi.price 
                       FROM order_items oi 
                       JOIN ebooks e ON oi.ebook_id = e.ebook_id 
                       WHERE oi.order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll();

// ดึงข้อมูลการชำระเงิน (ถ้ามี)
$stmt = $pdo->prepare("SELECT * FROM payments WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$payment = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>รายละเอียดคำสั่งซื้อ #<?= $order['order_id'] ?></h2>

        <h4>รายการหนังสือ</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>ชื่อหนังสือ</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>ยอดรวม: <?= number_format($order['total_price'], 2) ?> บาท</h4>

        <?php if ($payment): ?>
            <h4>รายละเอียดการชำระเงิน</h4>
            <p>ยอดเงินที่โอน: <?= number_format($payment['transfer_amount'], 2) ?> บาท</p>
            <p>วันที่โอน: <?= $payment['transfer_date'] ?></p>
            <p>เวลาที่โอน: <?= $payment['transfer_time'] ?></p>
            <p>
                หลักฐานการโอน: 
                <a href="<?= $payment['proof_image'] ?>" target="_blank">
                    <img src="<?= $payment['proof_image'] ?>" alt="Proof of payment" style="width: 200px;">
                </a>
            </p>

            <form action="admin_confirm_payment.php" method="post">
                <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">
                <button type="submit" name="action" value="confirm" class="btn btn-success">ยืนยันการชำระเงิน</button>
                <button type="submit" name="action" value="reject" class="btn btn-danger">ปฏิเสธการชำระเงิน</button>
            </form>
        <?php else: ?>
            <p>ยังไม่มีการชำระเงิน</p>
        <?php endif; ?>
    </div>
</body>
</html>
