CREATE DATABASE IF NOT EXISTS wap_system;
USE wap_system;

CREATE TABLE student (
    student_id VARCHAR(20) NOT NULL UNIQUE PRIMARY KEY,
    student_email VARCHAR(100) NOT NULL UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE admin (
    admin_id VARCHAR(20) NOT NULL UNIQUE PRIMARY KEY,
    admin_email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE room (
    room_name VARCHAR(100) NOT NULL UNIQUE PRIMARY KEY,
    `block` VARCHAR(20) NOT NULL,
    floor VARCHAR(20) NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    min_capacity INT NOT NULL,
    max_capacity INT NOT NULL,
    amenities VARCHAR(255) NOT NULL
);

CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    booking_purpose VARCHAR(255),
    number_of_people VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('Pending Approval', 'Approved', 'Cancelled by Admin') DEFAULT 'Pending Approval',
    approved_by_admin VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (room_name) REFERENCES room(room_name),
    FOREIGN KEY (approved_by_admin) REFERENCES admin(admin_id)
);

CREATE TABLE class_timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday'),
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (room_name) REFERENCES room(room_name)
);