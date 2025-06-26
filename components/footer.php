</div> <!-- End of main-content -->

    <!-- Footer -->
    <footer class="footer" style="background: white; color: black; text-align: center; padding: 2rem;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Taylor's University. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>