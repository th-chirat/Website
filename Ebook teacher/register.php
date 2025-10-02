<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
// เตรยี มค าสงั่ SQL และท าการบันทกึขอ้ มลู สมาชกิ
$stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?,
?)");
if ($stmt->execute([$username, $email, $password])) {
echo "<div class='alert alert-success'>สมัครสมาชิกเรียบร้อย</div>";
} else {
echo "<div class='alert alert-danger'>เกิดข้อผิดพลาด</div>";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
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
<div class="card">
<div class="card-header">
<h3 class="text-center">Register</h3>
</div>
<div class="card-body">
<form method="post">
<div class="mb-3">
<label for="username" class="form-label">Username</label>
<input type="text" class="form-control" id="username" name="username"

placeholder="Enter your username" required>

</div>
<div class="mb-3">
<label for="email" class="form-label">Email</label>
<input type="email" class="form-control" id="email" name="email"

placeholder="Enter your email" required>

</div>
<div class="mb-3">
<label for="password" class="form-label">Password</label>
<input type="password" class="form-control" id="password"

name="password" placeholder="Enter your password" required>

</div>
<button type="submit" class="btn btn-primary w-100">Register</button>
</form>
</div>
</div>
</div>
</div>
</div>
<?php
include './partials/footer.php'; // เรยี กใชส้ ว่ นทา้ยเว็บ
?>
<!-- Bootstrap JS CDN -->
<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>