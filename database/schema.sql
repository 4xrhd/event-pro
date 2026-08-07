-- EventPro Database Schema
-- Run this script to create the database and all required tables

CREATE DATABASE IF NOT EXISTS eventpro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventpro;

-- Users table with role support
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer', 'staff') NOT NULL DEFAULT 'customer',
    reset_token VARCHAR(255) NULL,
    reset_token_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table with user relationship
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    event_date DATETIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    capacity INT NULL,
    status ENUM('draft', 'published', 'cancelled', 'completed') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_event_date (event_date),
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets table with proper relationships
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('pending', 'completed', 'refunded') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    stripe_payment_id VARCHAR(255) NULL,
    is_checked_in BOOLEAN NOT NULL DEFAULT FALSE,
    checked_in_at DATETIME NULL,
    checked_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_event_id (event_id),
    INDEX idx_user_id (user_id),
    INDEX idx_payment_status (payment_status),
    INDEX idx_is_checked_in (is_checked_in)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales/Analytics tracking table
CREATE TABLE IF NOT EXISTS sales_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    event_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    action ENUM('sale', 'refund') NOT NULL DEFAULT 'sale',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
-- Password: Admin@123 (hashed with PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@eventpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Staff User', 'staff@eventpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff'),
('Customer User', 'customer@eventpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

-- Insert sample events
INSERT INTO events (user_id, title, description, event_date, venue, price, capacity, status) VALUES
(1, 'Tech Conference 2026', 'Annual technology conference featuring the latest trends in software development.', '2026-06-15 09:00:00', 'Convention Center, Hall A', 150.00, 500, 'published'),
(1, 'Music Festival', 'Live music performances from local and international artists.', '2026-07-20 18:00:00', 'City Park Amphitheater', 75.00, 2000, 'published'),
(3, 'Art Exhibition', 'Contemporary art showcase featuring emerging artists.', '2026-05-10 10:00:00', 'Downtown Gallery', 25.00, 200, 'published');

-- Insert sample tickets
INSERT INTO tickets (event_id, user_id, name, email, quantity, total_amount, payment_status, payment_method) VALUES
(1, 3, 'John Doe', 'john@example.com', 2, 300.00, 'completed', 'stripe'),
(2, 3, 'Jane Smith', 'jane@example.com', 4, 300.00, 'completed', 'cash');
