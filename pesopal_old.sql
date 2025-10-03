-- PesoPal Database Schema
-- Personal Finance Management System
-- Created: October 2, 2025

-- Create database
CREATE DATABASE IF NOT EXISTS pesopal;
USE pesopal;

-- Users table for authentication and user management
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    birthday DATE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Transactions table for income and expenses
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    description VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    transaction_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Budget categories table for budget planning
CREATE TABLE budget_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    allocated_amount DECIMAL(12, 2) NOT NULL,
    spent_amount DECIMAL(12, 2) DEFAULT 0.00,
    time_frame VARCHAR(50) DEFAULT 'Monthly',
    notes TEXT,
    color_class VARCHAR(50) DEFAULT 'bg-gray-500',
    icon_class VARCHAR(100) DEFAULT 'bg-gray-100 text-gray-600 rounded-full p-1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Savings goals table for financial goals tracking
CREATE TABLE savings_goals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    target_amount DECIMAL(12, 2) NOT NULL,
    saved_amount DECIMAL(12, 2) DEFAULT 0.00,
    target_date DATE NOT NULL,
    color VARCHAR(20) DEFAULT '#2563eb',
    icon_class VARCHAR(100) DEFAULT 'bg-blue-100 text-blue-600 rounded-full p-1',
    notes TEXT,
    status ENUM('active', 'completed', 'paused') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Invoices table for bill tracking and payment management
CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    category ENUM('Utilities', 'Rent', 'Insurance', 'Subscriptions', 'Services', 'Medical', 'Other') NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    due_date DATE NOT NULL,
    status ENUM('Unpaid', 'Paid', 'Overdue', 'Upcoming') DEFAULT 'Unpaid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Receipts table for expense documentation
CREATE TABLE receipts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    vendor VARCHAR(255),
    category VARCHAR(100),
    amount DECIMAL(12, 2) NOT NULL,
    receipt_date DATE NOT NULL,
    file_path VARCHAR(500), -- For storing receipt image/document path
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- User preferences and settings
CREATE TABLE user_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    monthly_budget DECIMAL(12, 2) DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'PHP',
    date_format VARCHAR(20) DEFAULT 'YYYY-MM-DD',
    timezone VARCHAR(50) DEFAULT 'Asia/Manila',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample data for testing

-- Sample user
INSERT INTO users (first_name, last_name, email, phone, birthday, password_hash) VALUES
('John', 'Doe', 'john.doe@example.com', '+639171234567', '1990-01-15', '$2y$10$example_hash_here');

-- Sample transactions
INSERT INTO transactions (user_id, type, description, category, amount, transaction_date) VALUES
(1, 'expense', 'Groceries', 'Food', 2200.00, '2025-09-20'),
(1, 'expense', 'Electricity', 'Bills', 1500.00, '2025-09-21'),
(1, 'expense', 'Grab ride', 'Transport', 250.00, '2025-09-22'),
(1, 'expense', 'Netflix', 'Entertainment', 370.00, '2025-09-23'),
(1, 'income', 'Salary', 'Salary', 50000.00, '2025-09-15'),
(1, 'expense', 'Pharmacy', 'Health', 890.00, '2025-09-25');

-- Sample budget categories
INSERT INTO budget_categories (user_id, title, allocated_amount, spent_amount, time_frame, color_class, icon_class) VALUES
(1, 'Food & Dining', 12000.00, 9500.00, 'Monthly', 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1'),
(1, 'Transportation', 8000.00, 4200.00, 'Monthly', 'bg-blue-500', 'bg-blue-100 text-blue-600 rounded-full p-1'),
(1, 'Entertainment', 9000.00, 7200.00, 'Monthly', 'bg-purple-500', 'bg-purple-100 text-purple-600 rounded-full p-1'),
(1, 'Shopping', 9000.00, 3200.00, 'Monthly', 'bg-green-500', 'bg-green-100 text-green-600 rounded-full p-1');

-- Sample savings goals
INSERT INTO savings_goals (user_id, title, target_amount, saved_amount, target_date, color, icon_class) VALUES
(1, 'Emergency Fund', 120000.00, 78500.00, '2024-12-31', '#2563eb', 'bg-blue-100 text-blue-600 rounded-full p-1'),
(1, 'Vacation Fund', 50000.00, 32000.00, '2024-06-30', '#22c55e', 'bg-green-100 text-green-600 rounded-full p-1'),
(1, 'New Laptop', 80000.00, 45000.00, '2024-03-31', '#a855f7', 'bg-purple-100 text-purple-600 rounded-full p-1');

-- Sample invoices
INSERT INTO invoices (user_id, title, category, amount, due_date, status) VALUES
(1, 'Electric Bill - September', 'Utilities', 1500.00, '2025-10-15', 'Unpaid'),
(1, 'Internet Bill', 'Utilities', 2500.00, '2025-10-20', 'Unpaid'),
(1, 'Apartment Rent', 'Rent', 15000.00, '2025-10-01', 'Paid');

-- Sample receipts
INSERT INTO receipts (user_id, title, vendor, category, amount, receipt_date) VALUES
(1, 'Grocery Shopping', 'SM Supermarket', 'Food', 2200.00, '2025-09-20'),
(1, 'Gas Fill-up', 'Shell Station', 'Transport', 1200.00, '2025-09-18'),
(1, 'Medical Checkup', 'Metro Hospital', 'Health', 3500.00, '2025-09-15');

-- User settings
INSERT INTO user_settings (user_id, monthly_budget, currency) VALUES
(1, 45000.00, 'PHP');

-- Create indexes for better performance
CREATE INDEX idx_transactions_user_date ON transactions(user_id, transaction_date);
CREATE INDEX idx_transactions_category ON transactions(category);
CREATE INDEX idx_budget_categories_user ON budget_categories(user_id);
CREATE INDEX idx_savings_goals_user ON savings_goals(user_id);
CREATE INDEX idx_invoices_user_status ON invoices(user_id, status);
CREATE INDEX idx_receipts_user_date ON receipts(user_id, receipt_date);