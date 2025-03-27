<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy thông tin người dùng
$user = $_SESSION['user'];

// Chuyển hướng đến trang danh sách nhân viên
header('Location: pages/employee_list.php');
exit;
?>
