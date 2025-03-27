<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kiểm tra quyền admin
$isAdmin = isAdmin();
if (!$isAdmin) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'message' => 'Bạn không có quyền thực hiện thao tác này!'
    ];
    header('Location: employee_list.php');
    exit;
}

// Lấy hành động và ID từ request
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';

// Khởi tạo biến
$employee = [
    'Ma_NV' => '',
    'Ten_NV' => '',
    'Phai' => 'NAM',
    'Noi_Sinh' => '',
    'Ma_Phong' => '',
    'Luong' => 0
];

// Tiêu đề trang
$pageTitle = 'Thêm nhân viên mới';
$submitAction = '../crud.php?action=add';

// Nếu là sửa, lấy thông tin nhân viên
if ($action === 'edit' && !empty($id)) {
    $pageTitle = 'Cập nhật thông tin nhân viên';
    $submitAction = '../crud.php?action=edit';
    
    $employeeData = getEmployeeById($id);
    if ($employeeData) {
        $employee = $employeeData;
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'message' => 'Không tìm thấy nhân viên!'
        ];
        header('Location: employee_list.php');
        exit;
    }
}

// Lấy danh sách phòng ban
$departments = getDepartments();

// Lấy dữ liệu form và lỗi từ session (nếu có)
if (isset($_SESSION['form_data'])) {
    $employee = array_merge($employee, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);

// Include header
include_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo $pageTitle; ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo $submitAction; ?>" method="POST">
                        <div class="mb-3 row">
                            <label for="ma_nv" class="col-sm-3 col-form-label">Mã nhân viên:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ma_nv" name="ma_nv" value="<?php echo htmlspecialchars($employee['Ma_NV']); ?>" <?php echo ($action === 'edit') ? 'readonly' : ''; ?> required maxlength="3">
                                <small class="form-text text-muted">Tối đa 3 ký tự</small>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="ten_nv" class="col-sm-3 col-form-label">Tên nhân viên:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="ten_nv" name="ten_nv" value="<?php echo htmlspecialchars($employee['Ten_NV']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label">Giới tính:</label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="phai" id="phai_nam" value="NAM" <?php echo ($employee['Phai'] === 'NAM') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="phai_nam">Nam</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="phai" id="phai_nu" value="NU" <?php echo ($employee['Phai'] === 'NU') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="phai_nu">Nữ</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="noi_sinh" class="col-sm-3 col-form-label">Nơi sinh:</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="noi_sinh" name="noi_sinh" value="<?php echo htmlspecialchars($employee['Noi_Sinh']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="ma_phong" class="col-sm-3 col-form-label">Phòng ban:</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="ma_phong" name="ma_phong" required>
                                    <option value="">-- Chọn phòng ban --</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo $department['Ma_Phong']; ?>" <?php echo ($employee['Ma_Phong'] === $department['Ma_Phong']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($department['Ten_Phong']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="luong" class="col-sm-3 col-form-label">Lương:</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="luong" name="luong" value="<?php echo $employee['Luong']; ?>" min="0" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="employee_list.php" class="btn btn-secondary me-2">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>