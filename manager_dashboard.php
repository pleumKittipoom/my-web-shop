<?php
session_start();

// ตรวจสอบว่าเป็น manager หรือไม่
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "manager") {
    header("Location: index.html"); // ถ้าไม่ใช่ manager ให้กลับไปหน้า login
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-8 bg-white shadow-lg rounded-lg text-center">
        <h1 class="text-2xl font-bold text-gray-800">Manager Dashboard</h1>
        <p class="text-gray-600 mt-2">ยินดีต้อนรับ, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</p>

        <!-- ปุ่มเมนู -->
        <div class="mt-6 flex flex-wrap justify-center gap-4">
            <a href="addproduct.html" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                เพิ่มสินค้า
            </a>
            <a href="show_products.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg">
                แก้ไขสินค้า
            </a>
            <a href="products.php" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg">
                Product
            </a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg">
                ออกจากระบบ
            </a>
        </div>
    </div>

</body>
</html>
