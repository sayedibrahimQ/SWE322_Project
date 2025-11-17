<?php
$page_title = 'Post New Blood Request';
$user_type_required = 'hospital'; 
require_once __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodRequest.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();
    
    $request = new BloodRequest($db);

    $request->hospital_id = $_SESSION['user_id'];
    $request->blood_type_needed = $_POST['blood_type'];
    $request->urgency_level = $_POST['urgency'];

    if ($request->create()) {
        $_SESSION['form_message'] = "Urgent blood request was successfully posted!";
        $_SESSION['form_message_type'] = "success";
    } else {
        $_SESSION['form_message'] = "There was an error posting your request. Please try again.";
        $_SESSION['form_message_type'] = "error";
    }

    header("Location: hospital_dashboard.php");
    exit();
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

<div class="form-page-container">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 10px;">
            <i class="fas fa-bullhorn" style="color: var(--primary-color);"></i>
            Post an Urgent Blood Request
        </h2>
        <p style="text-align: center; color: #777; margin-bottom: 25px;">
            This request will be immediately visible to all registered donors.
        </p>

        <form action="create_request.php" method="POST">
            <div class="form-group">
                <label for="blood_type">Blood Type Needed</label>
                <select name="blood_type" id="blood_type" required>
                    <option value="" disabled selected>-- Select a blood type --</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>

            <div class="form-group">
                <label for="urgency">Urgency Level</label>
                <select name="urgency" id="urgency" required>
                    <option value="Urgent">Urgent (Immediate Need)</option>
                    <option value="High">High (Needed within 24 hours)</option>
                    <option value="Medium">Medium (Needed within 2-3 days)</option>
                </select>
            </div>

            <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 12px;">
                <i class="fas fa-paper-plane"></i> Post Request Now
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="hospital_dashboard.php">Cancel and return to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>