<?php
session_start();
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['order_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transfer_amount = $_POST['transfer_amount'];
    $transfer_date = $_POST['transfer_date'];
    $transfer_time = $_POST['transfer_time'];
    $proof_image = $_FILES['proof_image']['name'];
    $target_dir = "../payment_slip/";
    $target_file = $target_dir . basename($proof_image);

    // อัปโหลดไฟล์หลักฐานการโอน
    if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $target_file)) {
        // บันทึกข้อมูลการชำระเงินในตาราง payments
        $stmt = $conn->prepare("INSERT INTO payments (order_id, transfer_amount, transfer_date, transfer_time, proof_image, payment_status, created_at) 
                                VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([$order_id, $transfer_amount, $transfer_date, $transfer_time, $proof_image]);

        // อัปเดตสถานะคำสั่งซื้อเป็น 'Paid'
        $update_order = $conn->prepare("UPDATE orders SET order_status = 'Confirmed' WHERE order_id = ?");
        $update_order->execute([$order_id]);

        $_SESSION['success_message'] = "Payment has been made successfully!";
        header('Location: manage_payment.php');
        exit();
    } else {
        echo "Error uploading proof of payment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Payment for Order ID: <?= htmlspecialchars($order_id) ?></h1>

    <!-- ฟอร์มการชำระเงิน -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="transfer_amount" class="form-label">Transfer Amount</label>
            <input type="number" step="0.01" class="form-control" id="transfer_amount" name="transfer_amount" required>
        </div>

        <div class="mb-3">
            <label for="transfer_date" class="form-label">Transfer Date</label>
            <input type="date" class="form-control" id="transfer_date" name="transfer_date" required>
        </div>

        <div class="mb-3">
            <label for="transfer_time" class="form-label">Transfer Time</label>
            <input type="time" class="form-control" id="transfer_time" name="transfer_time" required>
        </div>

        <div class="mb-3">
            <label for="proof_image" class="form-label">Proof of Transfer (Image)</label>
            <input type="file" class="form-control" id="proof_image" name="proof_image" required>
        </div>

        <button type="submit" class="btn btn-success">Submit Payment</button>
    </form>
</div>

<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
