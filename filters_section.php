<style>

    .filter-card {
        background-color: #ffffff;
        border-radius: 1rem; /* Rounded corners */
        padding: 2.5rem;
        width: 100%;
        max-width: 800px; /* Max width for larger screens */
        margin: 0 auto; /* Center the card if its parent allows */
    }
   
    .filter-input, .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #b79f9f; 
        border-radius: 0.5rem; /* Rounded corners */
        font-size: 1rem;
        color: #374151; /* Darker text color for actual input */
        background-color: #ffffff; /* White background */
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #a07a7a; /* Slightly darker reddish-brown on focus */
        box-shadow: 0 0 0 2px rgba(183, 159, 159, 0.3); /* Soft shadow on focus */
    }
    
    .filter-input::placeholder, .filter-select option[value=""][disabled] {
        color: #a07a7a; /* Reddish-brown placeholder color */
    }
    .filter-select option {
        color: #374151; /* Default option text color */
    }
    .filter-label {
        font-weight: 600; /* Semi-bold */
        color: #1f2937; /* Dark text */
        margin-bottom: 0.5rem;
        display: block; /* Ensure label is on its own line */
    }
</style>

<div class="filter-card">
    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-left">Filters</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <label for="startDate" class="filter-label">Date Range</label>
            <input type="date" id="startDate" class="filter-input" placeholder="Select Date Range">
        </div>
        <div>
            <label for="room" class="filter-label">Room</label>
            <select id="room" class="filter-select">
                <option value="" disabled selected>Select Room</option>
                <option value="discussion-room">Discussion Room</option>
                <option value="classroom">Classroom</option>
            </select>
        </div>

        <div>
            <label for="status" class="filter-label">Status</label>
            <select id="status" class="filter-select">
                <option value="" disabled selected>Select Status</option>
                <option value="approved">Approved</option>
                <option value="cancelled">Cancelled</option>
                <option value="in-progress">In Progress</option>
            </select>
        </div>

        <div>
            <label for="search" class="filter-label">Search</label>
            <input type="text" id="search" placeholder="Search by Name, ID" class="filter-input">
        </div>
    </div>
</div>