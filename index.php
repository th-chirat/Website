<?php
include 'db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการค้นหาหรือไม่
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    // ดึงข้อมูลจากฐานข้อมูลโดยใช้เงื่อนไขการค้นหาตามชื่อหนังสือ
    $stmt = $conn->prepare("SELECT * FROM ebooks WHERE name LIKE :searchQuery");
    $stmt->execute(['searchQuery' => '%' . $searchQuery . '%']);
} else {
    // ถ้าไม่มีการค้นหา ให้ดึงข้อมูลทั้งหมด
    $stmt = $conn->query("SELECT * FROM ebooks");
}

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
    <title>E-book Shop</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- เรียกไฟล์ CSS ที่แยกออกมา -->
    <link href="css/styles.css" rel="stylesheet">

</head>

<body>
    <?php
    include './partials/header.php'; // เรียกใช้ส่วนหัว
    ?>
    <div class="d-flex justify-content-center" style="margin-top: 50px;">
        <!-- ฟอร์มค้นหาถัดจาก Navbar -->
        <form class="d-flex" action="index.php" method="GET" style="width: 400px;">
            <input class="form-control me-2" type="search" name="search" placeholder="ค้นหาหนังสือ" aria-label="Search" value="<?= htmlspecialchars($searchQuery) ?>">
            <button class="btn btn-outline-success" type="submit">ค้นหา</button>
        </form>
    </div>

 <!-- แสดงข้อมูลหนังสือในรูปแบบ Card -->
<div class="container mt-5">
    <div class="row">
        <?php if (count($ebooks) > 0): ?>
            <?php foreach ($ebooks as $ebook): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100"> <!-- เพิ่ม class h-100 เพื่อให้ Card สูงเท่ากัน -->
                        <img src="images/<?= htmlspecialchars($ebook['image']) ?>" class="card-img-top custom-image" alt="<?= htmlspecialchars($ebook['name']) ?>">
                        <div class="card-body d-flex flex-column"> <!-- เพิ่ม d-flex และ flex-column เพื่อจัดเนื้อหาภายในให้ยืดหยุ่น -->
                            <h5 class="card-title"><?= htmlspecialchars($ebook['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(limitCharacters($ebook['description'], 100)) ?></p> <!-- ใช้ฟังก์ชัน limitCharacters -->
                            <p class="card-text mt-auto">ราคา: <?= htmlspecialchars($ebook['price']) ?> บาท</p> <!-- ใส่ mt-auto เพื่อให้ข้อความราคาติดอยู่ด้านล่าง -->
                            <button class="btn btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#ebookModal" 
                                        onclick="showDetails('<?= htmlspecialchars($ebook['image']) ?>', '<?= htmlspecialchars($ebook['name']) ?>', '<?= htmlspecialchars($ebook['description']) ?>', '<?= htmlspecialchars($ebook['price']) ?>', '<?= htmlspecialchars($ebook['created_at']) ?>')">
                                    รายละเอียด
                                </button>
<!-- 
                            <a href="ebook_details.php?id=<?= $ebook['ebook_id'] ?>" class="btn btn-primary">รายละเอียด</a> 
-->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">ไม่พบหนังสือตามการค้นหา</p>
        <?php endif; ?>
    </div>
</div>

    <!-- Include ไฟล์ modal.php -->
    <?php include 'modal.php'; ?>
    
    <?php
    include './partials/footer.php'; // เรียกใช้ส่วนท้ายมารวม
    ?>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetails(image, name, description, price, createdAt) {
            document.getElementById('ebookImage').src = 'images/' + image;
            document.getElementById('ebookName').innerText = name;
            document.getElementById('ebookDescription').innerText = description;
            document.getElementById('ebookPrice').innerText = price;
            document.getElementById('ebookCreated').innerText = createdAt;
        }
    </script>

</body>

</html>

