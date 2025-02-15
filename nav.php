<?php
// ตรวจสอบการเพิ่มสินค้าในตะกร้า
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    // คำนวณจำนวนสินค้าทั้งหมดในตะกร้า
    $cart_count = array_sum($_SESSION['cart']);
}
?>

<nav class="bg-white shadow-lg fixed top-0 left-0 w-full z-50">
    <div class="container mx-auto p-1.5 flex justify-between items-center">
        <a href="products.php" class="text-xl hover:text-blue-800 font-bold">Store</a>

        <form action="products.php" method="get" class="flex items-center space-x-2 mt-4">
            <select name="category" id="categoryDropdown" onchange="this.form.submit()" class="border p-1.5 rounded text-sm w-20 sm:w-25 md:w-40">
                <option value="" <?=(!isset($_GET['category']) || $_GET['category']=="" ) ? "selected" : "" ?>>ทั้งหมด
                </option>
                <option value="electronics" <?=(isset($_GET['category']) && $_GET['category']=="electronics" )
                    ? "selected" : "" ?>>อิเล็กทรอนิกส์</option>
                <option value="Home air conditioner" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Home air conditioner" ) ? "selected" : "" ?>>แอร์บ้าน</option>
                <option value="Wall mounted air conditioner" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Wall mounted air conditioner" ) ? "selected" : "" ?>>แอร์ผนัง</option>
                <option value="Ceiling air conditioner" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Ceiling air conditioner" ) ? "selected" : "" ?>>แอร์เพดาน</option>
                <option value="Portable Air Conditioner" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Portable Air Conditioner" ) ? "selected" : "" ?>>แอร์เคลื่อนที่</option>
                <option value="Window air conditioner" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Window air conditioner" ) ? "selected" : "" ?>>แอร์หน้าต่าง</option>
                <option value="Air purifier" <?=(isset($_GET['category']) &&
                    $_GET['category']=="Air purifier" ) ? "selected" : "" ?>>เครื่องฟอกอากาศ</option>
            </select>
        </form>

        <form action="search.php" method="get" class="flex items-center space-x-2 mt-4">
            <input type="hidden" name="category" id="searchCategory"
                value="<?= isset($_GET['category']) ? $_GET['category'] : '' ?>">
            <input type="text" name="keyword" placeholder="ค้นหาสินค้า" class="border p-1.5 rounded w-full"
                value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>" />
            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <button type="submit" class="bg-white-500 p-2 rounded-full">
                <i class="fas fa-search text-grey"></i>
            </button>
        </form>

        <div>  
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin_dashboard.php" class="text-gray-700 hover:text-blue-500 mx-2">Admin</a>
            <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
            <a href="manager_dashboard.php" class="text-gray-700 hover:text-blue-500 mx-2">Manager</a>
            <?php else: ?>
            <a href="products.php" class="text-gray-700 hover:text-blue-500 mx-2">Products</a>
            <?php endif; ?>

            <a href="cart.php" class="text-gray-700 hover:text-blue-500 mx-2">
                Orders 
                <?php if ($cart_count > 0): ?>
                    <span class="bg-red-500 text-white rounded-full text-xs px-2 py-1"><?= $cart_count ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="text-gray-700 hover:text-blue-500 mx-2">Profile</a>

            <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php" class="text-gray-700 hover:text-blue-500 mx-2">Logout</a>
            <?php else: ?>
            <a href="index.html" class="text-gray-700 hover:text-blue-500 mx-2">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
