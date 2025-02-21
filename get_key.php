<?php
// ตั้งค่า Secret Key ที่ฝั่งเซิร์ฟเวอร์
$secretKey = "mySuperSecureKey123!@#"; 

// ส่งค่า Secret Key เป็น JSON (ใช้ HTTPS เท่านั้น)
header('Content-Type: application/json');
echo json_encode(["secretKey" => $secretKey]);
?>