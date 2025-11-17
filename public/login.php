<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] === 'donor') {
        header("Location: donor_dashboard.php");
    } elseif ($_SESSION['user_type'] === 'hospital') {
        header("Location: hospital_dashboard.php");
    } elseif ($_SESSION['user_type'] === 'admin') {
        header("Location: admin_dashboard.php");
    }
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/Database.php';
    require_once __DIR__ . '/../classes/User.php';
    require_once __DIR__ . '/../classes/Donor.php';
    require_once __DIR__ . '/../classes/Hospital.php';
    require_once __DIR__ . '/../classes/Admin.php';

    $database = new Database();
    $db = $database->connect();

    $user_type = $_POST['user_type'] ?? '';
    $login_identifier = $_POST['login_identifier'] ?? ''; 
    $password = $_POST['password'] ?? '';

    $user_instance = null;

    if ($user_type === 'donor') {
        $user_instance = new Donor($db);
    } elseif ($user_type === 'hospital') {
        $user_instance = new Hospital($db);
    } elseif ($user_type === 'admin') {
        $user_instance = new Admin($db);
    }

    if ($user_instance) {
        $user_instance->email = $login_identifier;
        $user_instance->password = $password;

        $user_data = $user_instance->login();

        if ($user_data) {
            if ($user_type === 'hospital' && $user_data['is_approved'] == 0) {
                $message = "Your account is pending approval by an administrator.";
            } else {
                $pk_column = $user_instance->getPrimaryKeyColumn();
                $_SESSION['user_id'] = $user_data[$pk_column];
                $_SESSION['user_type'] = $user_type;
                $_SESSION['user_name'] = $user_data['full_name'] ?? $user_data['hospital_name'] ?? $user_data['username'];

                header("Location: " . $user_type . "_dashboard.php");
                exit();
            }
        } else {
            $message = "Invalid credentials. Please check your details and try again.";
        }
    } else {
        $message = "Please select a valid user type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blood Donation System</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #c0392b;
            --secondary-color: #e74c3c;
            --dark-color: #333;
            --light-color: #f4f4f4;
            --white-color: #fff;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
        }
        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: 100vh;
        }
        .login-image-panel {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1582719478212-c857e5401b31?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--white-color);
            text-align: center;
            padding: 40px;
        }
        .login-image-panel h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .login-image-panel p { font-size: 1.2rem; max-width: 400px; }

        .login-form-panel {
            background: var(--white-color);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }
        .form-wrapper { width: 100%; max-width: 400px; }
        .form-wrapper .logo {
            font-size: 1.8rem; font-weight: 700; color: var(--primary-color);
            text-align: center; margin-bottom: 10px;
        }
        .form-wrapper .logo i { margin-right: 8px; }
        .form-wrapper h2 { text-align: center; color: var(--dark-color); margin-bottom: 25px; }

        .form-group { margin-bottom: 20px; }
        .input-wrapper { position: relative; }
        .input-wrapper i {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%); color: #aaa;
        }
        .form-group input, .form-group select {
            width: 100%; padding: 15px 15px 15px 45px;
            border: 1px solid #ddd; border-radius: 8px;
            font-size: 1rem; font-family: 'Poppins', sans-serif;
        }
        .form-group select { padding-left: 15px; } 
        button {
            width: 100%; padding: 15px; background: var(--primary-color);
            color: var(--white-color); border: none; border-radius: 8px;
            cursor: pointer; font-size: 1.1rem; font-weight: 600;
            transition: background-color 0.3s ease;
        }
        button:hover { background-color: var(--secondary-color); }

        .message { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: var(--white-color); text-align: center; background-color: #e74c3c; }
        .register-link { text-align: center; margin-top: 25px; color: #555; }
        .register-link a { color: var(--primary-color); font-weight: 600; text-decoration: none; }

        @media (max-width: 850px) {
            .login-container { grid-template-columns: 1fr; }
            .login-image-panel { display: none; } 
            .login-form-panel { height: 100vh; }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-image-panel">
        <h1>Be a Hero Today</h1>
        <p>Your simple act of kindness connects a community and saves lives. Log in to make a difference.</p>
    </div>

    <div class="login-form-panel">
        <div class="form-wrapper">
            <div class="logo"><i class="fas fa-hand-holding-medical"></i> VitalFlow</div>
            <h2>Welcome Back!</h2>

            <?php if (!empty($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <select name="user_type" id="user_type" required>
                        <option value="donor">Login as Donor</option>
                        <option value="hospital">Login as Hospital</option>
                        <option value="admin">Login as Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="text" name="login_identifier" placeholder="Email (or Username for Admin)" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                <button type="submit">Login</button>
            </form>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>