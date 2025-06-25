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

INSERT INTO admin (admin_id, admin_email, password) 
VALUES ('ADM00001', 'taylorsadmin@taylors.edu.my', '$2y$10$ESXmQGWlVwtLwh5lijFM8uTOFzKMSpR5DwKb3VfGucsx2FbyU7iuG');
-- admin password: admin123

CREATE TABLE room (
    room_id VARCHAR(20) NOT NULL UNIQUE PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL UNIQUE,
    `block` VARCHAR(20) NOT NULL,
    floor VARCHAR(20) NOT NULL,
    `type` VARCHAR(100) NOT NULL,
    min_occupancy INT NOT NULL,
    max_occupancy INT NOT NULL,
    amenities VARCHAR(255) NOT NULL
);

INSERT INTO room (room_id, room_name, `block`, floor, `type`, min_occupancy, max_occupancy, amenities) 
VALUES 
('DR3.1', 'Discussion Room 3.1', 'C', '3', 'Discussion Room', 4, 8, 'Whiteboard, TV with Wireless Display'),
('C7.01', 'Computer Lab C7.01', 'C', '7.01', 'Computer Lab', 1, 30, 'Whiteboard, TV with Wireless Display, 30 Computers'),
('D8.01', 'Classroom D8.01', 'D', '8.01', 'Classroom', 1, 30, 'Whiteboard, TV with Wireless Display'),
('LT2', 'Lecture Theatre 2', 'B', '1', 'Lecture Theatre', 1, 200, 'Whiteboard, TV with Wireless Display');

CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) NOT NULL,
    room_id VARCHAR(20) NOT NULL,
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
    FOREIGN KEY (room_id) REFERENCES room(room_id),
    FOREIGN KEY (room_name) REFERENCES room(room_name),
    FOREIGN KEY (approved_by_admin) REFERENCES admin(admin_id)
);

CREATE TABLE class_timetable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id VARCHAR(20) NOT NULL,
    day_of_week ENUM('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (room_id) REFERENCES room(room_id)
);

INSERT INTO class_timetable (room_id, day_of_week, start_time, end_time) 
VALUES 
('LT2', 'Monday', '09:00:00', '11:00:00'),
('LT2', 'Monday', '14:00:00', '16:00:00'),
('LT2', 'Tuesday', '10:00:00', '12:00:00'),
('LT2', 'Tuesday', '13:00:00', '14:00:00'),
('LT2', 'Wednesday', '09:00:00', '11:00:00'),
('LT2', 'Thursday', '12:00:00', '17:00:00'),
('LT2', 'Friday', '11:00:00', '13:00:00')
;