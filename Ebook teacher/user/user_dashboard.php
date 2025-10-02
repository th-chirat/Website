<?php
session_start();

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

include 'user_header.php';
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการค้นหาหรือไม่
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM ebooks WHERE name LIKE ?");
    $stmt->execute(['%' . $searchQuery . '%']);
} else {
    $stmt = $conn->query("SELECT * FROM ebooks");
}
$ebooks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
           <!-- แสดงข้อความสำเร็จ -->
           <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); // ลบข้อความหลังจากแสดง ?>
        <?php endif; ?>
        
        <!-- แสดงชื่อผู้ใช้ -->
        <h1>Welcome, <?= $_SESSION['username'] ?></h1>
        <p>This is the user dashboard.</p>
        <a href="../logout.php" class="btn btn-danger mb-4">Logout</a>

        <!-- ฟอร์มค้นหา eBooks -->
        <form method="GET" action="" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search eBook by name" value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- แสดงผล eBooks ทั้งหมดในรูปแบบ card layout 4 คอลัมน์ -->
        <div class="row">
            <?php foreach ($ebooks as $ebook): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="../images/<?= htmlspecialchars($ebook['image']) ?>" class="card-img-top custom-image" alt="<?= htmlspecialchars($ebook['name']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($ebook['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($ebook['description']) ?></p>
                            <p class="card-text">Price: <?= htmlspecialchars($ebook['price']) ?> THB</p>
                            <!-- ปุ่ม Add to Cart -->
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="ebook_id" value="<?= $ebook['ebook_id'] ?>">
                                <button type="submit" class="btn btn-success">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
