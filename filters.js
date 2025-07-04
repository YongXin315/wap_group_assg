// Get references to the filter elements
const startDateInput = document.getElementById('startDate');
const roomSelect = document.getElementById('room');
const statusSelect = document.getElementById('status');
const searchInput = document.getElementById('search');

// Function to handle filter changes (for demonstration, logs to console)
function handleFilterChange() {
    const filters = {
        startDate: startDateInput.value,
        room: roomSelect.value,
        status: statusSelect.value,
        searchQuery: searchInput.value.trim()
    };

    console.log('Current filters:', filters);

    /*
    fetch('/api/bookings/filter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(filters),
    })
    .then(response => response.json())
    .then(data => {
        console.log('Filtered data received:', data);
        // Update your UI with the filtered data
    })
    .catch((error) => {
        console.error('Error fetching filtered data:', error);
    });
    */
}

// Add event listeners to trigger filtering on change/input
startDateInput.addEventListener('change', handleFilterChange);
roomSelect.addEventListener('change', handleFilterChange);
statusSelect.addEventListener('change', handleFilterChange);
searchInput.addEventListener('input', handleFilterChange); 

