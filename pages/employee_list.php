<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Lấy thông tin người dùng
$user = $_SESSION['user'];
$isAdmin = isAdmin();

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Số nhân viên trên mỗi trang
$offset = ($page - 1) * $limit;

// Lấy tổng số nhân viên
try {
    $conn = getConnection();
    $stmt = $conn->query("SELECT COUNT(*) as total FROM NHANVIEN");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalEmployees = $row['total'];
    $totalPages = ceil($totalEmployees / $limit);
} catch (PDOException $e) {
    $totalEmployees = 0;
    $totalPages = 1;
}

// Lấy danh sách nhân viên với phân trang
try {
    $stmt = $conn->prepare("SELECT n.*, p.Ten_Phong 
                           FROM NHANVIEN n 
                           JOIN PHONGBAN p ON n.Ma_Phong = p.Ma_Phong 
                           ORDER BY n.Ma_NV 
                           LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $employees = [];
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Lỗi khi lấy danh sách nhân viên: ' . $e->getMessage()
    ];
}

// Tiêu đề trang
$pageTitle = 'Danh sách nhân viên';

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo $pageTitle; ?></h2>
        <?php if ($isAdmin): ?>
            <a href="employee_form.php?action=add" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm nhân viên
            </a>
        <?php endif; ?>
    </div>
    
    <?php echo displayFlashMessage(); ?>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Mã NV</th>
                            <th>Tên NV</th>
                            <th>Giới tính</th>
                            <th>Nơi sinh</th>
                            <th>Phòng ban</th>
                            <th>Lương</th>
                            <?php if ($isAdmin): ?>
                                <th>Thao tác</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employees)): ?>
                            <tr>
                                <td colspan="<?php echo $isAdmin ? 7 : 6; ?>" class="text-center">Không có nhân viên nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employees as $employee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($employee['Ma_NV']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['Ten_NV']); ?></td>
                                    <td class="text-center">
                                        <?php if ($employee['Phai'] == 'NAM'): ?>
                                            <img src="../assets/images/man.jpg" alt="Nam" width="24" height="24" title="Nam">
                                        <?php else: ?>
                                            <img src="../assets/images/woman.jpg" alt="Nữ" width="24" height="24" title="Nữ">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($employee['Noi_Sinh']); ?></td>
                                    <td><?php echo htmlspecialchars($employee['Ten_Phong']); ?></td>
                                    <td><?php echo formatCurrency($employee['Luong']); ?></td>
                                    <?php if ($isAdmin): ?>
                                        <td>
                                            <a href="employee_form.php?action=edit&id=<?php echo $employee['Ma_NV']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Sửa
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $employee['Ma_NV']; ?>', '<?php echo $employee['Ten_NV']; ?>')" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Xóa
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa nhân viên <span id="employeeName" class="fw-bold"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('employeeName').textContent = name;
    document.getElementById('deleteLink').href = '../crud.php?action=delete&id=' + id;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<?php
// Include footer
include_once '../includes/footer.php';
?>