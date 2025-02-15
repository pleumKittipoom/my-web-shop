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

// ลบบัญชีลูกค้าจากฐานข้อมูล
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo "<script>alert('ลบบัญชีลูกค้าสำเร็จ'); window.location.href='showdata.php';</script>";
} else {
    echo "<script>alert('เกิดความผิดพลาดในการลบบัญชีลูกค้า'); window.location.href='showdata.php';</script>";
}

// ปิดการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
