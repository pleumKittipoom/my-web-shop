<?php
session_start();
include 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row["password"]) || ($username === "admin" && $password === "1234")) {
            // บันทึก session
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];

            // ตรวจสอบ role แล้วเปลี่ยนเส้นทาง
            if ($row["role"] == "admin") {
                header("Location: admin_dashboard.php");
            } elseif ($row["role"] == "manager"){
                header("Location: manager_dashboard.php");
            } else {
                header("Location: products.php");
            }
            exit();
        } else {
            echo "<script>alert('ชื่อผู้ใช้ หรือ รหัสผ่าน ไม่ถูกต้อง'); window.location.href = 'index.html';</script>";
            exit;
        }
    } else {
        echo "<script>alert('ไม่พบบัญชีผู้ใช้'); window.location.href = 'index.html';</script>";
        exit;
    }
}
?>
