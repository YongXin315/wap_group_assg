<footer class="footer">
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

        function bookRoom(roomId) {
            window.location.href = `booking.php?room_id=${roomId}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dateTimeInput = document.getElementById('date-time');
            if (dateTimeInput) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                dateTimeInput.min = minDateTime;
            }
        });

        document.querySelector('.search-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const roomType = document.getElementById('room-type').value;
            const dateTime = document.getElementById('date-time').value;

            if (!roomType && !dateTime) {
                alert('Please select a room type or date/time to search.');
                return;
            }

            // Filter rooms based on search criteria
            filterRooms(roomType, dateTime);

            // Scroll to rooms section
            document.getElementById('rooms').scrollIntoView({
                behavior: 'smooth'
            });
        });

        function filterRooms(roomType, dateTime) {
            const roomCards = document.querySelectorAll('.room-card');

            roomCards.forEach(card => {
                const cardType = card.getAttribute('data-room-type');
                let shouldShow = true;

                if (roomType && cardType !== roomType) {
                    shouldShow = false;
                }

                // For dateTime filtering, we would need to check availability with the server
                // This is a simplified version that just filters by room type

                card.style.display = shouldShow ? 'block' : 'none';
            });
        }

        // Add some interactive effects
        document.querySelectorAll('.room-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>