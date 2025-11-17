blood_donation_system/
│
├── index.php                 # Landing/Home page
├── config/
│   └── Database.php
│
├── classes/                  # Your existing classes are good
│   ├── User.php
│   ├── Donor.php
│   ├── Hospital.php
│   ├── Admin.php
│   ├── BloodRequest.php
│   ├── BloodDrive.php
│   ├── Registration.php
│   └── DonorResponse.php     # Class for the new table
│
├── public/
│   ├── register.php          # Combined registration form (choose donor or hospital)
│   ├── login.php
│   ├── logout.php            # Script to destroy the session
│   │
│   ├── donor_dashboard.php   # Main page for logged-in donors
│   ├── hospital_dashboard.php# Main page for logged-in hospitals
│   ├── admin_dashboard.php   # Main page for logged-in admin
│   │
│   ├── create_request.php    # Form for hospitals to post a blood request
│   ├── view_requests.php     # Page for donors to see available requests
│   ├── create_drive.php      # Form for hospitals to create a blood drive
│   ├── view_drives.php       # Page for donors to see upcoming drives
│   └── manage_approvals.php  # Page for admin to approve hospitals
│
├── includes/
│   ├── header.php            # Common HTML head, navigation bar
│   └── footer.php            # Common HTML footer, scripts
