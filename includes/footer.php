</main>
    
    <footer class="bg-light py-3 mt-5 border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Hệ thống Quản lý Nhân sự</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Phiên bản 1.0</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Toast message script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo toasts
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl);
            });
            
            // Hiển thị tất cả toasts
            toastList.forEach(function(toast) {
                toast.show();
            });
        });
    </script>
</body>
</html>