-- ============================================================
-- SAFE DATABASE MIGRATION — Ankur Infotech Pvt. Ltd.
-- ============================================================
-- Purpose: Create missing tables and add missing columns
-- without removing existing data
-- MySQL 8.0+ / MariaDB 10.5+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';

-- ══════════════════════════════════════════════════════════════
-- 1. CREATE SERVICES TABLE IF NOT EXISTS
-- ══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS services (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title           VARCHAR(200)  NOT NULL,
  slug            VARCHAR(200)  NOT NULL UNIQUE,
  tagline         VARCHAR(300)  DEFAULT NULL,
  summary         TEXT          DEFAULT NULL,
  description     LONGTEXT      DEFAULT NULL,
  icon            VARCHAR(20)   DEFAULT '⚙️',
  lucide_icon     VARCHAR(100)  DEFAULT 'settings',
  icon_color      VARCHAR(50)   DEFAULT 'blue',
  badge           VARCHAR(100)  DEFAULT NULL,
  price_from      VARCHAR(100)  DEFAULT NULL,
  features        JSON          DEFAULT NULL,
  highlights      JSON          DEFAULT NULL,
  screenshot_url  VARCHAR(500)  DEFAULT NULL,
  active          TINYINT(1)    NOT NULL DEFAULT 1,
  position        SMALLINT      NOT NULL DEFAULT 0,
  created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug   (slug),
  INDEX idx_active (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- 2. ADD MISSING COLUMNS TO SERVICES TABLE (if they don't exist)
-- ══════════════════════════════════════════════════════════════
ALTER TABLE services ADD COLUMN IF NOT EXISTS tagline VARCHAR(300) DEFAULT NULL AFTER slug;
ALTER TABLE services ADD COLUMN IF NOT EXISTS lucide_icon VARCHAR(100) DEFAULT 'settings' AFTER icon;
ALTER TABLE services ADD COLUMN IF NOT EXISTS badge VARCHAR(100) DEFAULT NULL AFTER icon_color;
ALTER TABLE services ADD COLUMN IF NOT EXISTS price_from VARCHAR(100) DEFAULT NULL AFTER badge;
ALTER TABLE services ADD COLUMN IF NOT EXISTS highlights JSON DEFAULT NULL AFTER features;
ALTER TABLE services ADD COLUMN IF NOT EXISTS screenshot_url VARCHAR(500) DEFAULT NULL AFTER highlights;

-- ══════════════════════════════════════════════════════════════
-- 3. CREATE PRICING_COMPARISON_DATA TABLE IF NOT EXISTS
-- ══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS pricing_comparison_data (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feature_id  INT UNSIGNED  NOT NULL,
  plan_id     INT UNSIGNED  NOT NULL,
  value       TEXT          DEFAULT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_feature_plan (feature_id, plan_id),
  INDEX idx_feature (feature_id),
  INDEX idx_plan (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- 4. CREATE PRICING_FEATURES TABLE IF NOT EXISTS
-- ══════════════════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS pricing_features (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(300)  NOT NULL,
  description TEXT          DEFAULT NULL,
  position    SMALLINT      NOT NULL DEFAULT 0,
  active      TINYINT(1)    NOT NULL DEFAULT 1,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- 5. CREATE MISSING ADMIN TABLES IF NOT EXISTS
-- ══════════════════════════════════════════════════════════════

-- Banners table
CREATE TABLE IF NOT EXISTS banners (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(300)  NOT NULL,
  subtitle    VARCHAR(500)  DEFAULT NULL,
  image_url   VARCHAR(500)  DEFAULT NULL,
  image_alt   VARCHAR(300)  DEFAULT NULL,
  cta_label   VARCHAR(100)  DEFAULT NULL,
  cta_url     VARCHAR(300)  DEFAULT NULL,
  active      TINYINT(1)    NOT NULL DEFAULT 1,
  position    SMALLINT      NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- News/Blog table
CREATE TABLE IF NOT EXISTS news (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(400)  NOT NULL,
  slug         VARCHAR(400)  NOT NULL UNIQUE,
  excerpt      TEXT          DEFAULT NULL,
  content      LONGTEXT      DEFAULT NULL,
  image_url    VARCHAR(500)  DEFAULT NULL,
  cover_url    VARCHAR(500)  DEFAULT NULL,
  author_id    INT UNSIGNED  DEFAULT NULL,
  author_name  VARCHAR(150)  DEFAULT 'Ankur Infotech Pvt. Ltd.',
  author_title VARCHAR(150)  DEFAULT 'Team',
  read_time    TINYINT UNSIGNED DEFAULT NULL,
  category     VARCHAR(100)  DEFAULT 'News',
  tags         JSON          DEFAULT NULL,
  featured     TINYINT(1)    NOT NULL DEFAULT 0,
  active       TINYINT(1)    NOT NULL DEFAULT 1,
  published    TINYINT(1)    NOT NULL DEFAULT 0,
  published_at DATETIME      DEFAULT NULL,
  meta_title   VARCHAR(300)  DEFAULT NULL,
  meta_desc    VARCHAR(500)  DEFAULT NULL,
  views        INT UNSIGNED  NOT NULL DEFAULT 0,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug      (slug),
  INDEX idx_active    (active, published_at DESC),
  INDEX idx_published (published, published_at DESC),
  INDEX idx_category  (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE IF NOT EXISTS products (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name                VARCHAR(200)  NOT NULL,
  slug                VARCHAR(200)  NOT NULL UNIQUE,
  tagline             VARCHAR(300)  DEFAULT NULL,
  summary             TEXT          DEFAULT NULL,
  description         LONGTEXT      DEFAULT NULL,
  icon                VARCHAR(20)   DEFAULT '📦',
  lucide_icon         VARCHAR(60)   DEFAULT 'package',
  icon_color          VARCHAR(30)   DEFAULT 'blue',
  badge               VARCHAR(60)   DEFAULT NULL,
  category            VARCHAR(100)  DEFAULT NULL,
  accent              VARCHAR(100)  DEFAULT 'from-blue-500 to-indigo-600',
  highlights          JSON          DEFAULT NULL,
  features            JSON          DEFAULT NULL,
  modules             JSON          DEFAULT NULL,
  tech_stack          JSON          DEFAULT NULL,
  screenshots         JSON          DEFAULT NULL,
  compliance          JSON          DEFAULT NULL,
  faqs                JSON          DEFAULT NULL,
  price_from          DECIMAL(12,2) DEFAULT NULL,
  show_on_home        TINYINT(1)    NOT NULL DEFAULT 1,
  home_position       SMALLINT      NOT NULL DEFAULT 0,
  home_card_wide      TINYINT(1)    NOT NULL DEFAULT 0,
  home_card_dark      TINYINT(1)    NOT NULL DEFAULT 0,
  home_bg_css         TEXT          DEFAULT NULL,
  demo_screenshot_url VARCHAR(500)  DEFAULT NULL,
  tab_label           VARCHAR(100)  DEFAULT NULL,
  sort_order          SMALLINT      NOT NULL DEFAULT 0,
  active              TINYINT(1)    NOT NULL DEFAULT 1,
  position            SMALLINT      NOT NULL DEFAULT 0,
  created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug   (slug),
  INDEX idx_active (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Testimonials table
CREATE TABLE IF NOT EXISTS testimonials (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  author_name VARCHAR(150)  NOT NULL,
  author_role VARCHAR(200)  DEFAULT NULL,
  author_org  VARCHAR(200)  DEFAULT NULL,
  photo_url   VARCHAR(500)  DEFAULT NULL,
  quote       TEXT          NOT NULL,
  rating      TINYINT       NOT NULL DEFAULT 5 CHECK (rating BETWEEN 1 AND 5),
  product_ref VARCHAR(100)  DEFAULT NULL,
  active      TINYINT(1)    NOT NULL DEFAULT 1,
  position    SMALLINT      NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQs table
CREATE TABLE IF NOT EXISTS faqs (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category   VARCHAR(100)  NOT NULL DEFAULT 'General',
  question   TEXT          NOT NULL,
  answer     TEXT          NOT NULL,
  active     TINYINT(1)    NOT NULL DEFAULT 1,
  position   SMALLINT      NOT NULL DEFAULT 0,
  created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active_cat (active, category, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Team Members table
CREATE TABLE IF NOT EXISTS team_members (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120)  NOT NULL,
  role          VARCHAR(120)  NOT NULL DEFAULT '',
  bio           TEXT          DEFAULT NULL,
  photo_url     VARCHAR(500)  DEFAULT NULL,
  email         VARCHAR(180)  DEFAULT NULL,
  linkedin_url  VARCHAR(500)  DEFAULT NULL,
  twitter_url   VARCHAR(500)  DEFAULT NULL,
  github_url    VARCHAR(500)  DEFAULT NULL,
  is_leadership TINYINT(1)    NOT NULL DEFAULT 0,
  active        TINYINT(1)    NOT NULL DEFAULT 1,
  position      SMALLINT      NOT NULL DEFAULT 0,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_active_pos (active, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pricing Plans table
CREATE TABLE IF NOT EXISTS pricing_plans (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(100)  NOT NULL,
  tag         VARCHAR(200)  DEFAULT NULL,
  price_label VARCHAR(100)  NOT NULL DEFAULT 'Contact us',
  period      VARCHAR(60)   DEFAULT '/ month',
  cta_label   VARCHAR(80)   DEFAULT 'Get started',
  cta_url     VARCHAR(300)  DEFAULT NULL,
  is_popular  TINYINT(1)    NOT NULL DEFAULT 0,
  features    JSON          DEFAULT NULL,
  active      TINYINT(1)    NOT NULL DEFAULT 1,
  position    SMALLINT      NOT NULL DEFAULT 0,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Settings table
CREATE TABLE IF NOT EXISTS site_settings (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key  VARCHAR(100) NOT NULL UNIQUE,
  setting_val  LONGTEXT     DEFAULT NULL,
  updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- MIGRATION COMPLETE
-- ══════════════════════════════════════════════════════════════
-- All missing tables and columns have been created/added safely
-- No existing data has been removed or modified
-- ══════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 1;
