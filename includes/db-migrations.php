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
            saveSetting('company_phone', '+977-071-438585, 071-437612');
            saveSetting('company_address', 'Butwal, Rupandehi, Nepal');
            saveSetting('company_name', 'Ankur Infotech Pvt. Ltd.');
            saveSetting('developed_by_name', 'Ankur Infotech Pvt. Ltd.');
            saveSetting('developed_by_url', 'https://ankurinfotech.com.np');
        }
    } catch (\Throwable $e) {
        // site_settings might not be available
    }
}

// Auto-run on page load if needed (optional)
// Call this from config.php or run manually when needed
