<?php
session_start();
include 'dbconfig.php';  // ใช้การเชื่อมต่อฐานข้อมูลจาก dbconfig.php

// ตรวจสอบการเข้าถึงข้อมูลสำหรับผู้ใช้ที่มีบทบาทเป็น admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.html");
    exit();
}

// ตรวจสอบว่าได้ส่งค่ารหัสผู้ใช้มาใน URL หรือไม่
if (!isset($_GET['id'])) {
    echo "ไม่พบข้อมูลลูกค้า";
    exit();
}

$user_id = $_GET['id'];

// ดึงข้อมูลลูกค้าจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ถ้าหากไม่พบข้อมูล
if ($result->num_rows === 0) {
    echo "ไม่พบข้อมูลลูกค้าตามรหัสที่กำหนด";
    exit();
}

$user = $result->fetch_assoc(); // ดึงข้อมูลลูกค้า

// ถ้าหากฟอร์มถูกส่งมา
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $province = $_POST['province'];
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // อัพเดตข้อมูลลูกค้า
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, sex = ?, age = ?, province = ?, email = ?, role = ?  WHERE id = ?");
    $stmt->bind_param("sssssssi", $first_name, $last_name, $sex, $age, $province, $email, $role, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('ข้อมูลโปรไฟล์ถูกอัปเดตแล้ว'); window.location.href='showdata.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg mt-10">
        <h1 class="text-3xl font-semibold text-gray-800 text-center mb-6">แก้ไขโปรไฟล์ (Admin)</h1>

        <form method="POST">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">ชื่อจริง:</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">นามสกุล:</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">เพศ:</label>
                    <select name="sex"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="male" <?php echo ($user['sex'] == 'Male') ? 'selected' : ''; ?>>ชาย</option>
                        <option value="female" <?php echo ($user['sex'] == 'Female') ? 'selected' : ''; ?>>หญิง</option>
                    </select>
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">อายุ:</label>
                    <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">จังหวัด:</label>
                    <select name="province"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="กรุงเทพมหานคร" <?php echo ($user['province'] == 'กรุงเทพมหานคร') ? 'selected' : ''; ?>>กรุงเทพมหานคร</option>
                        <option value="กระบี่" <?php echo ($user['province'] == 'กระบี่') ? 'selected' : ''; ?>>กระบี่</option>
                        <option value="กาญจนบุรี" <?php echo ($user['province'] == 'กาญจนบุรี') ? 'selected' : ''; ?>>กาญจนบุรี</option>
                        <option value="กาฬสินธุ์" <?php echo ($user['province'] == 'กาฬสินธุ์') ? 'selected' : ''; ?>>กาฬสินธุ์</option>
                        <option value="กำแพงเพชร" <?php echo ($user['province'] == 'กำแพงเพชร') ? 'selected' : ''; ?>>กำแพงเพชร</option>
                        <option value="ขอนแก่น" <?php echo ($user['province'] == 'ขอนแก่น') ? 'selected' : ''; ?>>ขอนแก่น</option>
                        <option value="จันทบุรี" <?php echo ($user['province'] == 'จันทบุรี') ? 'selected' : ''; ?>>จันทบุรี</option>
                        <option value="ฉะเชิงเทรา" <?php echo ($user['province'] == 'ฉะเชิงเทรา') ? 'selected' : ''; ?>>ฉะเชิงเทรา</option>
                        <option value="ชัยนาท" <?php echo ($user['province'] == 'ชัยนาท') ? 'selected' : ''; ?>>ชัยนาท</option>
                        <option value="ชัยภูมิ" <?php echo ($user['province'] == 'ชัยภูมิ') ? 'selected' : ''; ?>>ชัยภูมิ</option>
                        <option value="ชุมพร" <?php echo ($user['province'] == 'ชุมพร') ? 'selected' : ''; ?>>ชุมพร</option>
                        <option value="ชลบุรี" <?php echo ($user['province'] == 'ชลบุรี') ? 'selected' : ''; ?>>ชลบุรี</option>
                        <option value="เชียงใหม่" <?php echo ($user['province'] == 'เชียงใหม่') ? 'selected' : ''; ?>>เชียงใหม่</option>
                        <option value="เชียงราย" <?php echo ($user['province'] == 'เชียงราย') ? 'selected' : ''; ?>>เชียงราย</option>
                        <option value="ตรัง" <?php echo ($user['province'] == 'ตรัง') ? 'selected' : ''; ?>>ตรัง</option>
                        <option value="ตราด" <?php echo ($user['province'] == 'ตราด') ? 'selected' : ''; ?>>ตราด</option>
                        <option value="ตาก" <?php echo ($user['province'] == 'ตาก') ? 'selected' : ''; ?>>ตาก</option>
                        <option value="นครนายก" <?php echo ($user['province'] == 'นครนายก') ? 'selected' : ''; ?>>นครนายก</option>
                        <option value="นครปฐม" <?php echo ($user['province'] == 'นครปฐม') ? 'selected' : ''; ?>>นครปฐม</option>
                        <option value="นครพนม" <?php echo ($user['province'] == 'นครพนม') ? 'selected' : ''; ?>>นครพนม</option>
                        <option value="นครราชสีมา" <?php echo ($user['province'] == 'นครราชสีมา') ? 'selected' : ''; ?>>นครราชสีมา</option>
                        <option value="นครศรีธรรมราช" <?php echo ($user['province'] == 'นครศรีธรรมราช') ? 'selected' : ''; ?>>นครศรีธรรมราช</option>
                        <option value="นครสวรรค์" <?php echo ($user['province'] == 'นครสวรรค์') ? 'selected' : ''; ?>>นครสวรรค์</option>
                        <option value="นราธิวาส" <?php echo ($user['province'] == 'นราธิวาส') ? 'selected' : ''; ?>>นราธิวาส</option>
                        <option value="น่าน" <?php echo ($user['province'] == 'น่าน') ? 'selected' : ''; ?>>น่าน</option>
                        <option value="นนทบุรี" <?php echo ($user['province'] == 'นนทบุรี') ? 'selected' : ''; ?>>นนทบุรี</option>
                        <option value="บึงกาฬ" <?php echo ($user['province'] == 'บึงกาฬ') ? 'selected' : ''; ?>>บึงกาฬ</option>
                        <option value="บุรีรัมย์" <?php echo ($user['province'] == 'บุรีรัมย์') ? 'selected' : ''; ?>>บุรีรัมย์</option>
                        <option value="ประจวบคีรีขันธ์" <?php echo ($user['province'] == 'ประจวบคีรีขันธ์') ? 'selected' : ''; ?>>ประจวบคีรีขันธ์</option>
                        <option value="ปทุมธานี" <?php echo ($user['province'] == 'ปทุมธานี') ? 'selected' : ''; ?>>ปทุมธานี</option>
                        <option value="ปราจีนบุรี" <?php echo ($user['province'] == 'ปราจีนบุรี') ? 'selected' : ''; ?>>ปราจีนบุรี</option>
                        <option value="ปัตตานี" <?php echo ($user['province'] == 'ปัตตานี') ? 'selected' : ''; ?>>ปัตตานี</option>
                        <option value="พะเยา" <?php echo ($user['province'] == 'พะเยา') ? 'selected' : ''; ?>>พะเยา</option>
                        <option value="พระนครศรีอยุธยา" <?php echo ($user['province'] == 'พระนครศรีอยุธยา') ? 'selected' : ''; ?>>พระนครศรีอยุธยา</option>
                        <option value="พังงา" <?php echo ($user['province'] == 'พังงา') ? 'selected' : ''; ?>>พังงา</option>
                        <option value="พิจิตร" <?php echo ($user['province'] == 'พิจิตร') ? 'selected' : ''; ?>>พิจิตร</option>
                        <option value="พิษณุโลก" <?php echo ($user['province'] == 'พิษณุโลก') ? 'selected' : ''; ?>>พิษณุโลก</option>
                        <option value="เพชรบุรี" <?php echo ($user['province'] == 'เพชรบุรี') ? 'selected' : ''; ?>>เพชรบุรี</option>
                        <option value="เพชรบูรณ์" <?php echo ($user['province'] == 'เพชรบูรณ์') ? 'selected' : ''; ?>>เพชรบูรณ์</option>
                        <option value="แพร่" <?php echo ($user['province'] == 'แพร่') ? 'selected' : ''; ?>>แพร่</option>
                        <option value="พัทลุง" <?php echo ($user['province'] == 'พัทลุง') ? 'selected' : ''; ?>>พัทลุง</option>
                        <option value="ภูเก็ต" <?php echo ($user['province'] == 'ภูเก็ต') ? 'selected' : ''; ?>>ภูเก็ต</option>
                        <option value="มหาสารคาม" <?php echo ($user['province'] == 'มหาสารคาม') ? 'selected' : ''; ?>>มหาสารคาม</option>
                        <option value="มุกดาหาร" <?php echo ($user['province'] == 'มุกดาหาร') ? 'selected' : ''; ?>>มุกดาหาร</option>
                        <option value="แม่ฮ่องสอน" <?php echo ($user['province'] == 'แม่ฮ่องสอน') ? 'selected' : ''; ?>>แม่ฮ่องสอน</option>
                        <option value="ยโสธร" <?php echo ($user['province'] == 'ยโสธร') ? 'selected' : ''; ?>>ยโสธร</option>
                        <option value="ยะลา" <?php echo ($user['province'] == 'ยะลา') ? 'selected' : ''; ?>>ยะลา</option>
                        <option value="ร้อยเอ็ด" <?php echo ($user['province'] == 'ร้อยเอ็ด') ? 'selected' : ''; ?>>ร้อยเอ็ด</option>
                        <option value="ระนอง" <?php echo ($user['province'] == 'ระนอง') ? 'selected' : ''; ?>>ระนอง</option>
                        <option value="ระยอง" <?php echo ($user['province'] == 'ระยอง') ? 'selected' : ''; ?>>ระยอง</option>
                        <option value="ราชบุรี" <?php echo ($user['province'] == 'ราชบุรี') ? 'selected' : ''; ?>>ราชบุรี</option>
                        <option value="ลพบุรี" <?php echo ($user['province'] == 'ลพบุรี') ? 'selected' : ''; ?>>ลพบุรี</option>
                        <option value="ลำปาง" <?php echo ($user['province'] == 'ลำปาง') ? 'selected' : ''; ?>>ลำปาง</option>
                        <option value="ลำพูน" <?php echo ($user['province'] == 'ลำพูน') ? 'selected' : ''; ?>>ลำพูน</option>
                        <option value="เลย" <?php echo ($user['province'] == 'เลย') ? 'selected' : ''; ?>>เลย</option>
                        <option value="ศรีสะเกษ" <?php echo ($user['province'] == 'ศรีสะเกษ') ? 'selected' : ''; ?>>ศรีสะเกษ</option>
                        <option value="สกลนคร" <?php echo ($user['province'] == 'สกลนคร') ? 'selected' : ''; ?>>สกลนคร</option>
                        <option value="สงขลา" <?php echo ($user['province'] == 'สงขลา') ? 'selected' : ''; ?>>สงขลา</option>
                        <option value="สมุทรสาคร" <?php echo ($user['province'] == 'สมุทรสาคร') ? 'selected' : ''; ?>>สมุทรสาคร</option>
                        <option value="สมุทรปราการ" <?php echo ($user['province'] == 'สมุทรปราการ') ? 'selected' : ''; ?>>สมุทรปราการ</option>
                        <option value="สระแก้ว" <?php echo ($user['province'] == 'สระแก้ว') ? 'selected' : ''; ?>>สระแก้ว</option>
                        <option value="สระบุรี" <?php echo ($user['province'] == 'สระบุรี') ? 'selected' : ''; ?>>สระบุรี</option>
                        <option value="สงขลา" <?php echo ($user['province'] == 'สงขลา') ? 'selected' : ''; ?>>สงขลา</option>
                        <option value="สุพรรณบุรี" <?php echo ($user['province'] == 'สุพรรณบุรี') ? 'selected' : ''; ?>>สุพรรณบุรี</option>
                    </select>
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">อีเมล:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <div class="flex items-center justify-between">
                    <label class="font-medium text-gray-700 w-1/3">role:</label>
                    <input type="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>"
                        class="w-2/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </div>

                <div class="mt-6 text-center">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        อัปเดตข้อมูล
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="showdata.php" class="text-gray-600 hover:text-gray-800">ยกเลิก</a>
        </div>
    </div>

</body>

</html>