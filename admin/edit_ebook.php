<?php
// เริ่มต้น session
session_start();
include '../db.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง ebook_id มาหรือไม่
if (!isset($_GET['id'])) {
    echo "ไม่พบ eBook ที่ต้องการแก้ไข";
    exit();
}

$ebook_id = $_GET['id'];

// ดึงข้อมูล eBook ที่ต้องการแก้ไข
$stmt = $conn->prepare("SELECT * FROM ebooks WHERE ebook_id = ?");
$stmt->execute([$ebook_id]);
$ebook = $stmt->fetch();

// ตรวจสอบว่าพบ eBook หรือไม่
if (!$ebook) {
    echo "ไม่พบ eBook ที่ต้องการแก้ไข";
    exit();
}

// ถ้าผู้ใช้ส่งข้อมูลเพื่อแก้ไข
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // จัดการกับการอัพโหลดรูปภาพใหม่
    $image = $ebook['image']; // ใช้รูปภาพเดิมถ้าไม่มีการอัปโหลดใหม่
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $target_image_path = "../images/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_image_path);
    }

    // จัดการกับการอัพโหลดไฟล์ PDF ใหม่
    $pdf_file = $ebook['pdf_file']; // ใช้ไฟล์ PDF เดิมถ้าไม่มีการอัปโหลดใหม่
    if (!empty($_FILES['pdf_file']['name'])) {
        $pdf_file = basename($_FILES['pdf_file']['name']);
        $target_pdf_path = "../pdfs/" . $pdf_file;
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_pdf_path);
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE ebooks SET name = ?, description = ?, image = ?, pdf_file = ?, price = ? WHERE ebook_id = ?");
    if ($stmt->execute([$name, $description, $image, $pdf_file, $price, $ebook_id])) {
        $_SESSION['success_message'] = "แก้ไขข้อมูลสำเร็จ";
        header('Location: manage_ebooks.php');
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit eBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Edit eBook</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($ebook['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($ebook['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image">
            <?php if ($ebook['image']): ?>
                <img src="../images/<?php echo htmlspecialchars($ebook['image']); ?>" alt="Current Image" width="100" class="mt-2">
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="pdf_file" class="form-label">PDF File</label>
            <input type="file" class="form-control" id="pdf_file" name="pdf_file">
            <?php if ($ebook['pdf_file']): ?>
                <a href="../pdfs/<?php echo htmlspecialchars($ebook['pdf_file']); ?>" target="_blank" class="d-block mt-2">View Current PDF</a>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($ebook['price']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="created_at" class="form-label">Created At</label>
            <input type="text" class="form-control" id="created_at" name="created_at" value="<?php echo htmlspecialchars($ebook['created_at']); ?>" disabled>
        </div>
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a href="manage_ebooks.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
