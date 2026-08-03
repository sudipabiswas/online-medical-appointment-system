-- Database schema for Online Medical Appointment System
CREATE DATABASE IF NOT EXISTS healthsuite_db;
USE healthsuite_db;

-- Clear existing records safely
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS users;

-- Table 1: Users table
CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Patient', 'Doctor', 'Admin') DEFAULT 'Patient',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table 2: Doctors table
CREATE TABLE doctors (
  doctor_id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_name VARCHAR(100) NOT NULL,
  specialty VARCHAR(100) NOT NULL,
  clinic_room VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- Table 3: Appointments table
CREATE TABLE appointments (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  appointment_time DATETIME NOT NULL,
  status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
  FOREIGN KEY (patient_id) REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (doctor_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert Seed Users
INSERT INTO users (user_id, full_name, email, password_hash, role) VALUES
(1, 'Dr. Alice Vance', 'avance@healthsuite.org', '$2y$10$a1b2c3...', 'Doctor'),
(2, 'Bob Miller', 'bmiller@univ.edu', '$2y$10$x7y8z9...', 'Patient'),
(3, 'Charlie Brown', 'cbrown@univ.edu', '$2y$10$m1n2o3...', 'Patient');

-- Insert Seed Appointments
INSERT INTO appointments (appointment_id, patient_id, doctor_id, appointment_time, status) VALUES
(101, 2, 1, '2026-08-01 10:00:00', 'Scheduled'),
(102, 3, 1, '2026-08-01 11:30:00', 'Scheduled');
