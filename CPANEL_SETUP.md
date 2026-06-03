# cPanel Deployment Guide — Ankur Infotech Butwal Project

## Quick Setup (5 minutes)

### 1. Database Setup in cPanel

```bash
# In cPanel → MySQL Databases:
1. Click "Create New Database"
   - Name: ankurinfotechcom_admin
   - Create Database

2. Click "Create New User"
   - Username: ankurinfotechcom_admin
   - Password: [Generate strong password, copy to clipboard]
   - Create User

3. Click "Add User to Database"
   - User: ankurinfotechcom_admin
   - Database: ankurinfotechcom_admin
   - Privileges: ALL PRIVILEGES
   - Make Changes

4. Database Connection:
   - Host: localhost
   - Database: ankurinfotechcom_admin
   - User: ankurinfotechcom_admin
   - Password: [The password from step 2]
```

### 2. Clone Repository via Git

```bash
# In cPanel → Git Version Control:
1. Click "Create" → New Repository
2. Clone Repository
   - Clone URL: https://github.com/adakash26434/butwal.git
   - Repository Path: /home/ankurinfotechcom/public_html/butwal
   - Branch: main

# Or via SSH terminal:
cd ~/public_html
git clone https://github.com/adakash26434/butwal.git
cd butwal
git checkout main
git pull origin main
```

### 3. Import Database Schema

```bash
# In cPanel → phpMyAdmin:
1. Select database: ankurinfotechcom_admin
2. Click "Import" tab
3. Choose File: database.sql (from project root)
4. Click "Go"

# Or via SSH terminal:
mysql -u ankurinfotechcom_admin -p ankurinfotechcom_admin < database.sql
# Enter password when prompted
```

### 4. Configure Application

```bash
# Edit includes/config.php:
1. Open file: public_html/butwal/includes/config.php
2. Update database credentials:
   - DB_HOST: localhost
   - DB_NAME: ankurinfotechcom_admin
   - DB_USER: ankurinfotechcom_admin
   - DB_PASS: [Your MySQL password from step 1]
   - SITE_URL: https://ankurinfotech.com.np

3. Save file
```

### 5. Set File Permissions

```bash
# In SSH terminal:
cd ~/public_html/butwal

# Make directories writable
chmod 755 . -R
chmod 777 uploads/ storage/cache/ storage/logs/

# Verify
ls -la | grep -E "uploads|storage"
```

### 6. Test Installation

1. Open browser: https://ankurinfotech.com.np/admin/
2. Login with default admin credentials (set in database)
3. Check Admin Dashboard → Settings → Site Configuration

---

## Git Pull Workflow for Updates

### Pulling Latest Changes

```bash
# In SSH terminal:
cd ~/public_html/butwal

# Check status
git status

# Pull latest changes (from branch: main or v0/aakashpame-9474-ab834206)
git pull origin main

# If there are conflicts:
git stash  # Save local changes
git pull origin main
```

### After Git Pull

If `database.sql` was updated:

```bash
# Backup existing database
mysqldump -u ankurinfotechcom_admin -p ankurinfotechcom_admin > backup_$(date +%s).sql

# Merge new tables/columns (auto-migrations run on first access)
# OR manually import new schema
mysql -u ankurinfotechcom_admin -p ankurinfotechcom_admin < database.sql
```

---

## Storage & Uploads Management

### Directory Structure

```
public_html/butwal/
├── uploads/                 # User uploads (NOT in git)
│   └── .gitkeep            # Placeholder for git
├── assets/
│   ├── images/
│   │   ├── uploads/        # Admin image uploads
│   │   └── .gitkeep
│   └── ...
├── storage/
│   ├── cache/              # Application cache
│   ├── logs/               # Error logs
│   └── .gitkeep files      # Placeholders
└── ...
```

### Important: Storage is NOT Tracked by Git

The `.gitignore` file prevents uploads, images, and logs from being committed to git:

```
# From .gitignore:
uploads/*
!uploads/.gitkeep
storage/*
!storage/.gitkeep
```

This means:
- ✅ Code changes are tracked
- ✅ Database schema is tracked
- ❌ User uploads are NOT tracked
- ❌ Cache/logs are NOT tracked

### Symlink Alternative (Recommended for Large Installs)

Instead of committing uploads, use symlinks:

```bash
# Create storage outside project
mkdir ~/storage/uploads ~/storage/cache

# Create symlinks in project
ln -s ~/storage/uploads public_html/butwal/uploads
ln -s ~/storage/cache public_html/butwal/storage/cache

# Update .gitignore to exclude symlinks
```

---

## Database Migrations

### Automatic Migrations

When the application boots on a new server:

1. `includes/db.php` checks if database exists
2. If tables missing: `includes/sqlite-init.php` runs (creates schema)
3. If new columns needed: `sqliteMigrate()` function adds them
4. ✅ No manual SQL needed on fresh installs

### Manual Migrations (if needed)

Add new ALTER statements to `includes/sqlite-init.php`:

```php
function sqliteMigrate(PDO $pdo): void {
    $migrationSql = [
        // v1.2.0 — Example migration
        "ALTER TABLE table_name ADD COLUMN new_column VARCHAR(255) DEFAULT 'value'",
    ];
    
    foreach ($migrationSql as $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'duplicate column') === false) {
                error_log("Migration error: " . $e->getMessage());
            }
        }
    }
}
```

---

## Troubleshooting

### Database Connection Error

```
Error: SQLSTATE[HY000]: General error: 1030 Got error 28

Solution:
1. Check DB credentials in includes/config.php
2. Verify database exists in cPanel
3. Verify user has ALL privileges
4. Restart MySQL: cPanel → Restart Services → MySQL
```

### Upload Directory Permission Error

```
Error: Permission denied (write to uploads/)

Solution:
chmod 777 uploads/
chmod 777 storage/cache/
chmod 777 storage/logs/
```

### Blank Page or 500 Error

```
Solution:
1. Check error logs: cPanel → Error Log (last 10 lines)
2. Check PHP error log: ~/public_html/error_log
3. Verify PHP version is 7.4+ (needed for features used)
4. Verify all extensions installed:
   - PDO (MySQL)
   - MySQL (ext)
   - JSON
   - SPL
```

### Git Pull Conflicts

```
Error: Your local changes to 'file.php' would be overwritten by merge

Solution:
git stash        # Save local changes
git pull origin main
git stash pop    # Restore local changes (if needed)
```

---

## Best Practices

1. **Always backup before git pull:**
   ```bash
   mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Keep dev-config.php local (not in git):**
   - Add real DB password only to dev-config.php
   - Never commit to git
   - Use includes/config.php as template

3. **Monitor logs regularly:**
   ```bash
   tail -f ~/public_html/error_log
   tail -f ~/public_html/butwal/storage/logs/*.log
   ```

4. **Keep cPanel MySQL user password secure:**
   - Use strong password (20+ characters)
   - Store in password manager
   - Don't share in plain text

5. **Test before going live:**
   - Test on staging first
   - Verify all admin pages work
   - Check user-facing features
   - Monitor error logs after deployment

---

## Support

For issues:
1. Check error logs: `tail -f error_log`
2. Check database: `SELECT * FROM site_settings LIMIT 1`
3. Review git history: `git log --oneline -5`
4. Contact: ankurinfotech8@gmail.com

Last Updated: 2026-06-03
