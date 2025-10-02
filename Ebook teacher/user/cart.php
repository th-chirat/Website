<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

$stmt = $pdo->prepare("SELECT c.cart_id, e.name, e.price, c.quantity 
                       FROM carts c 
                       JOIN ebooks e ON c.ebook_id = e.ebook_id 
                       WHERE c.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll();
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>ตะกร้าสินค้า</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ชื่อหนังสือ</th>
                    <th>ราคา</th>
                    <th>จำนวน</th>
                    <th>รวม</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item_total, 2) ?></td>
                        <td>
                            <a href="remove_from_cart.php?cart_id=<?= $item['cart_id'] ?>" class="btn btn-danger btn-sm">ลบ</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4>ยอดรวม: <?= number_format($total_price, 2) ?> บาท</h4>
        <a href="checkout.php" class="btn btn-primary">ดำเนินการสั่งซื้อ</a>
    </div>
</body>
</html>
