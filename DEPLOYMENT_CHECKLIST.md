# Butwal Project — cPanel Deployment Checklist

## Pre-Deployment (Before Going Live)

### 1. Database Setup
- [ ] Create MySQL database in cPanel: `ankurinfotechcom_admin`
- [ ] Create MySQL user: `ankurinfotechcom_admin` with strong password (20+ chars)
- [ ] Add user to database with ALL privileges
- [ ] Test connection with phpMyAdmin
- [ ] **Save credentials securely** (password manager)

### 2. Git Clone
- [ ] Clone repository: `git clone https://github.com/adakash26434/butwal.git`
- [ ] Verify branch: `git status` (should show `main` or deployment branch)
- [ ] Check latest commit: `git log -1` (verify you have latest code)

### 3. Database Import
- [ ] Backup any existing database (if upgrading)
- [ ] Import schema: Import `database.sql` via phpMyAdmin
- [ ] Verify tables created: 20+ tables should exist
- [ ] Check data: Select from `site_settings` — should return empty

### 4. Configuration
- [ ] Copy `includes/config.php` and update:
  - [ ] `DB_HOST`: localhost
  - [ ] `DB_NAME`: ankurinfotechcom_admin
  - [ ] `DB_USER`: ankurinfotechcom_admin
  - [ ] `DB_PASS`: [Your password from step 1]
  - [ ] `SITE_URL`: https://ankurinfotech.com.np
  - [ ] `SITE_NAME`: Ankur Infotech Pvt. Ltd.

- [ ] Create `includes/dev-config.php` (local dev only):
  ```php
  <?php
  // Only for local/dev environment
  // Override any settings here
  ```

### 5. File Permissions
- [ ] `chmod 755` root directory and all files (default)
- [ ] `chmod 777` uploads/ directory (writable)
- [ ] `chmod 777` storage/cache/ directory (writable)
- [ ] `chmod 777` storage/logs/ directory (writable)
- [ ] Verify in SSH: `ls -la uploads/ | head -3`

### 6. SSL Certificate
- [ ] HTTPS enabled (Check browser — lock icon)
- [ ] SSL certificate auto-renewed (cPanel AutoSSL)
- [ ] Force HTTPS: `.htaccess` contains redirect rule

### 7. Initial Test
- [ ] Visit homepage: https://ankurinfotech.com.np
- [ ] Homepage loads without errors
- [ ] All navigation links work
- [ ] No console errors (F12 → Console)
- [ ] Images load correctly

### 8. Admin Login
- [ ] Visit admin: https://ankurinfotech.com.np/admin/
- [ ] Set initial admin password in database:
  ```sql
  INSERT INTO users (display_name, email, password_hash, role, active) 
  VALUES ('Admin', 'admin@example.com', '[bcrypt hash]', 'admin', 1);
  ```
- [ ] Login with admin credentials
- [ ] Dashboard loads without database errors

---

## Post-Deployment

### 1. Configure Company Details
- [ ] Admin → Settings → Company Settings
- [ ] Update company name
- [ ] Update phone number
- [ ] Update address
- [ ] Update tagline
- [ ] Set developer name & URL
- [ ] Click Save

### 2. Seed Initial Content
- [ ] Add team members: Admin → Team
- [ ] Add services: Admin → Services
- [ ] Add products: Admin → Products
- [ ] Add pricing plans: Admin → Pricing Plans
- [ ] Add partners/clients: Admin → Partners

### 3. Verify Public Pages
- [ ] Homepage displays company name
- [ ] Team page shows team members
- [ ] Services page shows all services
- [ ] Pricing page shows pricing plans
- [ ] Partners page shows partners
- [ ] Footer shows company details
- [ ] Footer shows developer attribution

### 4. Form Testing
- [ ] Contact form submits successfully
- [ ] Demo request form submits
- [ ] Submissions appear in Admin → Leads
- [ ] Email notifications work (if configured)

### 5. Monitoring
- [ ] Check error log daily: `tail -f error_log`
- [ ] Monitor performance: Admin → Dashboard
- [ ] Review contact submissions: Admin → Leads
- [ ] Check visitor analytics

---

## Git Workflow (For Future Updates)

### Pulling Latest Code

```bash
# In SSH terminal:
cd ~/public_html/butwal

# 1. Check what changed
git status

# 2. Backup database (if schema might change)
mysqldump -u admin -p admin_db > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Pull latest
git pull origin main

# 4. If new migrations needed, they auto-run on next page load
# No manual SQL needed!
```

