<?php
// includes/header.php

// Start the session to access session variables
session_start();

// SECURITY CHECK:
// Check if the user is not logged in OR if they are not the correct user type.
if (!isset($_SESSION['user_id']) || !isset($user_type_required) || $_SESSION['user_type'] !== $user_type_required) {
    // If check fails, destroy the session and redirect to login
    session_destroy();
    header("Location: login.php");
    exit();
}

// Get user type from session for dynamic navigation
$current_user_type = $_SESSION['user_type'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - VitalFlow</title>
    
    <!-- Google Fonts: Poppins for a modern look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <style>
        /* --- CORE STYLES & VARIABLES --- */
        :root {
            --primary-color: #c0392b;
            --secondary-color: #e74c3c;
            --dark-color: #333;
            --light-color: #f8f9fa; /* Lighter background */
            --white-color: #fff;
            --success-color: #2ecc71;
            --pending-color: #f1c40f;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            margin: 0;
            padding-top: 70px; /* Space for the fixed header */
        }
        
        /* --- MODERNIZED HEADER/NAVBAR --- */
        .main-header {
            background-color: var(--white-color);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            padding: 0 2rem;
        }
        .header-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        .header-logo a i { margin-right: 8px; }

        .main-nav { display: flex; align-items: center; gap: 10px; }
        .main-nav a {
            color: var(--dark-color);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .main-nav a:hover, .main-nav a.active {
            background-color: var(--light-color);
            color: var(--primary-color);
        }
        .main-nav a.logout-btn {
            background-color: var(--primary-color);
            color: var(--white-color);
        }
        .main-nav a.logout-btn:hover { background-color: var(--secondary-color); }
        
        /* --- GLOBAL & UTILITY STYLES --- */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .welcome-banner { background-color: var(--white-color); padding: 25px; margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border-color); }
        .welcome-banner h1 { margin: 0; color: var(--dark-color); }
        .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
        .card { background-color: var(--white-color); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 25px; border: 1px solid var(--border-color); }
        .card h2 { color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 15px; margin-top: 0; margin-bottom: 20px; font-size: 1.4rem; }
        .item { border-bottom: 1px solid #f0f0f0; padding: 15px 0; }
        .item:first-of-type { padding-top: 0; }
        .item:last-child { border-bottom: none; padding-bottom: 0; }
        .item strong { color: var(--dark-color); }
        .btn { display: inline-block; padding: 12px 20px; background-color: var(--primary-color); color: var(--white-color); text-decoration: none; border-radius: 8px; border: none; cursor: pointer; margin-top: 10px; font-weight: 500; transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .status { padding: 5px 12px; border-radius: 20px; color: var(--white-color); font-size: 0.8em; font-weight: 500; }
        .status-registered, .status-pending { background-color: var(--pending-color); }
        .status-attended, .status-open { background-color: var(--success-color); }
        
        /* --- ALL YOUR EXISTING COMPONENT STYLES --- */
        /* You can copy/paste ALL of your other style blocks here without conflict */
        /* --- Form Styles --- */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; }
        /* --- Message/Alert Styles --- */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; color: var(--white-color); }
        .alert-success { background-color: var(--success-color); }
        .alert-error { background-color: #e74c3c; }
        /* --- Admin Dashboard Specific Styles --- */
        .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card { background-color: var(--white-color); padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; }
        .summary-card .icon { font-size: 2.5rem; padding: 20px; border-radius: 50%; color: var(--white-color); }
        .icon-donors { background-color: #3498db; } .icon-hospitals { background-color: #2ecc71; } .icon-requests { background-color: #e67e22; }
        .summary-card .info h3 { margin: 0; font-size: 2rem; color: var(--dark-color); }
        .summary-card .info p { margin: 0; color: #777; }
        .management-section .card { overflow-x: auto; }
        .management-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .management-table th, .management-table td { padding: 15px; text-align: left; border-bottom: 1px solid var(--light-color); }
        .management-table th { background-color: #f9f9f9; font-weight: 600; color: #555; }
        .management-table tr:hover { background-color: #f5f5f5; }
        .btn-success { background-color: var(--success-color); padding: 8px 15px; }
        .btn-success:hover { background-color: #27ae60; }
        .no-data { text-align: center; padding: 40px; background-color: #f9f9f9; border-radius: 8px; color: #888; }
        .no-data i { font-size: 3rem; display: block; margin-bottom: 15px; color: #ccc; }
        /* --- Focused Form Page Layout --- */
        .form-page-container { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 70px); padding: 20px; }
        .form-page-container .card { max-width: 500px; width: 100%; }
        /* --- View Pages Styles --- */
        .page-header { background: var(--white-color); padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .filter-bar { display: flex; gap: 15px; }
        .filter-bar input, .filter-bar select { padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .filter-bar input { flex-grow: 1; }
        .requests-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; }
        .request-card { background: var(--white-color); border-radius: 8px; overflow: hidden; display: flex; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .request-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .request-card .blood-type-badge { background-color: var(--primary-color); color: var(--white-color); font-size: 2rem; font-weight: bold; padding: 20px; display: flex; justify-content: center; align-items: center; writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg); }
        .request-card .details { padding: 20px; flex-grow: 1; }
        .request-card .details h3 { margin-top: 0; }
        .request-card .info-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #555; }
        .request-card .info-item i { color: var(--primary-color); width: 20px; text-align: center; }
        /* --- Modal Styles --- */
        .modal { display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background-color: #fefefe; padding: 30px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px; text-align: center; }
        .modal-buttons { margin-top: 20px; }
        .btn-secondary { background-color: #aaa; }
        .btn-secondary:hover { background-color: #888; }
        /* --- Drive Card Styles --- */
        .drive-card { background: var(--white-color); border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; overflow: hidden; transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
        .drive-card:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .drive-card-header { background-color: var(--primary-color); color: var(--white-color); padding: 20px; }
        .drive-card-header h3 { margin: 0; font-size: 1.5rem; }
        .drive-card-body { padding: 20px; flex-grow: 1; }
        .drive-card-body .info-item { display: flex; align-items: start; gap: 15px; margin-bottom: 15px; color: #555; }
        .drive-card-body .info-item i { color: var(--primary-color); font-size: 1.2rem; margin-top: 3px; width: 20px; text-align: center; }
        .drive-card-footer { background-color: var(--light-color); padding: 15px 20px; text-align: right; }
        /* --- Item Cancel Styles --- */
        .item { position: relative; padding-right: 60px; }
        .item-actions { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); display: flex; align-items: center; gap: 10px; }
        .btn-cancel { background: #e74c3c; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; font-size: 16px; line-height: 24px; text-align: center; cursor: pointer; opacity: 0.5; transition: all 0.2s ease; padding: 0; flex-shrink: 0; }
        .item:hover .btn-cancel { opacity: 1; transform: scale(1.1); }
/* --- MODERNIZED Respond to Request Page Styles --- */
.response-card {
            background: var(--white-color);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.07);
            max-width: 900px; /* Wider card for two columns */
            margin: 20px auto;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .response-card-header {
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white-color);
            border-bottom: 1px solid var(--border-color);
        }
        .response-card-header h2 { margin: 0; }
        .response-card-blood-badge {
            font-size: 2rem; font-weight: 700; color: var(--white-color);
            background-color: var(--primary-color);
            width: 80px; height: 80px; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            flex-shrink: 0;
        }
        .response-card-grid-body {
            display: grid;
            grid-template-columns: 1fr 1fr; /* Two equal columns */
            gap: 30px;
            padding: 30px;
        }
        /* Make it single-column on smaller screens */
        @media (max-width: 768px) {
            .response-card-grid-body { grid-template-columns: 1fr; }
        }
        .details-panel h3 { margin-top: 0; margin-bottom: 20px; font-size: 1.3rem; }
        .details-panel .info-item {
            display: flex;
            align-items: start;
            gap: 15px;
            margin-bottom: 20px;
        }
        .details-panel .info-item i {
            font-size: 1.2rem; color: var(--primary-color);
            margin-top: 5px; width: 25px; text-align: center;
        }
        .details-panel .info-item div { line-height: 1.4; }
        .details-panel .info-item strong { display: block; color: var(--dark-color); }
        .details-panel .info-item span { color: #555; }
        .map-container {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            height: 100%;
            min-height: 300px;
        }
        .map-container iframe { width: 100%; height: 100%; border: 0; }

        .response-card-footer {
            padding: 20px 30px;
            background-color: var(--light-color);
            text-align: center;
            border-top: 1px solid var(--border-color);
        }
        .btn-confirm {
            font-size: 1.2rem; padding: 15px 30px; width: 100%;
            background-color: var(--success-color);
        }
        .btn-confirm:hover { background-color: #27ae60; }
        .btn-confirm:disabled { background-color: #aaa; cursor: not-allowed; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="header-logo">
                <a href="<?php echo $current_user_type; ?>_dashboard.php"><i class="fas fa-hand-holding-medical"></i> VitalFlow</a>
            </div>
            <nav class="main-nav">
                <!-- Dynamically generate navigation links based on user type -->
                <?php if ($current_user_type === 'donor'): ?>
                    <a href="donor_dashboard.php">Dashboard</a>
                    <a href="view_requests.php">Urgent Requests</a>
                    <a href="view_drives.php">Find Drives</a>
                <?php elseif ($current_user_type === 'hospital'): ?>
                    <a href="hospital_dashboard.php">Dashboard</a>
                <?php elseif ($current_user_type === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="manage_approvals.php">Approvals</a>
                <?php endif; ?>
                
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>
    </header>