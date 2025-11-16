<?php
// public/manage_approvals.php

$page_title = 'Manage Hospital Approvals';
$user_type_required = 'admin'; // Security check
require_once __DIR__ . '/../includes/header.php';

// Include necessary class files
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Admin.php';

$database = new Database();
$db = $database->connect();

$admin = new Admin($db);

// --- HANDLE HOSPITAL APPROVAL FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve_hospital') {
    $hospital_id_to_approve = $_POST['hospital_id'];
    if ($admin->approveHospital($hospital_id_to_approve)) {
        // Use a session flash message for success
        $_SESSION['form_message'] = "Hospital has been successfully approved!";
        $_SESSION['form_message_type'] = "success";
    } else {
        $_SESSION['form_message'] = "Failed to approve the hospital. It may have already been approved or an error occurred.";
        $_SESSION['form_message_type'] = "error";
    }
    // Redirect to the same page to show the updated list and message
    header("Location: manage_approvals.php");
    exit();
}

// Check for and display flash messages
$form_message = '';
if (isset($_SESSION['form_message'])) {
    $form_message = $_SESSION['form_message'];
    $form_message_type = $_SESSION['form_message_type'];
    unset($_SESSION['form_message'], $_SESSION['form_message_type']);
}

// --- FETCH DATA FOR DISPLAY ---
// Get all hospitals waiting for approval
$pending_hospitals = $admin->getPendingHospitals();
?>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="container">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Pending Hospital Approvals</h1>
                <p>Review and approve new hospital accounts to grant them access to the platform.</p>
            </div>
            <a href="admin_dashboard.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
    
    <!-- Display form submission messages -->
    <?php if ($form_message): ?>
        <div class="alert alert-<?php echo $form_message_type; ?>">
            <?php echo htmlspecialchars($form_message); ?>
        </div>
    <?php endif; ?>

    <div class="management-section">
        <div class="card">
            <?php if ($pending_hospitals->rowCount() > 0): ?>
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>Hospital Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Full Address</th>
                            <th>Registered On</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pending_hospitals->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['hospital_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo date('M j, Y, g:i a', strtotime($row['reg_date'])); ?></td>
                                <td style="text-align: center;">
                                    <form method="POST" action="manage_approvals.php" onsubmit="return confirm('Are you sure you want to approve this hospital?');">
                                        <input type="hidden" name="action" value="approve_hospital">
                                        <input type="hidden" name="hospital_id" value="<?php echo $row['hospital_id']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle"></i> Approve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-check-double"></i>
                    <h2>All Clear!</h2>
                    <p>There are no pending hospital approvals at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>