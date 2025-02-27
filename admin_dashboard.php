<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบว่า user เป็น admin หรือไม่
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html"); // ถ้าไม่ใช่ admin ให้กลับไปหน้า login
    exit();
}

// ใช้ SQL IN เพื่อดึงทั้ง customer และ manager
$stmt = $conn->prepare("SELECT id, username, first_name, last_name, email, role FROM users WHERE role IN (?, ?)");
$role1 = 'customer';
$role2 = 'manager';
$stmt->bind_param("ss", $role1, $role2);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen" style="background: url('https://wallpapercave.com/wp/wp3837811.jpg') no-repeat center center fixed; background-size: cover;">
    <div class="container mx-auto p-8 bg-white/80 shadow-lg rounded-lg grid grid-cols-1 md:grid-cols-12 gap-4 my-4">
        
        <!-- Sidebar Admin Dashboard -->
        <div class="md:col-span-2 p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600 mt-2">ยินดีต้อนรับ, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>!</p>
            <div class="mt-6 flex flex-col space-y-4">
                <a href="profile.php" class="flex items-center space-x-2 text-gray-700 hover:text-blue-500">
                    <i class="ph ph-user"></i> <span>Profile</span>
                </a>
                <a href="addcustomer.html" class="flex items-center space-x-2 text-gray-700 hover:text-green-500">
                    <i class="ph ph-user-plus"></i> <span>เพิ่มลูกค้า</span>
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

        <!-- รายชื่อผู้ใช้ -->
        <div class="md:col-span-10 p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">รายชื่อผู้ใช้ (ลูกค้า & ผู้จัดการ)</h1>
            <?php if ($result->num_rows === 0): ?>
                <p class="text-center text-gray-600">ไม่พบข้อมูล</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-auto w-full border-collapse border border-gray-300 shadow-md">
                        <thead>
                            <tr class="bg-blue-500 text-white">
                                <th class="p-3 border border-gray-300">Username</th>
                                <th class="p-3 border border-gray-300">ชื่อ</th>
                                <th class="p-3 border border-gray-300">นามสกุล</th>
                                <th class="p-3 border border-gray-300">Email</th>
                                <th class="p-3 border border-gray-300">Role</th>
                                <th class="p-3 border border-gray-300">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-100">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-200">
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['first_name']); ?></td>
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['last_name']); ?></td>
                                    <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        <span class="px-3 py-1 rounded-full text-white <?php echo $row['role'] === 'manager' ? 'bg-green-500' : 'bg-gray-500'; ?>">
                                            <?php echo htmlspecialchars($row['role']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 border border-gray-300 text-center">
                                        <a href="edit_customer.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded-lg mx-1">แก้ไข</a>
                                        <a href="delete_user.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded-lg mx-1">ลบ</a>
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

