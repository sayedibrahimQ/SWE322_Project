<?php
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/Database.php';
    require_once __DIR__ . '/../classes/Donor.php';
    require_once __DIR__ . '/../classes/Hospital.php';

    $database = new Database();
    $db = $database->connect();

    $user_type = $_POST['user_type'] ?? '';

    if ($user_type === 'donor') {
        $donor = new Donor($db);
        
        $donor->full_name = $_POST['donor_name'];
        $donor->email = $_POST['donor_email'];
        $donor->password = $_POST['donor_password'];
        $donor->blood_type = $_POST['blood_type'];
        $donor->phone = $_POST['donor_phone'];
        $donor->city = $_POST['donor_city'];

        if ($donor->register()) {
            $message = "Donor registration successful! You can now login.";
        } else {
            $message = "Registration failed. The email address may already be in use.";
        }

    } elseif ($user_type === 'hospital') {
        $hospital = new Hospital($db);

        $hospital->hospital_name = $_POST['hospital_name'];
        $hospital->email = $_POST['hospital_email'];
        $hospital->password = $_POST['hospital_password'];
        $hospital->phone = $_POST['hospital_phone'];
        $hospital->address = $_POST['hospital_address'];
        $hospital->city = $_POST['hospital_city'];

        if ($hospital->register()) {
            $message = "Hospital registration successful! Your account will be active after admin approval.";
        } else {
            $message = "Registration failed. The email address may already be in use.";
        }

    } else {
        $message = "Invalid user type selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Donation System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #c0392b; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"], select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #c0392b; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #a93226; }
        .user-type-selection { text-align: center; margin-bottom: 20px; }
        .user-type-selection label { display: inline-block; margin: 0 10px; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #fff; text-align: center; }
        .message.success { background-color: #2ecc71; }
        .message.error { background-color: #e74c3c; }
        .login-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Create an Account</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group user-type-selection">
            <label><input type="radio" name="user_type" value="donor" id="donor_radio" required checked> Register as a Donor</label>
            <label><input type="radio" name="user_type" value="hospital" id="hospital_radio" required> Register as a Hospital</label>
        </div>
        
        <div id="donor_fields">
            <div class="form-group">
                <label for="donor_name">Full Name</label>
                <input type="text" name="donor_name" id="donor_name" required>
            </div>
            <div class="form-group">
                <label for="donor_email">Email Address</label>
                <input type="email" name="donor_email" id="donor_email" required>
            </div>
            <div class="form-group">
                <label for="donor_password">Password</label>
                <input type="password" name="donor_password" id="donor_password" required>
            </div>
            <div class="form-group">
                <label for="blood_type">Blood Type</label>
                <select name="blood_type" id="blood_type" required>
                    <option value="">-- Select Blood Type --</option>
                    <option value="A+">A+</option> <option value="A-">A-</option>
                    <option value="B+">B+</option> <option value="B-">B-</option>
                    <option value="AB+">AB+</option> <option value="AB-">AB-</option>
                    <option value="O+">O+</option> <option value="O-">O-</option>
                </select>
            </div>
            <div class="form-group">
                <label for="donor_phone">Phone Number</label>
                <input type="text" name="donor_phone" id="donor_phone" required>
            </div>
            <div class="form-group">
                <label for="donor_city">City</label>
                <input type="text" name="donor_city" id="donor_city" required>
            </div>
        </div>

        <div id="hospital_fields" style="display: none;">
            <div class="form-group">
                <label for="hospital_name">Hospital Name</label>
                <input type="text" name="hospital_name" id="hospital_name" required>
            </div>
            <div class="form-group">
                <label for="hospital_email">Email Address</label>
                <input type="email" name="hospital_email" id="hospital_email" required>
            </div>
            <div class="form-group">
                <label for="hospital_password">Password</label>
                <input type="password" name="hospital_password" id="hospital_password" required>
            </div>
            <div class="form-group">
                <label for="hospital_phone">Phone Number</label>
                <input type="text" name="hospital_phone" id="hospital_phone" required>
            </div>
             <div class="form-group">
                <label for="hospital_city">City</label>
                <input type="text" name="hospital_city" id="hospital_city" required>
            </div>
            <div class="form-group">
                <label for="hospital_address">Full Address</label>
                <textarea name="hospital_address" id="hospital_address" rows="3" required></textarea>
            </div>
        </div>

        <button type="submit">Register</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const donorRadio = document.getElementById('donor_radio');
        const hospitalRadio = document.getElementById('hospital_radio');
        const donorFields = document.getElementById('donor_fields');
        const hospitalFields = document.getElementById('hospital_fields');

        function toggleFields() {
            if (donorRadio.checked) {
                donorFields.style.display = 'block';
                donorFields.querySelectorAll('input, select').forEach(el => el.required = true);
                hospitalFields.style.display = 'none';
                hospitalFields.querySelectorAll('input, textarea').forEach(el => el.required = false);
            } else {
                donorFields.style.display = 'none';
                donorFields.querySelectorAll('input, select').forEach(el => el.required = false);
                hospitalFields.style.display = 'block';
                hospitalFields.querySelectorAll('input, textarea').forEach(el => el.required = true);
            }
        }
        
        donorRadio.addEventListener('change', toggleFields);
        hospitalRadio.addEventListener('change', toggleFields);

        toggleFields();
    });
</script>

</body>
</html>