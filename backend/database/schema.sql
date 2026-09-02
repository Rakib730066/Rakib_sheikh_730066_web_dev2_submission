-- ============================================
-- Smart Event Management System Database
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS developmentdb;
USE developmentdb;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- EVENTS TABLE
-- ============================================
CREATE TABLE events (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description LONGTEXT NOT NULL,
  date DATETIME NOT NULL,
  location VARCHAR(255) NOT NULL,
  category VARCHAR(100) NOT NULL,
  capacity INT NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_category (category),
  INDEX idx_date (date),
  FULLTEXT INDEX ft_search (title, description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- REGISTRATIONS TABLE
-- ============================================
CREATE TABLE registrations (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  UNIQUE KEY unique_registration (user_id, event_id),
  INDEX idx_user_id (user_id),
  INDEX idx_event_id (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEED DATA (for testing)
-- ============================================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$2y$10$cB7iSrU0fj/cfNbE/YdQnuGdMLaeqQEsXgqV9ELH6DxFB.4GhGpkm', 'admin');

-- Regular users
INSERT INTO users (name, email, password, role) VALUES 
('John Doe', 'john@example.com', '$2y$10$xTra3ATCtqD5epA2LGAfg.4iednz4NJ4sf7f2Iqc8QLupPkaaAjwm', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$xTra3ATCtqD5epA2LGAfg.4iednz4NJ4sf7f2Iqc8QLupPkaaAjwm', 'user'),
('Bob Wilson', 'bob@example.com', '$2y$10$xTra3ATCtqD5epA2LGAfg.4iednz4NJ4sf7f2Iqc8QLupPkaaAjwm', 'user');

-- Sample events (created by admin)
INSERT INTO events (title, description, date, location, category, created_by) VALUES
('Web Development Workshop', 'Learn modern web development techniques including Vue.js, React, and vanilla JavaScript. Perfect for beginners and intermediate developers.', '2026-08-15 14:00:00', 'Tech Hub Downtown', 'workshop', 1),
('JavaScript Conference 2026', 'Annual conference for JavaScript developers. Featuring keynote speakers from major companies and hands-on workshops.', '2026-09-10 09:00:00', 'Convention Center', 'conference', 1),
('React Meetup', 'Monthly React community meetup for sharing experiences and learning new patterns. All skill levels welcome.', '2026-08-28 18:00:00', 'Coffee Shop Downtown', 'meetup', 1),
('PHP Advanced Patterns', 'Deep dive into advanced PHP design patterns and best practices for enterprise applications.', '2026-10-05 10:00:00', 'Developer Academy', 'workshop', 1),
('Database Design Masterclass', 'Learn database design principles, optimization, and best practices for scalable applications.', '2026-11-20 15:00:00', 'Tech Venue', 'masterclass', 1);

-- Sample registrations
INSERT INTO registrations (user_id, event_id) VALUES 
(2, 1),  -- John registered for Web Development Workshop
(2, 2),  -- John registered for JavaScript Conference
(3, 1),  -- Jane registered for Web Development Workshop
(3, 3),  -- Jane registered for React Meetup
(4, 2),  -- Bob registered for JavaScript Conference
(4, 4);  -- Bob registered for PHP Advanced Patterns
