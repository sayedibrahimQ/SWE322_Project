<?php
// public/hospital_dashboard.php (AJAX Version)

$page_title = 'Hospital Dashboard';
$user_type_required = 'hospital';
require_once __DIR__ . '/../includes/header.php'; // Security check and header HTML

// --- REMOVED ALL PHP FORM PROCESSING AND DATA FETCHING ---
// This is now handled by JavaScript and the API files.
?>

<div class="container">
    <!-- The welcome banner is simplified and no longer needs button links -->
    <div class="welcome-banner">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
        <p>Manage your blood requests and donation drives from this dynamic control panel.</p>
    </div>

    <!-- This div will be used to show success/error messages from AJAX calls -->
    <div id="ajax-message-container"></div>

    <div class="grid-container" style="grid-template-columns: 1fr 1.5fr; align-items: start;">
        <!-- Left Column: FORMS for creating new entries -->
        <div class="form-column">
            <div class="card">
                <h2>Post a New Blood Request</h2>
                <!-- IMPORTANT: Form action is removed, ID is added for JS -->
                <form id="createRequestForm">
                    <div class="form-group">
                        <label for="blood_type">Blood Type Needed</label>
                        <select name="blood_type" id="blood_type" required>
                            <option value="">-- Select --</option>
                            <option value="A+">A+</option><option value="A-">A-</option>
                            <option value="B+">B+</option><option value="B-">B-</option>
                            <option value="AB+">AB+</option><option value="AB-">AB-</option>
                            <option value="O+">O+</option><option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="urgency">Urgency Level</label>
                        <select name="urgency" id="urgency" required>
                            <option value="Urgent">Urgent</option>
                            <option value="High">High</option>
                            <option value="Medium">Medium</option>
                        </select>
                    </div>
                    <button type="submit" class="btn">Post Request</button>
                </form>
            </div>
            
            <!-- We will leave the "Create Drive" form as is for simplicity, but it could be converted to AJAX too -->
             <<!-- New AJAX-enabled form for creating a blood drive -->
            <div class="card" style="margin-top: 20px;">
                <h2>Create a New Blood Drive</h2>
                <form id="createDriveForm">
                    <div class="form-group">
                        <label for="drive_name">Drive Name / Title</label>
                        <input type="text" name="drive_name" id="drive_name" required>
                    </div>
                    <div class="form-group">
                        <label for="location_address">Location Address</label>
                        <textarea name="location_address" id="location_address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Date and Time</label>
                        <input type="datetime-local" name="start_time" id="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn">Schedule Drive</button>
                </form>
            </div>

        </div>

        <!-- Right Column: LISTS of existing entries -->
        <div class="data-column">
            <div class="card">
                <h2>My Active Blood Requests</h2>
                <!-- This div will be dynamically populated by JavaScript -->
                <div id="requestsList">
                    <p>Loading requests...</p>
                </div>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>My Scheduled Blood Drives</h2>
                <!-- This div will be dynamically populated by JavaScript -->
                <div id="drivesList">
                    <p>Loading drives...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- All JavaScript logic goes here -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestForm = document.getElementById('createRequestForm');
    const requestsListDiv = document.getElementById('requestsList');
    const drivesListDiv = document.getElementById('drivesList');
    const messageContainer = document.getElementById('ajax-message-container');
    const driveForm = document.getElementById('createDriveForm');
    // --- FUNCTION TO REFRESH DATA FROM THE API ---
    // --- FUNCTION TO REFRESH DATA FROM THE API (UPDATED) ---
    function refreshDashboardData() {
        fetch('../api/get_hospital_data_api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Requests List
                    requestsListDiv.innerHTML = ''; // Clear current list
                    if (data.requests.length > 0) {
                        data.requests.forEach(req => {
                            const item = document.createElement('div');
                            item.className = 'item';
                            // ADDED A CANCEL BUTTON with data attributes
                            item.innerHTML = `
                                <div class="item-actions">
                                    <span class="status status-${req.status.toLowerCase()}">${req.status.charAt(0).toUpperCase() + req.status.slice(1)}</span>
                                    <button class="btn-cancel" data-type="request" data-id="${req.request_id}" title="Cancel Request">&times;</button>
                                </div>
                                <strong>${req.blood_type_needed}</strong> - (${req.urgency_level})
                                <br>
                                <small>Posted: ${new Date(req.date_posted).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
                            `;
                            requestsListDiv.appendChild(item);
                        });
                    } else {
                        requestsListDiv.innerHTML = '<p>You have not posted any blood requests yet.</p>';
                    }

                    // Update Drives List
                    drivesListDiv.innerHTML = ''; // Clear current list
                    if (data.drives.length > 0) {
                        data.drives.forEach(drive => {
                            const item = document.createElement('div');
                            item.className = 'item';
                            // ADDED A CANCEL BUTTON with data attributes
                            item.innerHTML = `
                                <div class="item-actions">
                                    <button class="btn-cancel" data-type="drive" data-id="${drive.drive_id}" title="Cancel Drive">&times;</button>
                                </div>
                                <strong>${drive.drive_name}</strong>
                                <br>
                                <small>On: ${new Date(drive.start_time).toLocaleString('en-US', { dateStyle: 'long', timeStyle: 'short' })}</small>
                                <br>
                                <small>At: ${drive.location_address}</small>
                            `;
                            drivesListDiv.appendChild(item);
                        });
                    } else {
                        drivesListDiv.innerHTML = '<p>You have not scheduled any blood drives yet.</p>';
                    }
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    }
    
    // --- FUNCTION TO SHOW MESSAGES ---
    function showMessage(message, type) {
        messageContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000); // Message disappears after 5 seconds
    }
    // --- FUNCTION TO HANDLE CANCEL CLICKS USING EVENT DELEGATION ---
    function handleCancelClick(event) {
        // Check if the clicked element is a cancel button
        if (!event.target.classList.contains('btn-cancel')) {
            return;
        }

        const button = event.target;
        const type = button.dataset.type; // 'request' or 'drive'
        const id = button.dataset.id;
        const itemName = type === 'request' ? 'this blood request' : 'this blood drive';

        // Show a confirmation dialog
        if (!confirm(`Are you sure you want to permanently cancel ${itemName}?`)) {
            return; // User clicked "Cancel"
        }

        // Determine the correct API endpoint and form data
        const apiEndpoint = type === 'request' ? '../api/cancel_request_api.php' : '../api/cancel_drive_api.php';
        const formData = new FormData();
        formData.append(type === 'request' ? 'request_id' : 'drive_id', id);

        // Send the AJAX request to delete the item
        fetch(apiEndpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                refreshDashboardData(); // Refresh the lists to show the item is gone
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred while trying to cancel.', 'error');
        });
    }

    // Add a single event listener to the container of the lists
    document.querySelector('.data-column').addEventListener('click', handleCancelClick);

    // --- EVENT LISTENER FOR FORM SUBMISSION ---
    requestForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Stop the default page reload

        const formData = new FormData(requestForm);
        
        fetch('../api/create_request_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                requestForm.reset(); // Clear the form fields
                refreshDashboardData(); // Instantly refresh the data lists
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred. Please try again.', 'error');
        });
    });
    driveForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Stop the default page reload

        const formData = new FormData(driveForm);
        
        fetch('../api/create_drive_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                driveForm.reset(); // Clear the form fields
                refreshDashboardData(); // Instantly refresh the data lists
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred. Please try again.', 'error');
        });
    });
    // --- INITIAL DATA LOAD ON PAGE START ---
    refreshDashboardData();
});
</script>

</body>
</html>