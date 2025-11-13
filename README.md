# Web Application for Blood Donation Management System

> This project is a submission for the **SWE 322** course. in the Software Engineering Department, College of Computer and Information Sciences, Jouf University.

##  Project Abstract
This web application provides a platform for hospitals, donors, and patients to coordinate blood donation and transfusion services. Donors can register and view upcoming blood donation campaigns, while hospitals can request specific blood types when needed. The system aims to improve efficiency, ensure quicker access to donors, and help save lives by reducing delays in finding compatible blood.

---

##  Project Objectives
The main goals of this system are:
* To facilitate donor registration and scheduling.
* To enable hospitals to post urgent blood requests.
* To provide patients with information about nearby blood banks and available donors.
* To improve communication between donors, hospitals, and patients.

---

##  Target Audience
This application is designed to benefit:
* Hospitals and blood banks 
* Donors and patients in need of blood

---

##  Technology Stack
The project is built using the following technologies:

* **Languages & Frameworks:** PHP, AJAX, HTML, CSS, JavaScript 
* **Database:** MySQL / SQL Server 
* **Server:** Apache / XAMPP 
* **Modeling Tools:** Star UML / ArgoUML (for design) 

---

##  Getting Started

To run this project locally, you will need a server environment like XAMPP.

1.  **Prerequisites:**
    * Ensure you have **XAMPP** (or a similar stack with Apache and MySQL) installed.

2.  **Installation:**
    * Clone the repository into your `htdocs` folder (usually found at `C:\xampp\htdocs`):
        ```bash
        git clone [https://github.com/sayedibrahimq/SWE322_Project.git](https://github.com/sayedibrahimq/SWE322_Project.git)
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
        http://localhost/SWE322_Project
        ```
