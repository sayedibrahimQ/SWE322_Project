<?php
$page_title = 'Donor Dashboard';
$user_type_required = 'donor';
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Registration.php';
require_once __DIR__ . '/../classes/BloodRequest.php'; 

$database = new Database();
$db = $database->connect();

$registration = new Registration($db);
$request = new BloodRequest($db);

$my_registrations = $registration->readByDonor($_SESSION['user_id']);
$open_requests_count = $request->countOpen(); 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="container">
    <div class="welcome-banner">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <p>Your dashboard provides a summary of your activity and opportunities to help.</p>
    </div>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="icon icon-requests"><i class="fas fa-medkit"></i></div>
            <div class="info">
                <h3><?php echo $open_requests_count; ?></h3>
                <p>Urgent Requests Need Help</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="icon icon-hospitals"><i class="fas fa-calendar-check"></i></div>
            <div class="info">
                <h3><?php echo $my_registrations->rowCount(); ?></h3>
                <p>Your Upcoming Donations</p>
            </div>
        </div>
    </div>

    <div class="grid-container" style="grid-template-columns: 1fr 1fr;">
        <div class="card">
            <h2>Make a Difference</h2>
            <p>Browse detailed lists of urgent needs and upcoming community drives. Your next donation could be a lifeline.</p>
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="view_requests.php" class="btn" style="text-align: center; font-size: 1.1rem; padding: 15px;">
                    <i class="fas fa-bullhorn"></i> View Urgent Requests
                </a>
                <a href="view_drives.php" class="btn" style="text-align: center; font-size: 1.1rem; background-color:#3498db;">
                    <i class="fas fa-search-location"></i> Find a Blood Drive
                </a>
            </div>
        </div>
        
        <div class="card">
            <h2>My Registrations</h2>
            <div id="my-registrations-list">
                <?php if ($my_registrations->rowCount() > 0): ?>
                    <?php 
                    $my_registrations->execute(); 
                    while ($row = $my_registrations->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="item">
                            <strong><?php echo htmlspecialchars($row['drive_name']); ?></strong>
                            <br>
                            <small>On: <?php echo date('F j, Y, g:i a', strtotime($row['start_time'])); ?></small>
                            <span class="status status-<?php echo strtolower($row['status']); ?>" style="float: right;">
                                <?php echo htmlspecialchars(ucfirst($row['status'])); ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                     <div class="no-data" style="padding: 20px;">
                        <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p style="margin:0;">You haven't registered for any drives yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>