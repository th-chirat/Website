<?php
session_start();
include 'db.php'; // เชอื่ มตอ่ ฐานขอ้มูล
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$password = $_POST['password'];
// ดงึขอ้มลู ผใู้ชจ้ากฐานขอ้ มลู
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
// ตรวจสอบการมอี ยขู่ องผูใ้ชแ้ละรหัสผา่ น
if ($user && password_verify($password, $user['password_hash'])) {
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
// ตรวจสอบบทบาทของผใู้ชแ้ละเปลยี่ นเสน้ ทางไปยังหนา้ Dashboard ที่เหมาะสม
if ($user['role'] == 'Admin') {
header('Location: ./admin/admin_dashboard.php');
} else {
header('Location: ./user/user_dashboard.php');
}
exit();
} else {
$error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<!-- Bootstrap 5.3.2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet">
</head>
<body>
<?php
include './partials/header.php'; // เรยี กใชส้ ว่ นหัว
?>
<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-6">
<h2>Login</h2>
<?php if (isset($error)): ?>
<div class="alert alert-danger">
<?= $error ?>
</div>
<?php endif; ?>
<form method="POST" action="login.php">
<div class="mb-3">
<label for="username" class="form-label">Username</label>
<input type="text" class="form-control" id="username" name="username"

required>

</div>
<div class="mb-3">
<label for="password" class="form-label">Password</label>
<input type="password" class="form-control" id="password" name="password"

required>

</div>
<button type="submit" class="btn btn-primary">Login</button>
</form>
</div>
</div>
</div>
<?php
include './partials/footer.php'; // เรยี กใชส้ ว่ นทา้ยเว็บ
?>
</body>
</html>
