<?php
include 'components/header.php';
?>

<!-- Hero Section with Background Image -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-container">
        <h1>Book Study & Meeting Rooms at Taylor's</h1>
        <p>Fast and easy room reservations for all students</p>
        <a href="#find-rooms" class="btn-primary">Get Started</a>
    </div>
</section>
<!-- Remove the <br> tag that was here -->
<!-- Find Available Rooms Section -->
<section id="find-rooms" class="section">
    <div class="container">
        <h2 class="section-title">Find Available Rooms</h2>
        
        <div class="form-row">
            <div class="form-group">
                <label for="room-select" class="form-label">Room</label>
                <select id="room-select" class="form-control">
                    <option value="">Select a Room</option>
                    <option value="lab21">Computer Lab 21</option>
                    <option value="lab22">Computer Lab 22</option>
                    <option value="room31">Discussion Room 3.1</option>
                    <option value="room43">Discussion Room 4.3</option>
                    <option value="d410">Classroom D4.10</option>
                    <option value="theatre">Lecture Theatre</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date-time" class="form-label">When</label>
                <input type="text" id="date-time" placeholder="Select a Date and Time" class="form-control">
            </div>
        </div>
        
        <div class="form-actions">
            <button class="btn-primary">Search Available Rooms</button>
        </div>
    </div>
</section>

<!-- Available Rooms Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Available Rooms</h2>
        
        <div class="rooms-grid">
            <!-- Room Card 1 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Computer Lab 21</h3>
                    <p>Time Slot: 10:00 AM - 11:00 AM</p>
                    <p>Next available slot: 1 hour</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
            
            <!-- Room Card 2 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Computer Lab 22</h3>
                    <p>Time Slot: 8:00 AM - 10:00 AM</p>
                    <p>Next available slot: 2 hour</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
            
            <!-- Room Card 3 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Discussion Room 3.1</h3>
                    <p>Time Slot: 7:00 PM - 9:00 PM</p>
                    <p>Next available slot: 2 hour</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
            
            <!-- Room Card 4 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Discussion Room 4.3</h3>
                    <p>Time Slot: 2:00 PM - 3:00 PM</p>
                    <p>Next available slot: 1 hour</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
            
            <!-- Room Card 5 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Classroom D4.10</h3>
                    <p>Time Slot: 10:00 AM - 11:30 AM</p>
                    <p>Next available slot: 1 hour 30 mins</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
            
            <!-- Room Card 6 -->
            <div class="room-card">
                <div class="room-card-body">
                    <h3>Lecture Theatre</h3>
                    <p>Time Slot: 5:00 PM - 6:00 PM</p>
                    <p>Next available slot: 1 hour</p>
                    <button class="btn-primary">Book This Room</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'components/footer.php';
?>