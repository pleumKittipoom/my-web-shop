<?php
include 'dbconfig.php';

$productId = $_GET['id'] ?? 0;
if ($productId) {
    $stmt = $conn->prepare("SELECT id, name, description, image, price, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid product ID']);
}
?>
