-- SQL script to create database and a dedicated app user
-- Edit the password before running, e.g. replace 'changeme' with a strong password

CREATE DATABASE IF NOT EXISTS pdo_project CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Create a dedicated user for the app (change the password)
CREATE USER IF NOT EXISTS 'appuser'@'localhost' IDENTIFIED BY 'changeme';

GRANT ALL PRIVILEGES ON pdo_project.* TO 'appuser'@'localhost';
FLUSH PRIVILEGES;

-- To run (as a system user with MySQL access):
-- sudo mysql < scripts/setup_db.sql
