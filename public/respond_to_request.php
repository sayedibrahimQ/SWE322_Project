<?php
$page_title = 'Respond to Request';
$user_type_required = 'donor';
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodRequest.php';
require_once __DIR__ . '/../classes/DonorResponse.php';

$database = new Database();
$db = $database->connect();
$request = new BloodRequest($db);

// Get request ID from URL parameter
$request->request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if this donor has already responded to this request
$response = new DonorResponse($db);
$response->donor_id = $_SESSION['user_id'];
$response->request_id = $request->request_id;
$has_responded = $response->hasAlreadyResponded();

// Fetch the single request details
if (!$request->readOne() || $request->status !== 'open') {
    // If request not found or is no longer open, show an error and stop
    echo "<div class='container'><div class='page-header'><h1>Request Not Available</h1><p>This blood request may have been fulfilled or cancelled. <a href='view_requests.php'>View other requests</a>.</p></div></div>";
    require_once __DIR__ . '/../includes/footer.php'; // Assuming you create a footer file
    exit();
}

// URL-encode the address for the map query
$map_query = urlencode($request->hospital_address);
?>

<div class="container">
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Request Details</h1>
            <p style="margin:0;">Review the information below and confirm if you can help.</p>
        </div>
        <a href="view_requests.php" class="btn" style="margin-top:0;"><i class="fas fa-arrow-left"></i> Back to All Requests</a>
    </div>

    <div class="response-card">
        <div class="response-card-header">
            <div>
                <h2><?php echo htmlspecialchars($request->hospital_name); ?></h2>
                <p style="margin:0; color:#555;">Needs Your Help!</p>
            </div>
            <div class="response-card-blood-badge"><?php echo htmlspecialchars($request->blood_type_needed); ?></div>
        </div>

        <div class="response-card-grid-body">
            <div class="details-panel">
                <h3><i class="fas fa-info-circle"></i> Request Information</h3>
                <div class="info-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Urgency Level</strong><br>
                        <span><?php echo htmlspecialchars($request->urgency_level); ?></span>
                    </div>
                </div>
                 <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <strong>Date Posted</strong><br>
                        <span><?php echo date('F j, Y', strtotime($request->date_posted)); ?></span>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <strong>Contact Phone</strong><br>
                        <span><?php echo htmlspecialchars($request->hospital_phone); ?></span>
                    </div>
                </div>
                 <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Location Address</strong><br>
                        <span><?php echo htmlspecialchars($request->hospital_address); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="response-card-footer">
            <div id="ajax-message-container" style="margin-bottom: 15px;"></div>
            <?php if ($has_responded): ?>
                <button id="confirmBtn" class="btn btn-confirm" disabled>
                    <i class="fas fa-check-circle"></i> You Have Already Responded
                </button>
            <?php else: ?>
                <button id="confirmBtn" class="btn btn-confirm" data-request-id="<?php echo $request->request_id; ?>">
                    <i class="fas fa-hand-holding-heart"></i> Confirm I Can Help
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmBtn');
    
    // If the button doesn't exist (because user already responded), stop the script.
    if (!confirmBtn) return;

    const messageContainer = document.getElementById('ajax-message-container');

    confirmBtn.addEventListener('click', function() {
        const requestId = this.dataset.requestId;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending Response...';

        const formData = new FormData();
        formData.append('request_id', requestId);

        fetch('../api/submit_response_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let messageType = data.success ? 'success' : 'error';
            showMessage(data.message, messageType);

            if (data.success) {
                this.innerHTML = '<i class="fas fa-check-circle"></i> Response Sent!';
            } else {
                this.innerHTML = '<i class="fas fa-times-circle"></i> Already Responded';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('A network error occurred. Please try again.', 'error');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-hand-holding-heart"></i> Confirm I Can Help';
        });
    });

    function showMessage(message, type) {
        messageContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        // Message will stay until the user navigates away
    }
});
</script>

</body>
</html>