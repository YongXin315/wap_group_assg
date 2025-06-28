<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'components/header.php';
?>

<link rel="stylesheet" href="./assests/main.css">

<!-- My Bookings Page Content -->
<div class="bookings-container">
    <!-- Page Header -->
    <div class="bookings-header">
        <h1 class="bookings-title">My Bookings</h1>
        <p class="bookings-subtitle">Manage and track your room reservations</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-stats">
            <span class="stat-item">Total</span>
            <span class="stat-item">Approved</span>
            <span class="stat-item">Pending</span>
            <span class="stat-item">Cancelled</span>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bookings-table">
        <!-- Table Header -->
        <div class="table-header">
            <div class="table-header-cell">Room Name</div>
            <div class="table-header-cell">Date</div>
            <div class="table-header-cell">Time</div>
            <div class="table-header-cell">Purpose</div>
            <div class="table-header-cell">Status</div>
            <div class="table-header-cell">Actions</div>
        </div>

        <!-- Table Rows -->
        <div class="table-row">
            <div class="table-cell room-name" data-label="Room Name">Discussion Room 3.1</div>
            <div class="table-cell" data-label="Date">July 5, 2025</div>
            <div class="table-cell" data-label="Time">3:00 PM - 4:00 PM</div>
            <div class="table-cell" data-label="Purpose">Project Meeting</div>
            <div class="table-cell" data-label="Status">
                <span class="status-badge status-approved">Approved</span>
            </div>
            <div class="table-cell" data-label="Actions">
                <button class="cancel-button">Cancel Booking</button>
          </div>
        </div>

        <div class="table-row">
            <div class="table-cell room-name" data-label="Room Name">Classroom D8.01</div>
            <div class="table-cell" data-label="Date">June 30, 2025</div>
            <div class="table-cell" data-label="Time">10:00 AM - 12:00 PM</div>
            <div class="table-cell" data-label="Purpose">Presentation Practice</div>
            <div class="table-cell" data-label="Status">
                <span class="status-badge status-pending">Pending Approval</span>
                  </div>
            <div class="table-cell" data-label="Actions">
                <button class="cancel-button">Cancel Booking</button>
                  </div>
                </div>

        <div class="table-row">
            <div class="table-cell room-name" data-label="Room Name">Discussion Room 4.2</div>
            <div class="table-cell" data-label="Date">June 27, 2025</div>
            <div class="table-cell" data-label="Time">2:00 PM - 3:00 PM</div>
            <div class="table-cell" data-label="Purpose">Group Study</div>
            <div class="table-cell" data-label="Status">
                <span class="status-badge status-approved">Approved</span>
              </div>
            <div class="table-cell" data-label="Actions">
                <button class="cancel-button">Cancel Booking</button>
                      </div>
                    </div>

        <div class="table-row">
            <div class="table-cell room-name" data-label="Room Name">Lecture Theatre 13</div>
            <div class="table-cell" data-label="Date">June 20, 2025</div>
            <div class="table-cell" data-label="Time">9:00 AM - 1:00 PM</div>
            <div class="table-cell" data-label="Purpose">Workshop</div>
            <div class="table-cell" data-label="Status">
                <span class="status-badge status-cancelled">Cancelled by Admin</span>
                  </div>
            <div class="table-cell" data-label="Actions">
                <!-- No action for cancelled bookings -->
                  </div>
                </div>

        <div class="table-row">
            <div class="table-cell room-name" data-label="Room Name">Discussion Room 5.1</div>
            <div class="table-cell" data-label="Date">June 12, 2025</div>
            <div class="table-cell" data-label="Time">1:00 PM - 2:00 PM</div>
            <div class="table-cell" data-label="Purpose">Discussion</div>
            <div class="table-cell" data-label="Status">
                <span class="status-badge status-approved">Approved</span>
            </div>
            <div class="table-cell" data-label="Actions">
                <button class="cancel-button">Cancel Booking</button>
        </div>
      </div>
    </div>

    <!-- No Bookings Message (hidden when there are bookings) -->
    <div class="no-bookings" style="display: none;">
        <div class="no-bookings-icon">
            <i class="fas fa-calendar-times"></i>
        </div>
        <h3>No Bookings Found</h3>
        <p>You haven't made any room bookings yet. Start by exploring available rooms!</p>
        <a href="index.php" class="btn-primary">Browse Rooms</a>
  </div>
</div>

<?php
include 'components/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.stat-item');
    const tableRows = document.querySelectorAll('.table-row');
    
    // Add click event listeners to filter buttons
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterType = this.textContent.toLowerCase().split(':')[0].trim();
            
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter table rows based on status
            filterTableRows(filterType);
        });
    });
    
    function filterTableRows(filterType) {
        tableRows.forEach(row => {
            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                const status = statusCell.textContent.toLowerCase();
                
                // Show/hide rows based on filter
                if (filterType === 'total') {
                    row.classList.remove('hidden');
                } else if (filterType === 'approved' && status.includes('approved')) {
                    row.classList.remove('hidden');
                } else if (filterType === 'pending' && status.includes('pending')) {
                    row.classList.remove('hidden');
                } else if (filterType === 'cancelled' && status.includes('cancelled')) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            }
        });
        
        // Update the filter stats to show count of visible rows
        updateFilterStats();
    }
    
    function updateFilterStats() {
        const visibleRows = document.querySelectorAll('.table-row:not(.hidden)');
        const totalRows = document.querySelectorAll('.table-row');
        const approvedRows = document.querySelectorAll('.table-row:not(.hidden) .status-approved');
        const pendingRows = document.querySelectorAll('.table-row:not(.hidden) .status-pending');
        const cancelledRows = document.querySelectorAll('.table-row:not(.hidden) .status-cancelled');
        
        // Update the stat items with new counts
        const statItems = document.querySelectorAll('.stat-item');
        statItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes('total:')) {
                item.textContent = `Total: ${totalRows.length}`;
            } else if (text.includes('approved:')) {
                item.textContent = `Approved: ${approvedRows.length}`;
            } else if (text.includes('pending:')) {
                item.textContent = `Pending: ${pendingRows.length}`;
            } else if (text.includes('cancelled:')) {
                item.textContent = `Cancelled: ${cancelledRows.length}`;
            }
        });
    }
    
    // Initialize with "Total" filter active
    const totalButton = document.querySelector('.stat-item');
    if (totalButton) {
        totalButton.classList.add('active');
    }
});
</script> 