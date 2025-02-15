<?php
session_start();
include 'dbconfig.php';  // ตรวจสอบให้แน่ใจว่าไฟล์นี้มีตัวแปร $conn ที่เชื่อมต่อ MySQLi แล้ว

// ตรวจสอบว่าเป็น POST request หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// รับค่าจากฟอร์ม
$username         = trim($_POST['username'] ?? '');
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$first_name       = trim($_POST['first_name'] ?? '');
$last_name        = trim($_POST['last_name'] ?? '');
$sex              = $_POST['sex'] ?? '';
$age              = $_POST['age'] ?? '';
$province         = $_POST['province'] ?? '';
$email            = trim($_POST['email'] ?? '');

// ตรวจสอบว่า password และ confirm_password ตรงกันหรือไม่
if ($password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'รหัสผ่านไม่ตรงกัน']);
    exit;
}

// ตรวจสอบ Email ซ้ำ
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'อีเมลนี้ถูกใช้แล้ว']);
    exit;
}

// ตรวจสอบ Username ซ้ำ
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_assoc()) {
    echo json_encode(['success' => false, 'message' => 'Username นี้ถูกใช้แล้ว']);
    exit;
}

// เข้ารหัสรหัสผ่าน
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// เพิ่มข้อมูลลงในฐานข้อมูล
$sql = "INSERT INTO users (username, password, first_name, last_name, sex, age, province, email, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'customer')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiss", $username, $hashed_password, $first_name, $last_name, $sex, $age, $province, $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ!']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $stmt->error]);
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
