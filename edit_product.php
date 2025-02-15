<?php
session_start();
include 'dbconfig.php';

// ตรวจสอบการเข้าถึงสำหรับผู้ใช้ที่มีบทบาทเป็น manager
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "manager") {
    header("Location: index.html");
    exit();
}

// รับค่า ID ของสินค้าที่จะทำการแก้ไข
$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // หากไม่ได้อัปโหลดไฟล์ใหม่ ให้ใช้ไฟล์เดิมจากฐานข้อมูล
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
                // คำสั่ง SQL สำหรับการอัปเดตข้อมูล
                $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, description = ?, stock = ?, category = ? WHERE id = ?");
                $stmt->bind_param("sissisi", $name, $price, $profile_picture_path, $description, $stock,  $category, $productId);
            } else {
                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์!";
            }
        } else {
            echo "ขออภัย, เฉพาะไฟล์ประเภท JPG, JPEG, PNG, GIF , webp เท่านั้น";
        }
    } else {
        // หากไม่ได้เลือกไฟล์ใหม่ ให้ใช้รูปเดิม
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, stock = ?, category = ? WHERE id = ?");
        $stmt->bind_param("sisisi", $name, $price, $description, $stock,  $category, $productId);
    }

    // ตรวจสอบการอัปเดตข้อมูล
    if ($stmt->execute()) {
        // Redirect ไปยังหน้าเดียวกันและส่งค่าผ่าน URL
        header("Location: edit_product.php?id=$productId&success=1");
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาดในการอัปเดตข้อมูล"]);
    }

    $stmt->close();
}

// ดึงข้อมูลสินค้าเพื่อแสดงในฟอร์ม
$sql = "SELECT id, name, description, price, stock, category, image FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "ไม่พบสินค้านี้";
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'nav.html'; ?>

    <div class="container mx-auto p-4">
        <br><h1 class="text-2xl font-bold mb-6">แก้ไขข้อมูลสินค้า</h1>

        <form action="edit_product.php?id=<?php echo $productId; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold">ชื่อสินค้า</label>
                <input type="text" name="name" id="name" class="border border-gray-300 p-2 w-full" value="<?php echo $row['name']; ?>" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold">รายละเอียดสินค้า</label>
                <textarea name="description" id="description" class="border border-gray-300 p-2 w-full" required><?php echo $row['description']; ?></textarea>
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-semibold">ราคา</label>
                <input type="number" name="price" id="price" class="border border-gray-300 p-2 w-full" value="<?php echo $row['price']; ?>" required>
            </div>

            <div class="mb-4">
                <label for="stock" class="block text-sm font-semibold">จำนวนสินค้า</label>
                <input type="number" name="stock" id="stock" class="border border-gray-300 p-2 w-full" value="<?php echo $row['stock']; ?>" required>
            </div>

            <div class="mb-4">
                <label for="category" class="block text-sm font-semibold">หมวดหมูู่สินค้า</label>
                <input type="text" name="category" id="category" class="border border-gray-300 p-2 w-full" value="<?php echo $row['category']; ?>" required>
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-semibold">ไฟล์รูปภาพ</label>
                <!-- แสดงรูปภาพเดิม -->
                <div class="mb-2">
                    <img id="currentImage" src="<?php echo $row['image']; ?>" alt="Current Image" class="w-32 h-32 object-cover">
                </div>
                <!-- Input สำหรับเลือกไฟล์ -->
                <input type="file" name="profile_picture" id="image" class="border border-gray-300 p-2 w-full" onchange="previewImage(event)">
            </div>

            <!-- JavaScript สำหรับแสดงภาพที่เลือก -->
            <script>
                function previewImage(event) {
                    const file = event.target.files[0];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // เปลี่ยนแปลงแหล่งที่มาของ <img> tag เพื่อแสดงไฟล์ใหม่
                        const imgElement = document.getElementById('currentImage');
                        imgElement.src = e.target.result;
                    };

                    if (file) {
                        reader.readAsDataURL(file);
                    }
                }

                // เช็คค่า success ใน URL
                const urlParams = new URLSearchParams(window.location.search);
                const success = urlParams.get('success');

                // ถ้าค่า success มีค่าเป็น 1, แสดงข้อความแจ้งเตือน
                if (success == '1') {
                    alert("แก้ไขข้อมูลสินค้าสำเร็จ");
                }
            </script>

            <button type="submit" class="bg-blue-500 text-white p-2 rounded">บันทึกการแก้ไข</button>
            <a href="show_products.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">กลับไปดูสินค้าทั้งหมด</a>
        </form>
        <div class="text-center mt-6">
            <a href="manager_dashboard.php" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg">
                กลับไปหน้าผู้จัดการ
            </a>
        </div>
    </div>

</body>

</html>