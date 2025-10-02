<?php
// เริ่มต้น session
session_start();

// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
// include database connection from db.php
include '../db.php';

// ตรวจสอบว่ามีข้อความสำเร็จใน session หรือไม่
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    // ลบข้อความหลังจากแสดง
    unset($_SESSION['success_message']);
}

// ตรวจสอบว่ามีการส่งคำค้นหาหรือไม่
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    // ดึงข้อมูล eBooks โดยกรองตามคำค้นหา
    $sql = "SELECT * FROM ebooks WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(["%$searchQuery%"]);
    $result = $stmt;
} else {
    // ดึงข้อมูล eBooks ทั้งหมด
    $sql = "SELECT * FROM ebooks";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage eBooks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* กำหนดความกว้างของคอลัมน์ Description และ Actions */
        .description-column {
            width: 35%; /* ปรับตามที่ต้องการให้แคบลง */
        }
        .actions-column {
            width: 15%; /* ปรับความกว้างให้มากขึ้น */
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center">Manage eBooks</h1>

    <!-- ฟอร์มค้นหา -->
    <form method="GET" action="" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by eBook name" value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <!-- ปุ่ม Add eBook -->
    <div class="text-end mb-3">
        <a href="add_ebook.php" class="btn btn-success">Add eBook</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th class="description-column">Description</th>
                <th>Image</th>
                <th>PDF File</th>
                <th>Price</th>
                <th>Created At</th>
                <th class="actions-column">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch()): ?>
                <tr>
                    <td><?php echo $row['ebook_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="../images/<?php echo htmlspecialchars($row['image']); ?>" width="50">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['pdf_file']): ?>
                            <a href="../pdfs/<?php echo htmlspecialchars($row['pdf_file']); ?>" target="_blank">View PDF</a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['price']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <a href="edit_ebook.php?id=<?php echo $row['ebook_id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_ebook.php?id=<?php echo $row['ebook_id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
    include '../partials/footer.php'; // เรียกใช้ส่วนท้ายมารวม
    ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
