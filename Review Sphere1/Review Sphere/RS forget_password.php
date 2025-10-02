<?php
include 'db.php'; // เชื่อมต่อกับฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // ตรวจสอบว่ามีอีเมลนี้อยู่ในฐานข้อมูลหรือไม่
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // สร้าง token สำหรับการรีเซ็ตรหัสผ่าน
        $token = bin2hex(random_bytes(50));
        $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour')); // ลิงก์หมดอายุใน 1 ชั่วโมง

        // เก็บ token และเวลาหมดอายุลงในฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires_at]);

        // สร้างลิงก์รีเซ็ตรหัสผ่าน
        $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";

        // ส่งอีเมลรีเซ็ตรหัสผ่าน
        $to = $email;
        $subject = "Password Reset Request";
        $message = "Please click the following link to reset your password: $reset_link";
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-type: text/html\r\n";

        if (mail($to, $subject, $message, $headers)) {
            echo "<div class='alert alert-success'>An email has been sent to reset your password.</div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to send email. Please try again.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No account found with that email address.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Forgot Password</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
