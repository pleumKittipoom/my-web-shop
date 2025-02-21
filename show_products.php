<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการเข้าถึงสำหรับผู้ใช้ที่มีบทบาทเป็น manager
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
    <title>รายการสินค้า</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // ฟังก์ชันสำหรับแสดงข้อความเด้ง
        function showMessage(message) {
            let messageText = '';

            switch(message) {
                case 'delete_success':
                    messageText = 'ลบสินค้าสำเร็จ';
                    break;
                case 'delete_failed':
                    messageText = 'ลบสินค้าไม่สำเร็จ';
                    break;
                case 'invalid_request':
                    messageText = 'คำขอไม่ถูกต้อง';
                    break;
            }

            if (messageText) {
                alert(messageText);
            }
        }

        // ตรวจสอบหากมีข้อความใน URL
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            if (message) {
                showMessage(message);
            }
        }
    </script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen"  style="background: url('https://wallpapercave.com/wp/wp3837811.jpg') no-repeat center center fixed; background-size: cover;">

    <div class="container mx-auto p-8 bg-white/80 shadow-lg rounded-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">รายการสินค้า</h1>

        <?php if ($result->num_rows === 0): ?>
            <p class="text-center text-gray-600">ไม่พบข้อมูลสินค้า</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="table-auto w-full border-collapse border border-gray-300 shadow-md">
                    <thead>
                        <tr class="bg-blue-500 text-white">
                            <th class="p-3 border border-gray-300">ชื่อสินค้า</th>
                            <!-- <th class="p-3 border border-gray-300">รายละเอียด</th> -->
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
                                <!-- <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['description']); ?></td> -->
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

        <!-- ปุ่มกลับ -->
        <div class="text-center mt-6">
            <a href="manager_dashboard.php" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg">
                กลับไปหน้าผู้จัดการ
            </a>
        </div>
    </div>

</body>

</html>