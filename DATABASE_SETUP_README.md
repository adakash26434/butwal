# Database Setup Guide

This package contains all database migration files needed to safely set up your Ankur Infotech database.

## Files Included

1. **safe_database_migration.sql** - RECOMMENDED for existing databases
   - Creates missing tables without removing existing data
   - Adds missing columns safely
   - Safe to run multiple times
   - **USE THIS for production/live databases**

2. **fresh_database.sql** - For fresh database setup
   - Complete database schema
   - Initial seed data
   - **USE THIS only for brand new databases**

3. **migrate_services_table.sql** - Services table migration
   - Creates the services table
   - For databases that only need services table added

4. **includes/sqlite-init.php** - SQLite initialization
   - Used by the application for SQLite databases
   - Schema definitions for all tables

5. **scripts/init-pricing-table.php** - Pricing table initialization
   - Auto-populates pricing comparison table data
   - Runs on first admin visit to pricing table page

## How to Import

### Using cPanel/phpMyAdmin:
1. Go to phpMyAdmin in your hosting control panel
2. Select your database
3. Click "Import" tab
4. Choose `safe_database_migration.sql`
5. Click "Go" - it will preserve all existing data

### Using MySQL Command Line:
```bash
mysql -u USERNAME -p DATABASE_NAME < safe_database_migration.sql
```

### Using SSH:
```bash
ssh user@yourserver.com
cd /path/to/your/site
mysql -u USERNAME -p DATABASE_NAME < safe_database_migration.sql
```

## What Gets Updated

✓ Creates missing tables:
  - services
  - pricing_plans
  - pricing_features
  - banners
  - news
  - products
  - testimonials
  - faqs
  - team_members
  - site_settings

✓ Adds missing columns to existing tables

✓ **Preserves all existing data** - Nothing is deleted or removed

## After Import

1. Go to your admin panel
2. Click "Pricing Table" under Content menu
3. The pricing comparison data will auto-populate
4. You can now edit pricing features from the admin interface

## Troubleshooting

**Error: Table already exists**
- This is normal! safe_database_migration.sql uses `CREATE TABLE IF NOT EXISTS`
- Just ignore these messages and continue

**Error: Column already exists**
- Also normal! It uses `ADD COLUMN IF NOT EXISTS`
- Continue - existing data is preserved

**Services still showing as not found**
- Run the import again through phpMyAdmin
- Or contact support if issues persist

## Support

For issues with database setup, check:
1. Verify your database credentials are correct
2. Ensure you're connected to the correct database
3. Check that your hosting allows SQL imports
4. Contact your hosting provider if import fails
