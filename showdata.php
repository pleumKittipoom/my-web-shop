<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการเข้าถึงสำหรับผู้ใช้ที่มีบทบาทเป็น admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html");
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
    <title>รายชื่อลูกค้า & ผู้จัดการ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container mx-auto p-8 bg-white shadow-lg rounded-lg">
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
                                    <span class="px-3 py-1 rounded-full text-white 
                                        <?php echo $row['role'] === 'manager' ? 'bg-green-500' : 'bg-gray-500'; ?>">
                                        <?php echo htmlspecialchars($row['role']); ?>
                                    </span>
                                </td>
                                <td class="p-3 border border-gray-300 text-center">
                                    <a href="edit_customer.php?id=<?php echo htmlspecialchars($row['id']); ?>" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded-lg mx-1">
                                       แก้ไข
                                    </a>
                                    <a href="delete_user.php?id=<?php echo htmlspecialchars($row['id']); ?>" 
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
            <a href="admin_dashboard.php" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg">
                กลับไปหน้าแอดมิน
            </a>
        </div>
    </div>

</body>
</html>
