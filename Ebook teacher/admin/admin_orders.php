<?php
session_start();
include 'db.php';

// ตรวจสอบสิทธิ์ Admin
if ($_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}

// ดึงข้อมูลคำสั่งซื้อทั้งหมด
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>รายการคำสั่งซื้อ</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>ผู้ใช้งาน</th>
                    <th>ยอดรวม</th>
                    <th>สถานะคำสั่งซื้อ</th>
                    <th>วันที่สั่งซื้อ</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['order_id'] ?></td>
                        <td><?= $order['user_id'] ?></td>
                        <td><?= number_format($order['total_price'], 2) ?></td>
                        <td><?= $order['order_status'] ?></td>
                        <td><?= $order['created_at'] ?></td>
                        <td>
                            <a href="admin_order_details.php?order_id=<?= $order['order_id'] ?>" class="btn btn-info btn-sm">ดูรายละเอียด</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
