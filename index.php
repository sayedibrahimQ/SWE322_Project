<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Management System</title>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <style>
        /* --- General Styling & Reset --- */
        :root {
            --primary-color: #c0392b;
            --secondary-color: #e74c3c;
            --dark-color: #333;
            --light-color: #f4f4f4;
            --white-color: #fff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background-color: var(--white-color);
        }

        .container {
            max-width: 1100px;
            margin: auto;
            overflow: hidden;
            padding: 0 2rem;
        }

        a {
            text-decoration: none;
            color: var(--primary-color);
        }

        /* --- Header & Navigation --- */
        .main-header {
            background: var(--white-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .main-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .logo i {
            margin-right: 8px;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }

        .main-nav ul li {
            margin-left: 20px;
        }

        .main-nav ul li a {
            color: var(--dark-color);
            padding: 0.5rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .main-nav ul li a:hover {
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            color: var(--white-color);
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary { background-color: var(--primary-color); }
        .btn-secondary { background-color: transparent; border: 2px solid var(--primary-color); color: var(--primary-color); }
        
        /* --- Hero Section --- */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1524721696987-b9527df9e512?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80') no-repeat center center/cover;
            height: 85vh;
            color: var(--white-color);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        /* --- How It Works Section --- */
        .info-section {
            padding: 4rem 0;
            text-align: center;
        }

        .info-section h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .info-section .lead {
            font-size: 1.1rem;
            margin-bottom: 3rem;
            color: #666;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .step {
            padding: 2rem;
        }

        .step i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        .step h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* --- Stats Section --- */
        .stats-section {
            background-color: var(--light-color);
            padding: 4rem 0;
            text-align: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .stat h2 {
            font-size: 3rem;
            color: var(--primary-color);
        }
        .stat p {
            font-size: 1.2rem;
            font-weight: 500;
        }
        
        /* --- Footer --- */
        .main-footer {
            background-color: var(--dark-color);
            color: var(--white-color);
            text-align: center;
            padding: 2rem 0;
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .hero p { font-size: 1rem; }
            .steps-grid, .stats-grid { grid-template-columns: 1fr; }
            .main-nav ul { display: none; } /* Simple hide for mobile, can be replaced with a burger menu */
            .main-header .container { justify-content: center; }
            .main-header .logo { margin-bottom: 1rem; }
        }

    </style>
</head>
<body>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo"><i class="fas fa-hand-holding-medical"></i> VitalFlow</a>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#how-it-works">How It Works</a></li>
                    <!-- We will create these pages later -->
                    <li><a href="#">Find a Drive</a></li> 
                    <li><a href="#">Contact</a></li>
                    <li><a href="public/login.php" class="btn btn-secondary">Login</a></li>
                    <li><a href="public/register.php" class="btn btn-primary">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <h1>Donate Blood, Save Lives</h1>
            <p>Your single donation can help save up to three lives. Join our community of heroes today and make a real difference.</p>
            <a href="public/register.php" class="btn btn-primary">Become a Donor</a>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="info-section">
            <div class="container">
                <h2>A Simple Process to Save Lives</h2>
                <p class="lead">Becoming a lifesaver is easier than you think. Follow these simple steps.</p>
                <div class="steps-grid">
                    <div class="step">
                        <i class="fas fa-user-plus"></i>
                        <h3>1. Register</h3>
                        <p>Create an account as a donor or a hospital in just a few minutes.</p>
                    </div>
                    <div class="step">
                        <i class="fas fa-search-location"></i>
                        <h3>2. Find or Request</h3>
                        <p>Donors can find nearby blood drives. Hospitals can post urgent requests for specific blood types.</p>
                    </div>
                    <div class="step">
                        <i class="fas fa-tint"></i>
                        <h3>3. Donate</h3>
                        <p>Attend a blood drive or respond to an urgent request to give the gift of life.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat">
                        <h2>1 Donation</h2>
                        <p>Can save up to 3 lives.</p>
                    </div>
                    <div class="stat">
                        <h2>Every 2 Seconds</h2>
                        <p>Someone in the world needs blood.</p>
                    </div>
                    <div class="stat">
                        <h2>4.5 Million</h2>
                        <p>Lives saved each year by donations.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <p>&copy; 2024 Blood Donation Management System. All Rights Reserved.</p>
    </footer>

</body>
</html>