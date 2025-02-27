<?php
session_start();
include 'dbconfig.php';

// ดึงข้อมูลคำสั่งซื้อจากตาราง orders
$sql_orders = "SELECT * FROM orders";
$result_orders = $conn->query($sql_orders);

// ดึงข้อมูลสินค้าภายในคำสั่งซื้อจาก order_items
$sql_items = "SELECT oi.order_id, oi.product_id, p.name AS product_name, oi.quantity, oi.price AS unit_price, oi.total_price
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id";
$result_items = $conn->query($sql_items);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-7xl mx-auto bg-white p-6 shadow-lg rounded-lg">

    <!-- ตาราง Order List -->
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Order List</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Username</th>
                        <th class="border px-4 py-2">First Name</th>
                        <th class="border px-4 py-2">Last Name</th>
                        <th class="border px-4 py-2">Address</th>
                        <th class="border px-4 py-2">Payment Method</th>
                        <th class="border px-4 py-2">Total</th>
                        <th class="border px-4 py-2">Order Date</th>
                        <th class="border px-4 py-2">Phone Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_orders->num_rows > 0) {
                        while ($row = $result_orders->fetch_assoc()) {
                            echo "<tr class='hover:bg-gray-100'>";
                            echo "<td class='border px-4 py-2'>" . $row["id"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["username"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["first_name"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["last_name"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["address"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["payment_method"] . "</td>";
                            echo "<td class='border px-4 py-2 font-bold text-green-600'>" . number_format($row["total"], 2) . " ฿</td>";
                            echo "<td class='border px-4 py-2'>" . $row["order_date"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["phone_number"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center border px-4 py-2'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- ตาราง Order Items -->
        <h2 class="text-2xl font-bold text-gray-700 mt-8 mb-4">Order Items</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="border px-4 py-2">Order ID</th>
                        <th class="border px-4 py-2">Product ID</th>
                        <th class="border px-4 py-2">Product Name</th>
                        <th class="border px-4 py-2">Quantity</th>
                        <th class="border px-4 py-2">Unit Price</th>
                        <th class="border px-4 py-2">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_items->num_rows > 0) {
                        while ($row = $result_items->fetch_assoc()) {
                            echo "<tr class='hover:bg-gray-100'>";
                            echo "<td class='border px-4 py-2'>" . $row["order_id"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["product_id"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["product_name"] . "</td>";
                            echo "<td class='border px-4 py-2'>" . $row["quantity"] . "</td>";
                            echo "<td class='border px-4 py-2 font-bold text-blue-600'>" . number_format($row["unit_price"], 2) . " ฿</td>";
                            echo "<td class='border px-4 py-2 font-bold text-green-600'>" . number_format($row["total_price"], 2) . " ฿</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center border px-4 py-2'>No order items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- ปุ่มกลับไป Dashboard -->
        <div class="mt-6 text-center">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Dashboard</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
                <a href="manager_dashboard.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Dashboard</a>
            <?php else: ?>
                <a href="products.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Products</a>
            <?php endif; ?>
        </div>

    </div>

</body>

</html>

<?php
$conn->close();
?>
