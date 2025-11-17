<?php
$page_title = 'Hospital Dashboard';
$user_type_required = 'hospital';
require_once __DIR__ . '/../includes/header.php'; 
?>

<div class="container">
    <div class="welcome-banner">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
        <p>Manage your blood requests and donation drives from this dynamic control panel.</p>
    </div>

    <div id="ajax-message-container"></div>

    <div class="grid-container" style="grid-template-columns: 1fr 1.5fr; align-items: start;">
        <div class="form-column">
            <div class="card">
                <h2>Post a New Blood Request</h2>
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

        <div class="data-column">
            <div class="card">
                <h2>My Active Blood Requests</h2>
                <div id="requestsList">
                    <p>Loading requests...</p>
                </div>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>My Scheduled Blood Drives</h2>
                <div id="drivesList">
                    <p>Loading drives...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestForm = document.getElementById('createRequestForm');
    const requestsListDiv = document.getElementById('requestsList');
    const drivesListDiv = document.getElementById('drivesList');
    const messageContainer = document.getElementById('ajax-message-container');
    const driveForm = document.getElementById('createDriveForm');
    function refreshDashboardData() {
        fetch('../api/get_hospital_data_api.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    requestsListDiv.innerHTML = ''; 
                    if (data.requests.length > 0) {
                        data.requests.forEach(req => {
                            const item = document.createElement('div');
                            item.className = 'item';
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

                    drivesListDiv.innerHTML = ''; 
                    if (data.drives.length > 0) {
                        data.drives.forEach(drive => {
                            const item = document.createElement('div');
                            item.className = 'item';
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
    
    function showMessage(message, type) {
        messageContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => {
            messageContainer.innerHTML = '';
        }, 5000);
    }
    function handleCancelClick(event) {
        if (!event.target.classList.contains('btn-cancel')) {
            return;
        }

        const button = event.target;
        const type = button.dataset.type; 
        const id = button.dataset.id;
        const itemName = type === 'request' ? 'this blood request' : 'this blood drive';

        if (!confirm(`Are you sure you want to permanently cancel ${itemName}?`)) {
            return; 
        }

        const apiEndpoint = type === 'request' ? '../api/cancel_request_api.php' : '../api/cancel_drive_api.php';
        const formData = new FormData();
        formData.append(type === 'request' ? 'request_id' : 'drive_id', id);

        fetch(apiEndpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                refreshDashboardData(); 
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred while trying to cancel.', 'error');
        });
    }

    document.querySelector('.data-column').addEventListener('click', handleCancelClick);

    requestForm.addEventListener('submit', function(event) {
        event.preventDefault(); 

        const formData = new FormData(requestForm);
        
        fetch('../api/create_request_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                requestForm.reset(); 
                refreshDashboardData(); 
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
        event.preventDefault(); 

        const formData = new FormData(driveForm);
        
        fetch('../api/create_drive_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                driveForm.reset(); 
                refreshDashboardData();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred. Please try again.', 'error');
        });
    });
    refreshDashboardData();
});
</script>

</body>
</html>