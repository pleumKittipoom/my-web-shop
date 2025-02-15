<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบว่าเป็น POST request หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// รับค่าจากฟอร์ม
$name = trim($_POST['name'] ?? '');
$price = $_POST["price"];
$description = trim($_POST['description'] ?? '');
$stock = $_POST["stock"];
$category = trim($_POST['category'] ?? '');

// อัปโหลดไฟล์
$uploads_dir = 'uploads'; // Directory for uploaded files

// ตรวจสอบว่าโฟลเดอร์ uploads มีอยู่หรือไม่ ถ้าไม่มีก็สร้าง
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0777, true); // Create directory if it doesn't exist
}

// ตรวจสอบการอัปโหลดไฟล์
$profile_picture = $_FILES['profile_picture'] ?? null;
$profile_picture_path = '';

if ($profile_picture && $profile_picture['error'] === UPLOAD_ERR_OK) {
    // ตรวจสอบประเภทไฟล์ที่อัปโหลด เช่น JPG, PNG, GIF
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_ext = strtolower(pathinfo($profile_picture['name'], PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_exts)) {
        // กำหนดชื่อไฟล์เดิม
        $profile_picture_name = basename($profile_picture['name']);

        // กำหนด path ที่จะเก็บไฟล์
        $profile_picture_path = $uploads_dir . '/' . $profile_picture_name;

        // ตรวจสอบว่าไฟล์มีอยู่ในโฟลเดอร์หรือไม่
        if (file_exists($profile_picture_path)) {
            // ถ้าไฟล์มีอยู่แล้ว เปลี่ยนชื่อไฟล์
            $profile_picture_name = uniqid() . '.' . $file_ext;
            $profile_picture_path = $uploads_dir . '/' . $profile_picture_name;
        }

        // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
        if (move_uploaded_file($profile_picture['tmp_name'], $profile_picture_path)) {

            // คำสั่ง SQL สำหรับการบันทึกข้อมูล
            $stmt = $conn->prepare("INSERT INTO products (name, price, image, description, stock, category) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissis", $name, $price, $profile_picture_path, $description, $stock, $category);

            // ตรวจสอบการบันทึกข้อมูล
            if ($stmt->execute()) {
                // ส่งข้อมูลสำเร็จกลับไปที่ฝั่งลูกค้า
                echo "<script>alert('เพิ่มสินค้าสำเร็จ'); window.location.href='addproduct.html';</script>";
                echo json_encode(["success" => true, "message" => "เพิ่มสินค้าสำเร็จ"]);
            } else {
                echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการเพิ่มข้อมูล"]);
            }
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์!";
        }
    } else {
        echo "ขออภัย, เฉพาะไฟล์ประเภท JPG, JPEG, PNG, GIF , webp เท่านั้น";
    }
} else {
    echo "ไม่มีไฟล์หรือเกิดข้อผิดพลาดในการอัปโหลดไฟล์";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
