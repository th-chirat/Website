<?php
session_start();
include 'user_header.php';
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วและเป็น User
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูล eBooks ที่มีสถานะการชำระเงินเป็น Confirmed
$stmt = $conn->prepare("SELECT ebooks.*, payments.payment_status 
                        FROM ebooks 
                        JOIN order_items ON ebooks.ebook_id = order_items.ebook_id
                        JOIN orders ON order_items.order_id = orders.order_id
                        JOIN payments ON orders.order_id = payments.order_id
                        WHERE orders.user_id = ? AND payments.payment_status = 'Confirmed'");
$stmt->execute([$user_id]);
$ebooks = $stmt->fetchAll();
?>

<?php
// ฟังก์ชันสำหรับตัดข้อความตามจำนวนตัวอักษร
function limitCharacters($text, $limit) {
    if (mb_strlen($text, 'UTF-8') > $limit) {
        return mb_substr($text, 0, $limit, 'UTF-8') . '...'; // ตัดข้อความและใส่ ...
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View eBooks</title>
    
    <style>
        .custom-image {
            width: 100%;
            /* ให้เต็มความกว้างของ card */
            height: 70px;
            /* กำหนดความสูงของภาพ */
            object-fit: cover;
            /* ปรับภาพตามขนาดต้นฉบับ โดยไม่ทำให้ภาพบิดเบี้ยว */
            margin-top: 20px;
            /* เพิ่มระยะห่างจากด้านบน 20px */
        }

        .custom-container {
            padding-left: 50px;
            /* กำหนดระยะห่างด้านซ้าย 150px */
        }

        .nav-item-custom {
            margin-right: 50px;
            /* กำหนดระยะห่างด้านขวาสำหรับ nav-items */
        }
    </style>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>View eBooks</h1>

        <!-- ตรวจสอบว่ามี eBooks หรือไม่ -->
        <?php if (count($ebooks) > 0): ?>
            <div class="row">
                <?php foreach ($ebooks as $ebook): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <!-- แสดงรูปภาพ -->
                            <img src="../images/<?= htmlspecialchars($ebook['image']) ?>" class="card-img-top img-fluid custom-image" alt="<?= htmlspecialchars($ebook['name']) ?>">
                           <div class="card-body">
                                <!-- แสดงชื่อ, คำอธิบาย, และราคา -->
                                <h5 class="card-title"><?= htmlspecialchars($ebook['name']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(limitCharacters($ebook['description'], 100))  ?></p>
                                <p class="card-text"><strong>Price:</strong> <?= htmlspecialchars($ebook['price']) ?> THB</p>
                                <!-- ลิงก์เพื่อดาวน์โหลด PDF -->
                                <?php if (!empty($ebook['pdf_file'])): ?>
                                    <a href="../pdfs/<?= htmlspecialchars($ebook['pdf_file']) ?>" class="btn btn-primary" target="_blank">Download PDF</a>
                                <?php else: ?>
                                    <p class="text-danger">No PDF available</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No eBooks found for this user.</p>
        <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>