<style>

    .filter-card {
        background-color: #ffffff;
        border-radius: 1rem; 
        padding: 2.5rem;
        width: 100%;
        max-width: 800px; 
        margin: 0 auto; 
    }
   
    .filter-input, .filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #b79f9f; 
        border-radius: 0.5rem; 
        font-size: 1rem;
        color: #374151; 
        background-color: #ffffff; 
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: #a07a7a; 
        box-shadow: 0 0 0 2px rgba(183, 159, 159, 0.3); 
    }
    
    .filter-input::placeholder, .filter-select option[value=""][disabled] {
        color: #a07a7a; 
    }
    .filter-select option {
        color: #374151; 
    }
    .filter-label {
        font-weight: 600; 
        color: #1f2937; 
        margin-bottom: 0.5rem;
        display: block; 
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
