<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบว่าเป็น manager หรือไม่
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "manager") {
    header("Location: index.html");
    exit();
}

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$sql = "SELECT id, name, description, price, stock, image FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen" style="background: url('https://wallpapercave.com/wp/wp3837811.jpg') no-repeat center center fixed; background-size: cover;">

    <div class="container mx-auto p-8 bg-white/80 shadow-lg rounded-lg grid grid-cols-1 md:grid-cols-12 gap-4 my-8">
        
        <!-- Sidebar Manager -->
        <div class="md:col-span-2 p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800">Manager Dashboard</h1>
            <p class="text-gray-600 mt-2">ยินดีต้อนรับ, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</p>
            <div class="mt-6 flex flex-col space-y-4">
                <a href="profile.php" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500">
                    <i class="ph ph-user"></i> <span>Profile</span>
                </a>
                <a href="addproduct.html" class="flex items-center space-x-2 text-gray-700 hover:text-green-500">
                    <i class="ph ph-plus"></i> <span>เพิ่มสินค้า</span>
                </a>
                <a href="products.php" class="flex items-center space-x-2 text-gray-700 hover:text-yellow-500">
                    <i class="ph ph-shopping-cart"></i> <span>Product</span>
                </a>
                <a href="orders.php" class="flex items-center space-x-2 text-gray-700 hover:text-pink-500">
                    <i class="ph ph-receipt"></i> <span>Orders List</span>
                </a>
                <a href="logout.php" class="flex items-center space-x-2 text-gray-700 hover:text-red-500">
                    <i class="ph ph-sign-out"></i> <span>ออกจากระบบ</span>
                </a>
            </div>
        </div>

        <!-- รายการสินค้า -->
        <div class="md:col-span-10 p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">รายการสินค้า</h1>

            <?php if ($result->num_rows === 0): ?>
                <p class="text-center text-gray-600">ไม่พบข้อมูลสินค้า</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300 shadow-md">
                        <thead>
                            <tr class="bg-blue-500 text-white">
                                <th class="p-3 border border-gray-300">ชื่อสินค้า</th>
                                <th class="p-3 border border-gray-300">ราคา</th>
                                <th class="p-3 border border-gray-300">จำนวนสินค้า</th>
                                <th class="p-3 border border-gray-300">รูปภาพ</th>
                                <th class="p-3 border border-gray-300">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-200">
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['price']); ?> บาท</td>
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['stock']); ?></td>
                                    <td class="p-3 border border-gray-300 flex justify-center items-center">
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="w-20 h-20 object-cover">
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        <a href="edit_product.php?id=<?php echo htmlspecialchars($row['id']); ?>"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded-lg mx-1">
                                            แก้ไข
                                        </a>
                                        <a href="delete_product.php?id=<?php echo htmlspecialchars($row['id']); ?>"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded-lg mx-1">
                                            ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
