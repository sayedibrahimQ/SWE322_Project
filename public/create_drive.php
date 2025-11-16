<?php
// public/create_drive.php

$page_title = 'Schedule New Blood Drive';
$user_type_required = 'hospital'; // Security check
require_once __DIR__ . '/../includes/header.php';

// Include necessary class files
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodDrive.php';

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
    
    // Instantiate BloodDrive object
    $drive = new BloodDrive($db);

    // Assign properties from the form and session
    $drive->hospital_id = $_SESSION['user_id'];
    $drive->drive_name = $_POST['drive_name'];
    $drive->location_address = $_POST['location_address'];
    $drive->start_time = $_POST['start_time'];
    $drive->description = $_POST['description'];

    // Attempt to create the drive
    if ($drive->create()) {
        // Use a session "flash message" for success
        $_SESSION['form_message'] = "Your blood drive has been successfully scheduled!";
        $_SESSION['form_message_type'] = "success";
    } else {
        // Set an error flash message
        $_SESSION['form_message'] = "There was an error scheduling your blood drive. Please check your inputs and try again.";
        $_SESSION['form_message_type'] = "error";
    }

    // Redirect to the dashboard to show the result and prevent re-submission
    header("Location: hospital_dashboard.php");
    exit();
}

// Get the current date and time in the correct format for the datetime-local input min attribute
$min_datetime = date('Y-m-d\TH:i');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="form-page-container">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 10px;">
            <i class="fas fa-calendar-plus" style="color: var(--primary-color);"></i>
            Schedule a New Blood Drive
        </h2>
        <p style="text-align: center; color: #777; margin-bottom: 25px;">
            Organize an event for the community. This will be visible to all registered donors.
        </p>

        <form action="create_drive.php" method="POST">
            <div class="form-group">
                <label for="drive_name">Drive Name / Title</label>
                <input type="text" name="drive_name" id="drive_name" placeholder="e.g., Annual Summer Blood Drive" required>
            </div>
            
            <div class="form-group">
                <label for="start_time">Date and Time of Event</label>
                <input type="datetime-local" name="start_time" id="start_time" required min="<?php echo $min_datetime; ?>">
            </div>

            <div class="form-group">
                <label for="location_address">Location Address</label>
                <textarea name="location_address" id="location_address" rows="3" placeholder="e.g., 123 Main Street, Community Hall, Cityville" required></textarea>
            </div>

            <div class="form-group">
                <label for="description">Additional Information (Optional)</label>
                <textarea name="description" id="description" rows="4" placeholder="e.g., Free snacks and drinks will be provided for all donors. Please bring a valid ID."></textarea>
            </div>

            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 12px;">
                <i class="fas fa-check-circle"></i> Schedule Drive
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="hospital_dashboard.php">Cancel and return to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>