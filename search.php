<?php
session_start();
include 'dbconfig.php';
include 'nav.php';

// รับค่าจากฟอร์มค้นหาที่ผู้ใช้กรอกผ่าน GET หรือ POST
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : (isset($_POST['keyword']) ? $_POST['keyword'] : '');

// คำสั่ง SQL สำหรับค้นหาสินค้า
$sql = "SELECT id, name, description, image, price FROM products WHERE name LIKE ? OR description LIKE ?";
$stmt = $conn->prepare($sql);

// เตรียมค่าของตัวแปรในคำสั่ง SQL (ป้องกัน SQL Injection)
$searchTerm = "%" . $keyword . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);

// Execute query
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- เพิ่ม Font Awesome สำหรับไอคอน -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        function openModal(productId) {
            document.getElementById('modal').classList.remove('hidden');
            fetch('get_product_details.php?id=' + productId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modal-name').textContent = data.name;
                    document.getElementById('modal-description').textContent = data.description;
                    document.getElementById('modal-price').textContent = 'ราคา ' + data.price + ' บาท';
                    document.getElementById('modal-image').src = data.image;
                    document.getElementById('modal-stock').textContent = 'จำนวนสินค้าที่เหลือ ' + data.stock;
                    document.getElementById('modal-product-id').value = productId; // ตั้งค่า ID ที่นี่
                });

            // ให้ปุ่มเพิ่มลงตะกร้าแสดงเมื่อ Modal เปิด
            document.querySelector('.cart-button-modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }

        function addToCart(productId) {
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + productId + '&quantity=1'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ ' + data.message);
                    } else {
                        alert('❌ ' + data.message); // แสดงข้อความแจ้งเตือนหากสินค้าไม่พอ
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                });
        }
    </script>
    <style>
        .cart-button {
            position: absolute;
            bottom: 12px;
            right: 8px;
            /* background-color: rgb(233, 233, 233); */
            color: black;
            border: none;
            border-radius: 50%;
            padding: 10px;
            font-size: 20px;
            cursor: pointer;
            /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
        }

        .cart-button:hover {
            background-color: rgb(228, 233, 233);
        }

        .cart-button-modal {
            display: none;
            /* ซ่อนปุ่มโดยเริ่มต้น */
            /* background-color: rgb(233, 233, 233); */
            color: black;
            padding: 8px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
            width: 40px;
            height: 40px;
            z-index: 1000;
        }

        .cart-button-modal:hover {
            background-color: rgb(228, 233, 233);
        }
    </style>
</head>

<body class="bg-gray-100 pt-20">

    <div class="container mx-auto p-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="bg-white p-5 rounded-lg shadow-lg relative">';
                    echo '<img src="' . $row["image"] . '" alt="' . $row["name"] . '" class="w-full h-auto rounded-lg mb-4 cursor-pointer" onclick="openModal(' . $row["id"] . ')">';
                    echo '<h2 class="text-xl font-bold mb-2">' . $row["name"] . '</h2>';
                    echo '<p class="text-gray-900 font-bold mb-4">ราคา ' . $row["price"] . ' บาท</p>';
                    echo '<button type="button" class="cart-button" onclick="addToCart(' . $row["id"] . ')"><i class="fas fa-cart-plus"></i></button>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-center text-gray-600">ไม่มีสินค้าในหมวดนี้</p>';
            }
            $stmt->close();
            ?>
        </div>
    </div>

    <!-- Modal แสดงรายละเอียดสินค้า -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg w-96 max-h-screen overflow-y-auto">
            <img id="modal-image" class="w-full h-auto mb-4" src="" alt="Product Image">
            <h2 id="modal-name" class="text-xl font-bold mb-2"></h2>
            <p id="modal-description" class="text-gray-700 mb-4"></p>
            <p id="modal-price" class="text-gray-900 font-bold mb-4"></p>
            <p id="modal-stock" class="text-gray-900 font-bold mb-4"></p>

            <!-- ปุ่มปิดและเพิ่มลงตะกร้าอยู่ในแถวเดียวกัน -->
            <div class="flex justify-between">
                <button class="bg-red-500 text-white p-2 rounded w-full sm:w-auto" onclick="closeModal()">ปิด</button>
                <!-- ปุ่ม "เพิ่มลงตะกร้า" ใน Modal -->
                <input type="hidden" id="modal-product-id" value="">
                <button class="cart-button-modal" onclick="addToCart(document.getElementById('modal-product-id').value)">
                    <i class="fas fa-cart-plus"></i>
                </button>
            </div>
        </div>
    </div>

</body>

</html>