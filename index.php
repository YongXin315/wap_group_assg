<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

require_once 'db.php';
require_once 'component/header.php';

// Get room types from MongoDB
$roomTypes = [];
try {
    $roomTypes = $db->rooms->distinct('type');
} catch (Exception $e) {
    echo "<script>console.error('Error fetching room types: " . addslashes($e->getMessage()) . "');</script>";
}

// Get rooms from MongoDB
$rooms = [];
try {
    $roomsCollection = $db->rooms;
    $cursor = $roomsCollection->find();
    foreach ($cursor as $room) {
        $rooms[] = $room;
    }
} catch (Exception $e) {
    echo "<script>console.error('Error fetching rooms: " . addslashes($e->getMessage()) . "');</script>";
}
?>

<style>
  /* ===== CONTAINER STYLES ===== */
  .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
      box-sizing: border-box;
  }

  
  /* ===== HERO SECTION STYLES ===== */
.hero {
    background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('images/Taylors.jpg');
    background-size: cover;
    background-position: center;
    height: 60vh;
    min-height: 350px;
    max-height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    margin-top: 70px;
    position: relative;
}
.hero-overlay {
      height: 100%;
      width: 100%;
    position: absolute;
    top: 0;
    left: 0;
      z-index: 0;
    background: rgba(0,0,0,0.4);
}
.hero-container {
    position: relative;
    z-index: 1;
}
.hero h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
.hero p {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 2rem;
}
.btn-primary {
    background: #c3272b;
    color: white;
      padding: 15px 30px;
    border: none;
    border-radius: 5px;
      font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
    display: inline-block;
}
.btn-primary:hover {
    background: #a02024;
}
  /* ===== SECTION STYLES ===== */
.section {
      padding: 1rem 2rem;
      margin-bottom: 1rem;
}
.section-title {
    text-align: center;
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
    color: #333;
}
  /* ===== FORM ELEMENTS ===== */
.form-row {
    display: flex;
      flex-direction: row !important;
      gap: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      min-width: 600px;
      width: 100%;
      max-width: 900px;
      margin-left: auto;
      margin-right: auto;
}
.form-group {
    display: flex;
    flex-direction: column;
      flex: 1 1 200px;
      min-width: 180px;
    gap: 8px;
}
.form-label {
      display: block;
    margin-bottom: 0.5rem;
  }
  .form-group .form-control {
      min-width: 240px;
      width: 260px;
      max-width: 100%;
}
.form-actions {
      align-self: flex-end;
      min-width: 160px;
}
  /* ===== ROOM CARDS STYLES ===== */
.rooms-grid {
    display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}
.room-card {
      background: #FFFFFF;
      border: 1px solid #E5E7EB;
      border-radius: 12px;
      padding: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      min-width: 280px;
      margin: 10px;
      overflow: hidden;
}
.room-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.room-card-body {
      padding: 1.5rem;
}
.room-card h3 {
      margin-top: 0;
    margin-bottom: 0.5rem;
      font-size: 1.2rem;
  }
  .room-link {
      text-decoration: none;
      color: #171212;
      cursor: pointer;
      transition: color 0.2s;
  }
  .room-link:hover {
      text-decoration: underline;
    color: #c3272b;
}
.room-card p {
      margin-bottom: 0.5rem;
    color: #666;
  }
  @media (max-width: 1200px) {
      .rooms-grid {
          padding: 0 2rem;
      }
  }
  @media (max-width: 768px) {
      .hero-container {
          padding: 3rem 1rem;
      }
      .hero h1 {
          font-size: 2.5rem;
      }
      .section {
          padding: 3rem 1rem;
      }
      .form-row {
          flex-direction: row !important;
      }
      .form-group, .form-actions {
          min-width: 120px;
      }
      .rooms-grid {
          grid-template-columns: 1fr;
          gap: 1rem;
      }
  }
  @media (max-width: 480px) {
      .hero h1 {
          font-size: 2rem;
      }
      .hero p {
          font-size: 1rem;
      }
      .section-title {
          font-size: 1.2rem;
      }
}
</style>

