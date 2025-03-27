<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Hệ thống Quản lý Nhân sự'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo isset($isHomePage) ? 'assets/css/style.css' : '../assets/css/style.css'; ?>">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?php echo isset($isHomePage) ? 'index.php' : '../index.php'; ?>">
                    <i class="bi bi-people-fill me-2"></i>Quản lý Nhân sự
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo isset($isHomePage) ? 'pages/employee_list.php' : 'employee_list.php'; ?>">
                                <i class="bi bi-list-ul me-1"></i>Danh sách nhân viên
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo isset($isHomePage) ? 'pages/employee_form.php?action=add' : 'employee_form.php?action=add'; ?>">
                                    <i class="bi bi-person-plus me-1"></i>Thêm nhân viên
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="d-flex">
                        <?php if (isset($_SESSION['user'])): ?>
                            <span class="navbar-text me-3">
                                <i class="bi bi-person-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['user']['fullname']); ?>
                                (<?php echo $_SESSION['user']['role'] === 'admin' ? 'Admin' : 'User'; ?>)
                            </span>
                            <a href="<?php echo isset($isHomePage) ? 'logout.php' : '../logout.php'; ?>" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i>Đăng xuất
                            </a>
                        <?php else: ?>
                            <a href="<?php echo isset($isHomePage) ? 'login.php' : '../login.php'; ?>" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="py-4">
        <!-- Nội dung trang sẽ được đặt ở đây -->