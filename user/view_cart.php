<?php
session_start();
include 'user_header.php';
include '../db.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'User') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ดึงข้อมูล eBooks ที่อยู่ในตะกร้าของผู้ใช้
$stmt = $conn->prepare("SELECT cart.id as cart_id, ebooks.name, ebooks.price, ebooks.ebook_id, cart.quantity 
                        FROM cart 
                        JOIN ebooks ON cart.ebook_id = ebooks.ebook_id 
                        WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// อัปเดตจำนวนสินค้าในตะกร้า
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    
    $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);

    header("Location: view_cart.php");
    exit();
}

// ลบรายการออกจากตะกร้า
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->execute([$cart_id]);

    header("Location: view_cart.php");
    exit();
}

// ยืนยันการสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_order'])) {
    // เพิ่มข้อมูลการสั่งซื้อในตาราง orders
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, created_at) VALUES (?, NOW())");
    $order_stmt->execute([$user_id]);
    $order_id = $conn->lastInsertId();

    // เพิ่มข้อมูลรายการสั่งซื้อในตาราง order_items
    foreach ($cart_items as $item) {
        $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, ebook_id, quantity, price) VALUES (?, ?, ?, ?)");
        $order_item_stmt->execute([$order_id, $item['ebook_id'], $item['quantity'], $item['price']]);
    }

    // ลบข้อมูลออกจากตะกร้า
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $_SESSION['success_message'] = "Order confirmed successfully!";
    header("Location: user_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <!-- Bootstrap 5.3.2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Shopping Cart</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_price = 0; // ตัวแปรสำหรับเก็บราคารวมทั้งหมด
                foreach ($cart_items as $item): 
                    $item_total = $item['price'] * $item['quantity']; // คำนวณราคารวมของแต่ละรายการ
                    $total_price += $item_total; // บวกเข้ากับราคารวมทั้งหมด
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['price']) ?> THB</td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width: 80px;">
                                <button type="submit" name="update_quantity" class="btn btn-primary mt-2">Update</button>
                            </form>
                        </td>
                        <td><?= $item_total ?> THB</td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- แสดงราคารวมทั้งหมด -->
        <div class="text-end mb-4">
            <h4>Total Price: <?= $total_price ?> THB</h4>
        </div>

        <!-- ปุ่มยืนยันการสั่งซื้อ -->
        <form method="POST" action="add_order.php">
            <button type="submit" name="confirm_order" class="btn btn-success">Confirm Order</button>
        </form>
    </div>
    <?php include '../partials/footer.php'; ?>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
