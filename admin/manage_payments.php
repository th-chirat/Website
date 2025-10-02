<?php
session_start();

// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เป็น Admin หรือไม่
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// ตรวจสอบการคลิกปุ่ม Confirm หรือ Reject
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $action = $_POST['action']; // รับค่าจากฟอร์ม (confirm หรือ reject)

    if ($action == 'confirm') {
        // อัปเดตสถานะ payment_status เป็น Confirmed
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Confirmed' WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $_SESSION['success_message'] = "Payment Confirmed!";
    } elseif ($action == 'reject') {
        // อัปเดตสถานะ payment_status เป็น Rejected
        $stmt = $conn->prepare("UPDATE payments SET payment_status = 'Rejected' WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $_SESSION['error_message'] = "Payment Rejected!";
    }

    // เปลี่ยนเส้นทางกลับไปที่ manage_payment.php หลังจากอัปเดตเสร็จสิ้น
    header('Location: manage_payments.php');
    exit();
}

// ดึงข้อมูลการชำระเงินที่สถานะเป็น Pending
$stmt = $conn->prepare("SELECT payments.*, orders.user_id, users.username, orders.total_price 
                        FROM payments 
                        JOIN orders ON payments.order_id = orders.order_id
                        JOIN users ON orders.user_id = users.user_id
                        WHERE payments.payment_status = 'Pending'");
$stmt->execute();
$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments (Admin)</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Manage Payments (Admin)</h1>

    <!-- แสดงข้อความแจ้งเตือน -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- ตารางแสดงรายการการชำระเงิน -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Username</th>
                <th>Order ID</th>
                <th>Transfer Amount</th>
                <th>Transfer Date</th>
                <th>Transfer Time</th>
                <th>Proof Image</th>
                <th>Total Price</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($payments) > 0): ?>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                        <td><?= htmlspecialchars($payment['username']) ?></td>
                        <td><?= htmlspecialchars($payment['order_id']) ?></td>
                        <td><?= htmlspecialchars($payment['transfer_amount']) ?> THB</td>
                        <td><?= htmlspecialchars($payment['transfer_date']) ?></td>
                        <td><?= htmlspecialchars($payment['transfer_time']) ?></td>
                        <td>
                            <a href="../payment_slip/<?= htmlspecialchars($payment['proof_image']) ?>" target="_blank">View Image</a>
                        </td>
                        <td><?= htmlspecialchars($payment['total_price']) ?> THB</td>
                        <td><?= htmlspecialchars($payment['payment_status']) ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="payment_id" value="<?= $payment['payment_id'] ?>">
                                <button type="submit" name="action" value="confirm" class="btn btn-success">Confirm</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">No pending payments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
    include '../partials/footer.php'; // เรียกใช้ส่วนท้ายมารวม
    ?>
<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
