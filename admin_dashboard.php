<?php
session_start();

// ตรวจสอบว่า user เป็น admin หรือไม่
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html"); // ถ้าไม่ใช่ admin ให้กลับไปหน้า login
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-8 bg-white shadow-lg rounded-lg text-center">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">ยินดีต้อนรับ, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</p>

        <!-- ปุ่มเมนู -->
        <div class="mt-6 flex flex-wrap justify-center gap-4">
            <a href="showdata.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                ดูรายชื่อลูกค้า
            </a>
            <a href="addcustomer.html" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                เพิ่มลูกค้า
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
