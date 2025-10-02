<?php
session_start();
// นำเข้าไฟล์ admin_header.php
include 'admin_header.php';
include '../db.php';

$user_id = $_GET['id'];

// ดึงข้อมูลของผู้ใช้จากฐานข้อมูลเพื่อนำมาแสดงในฟอร์ม
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$username, $email, $role, $user_id])) {
        $_SESSION['success_message'] = "User updated successfully.";
        header("Location: manage_users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <h1>Edit User</h1>

        <form action="edit_user.php?id=<?= $user_id ?>" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="User" <?= $user['role'] == 'User' ? 'selected' : '' ?>>User</option>
                    <option value="Admin" <?= $user['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Update User</button>
        </form>
    </div>
    <?php
    include '../partials/footer.php'; // เรียกใช้ส่วนท้ายมารวม
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>