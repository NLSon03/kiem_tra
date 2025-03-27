<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kiểm tra quyền admin cho các thao tác CRUD
$isAdmin = ($_SESSION['user']['role'] === 'admin');

// Lấy hành động từ request
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

// Chỉ admin mới được thực hiện các thao tác CRUD
if (!$isAdmin) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Bạn không có quyền thực hiện thao tác này!'
    ];
    header('Location: pages/employee_list.php');
    exit;
}

// Xử lý các hành động CRUD
switch ($action) {
    case 'add':
        // Xử lý thêm nhân viên
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ma_nv = trim($_POST['ma_nv'] ?? '');
            $ten_nv = trim($_POST['ten_nv'] ?? '');
            $phai = trim($_POST['phai'] ?? '');
            $noi_sinh = trim($_POST['noi_sinh'] ?? '');
            $ma_phong = trim($_POST['ma_phong'] ?? '');
            $luong = (int)($_POST['luong'] ?? 0);
            
            // Validation
            $errors = [];
            if (empty($ma_nv)) {
                $errors[] = 'Mã nhân viên không được để trống';
            } elseif (strlen($ma_nv) > 3) {
                $errors[] = 'Mã nhân viên không được quá 3 ký tự';
            }
            
            if (empty($ten_nv)) {
                $errors[] = 'Tên nhân viên không được để trống';
            }
            
            if (empty($phai)) {
                $errors[] = 'Giới tính không được để trống';
            }
            
            if (empty($ma_phong)) {
                $errors[] = 'Mã phòng không được để trống';
            }
            
            if ($luong <= 0) {
                $errors[] = 'Lương phải lớn hơn 0';
            }
            
            // Kiểm tra mã nhân viên đã tồn tại chưa
            try {
                $conn = getConnection();
                $stmt = $conn->prepare("SELECT Ma_NV FROM NHANVIEN WHERE Ma_NV = :ma_nv");
                $stmt->bindParam(':ma_nv', $ma_nv);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $errors[] = 'Mã nhân viên đã tồn tại';
                }
            } catch (PDOException $e) {
                $errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
            }
            
            // Nếu không có lỗi, thêm nhân viên mới
            if (empty($errors)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO NHANVIEN (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) 
                                           VALUES (:ma_nv, :ten_nv, :phai, :noi_sinh, :ma_phong, :luong)");
                    $stmt->bindParam(':ma_nv', $ma_nv);
                    $stmt->bindParam(':ten_nv', $ten_nv);
                    $stmt->bindParam(':phai', $phai);
                    $stmt->bindParam(':noi_sinh', $noi_sinh);
                    $stmt->bindParam(':ma_phong', $ma_phong);
                    $stmt->bindParam(':luong', $luong);
                    $stmt->execute();
                    
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Thêm nhân viên thành công!'
                    ];
                    
                    header('Location: pages/employee_list.php');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
                }
            }
            
            // Nếu có lỗi, lưu vào session để hiển thị
            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                header('Location: pages/employee_form.php?action=add');
                exit;
            }
        }
        break;
        
    case 'edit':
        // Xử lý sửa nhân viên
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ma_nv = trim($_POST['ma_nv'] ?? '');
            $ten_nv = trim($_POST['ten_nv'] ?? '');
            $phai = trim($_POST['phai'] ?? '');
            $noi_sinh = trim($_POST['noi_sinh'] ?? '');
            $ma_phong = trim($_POST['ma_phong'] ?? '');
            $luong = (int)($_POST['luong'] ?? 0);
            
            // Validation
            $errors = [];
            if (empty($ma_nv)) {
                $errors[] = 'Mã nhân viên không được để trống';
            }
            
            if (empty($ten_nv)) {
                $errors[] = 'Tên nhân viên không được để trống';
            }
            
            if (empty($phai)) {
                $errors[] = 'Giới tính không được để trống';
            }
            
            if (empty($ma_phong)) {
                $errors[] = 'Mã phòng không được để trống';
            }
            
            if ($luong <= 0) {
                $errors[] = 'Lương phải lớn hơn 0';
            }
            
            // Nếu không có lỗi, cập nhật nhân viên
            if (empty($errors)) {
                try {
                    $conn = getConnection();
                    $stmt = $conn->prepare("UPDATE NHANVIEN 
                                           SET Ten_NV = :ten_nv, Phai = :phai, Noi_Sinh = :noi_sinh, 
                                               Ma_Phong = :ma_phong, Luong = :luong 
                                           WHERE Ma_NV = :ma_nv");
                    $stmt->bindParam(':ma_nv', $ma_nv);
                    $stmt->bindParam(':ten_nv', $ten_nv);
                    $stmt->bindParam(':phai', $phai);
                    $stmt->bindParam(':noi_sinh', $noi_sinh);
                    $stmt->bindParam(':ma_phong', $ma_phong);
                    $stmt->bindParam(':luong', $luong);
                    $stmt->execute();
                    
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Cập nhật nhân viên thành công!'
                    ];
                    
                    header('Location: pages/employee_list.php');
                    exit;
                } catch (PDOException $e) {
                    $errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
                }
            }
            
            // Nếu có lỗi, lưu vào session để hiển thị
            if (!empty($errors)) {
                $_SESSION['form_errors'] = $errors;
                $_SESSION['form_data'] = $_POST;
                header('Location: pages/employee_form.php?action=edit&id=' . $ma_nv);
                exit;
            }
        }
        break;
        
    case 'delete':
        // Xử lý xóa nhân viên
        if (!empty($id)) {
            try {
                $conn = getConnection();
                $stmt = $conn->prepare("DELETE FROM NHANVIEN WHERE Ma_NV = :ma_nv");
                $stmt->bindParam(':ma_nv', $id);
                $stmt->execute();
                
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Xóa nhân viên thành công!'
                ];
            } catch (PDOException $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'danger',
                    'message' => 'Lỗi khi xóa nhân viên: ' . $e->getMessage()
                ];
            }
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'danger',
                'message' => 'Không tìm thấy mã nhân viên!'
            ];
        }
        
        header('Location: pages/employee_list.php');
        exit;
        break;
        
    default:
        // Chuyển hướng về trang danh sách nếu không có hành động hợp lệ
        header('Location: pages/employee_list.php');
        exit;
}
?>