<?php
// ตรวจสอบว่ามีการเริ่ม session หรือยัง
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // เริ่ม session ถ้ายังไม่มีการเริ่ม
}

// ตรวจสอบว่าสิทธิ์ของผู้ใช้เป็น Admin หรือไม่
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <!-- Navbar สำหรับ Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container mt-4 ">
            <a class="navbar-brand" href="#">E-book Shop Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="manage_ebooks.php">Manage E-books</a>
                    </li>                 
                    <li class="nav-item">
                        <a class="nav-link" href="manage_payments.php">Manage Payments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_reports.php">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>                
            </div>
        </div>
    </nav>
</body>
</html>
