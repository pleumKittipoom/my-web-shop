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
            <select name="category" id="categoryDropdown" onchange="this.form.submit()" class="border rounded-2xl p-1.5 rounded text-sm w-20 sm:w-25 md:w-40">
                <option value="" <?= (!isset($_GET['category']) || $_GET['category'] == "") ? "selected" : "" ?>>ทั้งหมด
                </option>
                <option value="electronics" <?= (isset($_GET['category']) && $_GET['category'] == "electronics")
                                                ? "selected" : "" ?>>อิเล็กทรอนิกส์</option>
                <option value="Home air conditioner" <?= (isset($_GET['category']) &&
                                                            $_GET['category'] == "Home air conditioner") ? "selected" : "" ?>>แอร์บ้าน</option>
                <option value="Wall mounted air conditioner" <?= (isset($_GET['category']) &&
                                                                    $_GET['category'] == "Wall mounted air conditioner") ? "selected" : "" ?>>แอร์ผนัง</option>
                <option value="Ceiling air conditioner" <?= (isset($_GET['category']) &&
                                                            $_GET['category'] == "Ceiling air conditioner") ? "selected" : "" ?>>แอร์เพดาน</option>
                <option value="Portable Air Conditioner" <?= (isset($_GET['category']) &&
                                                                $_GET['category'] == "Portable Air Conditioner") ? "selected" : "" ?>>แอร์เคลื่อนที่</option>
                <option value="Window air conditioner" <?= (isset($_GET['category']) &&
                                                            $_GET['category'] == "Window air conditioner") ? "selected" : "" ?>>แอร์หน้าต่าง</option>
                <option value="Air purifier" <?= (isset($_GET['category']) &&
                                                    $_GET['category'] == "Air purifier") ? "selected" : "" ?>>เครื่องฟอกอากาศ</option>
            </select>
        </form>

        <form action="search.php" method="get" class="flex items-center space-x-2 mt-4">
            <input type="hidden" name="category" id="searchCategory"
                value="<?= isset($_GET['category']) ? $_GET['category'] : '' ?>">
            <input type="text" name="keyword" placeholder="ค้นหาสินค้า" class="border rounded-2xl p-1.5 rounded w-full"
                value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>" />
            <link rel="stylesheet"
                href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <button type="submit" class="bg-white-500 p-2 rounded-full">
                <i class="fas fa-search text-grey"></i>
            </button>
        </form>

        <button id="menu-toggle" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15" />
            </svg>
        </button>

        <div id="menu" class="hidden absolute top-16 right-4 bg-white border rounded-2xl border-gray-200 shadow-lg md:relative md:top-0 md:right-0 md:bg-transparent md:border-0 md:shadow-none md:flex md:items-center md:space-x-8">
            <ul class="font-medium flex flex-col p-4 md:p-0 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0">
                <li>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Admin</a>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'manager'): ?>
                        <a href="manager_dashboard.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Manager</a>
                    <?php else: ?>
                        <a href="products.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Products</a>
                    <?php endif; ?>
                </li>
                <li>
                    <a href="profile.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Profile</a>
                </li>
                <li>
                    <a href="cart.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Orders
                        <?php if ($cart_count > 0): ?>
                            <span class="bg-red-500 text-white rounded-full text-xs px-2 py-1 ml-1"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="logout.php" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Logout</a>
                    <?php else: ?>
                        <a href="index.html" class="block py-2 px-3 text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Login</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.getElementById("menu").classList.toggle("hidden");
    });
</script>


        <!-- <div>  
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
        </div> -->
   