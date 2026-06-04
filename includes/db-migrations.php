<?php
/**
 * Database Schema Migrations
 * Run this to update table structures for new features
 */

function runDbMigrations() {
    try {
        // Migration 1: Add team category field
        $result = execute("SHOW COLUMNS FROM team_members LIKE 'category'");
        if (empty($result)) {
            execute("ALTER TABLE team_members ADD COLUMN category TEXT DEFAULT 'management' AFTER is_leadership");
        }
    } catch (\Throwable $e) {
        // Column might already exist or SQLite (which doesn't support ALTER)
    }

    try {
        // Migration 2: Add company settings if not exist
        $check = queryOne("SELECT id FROM site_settings WHERE setting_key=?", ['company_phone']);
        if (!$check) {
            saveSetting('company_phone', '');
            saveSetting('company_address', '');
            saveSetting('company_name', defined('SITE_NAME') ? SITE_NAME : 'Company');
            saveSetting('developed_by_name', defined('SITE_NAME') ? SITE_NAME : 'Company');
            saveSetting('developed_by_url', '');
        }
    } catch (\Throwable $e) {
        // site_settings might not be available
    }
}

// Auto-run on page load if needed (optional)
// Call this from config.php or run manually when needed
