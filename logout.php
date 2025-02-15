<?php
session_start();
session_destroy();
header("Location: products.php"); // กลับไปหน้า products
exit();
?>
