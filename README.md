# Web Application for Blood Donation Management System

> [cite_start]This project is a submission for the **SWE 322** course [cite: 2] [cite_start]in the Software Engineering Department, College of Computer and Information Sciences, Jouf University[cite: 1].

##  Project Abstract
[cite_start]This web application provides a platform for hospitals, donors, and patients to coordinate blood donation and transfusion services[cite: 7]. [cite_start]Donors can register and view upcoming blood donation campaigns, while hospitals can request specific blood types when needed[cite: 8]. [cite_start]The system aims to improve efficiency, ensure quicker access to donors, and help save lives by reducing delays in finding compatible blood[cite: 9].

---

##  Project Objectives
The main goals of this system are:
* [cite_start]To facilitate donor registration and scheduling[cite: 11].
* [cite_start]To enable hospitals to post urgent blood requests[cite: 12].
* [cite_start]To provide patients with information about nearby blood banks and available donors[cite: 13].
* [cite_start]To improve communication between donors, hospitals, and patients[cite: 14].

---

##  Target Audience
This application is designed to benefit:
* [cite_start]Hospitals and blood banks [cite: 5]
* [cite_start]Donors and patients in need of blood [cite: 6]

---

##  Technology Stack
The project is built using the following technologies:

* [cite_start]**Languages & Frameworks:** PHP, AJAX, HTML, CSS, JavaScript [cite: 18]
* [cite_start]**Database:** MySQL / SQL Server [cite: 17]
* [cite_start]**Server:** Apache / XAMPP [cite: 19]
* [cite_start]**Modeling Tools:** Star UML / ArgoUML (for design) [cite: 16]

---

##  Getting Started

[cite_start]To run this project locally, you will need a server environment like XAMPP[cite: 19].

1.  **Prerequisites:**
    * [cite_start]Ensure you have **XAMPP** (or a similar stack with Apache and MySQL) installed[cite: 17, 19].

2.  **Installation:**
    * Clone the repository into your `htdocs` folder (usually found at `C:\xampp\htdocs`):
        ```bash
        git clone [https://github.com/your-username/your-repo-name.git](https://github.com/your-username/your-repo-name.git)
        ```
    * Start the **Apache** and **MySQL** services from your XAMPP control panel.

3.  **Database Setup:**
    * Open `phpMyAdmin` (usually at `http://localhost/phpmyadmin`).
    * Create a new database (e.g., `blood_donation_db`).
    * Import the `.sql` file (located in the `/database` folder of this repo) into your new database.
    * *(Note: You may need to update the database connection strings in the PHP files to match your database name, username, and password.)*

4.  **Running the Application:**
    * Open your web browser and navigate to:
        ```
        http://localhost/your-repo-name
        ```
