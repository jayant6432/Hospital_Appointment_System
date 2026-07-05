-- ==========================================================
-- Online Hospital Appointment Booking System
-- Database Schema
-- ==========================================================

CREATE DATABASE IF NOT EXISTS hospital_appointment_db;
USE hospital_appointment_db;

-- ----------------------------------------------------------
-- Table: departments
-- ----------------------------------------------------------
CREATE TABLE departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    description VARCHAR(255)
);

-- ----------------------------------------------------------
-- Table: doctors
-- ----------------------------------------------------------
CREATE TABLE doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department_id INT,
    specialization VARCHAR(100),
    phone VARCHAR(15),
    available_days VARCHAR(100) DEFAULT 'Mon-Sat',
    available_time VARCHAR(50) DEFAULT '10:00 AM - 4:00 PM',
    status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
        ON DELETE SET NULL
);

-- ----------------------------------------------------------
-- Table: patients
-- ----------------------------------------------------------
CREATE TABLE patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    gender ENUM('Male','Female','Other'),
    dob DATE,
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------
-- Table: admin
-- ----------------------------------------------------------
CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- ----------------------------------------------------------
-- Table: appointments
-- ----------------------------------------------------------
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time VARCHAR(20) NOT NULL,
    reason VARCHAR(255),
    status ENUM('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
);

-- ==========================================================
-- Sample Seed Data
-- ==========================================================

INSERT INTO departments (department_name, description) VALUES
('Cardiology', 'Heart and cardiovascular care'),
('Orthopedics', 'Bone, joint, and muscle care'),
('General Medicine', 'General health & consultation'),
('Gynecology', 'Women health & maternity care'),
('Pediatrics', 'Child healthcare');

-- Default password for all doctors below is: doctor123  (bcrypt hash, verified compatible with PHP password_verify)
INSERT INTO doctors (full_name, email, password, department_id, specialization, phone, available_days, available_time) VALUES
('Dr. A.K. Kaushik', 'kaushik@hospital.com', '$2b$10$UY1F04abn.R1CyZ3x9Ji8e7m4Lr6XMoOftZ0LoXBxSwIJyh5Kl8ku', 1, 'Cardiologist', '9876500001', 'Mon-Sat', '10:00 AM - 2:00 PM'),
('Dr. Akanksha Chaturvedi', 'akanksha@hospital.com', '$2b$10$UY1F04abn.R1CyZ3x9Ji8e7m4Lr6XMoOftZ0LoXBxSwIJyh5Kl8ku', 4, 'Gynecologist', '9876500002', 'Mon-Fri', '11:00 AM - 4:00 PM'),
('Dr. Rohit Mehra', 'rohit.mehra@hospital.com', '$2b$10$UY1F04abn.R1CyZ3x9Ji8e7m4Lr6XMoOftZ0LoXBxSwIJyh5Kl8ku', 2, 'Orthopedic Surgeon', '9876500003', 'Tue-Sat', '9:00 AM - 1:00 PM');

-- Default admin login -> username: admin | password: admin123
INSERT INTO admin (username, password) VALUES
('admin', '$2b$10$6GgWHYXM57m0ADTPzAnlR.Y4SZ8sBDrqJteuyj2Y2Q44XD5YzUxc6');
