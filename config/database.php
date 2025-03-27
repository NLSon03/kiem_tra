<?php
/**
 * Kết nối đến cơ sở dữ liệu
 * 
 * @return PDO Đối tượng PDO kết nối đến cơ sở dữ liệu
 */
function getConnection() {
    $host = 'localhost';
    $dbname = 'QL_NhanSu';
    $username = 'root';
    $password = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        // Thiết lập chế độ lỗi
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
}
?>