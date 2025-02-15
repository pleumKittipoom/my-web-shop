<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['username'])) {
    header('Location: products.php');
    exit;
}

// ตรวจสอบว่า user มีการสั่งซื้อหรือไม่
if (!empty($_SESSION['cart'])) {
    header('Location: products.php');
    exit;
}

// ดึงข้อมูลการสั่งซื้อล่าสุด
$username = $_SESSION['username'];
$order_result = $conn->query("SELECT * FROM orders WHERE username = '$username' ORDER BY order_date DESC LIMIT 1");
$order = $order_result->fetch_assoc();

// ถ้าไม่พบคำสั่งซื้อล่าสุด
if (!$order) {
    header('Location: products.php');
    exit;
}

// ดึงรายการสินค้าในคำสั่งซื้อ
$order_items_result = $conn->query("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = {$order['id']}");
$order_items = [];
while ($row = $order_items_result->fetch_assoc()) {
    $order_items[] = $row;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สรุปการสั่งซื้อ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 p-5">
    <h1 class="text-2xl font-bold mb-5">สรุปการสั่งซื้อ</h1>

    <h2 class="text-xl font-semibold mb-3">ข้อมูลการสั่งซื้อ</h2>
    <div class="bg-white p-5 border border-gray-200 rounded-lg mb-5">
        <p><strong>ชื่อผู้สั่งซื้อ:</strong> <?= htmlspecialchars($order['first_name']) . ' ' . htmlspecialchars($order['last_name']) ?></p>
        <p><strong>ที่อยู่จัดส่ง:</strong> <?= nl2br(htmlspecialchars($order['address'])) ?></p>
        <p><strong>เบอร์ติดต่อ:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
        <p><strong>วิธีการชำระเงิน:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>รวมทั้งหมด:</strong> ฿<?= number_format($order['total'], 2) ?></p>
    </div>

    <h2 class="text-xl font-semibold mb-3">รายการสินค้า</h2>
    <table class="w-full bg-white border border-gray-200 rounded-lg">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">ชื่อสินค้า</th>
                <th class="p-2">ราคา</th>
                <th class="p-2">จำนวน</th>
                <th class="p-2">รวม</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item) : ?>
                <tr>
                    <td class="p-2"><?= htmlspecialchars($item['name']) ?></td>
                    <td class="p-2 text-center">฿<?= number_format($item['price'], 2) ?></td>
                    <td class="p-2 text-center"><?= $item['quantity'] ?></td>
                    <td class="p-2 text-center">฿<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-5">
        <a href="products.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">กลับไปหน้าหลัก</a>
    </div>
</body>
</html>