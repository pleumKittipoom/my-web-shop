<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบว่า user ได้ล็อกอินหรือยัง
if (!isset($_SESSION["user_id"])) {
    header("Location: index.html");
    exit();
}

$userId = $_SESSION["user_id"];

// ดึงข้อมูลของผู้ใช้จากฐานข้อมูล
$sql = "SELECT username, first_name, last_name, sex, age, province, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// ถ้าพบข้อมูลผู้ใช้
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ใช้</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100" style="background: url('https://wallpapercave.com/wp/wp3837811.jpg') no-repeat center center fixed; background-size: cover;">

    <div class="max-w-2xl mx-auto p-8 bg-white/20 shadow-lg rounded-lg mt-4">
        <h1 class="text-3xl font-semibold text-gray-800 text-center text-white mb-6">ข้อมูลโปรไฟล์</h1>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">ชื่อผู้ใช้:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['username']); ?></p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">ชื่อจริง:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['first_name']); ?></p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">นามสกุล:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['last_name']); ?></p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">เพศ:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['sex'] == 'Male' ? 'ชาย' : 'หญิง'); ?></p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">อายุ:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['age']); ?> ปี</p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">จังหวัด:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['province']); ?></p>
            </div>

            <div class="flex items-center justify-between">
                <label class="font-medium text-white w-1/3">อีเมล:</label>
                <p class="text-white w-2/3"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="edit_profile.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4">
                แก้ไขข้อมูล
            </a>
            <a href="products.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                สินค้า
            </a>
        </div>
    </div>

</body>

</html>