<?php

$page_title = 'Admin Dashboard';
$user_type_required = 'admin';
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Donor.php';
require_once __DIR__ . '/../classes/Hospital.php';
require_once __DIR__ . '/../classes/BloodRequest.php';

$database = new Database();
$db = $database->connect();

$admin = new Admin($db);
$donor = new Donor($db);
$hospital = new Hospital($db);
$request = new BloodRequest($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_hospital') {
    $hospital_id_to_approve = $_POST['hospital_id'];
    if ($admin->approveHospital($hospital_id_to_approve)) {
        $_SESSION['form_message'] = "Hospital has been successfully approved!";
        $_SESSION['form_message_type'] = "success";
    } else {
        $_SESSION['form_message'] = "Failed to approve hospital.";
        $_SESSION['form_message_type'] = "error";
    }
    header("Location: admin_dashboard.php");
    exit();
}

$form_message = '';
if (isset($_SESSION['form_message'])) {
    $form_message = $_SESSION['form_message'];
    $form_message_type = $_SESSION['form_message_type'];
    unset($_SESSION['form_message'], $_SESSION['form_message_type']);
}

$total_donors = $donor->countAll();
$total_hospitals = $hospital->countAll();
$open_requests = $request->countOpen();
?>

<div class="container">
    <div class="welcome-banner">
        <h1>Administrator Control Panel</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>. Oversee and manage the platform from here.</p>
    </div>

    <?php if ($form_message): ?>
        <div class="alert alert-<?php echo $form_message_type; ?>">
            <?php echo htmlspecialchars($form_message); ?>
        </div>
    <?php endif; ?>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="icon icon-donors"><i class="fas fa-users"></i></div>
            <div class="info">
                <h3><?php echo $total_donors; ?></h3>
                <p>Registered Donors</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="icon icon-hospitals"><i class="fas fa-hospital"></i></div>
            <div class="info">
                <h3><?php echo $total_hospitals; ?></h3>
                <p>Registered Hospitals</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="icon icon-requests"><i class="fas fa-medkit"></i></div>
            <div class="info">
                <h3><?php echo $open_requests; ?></h3>
                <p>Open Blood Requests</p>
            </div>
        </div>
    </div>

    <div class="management-section">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 2px solid var(--light-color);">
                <h2>Pending Hospital Approvals</h2>
                <a href="manage_approvals.php" class="btn">
                    <i class="fas fa-tasks"></i> Manage All
                </a>
            </div>
            <?php if ($pending_hospitals->rowCount() > 0): ?>
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>Hospital Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registered On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pending_hospitals->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($row['reg_date'])); ?></td>
                                <td>
                                    <form method="POST" action="admin_dashboard.php">
                                        <input type="hidden" name="action" value="approve_hospital">
                                        <input type="hidden" name="hospital_id" value="<?php echo $row['hospital_id']; ?>">
                                        <button type="submit" class="btn btn-success">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-check-circle"></i>
                    <p>No pending hospital approvals. All accounts are up to date.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/js/all.min.js"></script>
</body>
</html>