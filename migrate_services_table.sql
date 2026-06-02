-- ============================================================
-- Migration: Add missing columns to services table
-- Run this if you get "services table not found" or column errors
-- ============================================================

-- Add missing columns to services table
ALTER TABLE services ADD COLUMN IF NOT EXISTS tagline VARCHAR(300) DEFAULT NULL AFTER slug;
ALTER TABLE services ADD COLUMN IF NOT EXISTS lucide_icon VARCHAR(100) DEFAULT 'settings' AFTER icon;
ALTER TABLE services ADD COLUMN IF NOT EXISTS badge VARCHAR(100) DEFAULT NULL AFTER icon_color;
ALTER TABLE services ADD COLUMN IF NOT EXISTS price_from VARCHAR(100) DEFAULT NULL AFTER badge;
ALTER TABLE services ADD COLUMN IF NOT EXISTS highlights JSON DEFAULT NULL AFTER features;
ALTER TABLE services ADD COLUMN IF NOT EXISTS screenshot_url VARCHAR(500) DEFAULT NULL AFTER highlights;

-- Verify structure
DESC services;
