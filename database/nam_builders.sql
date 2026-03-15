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

-- Service Images Table (multiple images per service)
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

-- Supply Categories Table
CREATE TABLE IF NOT EXISTS supply_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
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
    image_path VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES supply_categories(id) ON DELETE CASCADE
);


CREATE INDEX idx_supplies_category ON supplies(category_id);
CREATE INDEX idx_supplies_active   ON supplies(is_active);

-- NAM Builders: Updates / Posts feature
-- Run this to add the updates tables to the existing nam_builders database

USE nam_builders;

-- Main updates table
CREATE TABLE IF NOT EXISTS updates (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(255)  NOT NULL,
    description TEXT          NOT NULL,
    image_path  VARCHAR(255)  DEFAULT NULL,   -- backward compat: first/cover image
    is_active   TINYINT(1)    DEFAULT 1,
    sort_order  INT           DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Multiple images per post (supports the slideshow in the modal)
CREATE TABLE IF NOT EXISTS update_images (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    update_id   INT           NOT NULL,
    image_path  VARCHAR(255)  NOT NULL,
    sort_order  INT           DEFAULT 0,
    FOREIGN KEY (update_id) REFERENCES updates(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_updates_active     ON updates(is_active, sort_order);
CREATE INDEX IF NOT EXISTS idx_update_images_post ON update_images(update_id, sort_order);

-- NAM Builders: Site Stats feature
-- Run this on your nam_builders database

USE nam_builders;

CREATE TABLE IF NOT EXISTS site_stats (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    stat_key    VARCHAR(60)  NOT NULL UNIQUE,   -- machine key e.g. "projects_completed"
    label       VARCHAR(120) NOT NULL,           -- display label e.g. "Projects Completed"
    value       INT          NOT NULL DEFAULT 0, -- numeric value e.g. 150
    suffix      VARCHAR(10)  DEFAULT '',         -- e.g. "+" or "" or "k+"
    sort_order  INT          DEFAULT 0,
    is_active   TINYINT(1)   DEFAULT 1
);

-- Seed with current hardcoded values
INSERT IGNORE INTO site_stats (stat_key, label, value, suffix, sort_order) VALUES
  ('projects_completed', 'Projects Completed', 150, '+', 1),
  ('happy_clients',      'Happy Clients',       50, '+', 2),
  ('years_experience',   'Years Experience',    15, '+', 3),
  ('service_categories', 'Service Categories',   6, '',  4);

  -- Run this in your database to add the replied status
USE nam_builders;
ALTER TABLE contact_messages ADD COLUMN is_replied TINYINT(1) DEFAULT 0 AFTER is_read;