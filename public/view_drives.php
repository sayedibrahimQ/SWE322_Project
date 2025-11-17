<?php
$page_title = 'Find a Blood Drive';
$user_type_required = 'donor'; 
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodDrive.php';
require_once __DIR__ . '/../classes/Registration.php';

$database = new Database();
$db = $database->connect();

$form_message = '';
$form_message_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_for_drive') {
    $registration = new Registration($db);
    $registration->donor_id = $_SESSION['user_id'];
    $registration->drive_id = $_POST['drive_id'];

    if ($registration->create()) {
        $form_message = "Successfully registered for the drive! We look forward to seeing you there.";
        $form_message_type = 'success';
    } else {
        $form_message = "You have already registered for this drive, or there was an error.";
        $form_message_type = 'error';
    }
}

$drive = new BloodDrive($db);
$upcoming_drives = $drive->readUpcoming();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="container">
    <div class="page-header">
        <h1>Upcoming Blood Drives</h1>
        <p>Find a local event and schedule your next donation. Every pint counts!</p>
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search by drive name, hospital, or location...">
        </div>
    </div>

    <?php if ($form_message): ?>
        <div class="alert alert-<?php echo $form_message_type; ?>">
            <?php echo htmlspecialchars($form_message); ?>
        </div>
    <?php endif; ?>

    <div class="requests-grid">
        <?php if ($upcoming_drives->rowCount() > 0): ?>
            <?php while ($row = $upcoming_drives->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="drive-card" data-search-terms="<?php echo htmlspecialchars(strtolower($row['drive_name'] . ' ' . $row['hospital_name'] . ' ' . $row['location_address'])); ?>">
                    <div class="drive-card-header">
                        <h3><?php echo htmlspecialchars($row['drive_name']); ?></h3>
                    </div>
                    <div class="drive-card-body">
                        <div class="info-item"><i class="fas fa-hospital"></i> <strong>Organized by:</strong> <?php echo htmlspecialchars($row['hospital_name']); ?></div>
                        <div class="info-item"><i class="fas fa-calendar-alt"></i> <strong>Date & Time:</strong> <?php echo date('l, F j, Y \a\t g:i A', strtotime($row['start_time'])); ?></div>
                        <div class="info-item"><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($row['location_address']); ?></div>
                        <?php if (!empty($row['description'])): ?>
                            <div class="info-item"><i class="fas fa-info-circle"></i> <strong>Details:</strong> <?php echo htmlspecialchars($row['description']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="drive-card-footer">
                        <button class="btn" onclick="openConfirmationModal('<?php echo $row['drive_id']; ?>', '<?php echo htmlspecialchars(addslashes($row['drive_name'])); ?>')">
                            <i class="fas fa-check-circle"></i> Register Now
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>There are no upcoming blood drives scheduled at the moment. Please check back soon!</p>
        <?php endif; ?>
    </div>
</div>

<div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h2>Confirm Registration</h2>
        <p>Are you sure you want to register for the blood drive: <br><strong id="modal-drive-name"></strong>?</p>
        <div class="modal-buttons">
            <form action="view_drives.php" method="POST" style="display:inline;">
                <input type="hidden" name="action" value="register_for_drive">
                <input type="hidden" name="drive_id" id="modal-drive-id" value="">
                <button type="submit" class="btn"><i class="fas fa-check"></i> Yes, Register Me</button>
            </form>
            <button class="btn btn-secondary" onclick="closeConfirmationModal()"><i class="fas fa-times"></i> Cancel</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const driveCards = document.querySelectorAll('.drive-card');

        searchInput.addEventListener('keyup', function() {
            const searchText = searchInput.value.toLowerCase();

            driveCards.forEach(card => {
                const searchTerms = card.dataset.searchTerms;
                if (searchTerms.includes(searchText)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    const modal = document.getElementById('confirmationModal');
    
    function openConfirmationModal(driveId, driveName) {
        document.getElementById('modal-drive-id').value = driveId;
        document.getElementById('modal-drive-name').innerText = driveName;
        modal.style.display = 'flex';
    }

    function closeConfirmationModal() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            closeConfirmationModal();
        }
    }
</script>

</body>
</html>