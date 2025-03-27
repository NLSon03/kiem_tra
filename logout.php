<?php
session_start();

// Xóa tất cả dữ liệu session
$_SESSION = array();

// Hủy session
session_destroy();

// Thông báo đăng xuất thành công
session_start();
$_SESSION['flash_message'] = [
    'type' => 'success',
    'message' => 'Đăng xuất thành công!'
];

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit;
?>