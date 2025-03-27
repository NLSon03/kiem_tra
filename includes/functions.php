<?php
/**
 * Các hàm tiện ích cho hệ thống
 */

/**
 * Hiển thị thông báo flash message
 * 
 * @return string HTML của thông báo
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_message']['type'];
        $message = $_SESSION['flash_message']['message'];
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['flash_message']);
        
        return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    
    return '';
}

/**
 * Định dạng số tiền
 * 
 * @param int $amount Số tiền cần định dạng
 * @return string Chuỗi đã định dạng
 */
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

/**
 * Kiểm tra quyền admin
 * 
 * @return bool True nếu người dùng có quyền admin
 */
function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Lấy danh sách phòng ban
 * 
 * @return array Danh sách phòng ban
 */
function getDepartments() {
    try {
        $conn = getConnection();
        $stmt = $conn->query("SELECT * FROM PHONGBAN ORDER BY Ten_Phong");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Lấy thông tin nhân viên theo mã
 * 
 * @param string $id Mã nhân viên
 * @return array|null Thông tin nhân viên hoặc null nếu không tìm thấy
 */
function getEmployeeById($id) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT * FROM NHANVIEN WHERE Ma_NV = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return null;
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Lấy tên phòng ban theo mã
 * 
 * @param string $id Mã phòng ban
 * @return string Tên phòng ban
 */
function getDepartmentName($id) {
    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT Ten_Phong FROM PHONGBAN WHERE Ma_Phong = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['Ten_Phong'];
        }
        
        return 'Không xác định';
    } catch (PDOException $e) {
        return 'Không xác định';
    }
}
?>
