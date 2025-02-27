<?php
session_start();
include 'dbconfig.php';

// ถ้าไม่ได้ล็อกอิน แสดงข้อความเตือน
if (!isset($_SESSION['username'])) {
    echo "<script>alert('❌ ไม่สามารถทำรายการได้ กรุณาเข้าสู่ระบบก่อน'); window.location.href='products.php';</script>";
    exit;
}

// ตรวจสอบตะกร้าสินค้า
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// ดึงข้อมูลสินค้าในตะกร้า
$items = [];
$total = 0.0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart']))); 
    $result = $conn->query("SELECT id, name, price, stock FROM products WHERE id IN ($ids)");

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        $items[$product_id] = $row;
        
        if (isset($_SESSION['cart'][$product_id])) {
            $quantity = $_SESSION['cart'][$product_id];
            $subtotal = floatval($row['price']) * intval($quantity);
            $total += $subtotal;
        }
    }
}

// เมื่อกดปุ่มสั่งซื้อ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $username = $_SESSION['username'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $payment_method = trim($_POST['payment_method']);
    $phone_number = trim($_POST['phone_number']);

    // ตรวจสอบสต็อกสินค้า
    foreach ($_SESSION['cart'] as $id => $quantity) {
        if ($items[$id]['stock'] < $quantity) {
            echo "<script>alert('❌ สินค้าบางรายการมีจำนวนไม่พอในสต็อก'); window.location.href='cart.php';</script>";
            exit;
        }
    }

    // ใช้ Transaction เพื่อความปลอดภัย
    $conn->begin_transaction();
    try {
        // บันทึกคำสั่งซื้อในตาราง orders
        $stmt = $conn->prepare("INSERT INTO orders (username, first_name, last_name, address, payment_method, phone_number, total) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssd", $username, $first_name, $last_name, $address, $payment_method, $phone_number, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // บันทึกรายการสินค้าใน order_items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
        $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

        foreach ($_SESSION['cart'] as $id => $quantity) {
            $unit_price = $items[$id]['price'];
            $total_price = $unit_price * $quantity;
            
            // เพิ่มรายการสินค้าไปยัง order_items
            $stmt->bind_param("iiidd", $order_id, $id, $quantity, $unit_price, $total_price);
            $stmt->execute();

            // อัปเดตสต็อกสินค้า
            $update_stock->bind_param("iii", $quantity, $id, $quantity);
            $update_stock->execute();
        }

        // ปิด Statements
        $stmt->close();
        $update_stock->close();

        // Commit Transaction
        $conn->commit();

        // ล้างตะกร้าหลังจากการสั่งซื้อ
        $_SESSION['cart'] = [];

        // เปลี่ยนเส้นทางไปที่หน้าสรุปการสั่งซื้อ
        header('Location: order_summary.php');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('เกิดข้อผิดพลาดในการทำธุรกรรม กรุณาลองใหม่อีกครั้ง');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>การชำระเงิน</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-5">
    <h1 class="text-2xl font-bold mb-5">การชำระเงิน</h1>

    <h2 class="text-xl font-semibold mb-3">รายละเอียดสินค้า</h2>
    <table class="w-full bg-white border border-gray-200 rounded-lg mb-5">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 text-left">ชื่อสินค้า</th>
                <th class="p-2">ราคา</th>
                <th class="p-2">จำนวน</th>
                <th class="p-2">รวม</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $id => $quantity) : ?>
                <tr>
                    <td class="p-2"><?= htmlspecialchars($items[$id]['name']) ?></td>
                    <td class="p-2 text-center">฿<?= number_format($items[$id]['price'], 2) ?></td>
                    <td class="p-2 text-center"><?= $quantity ?></td>
                    <td class="p-2 text-center">฿<?= number_format($items[$id]['price'] * $quantity, 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="text-xl font-semibold mb-3">ข้อมูลการชำระเงิน</h2>
    <form method="post">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">ชื่อจริง</label>
            <input name="first_name" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">นามสกุล</label>
            <input name="last_name" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">เบอร์ติดต่อ</label>
            <input name="phone_number" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">ที่อยู่ในการจัดส่ง</label>
            <textarea name="address" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" rows="3" required></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">วิธีการชำระเงิน</label>
            <select name="payment_method" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                <option value="credit_card">บัตรเครดิต</option>
                <option value="paypal">เพย์พาล</option>
                <option value="cash_on_delivery">เก็บเงินปลายทาง</option>
            </select>
        </div>

        <div class="flex justify-between items-center">
            <a href="cart.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">🛒 ตะกร้า</a>
            <span class="text-xl font-bold">รวม: ฿<?= number_format($total, 2) ?></span>
            <button type="submit" class="bg-indigo-500 hover:bg-fuchsia-500 text-white px-4 py-2 rounded">ยืนยันการสั่งซื้อ</button>
        </div>
    </form>
</body>
</html>