<script>
// Helper: Format time as HH:MM
function formatTime(timeStr) {
    if (!timeStr) return '';
    const [h, m] = timeStr.split(':');
    const date = new Date();
    date.setHours(h, m);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function checkAndShowAvailability(cards, date, time, typeFilter = null) {
    const roomsGrid = document.querySelector('.rooms-grid');
    let checked = 0;
    let availableCards = [];
    let filteredCards = cards;
    if (typeFilter) {
        filteredCards = cards.filter(card => card.getAttribute('data-room-type') === typeFilter);
    }
    if (filteredCards.length === 0) {
        roomsGrid.innerHTML = '<div>No rooms found for this type.</div>';
        return;
    }
    roomsGrid.innerHTML = '<div>Checking availability...</div>';
    filteredCards.forEach(card => {
        const roomId = card.getAttribute('data-room-id');
        const startTime = time;
        let [h, m] = time.split(':');
        let endH = (parseInt(h) + 1).toString().padStart(2, '0');
        const endTime = endH + ':' + m;
        fetch(`handlers/check_availability.php?room_id=${encodeURIComponent(roomId)}&date=${encodeURIComponent(date)}&start_time=${encodeURIComponent(startTime)}&end_time=${encodeURIComponent(endTime)}`)
            .then(r => r.json())
            .then(data => {
                checked++;
                if (data.available) {
                    card.querySelector('.room-availability').textContent = 'Available';
                    let ps = card.querySelectorAll('p');
                    ps.forEach(p => {
                        if (p.textContent.includes('Capacity')) p.remove();
                    });
                    availableCards.push(card);
                } else {
                    card.style.display = 'none';
                }
                if (checked === filteredCards.length) {
                    if (availableCards.length === 0) {
                        roomsGrid.innerHTML = '<div>No available rooms for the selected time.</div>';
                    } else {
                        roomsGrid.innerHTML = '';
                        availableCards.forEach(c => {
                            c.style.display = '';
                            roomsGrid.appendChild(c);
                        });
                    }
                }
            });
    });
}

// On DOM ready
window.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-row');
    const roomTypeSelect = document.getElementById('room-type');
    const dateTimeInput = document.getElementById('date-time');
    const roomsGrid = document.querySelector('.rooms-grid');
    // Add data-room-id to each card for easier access
    Array.from(roomsGrid.children).forEach(card => {
        // Find the room id from PHP variable in data attribute
        const id = card.getAttribute('data-room-id');
        if (!id) {
            // Try to extract from PHP echo if not present
            const match = card.outerHTML.match(/data-room-id=\"([^"]+)\"/);
            if (match) card.setAttribute('data-room-id', match[1]);
        }
    });
    const allRooms = Array.from(roomsGrid.children);

    const availableRoomsTitle = document.getElementById('available-rooms-title');
    // Initial load: show availability for now
    const now = new Date();
    const date = now.toISOString().split('T')[0];
    const time = now.toTimeString().slice(0,5);
    checkAndShowAvailability(allRooms, date, time);
    if (availableRoomsTitle) availableRoomsTitle.textContent = 'Available Rooms (current)';

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const selectedType = roomTypeSelect.value;
        const dateTime = dateTimeInput.value;
        if (!dateTime) {
            alert('Please select a date and time.');
            return;
        }
        const [date, time] = dateTime.split('T');
        checkAndShowAvailability(allRooms, date, time, selectedType);
        // Check if selected date/time is now
        const selectedDate = new Date(dateTime);
        const now = new Date();
        // Compare only date and hour/minute
        if (
            selectedDate.getFullYear() === now.getFullYear() &&
            selectedDate.getMonth() === now.getMonth() &&
            selectedDate.getDate() === now.getDate() &&
            selectedDate.getHours() === now.getHours() &&
            selectedDate.getMinutes() === now.getMinutes()
        ) {
            if (availableRoomsTitle) availableRoomsTitle.textContent = 'Available Rooms (current)';
        } else {
            if (availableRoomsTitle) availableRoomsTitle.textContent = 'Available Rooms';
        }
    });
});
</script>

<!-- Hero Section with Background Image -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-container">
        <h1>Book Study & Meeting Rooms at Taylor's</h1>
        <p>Fast and easy room reservations for all students</p>
        <a href="#find-rooms" class="btn-primary">Get Started</a>
    </div>
</section>

<!-- Find Available Rooms Section -->
<section id="find-rooms" class="section">
    <div class="container">
        <h2 class="section-title">Find Available Rooms</h2>
        <form class="form-row" method="GET" action="#rooms">
            <div class="form-group">
                <label for="room-type" class="form-label">Room</label>
                <select id="room-type" name="room_type" class="form-control" style="color: #915457; border-color:#E5D1D1">
                    <option value="">Select a Room</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date-time" class="form-label">When</label>
                <input type="datetime-local" id="date-time" name="datetime" class="form-control" style="color: #915457; border-color: #E5D1D1;">
                </div>
            <div class="form-actions" style="align-self: flex-end;">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i> Search Available Rooms
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Available Rooms Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title" id="available-rooms-title">Available Rooms (current)</h2>
        <div class="rooms-grid">
            <?php foreach ($rooms as $room):
                $icon = 'fas fa-door-open';
                if (isset($room['type'])) {
                    switch ($room['type']) {
                        case 'Computer Lab': $icon = 'fas fa-desktop'; break;
                        case 'Classroom': $icon = 'fas fa-chalkboard-teacher'; break;
                        case 'Discussion Room': $icon = 'fas fa-users'; break;
                        case 'Lecture Theatre': $icon = 'fas fa-theater-masks'; break;
                    }
                }
            ?>
            <div class="room-card" data-room-type="<?php echo isset($room['type']) ? htmlspecialchars($room['type']) : ''; ?>" data-room-id="<?php echo isset($room['_id']) ? htmlspecialchars($room['_id']) : ''; ?>">
                <div class="room-card-body">
                    <h3>
                        <a href="roomdetails.php?room_id=<?php echo urlencode($room['_id']); ?>" class="room-link">
                            <?php echo isset($room['room_name']) ? htmlspecialchars($room['room_name']) : ''; ?>
                        </a>
                    </h3>
                    <p>Block <?php echo isset($room['block']) ? htmlspecialchars($room['block']) : ''; ?>, 
                        Floor <?php echo isset($room['floor']) ? htmlspecialchars($room['floor']) : ''; ?></p>
                    <div class="room-availability">Available for booking</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
require_once 'component/footer.php';
?>