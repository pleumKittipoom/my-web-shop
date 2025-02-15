<?php
session_start();
include 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $product_id = (int) $_POST['id'];
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    // ตรวจสอบ stock ของสินค้า
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        $stock = $product['stock'];

        // ตรวจสอบว่าสินค้าไม่เกิน stock
        $cart_quantity = $_SESSION['cart'][$product_id] ?? 0;
        if ($cart_quantity + $quantity > $stock) {
            echo json_encode(["success" => false, "message" => "สินค้าไม่เพียงพอใน stock"]);
            exit;
        }

        // เพิ่มสินค้าในตะกร้า
        $_SESSION['cart'][$product_id] = $cart_quantity + $quantity;
        echo json_encode(["success" => true, "message" => "เพิ่มสินค้าเรียบร้อย"]);
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบสินค้า"]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "คำขอไม่ถูกต้อง"]);
}
?>
