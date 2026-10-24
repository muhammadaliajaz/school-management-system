</div> <!-- Close container -->

    <footer class="bg-light text-center py-4 mt-auto border-top">
        <div class="container">
            <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> School Management System. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 3 seconds
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    </script>
</body>
</html>