### If Conflicts During Pull

```bash
# Save your local changes
git stash

# Pull latest
git pull origin main

# Reapply your changes (if needed)
git stash pop
```

### Rollback (if something breaks)

```bash
# See recent commits
git log --oneline -10

# Rollback to previous commit
git reset --hard HEAD~1

# Or go to specific commit
git checkout [commit-hash]
```

---

## Troubleshooting During Deployment

### "Connection refused" (MySQL)
```bash
# Solution:
1. Verify MySQL is running: cPanel → Restart Services → MySQL
2. Verify credentials in includes/config.php
3. Test in phpMyAdmin
```

### "Permission denied" on uploads
```bash
# Solution:
chmod 777 uploads/ storage/cache/ storage/logs/
```

### "500 Internal Server Error"
```bash
# Check logs:
tail -f error_log
tail -f storage/logs/*.log

# Common causes:
- Missing database tables (re-import database.sql)
- Wrong DB credentials
- PHP version < 7.4
```

### "Blank white page"
```bash
# Enable debug:
1. SSH into server
2. tail -f error_log
3. Reload page
4. Check error_log output
```

---

## Monthly Maintenance

### Week 1
- [ ] Review error logs
- [ ] Check disk space usage
- [ ] Verify SSL certificate auto-renewal

### Week 2
- [ ] Backup database: `mysqldump ... > backup.sql`
- [ ] Download backup to local machine
- [ ] Review any user submissions

### Week 3
- [ ] Check for git updates: `git remote -v && git fetch`
- [ ] Review security notices
- [ ] Update any outdated packages

### Week 4
- [ ] Full system backup via cPanel
- [ ] Verify disaster recovery procedures
- [ ] Document any changes made

---

## Performance Tips

### Optimize Images
```bash
# Reduce image file sizes
find uploads/ -name "*.jpg" -exec jpegoptim {} \;
find uploads/ -name "*.png" -exec pngquant --ext .png -f {} \;
```

### Enable Caching
In `includes/config.php`:
```php
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600);  // 1 hour
```

### Monitor Database
```sql
-- Check table sizes
SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES WHERE table_schema = 'ankurinfotechcom_admin'
ORDER BY size_mb DESC;
```

---

## Backup Strategy

### Automated Daily Backups (cPanel)
- Enable in cPanel → Backup Settings
- Email backups weekly
- Keep 30-day retention

### Manual Backup Before Major Changes
```bash
# Database backup
mysqldump -u admin -p admin_db > backup_$(date +%Y%m%d).sql

# Full project backup
zip -r backup_project_$(date +%Y%m%d).zip ~/public_html/butwal/

# Store in secure location
```

---

## Security Checklist

- [ ] Change admin password immediately after setup
- [ ] Remove/disable test admin accounts
- [ ] Enable HTTPS (already configured)
- [ ] Keep PHP updated to latest 7.4+
- [ ] Keep MySQL updated
- [ ] Review user access: Admin → Users
- [ ] Configure backup retention
- [ ] Monitor error logs for suspicious activity
- [ ] Never commit DB passwords to git
- [ ] Use strong passwords (20+ chars)

---

## After Going Live

### Day 1
- [ ] Monitor all pages load correctly
- [ ] Check error logs: `tail -f error_log`
- [ ] Test contact form submission
- [ ] Verify email notifications

### Week 1
- [ ] Google Search Console: Add sitemap
- [ ] Google Analytics: Verify tracking
- [ ] Test on mobile devices
- [ ] Check SSL certificate validity

### Month 1
- [ ] Review analytics
- [ ] Check error logs for patterns
- [ ] Optimize any slow pages
- [ ] Update DNS/SSL records if needed

---

## Emergency Contacts

- **Server Support:** cPanel support (from hosting provider)
- **Project Contact:** ankurinfotech8@gmail.com
- **Phone:** +977-071-438585

---

## Files Referenced

- `CPANEL_SETUP.md` — Detailed cPanel setup guide
- `README.md` — Project overview & documentation
- `database.sql` — Database schema
- `includes/config.php` — Main configuration file
- `.gitignore` — Git ignore patterns

---

**Deployment Checklist Version:** 1.0  
**Last Updated:** 2026-06-03  
**Status:** Ready for Production ✅
