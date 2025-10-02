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

// ดึงข้อมูล eBooks จากฐานข้อมูล
$sql = "SELECT * FROM ebooks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage eBooks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h1 class="text-center">Manage eBooks</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Image</th>
                <th>PDF File</th>
                <th>Price</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch()): ?>
                <tr>
                    <td><?php echo $row['ebook_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="../images/<?php echo $row['image']; ?>" width="50">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['pdf_file']): ?>
                            <a href="../pdfs/<?php echo $row['pdf_file']; ?>" target="_blank">View PDF</a>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <button class="btn btn-warning edit-btn" data-id="<?php echo $row['ebook_id']; ?>">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal สำหรับแก้ไขข้อมูล eBook -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit eBook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ฟอร์มนี้จะถูกโหลดผ่าน AJAX -->
                <form id="editForm">
                    <input type="hidden" id="ebook_id" name="ebook_id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" class="form-control" id="price" name="price" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// เปิด Modal และโหลดข้อมูลสำหรับแก้ไขผ่าน AJAX
$(document).on('click', '.edit-btn', function() {
    var ebook_id = $(this).data('id');
    $.ajax({
        url: 'edit_ebook.php',
        type: 'GET',
        data: { id: ebook_id },
        success: function(response) {
            var data = JSON.parse(response);
            $('#ebook_id').val(data.ebook_id);
            $('#name').val(data.name);
            $('#description').val(data.description);
            $('#price').val(data.price);
            $('#editModal').modal('show');
        }
    });
});

// เมื่อฟอร์มถูกส่ง (แก้ไขข้อมูล)
$('#editForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: 'edit_ebook.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response == 'success') {
                alert('แก้ไขข้อมูลหนังสือสำเร็จ');
                location.reload(); // รีโหลดหน้าเมื่อแก้ไขสำเร็จ
            } else {
                alert('เกิดข้อผิดพลาด');
            }
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
