CREATE DATABASE IF NOT EXISTS wap_system;
USE wap_system;

CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    student_email VARCHAR(255) NOT NULL UNIQUE,
    student_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id VARCHAR(50) NOT NULL UNIQUE,
    admin_email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE room (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL UNIQUE,
    `block` VARCHAR(50) NOT NULL,
    floor VARCHAR(50) NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    min_capacity INT NOT NULL,
    max_capacity INT NOT NULL,
    amenities VARCHAR(255) NOT NULL
);

CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    room_name VARCHAR(100) NOT NULL,
    booking_date date NOT NULL,
    start_time 
    end_time
    booking_purpose TEXT,
    number_of_people VARCHAR(50) NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (room_name) REFERENCES room(room_name)
);