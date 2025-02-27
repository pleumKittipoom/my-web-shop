<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการเข้าถึงสำหรับผู้ใช้ที่มีบทบาทเป็น manager
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "manager") {
    header("Location: index.html");
    exit();
}

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $productId = $_GET['id'];

    // ลบสินค้าจากฐานข้อมูล
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        // ลบสำเร็จ
        header("Location: manager_dashboard.php?message=delete_success");
        exit();
    } else {
        // หากลบไม่สำเร็จ
        header("Location: manager_dashboard.php?message=delete_failed");
        exit();
    }
} else {
    // หากไม่มี id ที่ส่งมา
    header("Location: manager_dashboard.php?message=invalid_request");
    exit();
}
?>
