-- NAM Builders and Supply Corp Database

-- Create Database
CREATE DATABASE IF NOT EXISTS nam_builders;
USE nam_builders;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clients Table
CREATE TABLE IF NOT EXISTS clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    icon_class VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service_needed VARCHAR(150),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$Tbgwlzktw7tTH3MLaZqeqOVjdw.LqqTMzuSo0AiItTCw7Mtv0uuIy', 'admin@nambuilders.com');

-- Insert sample services
INSERT INTO services (service_name, description, sort_order, is_active) VALUES
('General Construction', 'Complete construction solutions for residential, commercial, and industrial projects with expert project management.', 1, 1),
('Renovation & Remodeling', 'Transform your space with our professional renovation and remodeling services tailored to your needs.', 2, 1),
('Electrical Systems', 'Expert electrical installation, maintenance, and repair services ensuring safety and efficiency.', 3, 1),
('Fire Protection', 'Comprehensive fire protection systems installation and maintenance to keep your property safe.', 4, 1),
('Steel Fabrication', 'Custom steel fabrication services for structural and architectural applications.', 5, 1),
('Office Fit-Outs', 'Complete office design and fit-out solutions creating productive work environments.', 6, 1),
('Building Maintenance', 'Regular maintenance services to keep your building in optimal condition year-round.', 7, 1),
('Supply Services', 'Construction materials, electrical components, PPE, and office supplies delivered on time.', 8, 1);



-- Migration: Add service_images table for multiple images per service
-- Run this against your nam_builders database

CREATE TABLE IF NOT EXISTS service_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Index for fast lookups by service
CREATE INDEX idx_service_images_service_id ON service_images(service_id);

-- NAM Builders — Supplies Feature Migration
-- Run this against your nam_builders database

USE nam_builders;

-- Supply Categories Table
CREATE TABLE IF NOT EXISTS supply_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon_class VARCHAR(80) DEFAULT 'fas fa-boxes',
    color_hex VARCHAR(10) DEFAULT '#1565C0',
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Supplies Table
CREATE TABLE IF NOT EXISTS supplies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    supply_name VARCHAR(150) NOT NULL,
    description TEXT,
    unit VARCHAR(50) DEFAULT NULL COMMENT 'e.g. pcs, kg, m, box',
    image_path VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES supply_categories(id) ON DELETE CASCADE
);

-- Indexes for fast lookups
CREATE INDEX idx_supplies_category ON supplies(category_id);
CREATE INDEX idx_supplies_active   ON supplies(is_active);

-- Sample supply categories
INSERT INTO supply_categories (category_name, description, icon_class, color_hex, sort_order) VALUES
('Construction Materials', 'Cement, sand, gravel, hollow blocks, and other structural materials', 'fas fa-hard-hat', '#E65100', 1),
('Electrical Supplies', 'Wires, conduits, outlets, switches, circuit breakers, and lighting', 'fas fa-bolt', '#F9A825', 2),
('Plumbing Materials', 'PVC pipes, fittings, valves, faucets, and waterproofing supplies', 'fas fa-tint', '#1565C0', 3),
('Steel & Metal Works', 'Angle bars, G.I. pipes, MS plates, bolts, nuts, and welding materials', 'fas fa-industry', '#546E7A', 4),
('PPE & Safety Gear', 'Hard hats, safety vests, gloves, goggles, and fall protection equipment', 'fas fa-shield-alt', '#2E7D32', 5),
('Office Supplies', 'Paper, ink, stationery, filing materials, and office accessories', 'fas fa-briefcase', '#6A1B9A', 6);

-- Sample supplies
INSERT INTO supplies (category_id, supply_name, description, unit, sort_order) VALUES
(1, 'Portland Cement', 'Type I Portland Cement 40kg/bag, suitable for general construction use', 'bag', 1),
(1, 'Washed Sand', 'Fine-grade washed sand for masonry and concrete mixing', 'cu.m', 2),
(1, 'Crushed Gravel (3/4")', 'Coarse aggregate for reinforced concrete works', 'cu.m', 3),
(1, 'Hollow Blocks (4")', '4-inch standard hollow concrete blocks for partition walls', 'pcs', 4),
(2, 'THHN Wire (3.5mm²)', 'Solid copper THHN wire, 3.5mm² for branch circuit wiring', 'm', 1),
(2, 'PVC Conduit (1/2")', 'Rigid PVC electrical conduit, 1/2 inch diameter', 'length', 2),
(2, 'Circuit Breaker (20A)', '20-Ampere single-pole circuit breaker for panel boards', 'pcs', 3),
(3, 'PVC Pipe (1/2" x 6m)', 'Schedule 40 PVC pressure pipe, 1/2 inch x 6 meters', 'length', 1),
(3, 'Ball Valve (1/2")', 'Brass full-bore ball valve, 1/2 inch for water line shutoff', 'pcs', 2),
(4, 'Angle Bar (2" x 2" x 6m)', 'Mild steel angle bar, 2" x 2" x 6m for structural framing', 'length', 1),
(4, 'G.I. Pipe (1" x 6m)', 'Galvanized iron pipe, 1 inch x 6 meters, schedule 40', 'length', 2),
(5, 'Safety Hard Hat', 'ANSI-certified class E hard hat with adjustable ratchet suspension', 'pcs', 1),
(5, 'Safety Vest (Hi-Vis)', 'High-visibility ANSI Class 2 reflective safety vest', 'pcs', 2),
(5, 'Cut-Resistant Gloves', 'Level 5 cut-resistant work gloves for steel and glass handling', 'pair', 3),
(6, 'Bond Paper (A4 80gsm)', 'A4 size 80gsm multipurpose copy paper, 500 sheets per ream', 'ream', 1),
(6, 'Ballpen (Blue, 12s)', 'Medium point ballpen, blue ink, box of 12 pieces', 'box', 2);