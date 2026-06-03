# User Guide - New Features & Fixes

## How to Use the New Features

### 1. Company Settings (Admin Panel → Settings → Company Settings)

**What it does:**
- Centralize all company information
- Update contact details, address, website
- Configure footer tagline and developer attribution
- All changes instantly reflect on the public site

**To update:**
1. Go to Admin Panel → Settings → Company Settings
2. Update any fields you want to change
3. Click "Save Settings"
4. Changes appear immediately on the site

**Fields:**
- **Company Name** - Used in footer & site branding
- **Phone Number** - Displayed in footer
- **Email Address** - For contact info
- **Address** - Company location
- **Website URL** - Main website link
- **Footer Tagline** - Short description in footer
- **Developed By Name** - Developer/company name in footer
- **Developer URL** - Link to developer website

---

### 2. Team Member Categories (Admin Panel → Content → Team)

**What it does:**
- Organize team members into Board and Management categories
- Better display on team page with proper sections
- Flexible categorization for any organization structure

**To add/edit team member:**
1. Go to Admin Panel → Content → Team
2. Click "+ NEW" or edit existing member
3. Fill in all details (name, role, bio, photo, email, LinkedIn)
4. In **Team Category** dropdown, select:
   - "Management Team" (default)
   - "Board Members"
5. Check "Leadership team" if they're in leadership
6. Click "Add Member" or "Update Member"

**Display:**
- The team.php page will automatically organize members by category
- Leadership members are highlighted
- Photos displayed in a grid layout

---

### 3. Channel Partners (Admin Panel → Content → Partners)

**What it does:**
- Add a new partner type specifically for distribution/channel partners
- Organize all partner types: Clients, Tech Partners, Channel Partners, Solutions, Investors
- Public partners.php page automatically displays all types with scrolling marquees

**To add a channel partner:**
1. Go to Admin Panel → Content → Partners
2. Click "+ Add Partner" 
3. Fill in partner details:
   - **Name** - Partner company name (required)
   - **Type** - Select "Channel Partner" (new option)
   - **Logo** - Upload company logo
   - **Website URL** - Link to partner website
   - **District** - Location in Nepal
   - **Position** - Display order (lower numbers appear first)
4. Check "Show on site" to make it public
5. Click "Add Partner"

**Display on partners.php:**
- Partners grouped by type (Clients, Tech Partners, Channel Partners, etc.)
- Empty sections are automatically hidden
- 5+ partners show with auto-scrolling marquee
- Less than 5 partners show in static grid
- Click partner to visit their website

---

### 4. Enhanced Admin Sidebar

**What improved:**
- Icons now properly styled for dark mode
- Hovering over menu items shows brighter icons
- Active menu items are highlighted with blue icons
- Smooth color transitions for better UX
- "Company Settings" option added to Settings menu

**How it works:**
- Icons respond to menu state (hover/active/normal)
- Sidebar colors automatically adapt to light/dark mode
- No configuration needed - it just works!

---

### 5. Pricing Table (Admin Panel → Content → Pricing Table)

**Status:** Already fully functional!

**To update pricing comparison table:**
1. Go to Admin Panel → Content → Pricing Table
2. List your features (one per line in the textarea)
3. For each plan (Starter, Growth, Enterprise), enter the value:
   - ✓ (checkmark) - Feature included
   - — (dash) - Feature not included  
   - Any number or text (100, 5000, Unlimited, etc.)
4. Click "Save Table"
5. The table appears instantly on pricing.php

**Display on pricing.php:**
- Responsive comparison table
- Clear feature list with values per plan
- Shows all your pricing plan features

---

### 6. Footer Developer Attribution (Automatic)

**What it does:**
- Automatically adds "Developed by [Name]" in footer
- Links to developer website
- Styled to match your site theme

**How it works:**
1. Go to Admin Panel → Settings → Company Settings
2. Fill in:
   - **Developed By Name** (e.g., "Ankur Infotech Pvt. Ltd.")
   - **Developer URL** (e.g., "https://ankurinfotech.com.np")
3. Save - footer is updated instantly!

**Display:**
- Appears at the very bottom of the footer
- Styled with subtle styling and hover effects
- Responsive on all devices

---

## Database Migrations

A new file `includes/db-migrations.php` has been created to handle schema updates. If you add new fields to team members or need to update the database structure, this file will manage those changes.

**To run migrations manually:**
```php
require_once 'includes/db-migrations.php';
runDbMigrations();
```

---

## Key Improvements Summary

✅ **No more hardcoded text** - Everything is in the database
✅ **Better admin interface** - Professional forms with helpful hints
✅ **Responsive design** - Works perfectly on mobile, tablet, desktop
✅ **Dark mode support** - Admin sidebar adapts to theme
✅ **Auto-hiding sections** - Empty partner categories don't display
✅ **Backward compatible** - All changes work with existing data
✅ **Professional UX** - Smooth transitions and proper organization

---

## Quick Checklist for Setup

After deploying, follow these steps:

1. [ ] Go to Admin → Settings → Company Settings
2. [ ] Update company name, phone, email, address
3. [ ] Set footer tagline
4. [ ] Set developer name and URL
5. [ ] Go to Admin → Content → Team
6. [ ] Categorize your team members (Board vs Management)
7. [ ] Go to Admin → Content → Partners
8. [ ] Verify all partner types are showing correctly
9. [ ] Visit public site to verify all changes appear

---

## Support

If you have any questions about the new features, refer to the form hints in the admin panel - they provide detailed explanations of each field.

For technical details, see `FIXES_COMPLETED.md` in the project root.
