-- Ankur Infotech Pvt. Ltd. — Butwal Project Database Schema
-- MySQL/MariaDB compatible database schema for cPanel deployment
-- Last Updated: 2026-06-03
-- Compatible with: MySQL 5.7+, MariaDB 10.3+
--
-- INSTRUCTIONS FOR CPANEL SETUP:
-- 1. Create database in cPanel: ankurinfotechcom_admin
-- 2. Create user: ankurinfotechcom_admin with strong password
-- 3. Grant ALL privileges on ankurinfotechcom_admin.* to ankurinfotechcom_admin@localhost
-- 4. Import this file: mysql -u ankurinfotechcom_admin -p ankurinfotechcom_admin < database.sql
-- 5. Update includes/config.php with DB_PASS from step 2
-- 6. Clear browser cache and test admin login

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET COLLATION_CONNECTION = utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- CORE TABLES
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  display_name VARCHAR(255) NOT NULL DEFAULT '',
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'client',
  avatar_url VARCHAR(255),
  phone VARCHAR(20),
  org_name VARCHAR(255),
  district VARCHAR(100),
  bio TEXT,
  email_verified TINYINT NOT NULL DEFAULT 0,
  active TINYINT NOT NULL DEFAULT 1,
  last_login_at DATETIME,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role),
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_val LONGTEXT,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS team_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  role VARCHAR(100) NOT NULL DEFAULT '',
  bio TEXT,
  photo_url VARCHAR(255),
  email VARCHAR(255),
  linkedin_url VARCHAR(255),
  is_leadership TINYINT NOT NULL DEFAULT 0,
  category VARCHAR(50) NOT NULL DEFAULT 'management',
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active),
  INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- PRODUCTS & SERVICES
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  tagline VARCHAR(255) DEFAULT '',
  summary TEXT,
  description LONGTEXT,
  icon VARCHAR(50) DEFAULT 'settings',
  lucide_icon VARCHAR(50) DEFAULT 'layers',
  icon_color VARCHAR(50) DEFAULT 'blue',
  badge VARCHAR(50),
  price_from DECIMAL(12,2),
  highlights JSON,
  features JSON,
  screenshot_url VARCHAR(255),
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active),
  INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  tagline VARCHAR(255),
  summary TEXT,
  description LONGTEXT,
  icon VARCHAR(50) DEFAULT 'box',
  lucide_icon VARCHAR(50) DEFAULT 'package',
  icon_color VARCHAR(50) DEFAULT 'blue',
  badge VARCHAR(50),
  category VARCHAR(100),
  highlights JSON,
  features JSON,
  price_from DECIMAL(12,2),
  show_on_home TINYINT NOT NULL DEFAULT 0,
  home_position INT NOT NULL DEFAULT 0,
  home_card_wide TINYINT NOT NULL DEFAULT 0,
  home_card_dark TINYINT NOT NULL DEFAULT 0,
  home_bg_css TEXT,
  demo_screenshot_url VARCHAR(255),
  tab_label VARCHAR(100),
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active),
  INDEX idx_slug (slug),
  INDEX idx_home (show_on_home)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pricing_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  tag VARCHAR(50),
  price_label VARCHAR(100) NOT NULL DEFAULT 'Contact us',
  period VARCHAR(50) DEFAULT '/ month',
  cta_label VARCHAR(100) DEFAULT 'Get started',
  cta_url VARCHAR(255),
  is_popular TINYINT NOT NULL DEFAULT 0,
  features JSON,
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- CONTENT MANAGEMENT
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS news (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  excerpt TEXT,
  content LONGTEXT,
  image_url VARCHAR(255),
  cover_url VARCHAR(255),
  author_name VARCHAR(255),
  author_title VARCHAR(100),
  read_time INT,
  category VARCHAR(100) DEFAULT 'News',
  tags VARCHAR(255),
  featured TINYINT NOT NULL DEFAULT 0,
  active TINYINT NOT NULL DEFAULT 1,
  published TINYINT NOT NULL DEFAULT 0,
  published_at DATETIME,
  views INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_active (active),
  INDEX idx_published (published),
  INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS faqs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(100) NOT NULL DEFAULT 'General',
  question VARCHAR(255) NOT NULL,
  answer LONGTEXT NOT NULL,
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- BUSINESS ENTITIES
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS partners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  logo_url VARCHAR(255),
  url VARCHAR(255),
  type VARCHAR(50) NOT NULL DEFAULT 'client',
  district VARCHAR(100),
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type (type),
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  org_name VARCHAR(255) NOT NULL,
  logo_url VARCHAR(255),
  contact_email VARCHAR(255),
  contact_phone VARCHAR(20),
  status VARCHAR(50) NOT NULL DEFAULT 'active',
  district VARCHAR(100),
  province VARCHAR(100),
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author_name VARCHAR(255) NOT NULL,
  author_role VARCHAR(100),
  author_org VARCHAR(255),
  photo_url VARCHAR(255),
  quote TEXT NOT NULL,
  rating INT NOT NULL DEFAULT 5,
  product_ref VARCHAR(100),
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS portfolio (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  client_name VARCHAR(255),
  category VARCHAR(100),
  excerpt TEXT,
  description LONGTEXT,
  image_url VARCHAR(255),
  result_metric VARCHAR(255),
  featured TINYINT NOT NULL DEFAULT 0,
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  published_at DATETIME,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  description TEXT,
  image_url VARCHAR(255) NOT NULL,
  category VARCHAR(100) DEFAULT 'General',
  active TINYINT NOT NULL DEFAULT 1,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- SUBMISSIONS & LEADS
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS contact_submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  org_name VARCHAR(255),
  subject VARCHAR(255) DEFAULT 'General Enquiry',
  message LONGTEXT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'new',
  notes LONGTEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS demo_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product VARCHAR(255) NOT NULL,
  org_name VARCHAR(255) NOT NULL,
  contact_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  members INT,
  message LONGTEXT,
  status VARCHAR(50) NOT NULL DEFAULT 'new',
  notes LONGTEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- ADMIN & UTILITIES
-- ══════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS job_listings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  department VARCHAR(100),
  location VARCHAR(255) DEFAULT 'Kathmandu, Nepal',
  type VARCHAR(50) NOT NULL DEFAULT 'full-time',
  experience VARCHAR(100),
  salary_range VARCHAR(100),
  short_desc TEXT,
  description LONGTEXT,
  requirements LONGTEXT,
  perks LONGTEXT,
  deadline DATE,
  active TINYINT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (slug),
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS announcements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  body LONGTEXT,
  type VARCHAR(50) NOT NULL DEFAULT 'info',
  scope VARCHAR(50) NOT NULL DEFAULT 'banner',
  page_target VARCHAR(255),
  btn_text VARCHAR(100),
  btn_url VARCHAR(255),
  active TINYINT NOT NULL DEFAULT 1,
  dismissible TINYINT NOT NULL DEFAULT 1,
  starts_at DATETIME,
  ends_at DATETIME,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- END OF DATABASE SCHEMA
-- ══════════════════════════════════════════════════════════════════════════════
