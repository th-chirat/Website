<?php
session_start();
include 'user_header.php';
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ตรวจสอบการคลิกปุ่ม (แสดงเฉพาะสถานะ 'Pending', 'Paid', หรือ 'Confirmed')
if (isset($_GET['status']) && in_array($_GET['status'], ['Pending', 'Paid', 'Confirmed'])) {
    $filter_status = $_GET['status'];
} else {
    $filter_status = 'Pending'; // ค่าเริ่มต้นเป็น Pending
}

// ดึงข้อมูลการสั่งซื้อจากตาราง orders และ order_items
$stmt = $conn->prepare("SELECT orders.*, order_items.*, ebooks.name 
                        FROM orders 
                        JOIN order_items ON orders.order_id = order_items.order_id 
                        JOIN ebooks ON order_items.ebook_id = ebooks.ebook_id 
                        WHERE orders.user_id = ? AND orders.order_status = ?");
$stmt->execute([$user_id, $filter_status]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Payments</h1>

    <!-- ปุ่มแสดงสถานะการสั่งซื้อ -->
    <div class="mb-3">
        <a href="manage_payment.php?status=Pending" class="btn btn-warning">รอการจ่ายเงิน</a>
        <a href="manage_payment.php?status=Confirmed" class="btn btn-success">ชำระเงินแล้ว</a>
    </div>

    <!-- แสดงรายการสั่งซื้อ -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>eBook</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td><?= htmlspecialchars($order['quantity']) ?></td>
                    <td><?= htmlspecialchars($order['total_price']) ?> THB</td>
                    <td><?= htmlspecialchars($order['order_status']) ?></td>
                    <td>
                        <!-- ปุ่ม Payment -->
                        <?php if ($order['order_status'] == 'Pending'): ?>
                            <a href="payment.php?order_id=<?= $order['order_id'] ?>" class="btn btn-primary">Payment</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../partials/footer.php'; ?>
<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
