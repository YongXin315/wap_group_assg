<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}

// Function to handle login toggle between student and admin
function initializeLoginToggle() {
    echo <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.login-tabs .tab');
    const form = document.querySelector('form[action*="login_handler.php"]');
    const studentIdInput = document.querySelector('input[name="student_id"]');
    const passwordInput = document.querySelector('input[name="password"]');
    
    // Set initial state
    let currentMode = 'student';
    
    // Add click event listeners to tabs
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Update current mode
            currentMode = this.textContent.toLowerCase();
            
            // Update form action and input names based on mode
            if (currentMode === 'student') {
                form.action = 'handlers/login_handler.php';
                studentIdInput.name = 'student_id';
                studentIdInput.placeholder = 'Email Address or Student ID';
                passwordInput.name = 'password';
                
                // Update form styling for student mode
                form.classList.remove('admin-mode');
                form.classList.add('student-mode');
            } else if (currentMode === 'staff') {
                form.action = 'handlers/admin_login_handler.php';
                studentIdInput.name = 'admin_id';
                studentIdInput.placeholder = 'Admin ID or Email';
                passwordInput.name = 'admin_password';
                
                // Update form styling for admin mode
                form.classList.remove('student-mode');
                form.classList.add('admin-mode');
            }
            
            // Clear form inputs when switching modes
            studentIdInput.value = '';
            passwordInput.value = '';
            
            // Update visual feedback
            updateToggleVisuals(currentMode);
        });
    });
    
    function updateToggleVisuals(mode) {
        const container = document.querySelector('.login-form-container');
        const title = document.querySelector('.login-title');
        const subtitle = document.querySelector('.login-subtitle');
        const instruction = document.querySelector('.login-instruction');
        
        if (mode === 'student') {
            container.style.borderLeft = '4px solid #C3272B';
            title.innerHTML = 'Welcome to<br>Taylor\'s Room Booking System!';
            subtitle.textContent = 'You are connecting to Taylor\'s Education';
            instruction.textContent = 'Sign in with your Taylor\'s account.';
        } else if (mode === 'staff') {
            container.style.borderLeft = '4px solid #2E8B57';
            title.innerHTML = 'Admin Portal<br>Taylor\'s Room Booking System';
            subtitle.textContent = 'Administrative Access';
            instruction.textContent = 'Sign in with your admin credentials.';
        }
    }
    
    // Initialize with student mode
    updateToggleVisuals('student');
});
</script>
EOT;
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Function to redirect admin users to admin dashboard
function redirectAdmin() {
    if (isAdmin()) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

// Function to redirect non-admin users away from admin pages
function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Function to generate date options for the next 7 days
function getDateOptions() {
    $options = [];
    for ($i = 0; $i < 7; $i++) {
        $date = date('Y-m-d', strtotime("+$i days"));
        $displayDate = date('l, F j, Y', strtotime("+$i days")); // Format: Monday, January 1, 2023
        $options[$date] = $displayDate;
    }
    return $options;
}

// Function to generate time slot options from 8 AM to 10 PM
function getTimeOptions() {
    $options = [];
    for ($hour = 8; $hour <= 22; $hour++) {
        $time24h = sprintf("%02d:00", $hour);
        $time12h = date('g:i A', strtotime($time24h)); // Format: 8:00 AM
        $options[$time24h] = $time12h;
    }
    return $options;
}

function initializeFlatpickr() {
    echo <<<EOT
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
/* Hide the default date picker appearance immediately */
input#datepicker {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: white;
}

/* Ensure consistent styling before Flatpickr initializes */
input#datepicker::placeholder {
    color: #915457;
    opacity: 1;
}
</style>
<script>
// Initialize Flatpickr immediately when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Apply initial styling to prevent flash
    var datepicker = document.getElementById('datepicker');
    if (datepicker) {
        datepicker.style.color = "#915457";
        datepicker.style.borderColor = "#E5D1D1";
    }
    
    // Initialize Flatpickr with custom styling
    flatpickr("#datepicker", {
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(6), // Exactly 6 days from now (7 days total including today)
        disableMobile: "true",
        altInput: true,
        altFormat: "F j, Y", // More readable date format
        theme: "light",
        // Force the input to have the specified styling
        onReady: function(selectedDates, dateStr, instance) {
            // Apply styles to the input element
            instance.input.style.color = "#915457";
            instance.input.style.borderColor = "#E5D1D1";
            
            // Also apply to the alt input if it exists
            if (instance.altInput) {
                instance.altInput.style.color = "#915457";
                instance.altInput.style.borderColor = "#E5D1D1";
            }
        },
        // Ensure the calendar is positioned properly
        position: "auto",
        // Add static positioning to prevent layout issues
        static: true
    });
    
    // Apply custom styling to Flatpickr
    setTimeout(function() {
        // Custom styling for the calendar
        document.querySelectorAll(".flatpickr-calendar").forEach(function(calendar) {
            calendar.style.border = "1px solid #E5D1D1";
            calendar.style.borderRadius = "5px";
            calendar.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
            // Fix positioning issues
            calendar.style.position = "absolute";
            calendar.style.zIndex = "999";
            calendar.style.width = "auto";
            calendar.style.minWidth = "280px";
            // Ensure the calendar doesn't get cut off
            calendar.style.overflow = "visible";
        });
        
        // Style the month navigation and current month
        document.querySelectorAll(".flatpickr-month").forEach(function(month) {
            month.style.backgroundColor = "#c3272b";
            month.style.color = "white";
            month.style.borderRadius = "5px 5px 0 0";
            month.style.padding = "0"; // Changed from 10px 0 to 0
            month.style.height = "40px"; // Set a fixed height for the header
            // Ensure month header doesn't overflow
            month.style.overflow = "visible";
            month.style.display = "flex";
            month.style.alignItems = "center";
            month.style.justifyContent = "center"; // Center horizontally
            month.style.position = "relative"; // Ensure proper positioning context
        });
        
        // Fix the month dropdown positioning
        document.querySelectorAll(".flatpickr-current-month").forEach(function(currentMonth) {
            currentMonth.style.display = "flex";
            currentMonth.style.alignItems = "center";
            currentMonth.style.justifyContent = "center";
            currentMonth.style.width = "100%";
            currentMonth.style.padding = "0 10px";
            currentMonth.style.position = "absolute";
            currentMonth.style.left = "0";
            currentMonth.style.right = "0";
            currentMonth.style.textAlign = "center";
            currentMonth.style.height = "100%"; // Ensure it takes full height
            currentMonth.style.top = "0"; // Align to top of container
        });
        
        // Position the navigation arrows to maintain proper spacing and match header background
        document.querySelectorAll(".flatpickr-prev-month, .flatpickr-next-month").forEach(function(arrow) {
            arrow.style.position = "absolute"; // Absolute positioning
            arrow.style.zIndex = "1";
            arrow.style.backgroundColor = "#c3272b"; // Match header background
            arrow.style.height = "40px"; // Match header height
            arrow.style.top = "0"; // Position at the top
            arrow.style.display = "flex";
            arrow.style.alignItems = "center"; // Center vertically
            arrow.style.justifyContent = "center"; // Center horizontally
            arrow.style.padding = "0 10px";
            arrow.style.margin = "0"; // Remove any margin
            arrow.style.transition = "opacity 0.2s ease";
        });
        
        // Center the SVG icons inside the arrows
        document.querySelectorAll(".flatpickr-prev-month svg, .flatpickr-next-month svg").forEach(function(svg) {
            svg.style.display = "block"; // Ensure proper display
            svg.style.margin = "auto"; // Center in container
            svg.style.verticalAlign = "middle"; // Align vertically
            svg.style.fill = "white"; // Ensure icon color is white
        });
        
        // Specific positioning for prev/next arrows
        document.querySelectorAll(".flatpickr-prev-month").forEach(function(prev) {
            prev.style.left = "0";
            prev.style.borderRadius = "5px 0 0 0"; // Round top-left corner
        });
        
        document.querySelectorAll(".flatpickr-next-month").forEach(function(next) {
            next.style.right = "0";
            next.style.borderRadius = "0 5px 0 0"; // Round top-right corner
        });
        
        // Style the weekdays
        document.querySelectorAll(".flatpickr-weekday").forEach(function(weekday) {
            weekday.style.color = "#915457";
            weekday.style.fontWeight = "bold";
        });
        
        // Style the days - REMOVE BORDER
        document.querySelectorAll(".flatpickr-day").forEach(function(day) {
            day.style.border = "none"; // Remove the circle border
            day.style.color = "#915457";
        });
        
        // Style the selected day
        document.querySelectorAll(".flatpickr-day.selected").forEach(function(selected) {
            selected.style.backgroundColor = "#c3272b";
            selected.style.border = "none"; // Remove border from selected day
            selected.style.color = "white";
        });
        
        // Style today's date
        document.querySelectorAll(".flatpickr-day.today").forEach(function(today) {
            today.style.border = "none"; // Remove border from today's date
            today.style.backgroundColor = "rgba(195, 39, 43, 0.1)"; // Light background instead of border
        });
        
        // Add additional styling for input elements
        document.querySelectorAll("#datepicker, .flatpickr-input").forEach(function(input) {
            input.style.color = "#915457";
            input.style.borderColor = "#E5D1D1";
        });
        
        // Fix the calendar container to ensure proper layout
        document.querySelectorAll(".flatpickr-calendar .flatpickr-innerContainer").forEach(function(container) {
            container.style.overflow = "visible";
        });
        
        // Ensure the days container doesn't overflow
        document.querySelectorAll(".flatpickr-days").forEach(function(days) {
            days.style.width = "100%";
            days.style.minWidth = "280px";
        });
    }, 100);
});
</script>
EOT;
}
?>