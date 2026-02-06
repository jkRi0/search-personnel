-- Run this in phpMyAdmin or MySQL client

CREATE DATABASE IF NOT EXISTS personnel_db;
USE personnel_db;

CREATE TABLE offices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    office_name VARCHAR(150) NOT NULL
);

CREATE TABLE personnel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    office_id INT NOT NULL,
    FOREIGN KEY (office_id) REFERENCES offices(id)
);

-- Sample data
INSERT INTO offices (office_name) VALUES
('Human Resources'),
('Finance Office'),
('IT Department');

INSERT INTO personnel (full_name, position, office_id) VALUES
('Juan Dela Cruz', 'Clerk', 1),
('Maria Santos', 'Accountant', 2),
('Pedro Reyes', 'IT Support', 3);
