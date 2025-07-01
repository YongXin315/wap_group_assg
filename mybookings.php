<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include 'components/header.php';

require_once 'db.php'; // or your DB connection
$studentId = $_SESSION['student_id'];
$bookings = $db->bookings->find(['student_id' => $studentId]);
?>

<style>
/* ===== MY BOOKINGS PAGE STYLES ===== */
.bookings-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    background: white;
    min-height: calc(100vh - 140px);
}

.bookings-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem 0;
    border-bottom: 2px solid #f0f0f0;
}

.bookings-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #171212;
    margin-bottom: 1rem;
}

.bookings-subtitle {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 2rem;
}

.filter-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.filter-stats {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.stat-item {
    background: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    border: 1px solid #e0e0e0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: #C3272B;
    color: white;
    border-color: #C3272B;
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(195, 39, 43, 0.3);
}

.stat-item.active {
    background: #C3272B;
    color: white;
    border-color: #C3272B;
    box-shadow: 0 3px 6px rgba(195, 39, 43, 0.3);
}

.bookings-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #E5DBDB;
}

.table-header {
    background: #f8f9fa;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 120px;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #E5E8EB;
}

.table-header-cell {
    font-weight: 500;
    color: #171212;
    font-size: 14px;
    text-align: center;
}

.table-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr 1fr 120px;
    gap: 1rem;
    padding: 1rem;
    border-bottom: 1px solid #E5E8EB;
    align-items: center;
    transition: background-color 0.3s ease;
}

.table-row:hover {
    background-color: #f8f9fa;
}

.table-row:last-child {
    border-bottom: none;
}

.table-cell {
    font-size: 14px;
    color: #876363;
    text-align: center;
    word-wrap: break-word;
}

.table-cell.room-name {
    color: #171212;
    font-weight: 400;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    display: inline-block;
    min-width: 120px;
}

.status-approved {
    background: #86BB8D;
    color: white;
}

.status-pending {
    background: #F5F0F0;
    color: #171212;
}

.status-cancelled {
    background: #ffebee;
    color: #c62828;
}

.cancel-button {
    background: none;
    border: none;
    color: #876363;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: color 0.3s ease;
    text-decoration: underline;
}

.cancel-button:hover {
    color: #C3272B;
}

.no-bookings {
    text-align: center;
    padding: 4rem 2rem;
    color: #666;
    font-size: 1.1rem;
}

.no-bookings-icon {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.table-row.hidden {
    display: none;
}

/* RESPONSIVE ADJUSTMENTS */
@media (max-width: 768px) {
    .bookings-container {
        padding: 1rem;
    }
    .filter-section {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-stats {
        justify-content: center;
    }
    .table-header,
    .table-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    .table-header-cell,
    .table-cell {
        text-align: left;
        padding: 0.5rem;
    }
    .table-header {
        display: none;
    }
    .table-row {
        border: 1px solid #E5E8EB;
        border-radius: 8px;
        margin-bottom: 1rem;
        padding: 1rem;
    }
    .table-cell::before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #171212;
    }
}

@media (max-width: 480px) {
    .bookings-title {
        font-size: 2rem;
    }
    .filter-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

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
        <?php
        foreach ($bookings as $booking) {
            $status = isset($booking['status']) ? $booking['status'] : 'pending';
            $statusClass = 'status-badge ';
            if ($status === 'approved') $statusClass .= 'status-approved';
            elseif ($status === 'cancelled') $statusClass .= 'status-cancelled';
            else $statusClass .= 'status-pending';

            echo '<div class="table-row">';
            echo '<div class="table-cell room-name" data-label="Room Name">' . htmlspecialchars($booking['room_id']) . '</div>';
            echo '<div class="table-cell" data-label="Date">' . date('F j, Y', strtotime($booking['booking_date'])) . '</div>';
            echo '<div class="table-cell" data-label="Time">' . htmlspecialchars($booking['start_time']) . ' - ' . htmlspecialchars($booking['end_time']) . '</div>';
            echo '<div class="table-cell" data-label="Purpose">' . htmlspecialchars($booking['purpose']) . '</div>';
            echo '<div class="table-cell" data-label="Status"><span class="' . $statusClass . '">' . ucfirst($status) . '</span></div>';
            echo '<div class="table-cell" data-label="Actions">';
            
            // Show cancel button only for pending or approved bookings
            if ($status === 'pending' || $status === 'approved') {
                $bookingIdStr = (string)$booking['_id'];
                echo '<button class="cancel-button" onclick="cancelBooking(\'' . $bookingIdStr . '\')" data-booking-id="' . $bookingIdStr . '">Cancel</button>';
            } else {
                echo '<span style="color: #999; font-size: 12px;">-</span>';
            }
            
            echo '</div>';
            echo '</div>';
        }
        ?>

    </div>

    <!-- No Bookings Message -->
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
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filterType = this.textContent.toLowerCase().split(':')[0].trim();
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            filterTableRows(filterType);
        });
    });
    
    function filterTableRows(filterType) {
        tableRows.forEach(row => {
            const statusCell = row.querySelector('.status-badge');
            if (statusCell) {
                const status = statusCell.textContent.toLowerCase();
                if (filterType === 'total') row.classList.remove('hidden');
                else if (filterType === 'approved' && status.includes('approved')) row.classList.remove('hidden');
                else if (filterType === 'pending' && status.includes('pending')) row.classList.remove('hidden');
                else if (filterType === 'cancelled' && status.includes('cancelled')) row.classList.remove('hidden');
                else row.classList.add('hidden');
            }
        });
    }
    
    // Initialize with "Total" active
    const totalButton = document.querySelector('.stat-item');
    if (totalButton) totalButton.classList.add('active');
});

// Move the cancelBooking function inside the script tags
function cancelBooking(bookingId) {
    if (!confirm('Are you sure you want to cancel this booking?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('booking_id', bookingId);
    
    fetch('handlers/cancel_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Booking cancelled successfully!');
            location.reload(); // Refresh the page to show updated status
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while cancelling the booking.');
    });
}
</script>
</script>
