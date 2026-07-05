# Online Hospital Appointment Booking System
### MCA Major Project (23ONMCR-753) — Phase 2: Development Phase

A web-based hospital appointment booking system built with PHP, MySQL, and Bootstrap 5.
Supports three roles: **Patient**, **Doctor**, and **Admin**, each with dedicated dashboards.

---

## Tech Stack
- **Front-end:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Back-end:** PHP 8.x (PDO for database access)
- **Database:** MySQL / MariaDB
- **Server:** Apache (via XAMPP/WAMP) or PHP's built-in server

---

## Setup Instructions

### 1. Install a local server environment
Install **XAMPP** (Windows/Linux/Mac) or **WAMP** (Windows). Start Apache and MySQL from the control panel.

### 2. Place the project files
Copy the entire `hospital_project` folder into your server's web root:
- XAMPP: `C:\xampp\htdocs\hospital_project`
- WAMP: `C:\wamp64\www\hospital_project`

### 3. Create the database
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Click **Import** → choose the file `database.sql` from this project → click **Go**
   (This creates the database `hospital_appointment_db` with all tables and sample data.)

### 4. Configure the database connection
Open `config/db.php` and confirm these match your local MySQL setup (defaults work for most XAMPP/WAMP installs):
```php
$db_host = "localhost";
$db_name = "hospital_appointment_db";
$db_user = "root";
$db_pass = "";
```

### 5. Run the application
Visit: `http://localhost/hospital_project/index.php`

---

## Demo Login Credentials

| Role    | Username / Email             | Password   |
|---------|-------------------------------|------------|
| Patient | Register your own account     | —          |
| Doctor  | kaushik@hospital.com          | doctor123  |
| Doctor  | akanksha@hospital.com         | doctor123  |
| Doctor  | rohit.mehra@hospital.com      | doctor123  |
| Admin   | admin                          | admin123   |

---

## Folder Structure
```
hospital_project/
├── index.php                  # Landing page
├── database.sql               # DB schema + seed data
├── config/
│   └── db.php                 # PDO database connection
├── includes/
│   ├── header.php             # Shared navbar/header
│   └── footer.php             # Shared footer
├── assets/css/style.css       # Custom styling
├── patient/
│   ├── register.php
│   ├── login.php / logout.php
│   ├── dashboard.php
│   ├── doctors.php            # Search/browse doctors
│   ├── book_appointment.php
│   └── my_appointments.php
├── doctor/
│   ├── login.php / logout.php
│   └── dashboard.php          # Confirm/reject/complete appointments
└── admin/
    ├── login.php / logout.php
    ├── dashboard.php          # Stats overview
    ├── manage_doctors.php     # Add/activate/deactivate doctors
    ├── manage_departments.php
    └── all_appointments.php   # Hospital-wide appointment view
```

---

## Core Modules & Features
1. **Patient Module** — Register, login, search doctors by specialization/department, book appointment (date & time slot), view/cancel own appointments, track status.
2. **Doctor Module** — Login, view assigned appointments, filter by status, confirm/reject pending requests, mark completed.
3. **Admin Module** — Login, dashboard with hospital-wide stats, manage doctors (add/activate/deactivate), manage departments, view all appointments across the hospital.
4. **Security** — Passwords stored using bcrypt hashing (`password_hash` / `password_verify`); all DB queries use PDO prepared statements to prevent SQL injection; session-based access control per role.

---

## Testing Performed
This build was functionally tested end-to-end on a live PHP + MySQL server:
- Patient registration, login, and session handling
- Doctor search/filter and appointment booking (with double-booking prevention)
- Doctor login and appointment status updates (Pending → Confirmed/Cancelled → Completed)
- Admin login, doctor/department management (add, activate/deactivate), and hospital-wide appointment view
- All 20 PHP files pass `php -l` syntax validation with no errors
