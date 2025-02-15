<?php
session_start();
include 'dbconfig.php';

// ถ้าไม่ได้ล็อกอิน แสดงข้อความเตือนด้วย JavaScript
if (!isset($_SESSION['username'])) {
    echo "<script>alert('❌ ไม่สามารถทำรายการได้ กรุณาเข้าสู่ระบบก่อน'); window.location.href='products.php';</script>";
}

// ตรวจสอบตะกร้าสินค้า
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// ดึงข้อมูลสินค้าในตะกร้า
$items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $result = $conn->query("SELECT id, name, price, stock FROM products WHERE id IN ($ids)");
    while ($row = $result->fetch_assoc()) {
        $items[$row['id']] = $row;
        $total += $row['price'] * $_SESSION['cart'][$row['id']];
    }
}

// ฟังก์ชั่นสำหรับการทำการสั่งซื้อ (ที่นี่ใช้แค่การแสดงข้อมูล)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $username = $_SESSION['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $phone_number = $_POST['phone_number'];  

    // ตรวจสอบสต็อกสินค้า
    foreach ($_SESSION['cart'] as $id => $quantity) {
        $stock = $items[$id]['stock'];
        if ($stock < $quantity) {
            echo "<script>alert('❌ สินค้าบางรายการมีจำนวนไม่พอในสต็อก');</script>";
            exit;
        }
    }

    // เพิ่มรายการในฐานข้อมูล (ถ้าจำเป็น)
    $conn->query("INSERT INTO orders (username, first_name, last_name, address, payment_method, phone_number, total) 
                  VALUES ('$username', '$first_name', '$last_name', '$address', '$payment_method', '$phone_number', $total)");

    // ดึง ID ของคำสั่งซื้อที่เพิ่งเพิ่มเข้าไป
    $order_id = $conn->insert_id;

    // เริ่มต้นการทำงานใน Transaction เพื่อให้การลดสต็อกเป็นไปอย่างถูกต้อง
    $conn->begin_transaction();

    try {
        // เพิ่มข้อมูลสินค้าในคำสั่งซื้อ
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $price = $items[$id]['price'];
            $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) 
                          VALUES ($order_id, $id, $quantity, $price)");

            // ลดจำนวนสินค้าในตาราง products
            $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $id");
        }

        // ถ้าทุกอย่างสำเร็จ ทำการ Commit
        $conn->commit();

        // ล้างตะกร้าหลังจากการสั่งซื้อ
        $_SESSION['cart'] = [];

        // เปลี่ยนเส้นทางไปที่หน้าสรุปการสั่งซื้อ
        header('Location: order_summary.php');
        exit;
    } catch (Exception $e) {
        // หากเกิดข้อผิดพลาดในระหว่างการดำเนินการ ให้ยกเลิก (Rollback)
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
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
            <label for="first_name" class="block text-sm font-medium text-gray-700">ชื่อจริง</label>
            <input id="first_name" name="first_name" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="last_name" class="block text-sm font-medium text-gray-700">นามสกุล</label>
            <input id="last_name" name="last_name" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="phone_number" class="block text-sm font-medium text-gray-700">เบอร์ติดต่อ</label>
            <input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
        </div>

        <div class="mb-4">
            <label for="address" class="block text-sm font-medium text-gray-700">ที่อยู่ในการจัดส่ง</label>
            <textarea id="address" name="address" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" rows="3" required></textarea>
        </div>

        <div class="mb-4">
            <label for="payment_method" class="block text-sm font-medium text-gray-700">วิธีการชำระเงิน</label>
            <select id="payment_method" name="payment_method" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
                <option value="credit_card">บัตรเครดิต</option>
                <option value="paypal">เพย์พาล</option>
                <option value="cash_on_delivery">เก็บเงินปลายทาง</option>
            </select>
        </div>

        <div class="flex justify-between items-center">
            <a href="cart.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">🛒 ตะกร้า</a>
            <span class="text-xl font-bold">รวม: ฿<?= number_format($total, 2) ?></span>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">ยืนยันการสั่งซื้อ</button>
        </div>
    </form>
</body>

</html>
