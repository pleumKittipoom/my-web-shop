<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการล็อกอิน
// if (!isset($_SESSION['username'])) {
//   header('Location: products.php');
//   exit;
// }

// ตรวจสอบตะกร้าสินค้า
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// จัดการการเพิ่ม, ลด, ลบสินค้า
if (isset($_GET['action'], $_GET['id'])) {
  $product_id = $_GET['id'];
  $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

  // ดึงข้อมูลสินค้า (เช่น จำนวน stock)
  $result = $conn->query("SELECT stock FROM products WHERE id = $product_id");
  $product = $result->fetch_assoc();
  
  if ($product) {
    $stock = $product['stock'];
    switch ($_GET['action']) {
      case 'add':
        // ตรวจสอบจำนวนสินค้าในตะกร้ากับจำนวนสินค้าคงเหลือ
        if ($_SESSION['cart'][$product_id] + $quantity > $stock) {
          // ถ้าจำนวนสินค้าที่จะเพิ่มเกิน stock ให้แสดงข้อความเตือน
          echo "<script>alert('ไม่สามารถเพิ่มสินค้าเกินจำนวนที่มีในสต็อก');</script>";
        } else {
          $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + $quantity;
        }
        break;
      case 'remove':
        unset($_SESSION['cart'][$product_id]);
        break;
      case 'decrease':
        if (isset($_SESSION['cart'][$product_id]) && $_SESSION['cart'][$product_id] > 1) {
          $_SESSION['cart'][$product_id]--;
        }
        break;
    }
  }

  header('Location: cart.php');
  exit;
}

// ดึงข้อมูลสินค้า
$items = [];
if (!empty($_SESSION['cart'])) {
  $ids = implode(',', array_keys($_SESSION['cart']));
  $result = $conn->query("SELECT id, name, price FROM products WHERE id IN ($ids)");
  while ($row = $result->fetch_assoc()) {
    $items[$row['id']] = $row;
  }
}
?>



<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>ตะกร้าสินค้า</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-5">
  <h1 class="text-2xl font-bold mb-5">🛒 ตะกร้าสินค้า</h1>

  <table class="w-full bg-white border border-gray-200 rounded-lg">
    <thead>
      <tr class="bg-gray-200">
        <th class="p-2 text-left">ชื่อสินค้า</th>
        <th class="p-2">ราคา</th>
        <th class="p-2">จำนวน</th>
        <th class="p-2">รวม</th>
        <th class="p-2">จัดการ</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($_SESSION['cart'])) : ?>
        <tr>
          <td colspan="5" class="text-center p-4">ไม่มีสินค้าในตะกร้า</td>
        </tr>
      <?php else : ?>
        <?php foreach ($_SESSION['cart'] as $id => $quantity) : ?>
          <tr>
            <td class="p-2 text-left"><?= htmlspecialchars($items[$id]['name']) ?></td>
            <td class="p-2 text-center">฿<?= number_format($items[$id]['price'], 2) ?></td>
            <td class="p-2 text-center">
              <a href="cart.php?action=decrease&id=<?= $id ?>" class="text-blue-500">➖</a>
              <?= $quantity ?>
              <a href="cart.php?action=add&id=<?= $id ?>" class="text-blue-500">➕</a>
            </td>
            <td class="p-2 text-center">฿<?= number_format($items[$id]['price'] * $quantity, 2) ?></td>
            <td class="p-2 text-center">
              <a href="cart.php?action=remove&id=<?= $id ?>" class="text-red-500">ลบ</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="mt-5 flex space-x-4">
    <a href="products.php" class="bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">🛒 ซื้อสินค้าต่อ</a>
    <!-- <a href="cart.php?action=clear" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">🗑️ ล้างตะกร้า</a> -->
    <?php if (!empty($_SESSION['cart'])) : ?>
      <a href="checkout.php" class="bg-indigo-500 hover:bg-fuchsia-500 text-white font-bold py-2 px-4 rounded">ชำระเงิน</a>
    <?php endif; ?>
  </div>

</body>

</html>