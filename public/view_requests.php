<?php
$page_title = 'View Blood Requests';
$user_type_required = 'donor'; // Security: only donors can see this page
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodRequest.php';
require_once __DIR__ . '/../classes/DonorResponse.php';

$database = new Database();
$db = $database->connect();

// --- HANDLE DONOR RESPONSE FORM SUBMISSION ---
$form_message = '';
$form_message_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'respond_to_request') {
    $response = new DonorResponse($db);
    $response->donor_id = $_SESSION['user_id'];
    $response->request_id = $_POST['request_id'];

    if ($response->create()) {
        $form_message = "Your response has been sent to the hospital. Thank you for your generosity!";
        $form_message_type = 'success';
    } else {
        // hasAlreadyResponded() in the create() method prevents duplicates
        $form_message = "You have already responded to this request, or there was an error.";
        $form_message_type = 'error';
    }
}

// --- FETCH ALL OPEN REQUESTS ---
$request = new BloodRequest($db);
$open_requests = $request->readOpenRequests();
?>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="container">
    <div class="page-header">
        <h1>Find a Blood Request</h1>
        <p>Browse urgent requests from hospitals. Your help can save a life today.</p>
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search by hospital name or city...">
            <select id="bloodTypeFilter">
                <option value="">All Blood Types</option>
                <option value="A+">A+</option> <option value="A-">A-</option>
                <option value="B+">B+</option> <option value="B-">B-</option>
                <option value="AB+">AB+</option> <option value="AB-">AB-</option>
                <option value="O+">O+</option> <option value="O-">O-</option>
            </select>
        </div>
    </div>

    <!-- Display response message -->
    <?php if ($form_message): ?>
        <div class="alert alert-<?php echo $form_message_type; ?>">
            <?php echo htmlspecialchars($form_message); ?>
        </div>
    <?php endif; ?>

    <div class="requests-grid">
        <?php if ($open_requests->rowCount() > 0): ?>
            <?php while ($row = $open_requests->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="request-card" data-hospital="<?php echo htmlspecialchars($row['hospital_name']); ?>" data-city="<?php echo htmlspecialchars($row['hospital_address']); ?>" data-blood-type="<?php echo htmlspecialchars($row['blood_type_needed']); ?>">
                    <div class="blood-type-badge"><?php echo htmlspecialchars($row['blood_type_needed']); ?></div>
                    <div class="details">
                        <h3><?php echo htmlspecialchars($row['hospital_name']); ?></h3>
                        <div class="info-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['hospital_address']); ?></div>
                        <div class="info-item"><i class="fas fa-exclamation-triangle"></i> Urgency: <?php echo htmlspecialchars($row['urgency_level']); ?></div>
                        <div class="info-item"><i class="fas fa-clock"></i> Posted: <?php echo date('M j, Y', strtotime($row['date_posted'])); ?></div>
                        <a href="respond_to_request.php?id=<?php echo $row['request_id']; ?>" class="btn">
                            <i class="fas fa-info-circle"></i> View Details & Respond
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No open blood requests at the moment. The community is well-supplied!</p>
        <?php endif; ?>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h2>Confirm Your Offer</h2>
        <p>Are you sure you want to offer to donate <strong id="modal-blood-type"></strong> blood for the request from <strong id="modal-hospital-name"></strong>?</p>
        <p><small>The hospital will be notified and may contact you directly.</small></p>
        <div class="modal-buttons">
            <form action="view_requests.php" method="POST" style="display:inline;">
                <input type="hidden" name="action" value="respond_to_request">
                <input type="hidden" name="request_id" id="modal-request-id" value="">
                <button type="submit" class="btn"><i class="fas fa-check"></i> Yes, I Can Help</button>
            </form>
            <button class="btn btn-secondary" onclick="closeConfirmationModal()"><i class="fas fa-times"></i> Cancel</button>
        </div>
    </div>
</div>

<script>
    // --- JavaScript for Live Filtering ---
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const bloodTypeFilter = document.getElementById('bloodTypeFilter');
        const requestCards = document.querySelectorAll('.request-card');

        function filterRequests() {
            const searchText = searchInput.value.toLowerCase();
            const bloodType = bloodTypeFilter.value;

            requestCards.forEach(card => {
                const hospital = card.dataset.hospital.toLowerCase();
                const city = card.dataset.city.toLowerCase();
                const cardBloodType = card.dataset.bloodType;

                const textMatch = hospital.includes(searchText) || city.includes(searchText);
                const bloodTypeMatch = bloodType === "" || cardBloodType === bloodType;

                if (textMatch && bloodTypeMatch) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('keyup', filterRequests);
        bloodTypeFilter.addEventListener('change', filterRequests);
    });

    // --- JavaScript for Confirmation Modal ---
    const modal = document.getElementById('confirmationModal');
    
    function openConfirmationModal(requestId, hospitalName, bloodType) {
        document.getElementById('modal-request-id').value = requestId;
        document.getElementById('modal-hospital-name').innerText = hospitalName;
        document.getElementById('modal-blood-type').innerText = bloodType;
        modal.style.display = 'flex';
    }

    function closeConfirmationModal() {
        modal.style.display = 'none';
    }

    // Close modal if user clicks outside of the content
    window.onclick = function(event) {
        if (event.target == modal) {
            closeConfirmationModal();
        }
    }
</script>

</body>
</html>