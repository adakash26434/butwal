# Global Theme System — Complete Documentation

## Overview
The Butwal project has a comprehensive, production-ready global theme system with:
- **60+ CSS variables** for colors, typography, spacing, shadows, gradients
- **Light mode** (default) and **dark mode** (html.dark)
- **Lucide Icons** (self-hosted, 500+ icons)
- **3 font families** (Poppins, Noto Sans Devanagari, JetBrains Mono)
- **Responsive breakpoints** and **semantic color tokens**

## File Structure

### Core Theme Files
```
assets/
├── theme.css              (Design tokens + global components)
├── css/
│   ├── tailwind.min.css   (Tailwind production build)
│   ├── fonts.css          (Self-hosted @font-face declarations)
│   ├── daisyui.min.css    (Admin/portal only)
│   ├── admin-forms.css    (Form styling, input states)
│   ├── pages.css          (Public-page overrides)
│   └── home.css           (Homepage-only animations)
├── vendor/
│   ├── alpine.min.js      (Alpine pinned)
│   └── lucide.min.js      (Lucide pinned)
└── fonts/                 (woff2 files: Poppins, Noto Sans Devanagari, JetBrains Mono)
```

### Key Files Using Theme
- `includes/head.php` — Loads all theme CSS, fonts, Lucide
- `includes/helpers.php` — Defines `icon()` helper for Lucide
- `admin/company-settings.php` — Allows admin to customize theme colors

## CSS Variables — Light Mode (Default)

### Color Tokens (Light Mode: #fafbfc background)
```css
:root {
  /* Backgrounds & Cards */
  --background: #fafbfc          /* Page background */
  --card: #ffffff                /* Card/panel background */
  --muted: #f1f5f9               /* Subtle background */
  
  /* Text Colors */
  --foreground: #0f172a          /* Primary text */
  --muted-foreground: #64748b    /* Secondary text */
  
  /* Borders & Inputs */
  --border: #e2e8f0              /* Border color */
  --input: #e8ecf1               /* Input background */
  
  /* Brand Colors */
  --primary: #2563eb             /* Main brand blue */
  --secondary: #10b981           /* Green */
  --accent: #f59e0b              /* Amber */
  --destructive: #ef4444         /* Red */
  --success: #22c55e             /* Green success */
  --warning: #eab308             /* Yellow warning */
  --info: #3b82f6                /* Blue info */
  
  /* Soft/Light Variants */
  --primary-light: #dbeafe       /* Blue light */
  --success-soft: #dcfce7        /* Green light bg */
  --danger-soft: #fee2e2         /* Red light bg */
  --warning-soft: #fef3c7        /* Yellow light bg */
}
```

### Dark Mode Variables (html.dark)
```css
html.dark {
  --background: #0f172a
  --card: #1e293b
  --foreground: #f1f5f9
  --muted: #1e293b
  --muted-foreground: #94a3b8
  /* ...primary, secondary, accent colors remain same... */
}
```

### Typography
```css
:root {
  /* Font families */
  --font-display: 'Poppins', 'Noto Sans Devanagari', sans-serif
  --font-body: 'Poppins', 'Noto Sans Devanagari', sans-serif
  --font-mono: 'JetBrains Mono', monospace
  
  /* Type scale */
  --text-xs: 0.75rem
  --text-sm: 0.8125rem
  --text-base: 0.9375rem
  --text-md: 1rem
  --text-lg: 1.125rem
  --text-xl: 1.25rem
  --text-2xl: 1.5rem
  --text-3xl: 1.875rem
  --text-4xl: 2.25rem
  --text-5xl: 3rem
  
  /* Font weights */
  --fw-light: 300
  --fw-normal: 400
  --fw-medium: 500
  --fw-semibold: 600
  --fw-bold: 700
}
```

## How to Use Theme Variables

### 1. In CSS
```css
.button {
  background-color: var(--primary);
  color: var(--primary-fg);
  border: 1px solid var(--border);
  font-family: var(--font-body);
}

/* Dark mode automatically applies */
html.dark .button {
  /* No changes needed — variables redefine in html.dark */
}
```

### 2. In PHP (Inline Styles)
```php
<div style="background-color: var(--card); color: var(--foreground);">
  Content adapts to theme automatically
</div>
```

### 3. With Tailwind
```html
<!-- Tailwind classes work directly -->
<div class="bg-primary text-white">
  Styled with theme colors
</div>
```

## Lucide Icons — Self-Hosted

### Usage in PHP
```php
<?= icon('home', 24) ?>                  <!-- 24px home icon -->
<?= icon('check-circle', 20) ?>          <!-- 20px check circle -->
<?= icon('alert-circle', 16) ?>          <!-- 16px alert -->

<!-- Icon color respects text color -->
<div style="color: var(--primary);">
  <?= icon('star', 20) ?>               <!-- Blue star -->
</div>
```

### Available Icons (500+)
- Navigation: home, menu, x, chevron-*, arrow-*
- Status: check, check-circle, x-circle, alert-circle, info
- Social: mail, phone, linkedin, github, twitter
- Business: briefcase, users, building-*, package
- And hundreds more...

## Dark Mode Implementation

### How Dark Mode Works
```html
<!-- Admin Sets Theme Preference -->
<!-- Admin → Settings → Theme (Light/Dark/System) -->

<!-- On Page Load -->
<html class="light">  <!-- Default -->
<html class="dark">   <!-- User selected dark or system-dark -->
```

### Automatic Dark Mode (System Preference)
```php
// includes/head.php
$__themePref = (function_exists('currentUser') ? (currentUser()['theme_pref'] ?? '') : '');
// User can set: 'light', 'dark', or 'system' preference
```

### CSS Variable Override
All `html.dark` definitions override light mode automatically:
```css
:root { --background: #fafbfc; }    /* Light */
html.dark { --background: #0f172a; } /* Dark */
```

## Fonts — Global & Nepali Support

### Font Stack
1. **Poppins** (Latin) — Primary UI font
2. **Noto Sans Devanagari** (Nepali) — Fallback for Nepali text
3. **JetBrains Mono** — Code blocks / monospace
4. **System fonts** (ui-sans-serif) — Fallback

### Self-Hosted Font Files
```
assets/fonts/
├── poppins-deva-400.woff2  (Devanagari Regular)
├── poppins-deva-600.woff2  (Devanagari Semibold)
├── poppins-latn-400.woff2  (Latin Regular)
├── poppins-latn-600.woff2  (Latin Semibold)
├── poppins-latn-700.woff2  (Latin Bold)
├── TuG7UUFzXI5FBtUq5a8bjKYTZjtRU6Sgv3NaV_SNmI0b8QQCQmHN5TV_9qo.woff2  (Noto Sans Devanagari variable)
└── tDbv2o-flEEny0FZhsfKu5WU4zr3E_BX0PnT8RD8yKwBNntkaToggR7BYRbKPxDcwg.woff2  (JetBrains Mono variable)
```

### Font Usage
```css
body { font-family: var(--font-body); }           /* English + Nepali */
code { font-family: var(--font-mono); }           /* Code blocks */
h1, h2 { font-family: var(--font-display); }     /* Headings */
```

## Current Status

### ✓ Implemented
- 60+ CSS variables (colors, typography, spacing)
- Light & Dark modes fully functional
- Lucide icons (500+) self-hosted
- Typography system (3 fonts, 10-point type scale)
- DaisyUI component library
- Responsive breakpoints
- Elevation/shadow system
- Gradient definitions

### ⚠ In Progress
- Replacing ~1424 hardcoded colors with CSS variables
- Files identified: mailer.php (57), index.php (30), admin/settings.php (28), footer.php (24)
- Estimated completion: ~20 high-impact files

### ✓ Next Steps
1. Admin can modify colors via Admin → Company Settings (future feature)
2. All pages will auto-update when colors change
3. Complete separation of design tokens from code
4. Consistent appearance across light/dark modes

## Admin Settings Integration

### Available Settings (Admin → Company Settings)
- Primary Color (currently #2563eb)
- Secondary Color (currently #10b981)
- Accent Color (currently #f59e0b)
- Background Color (currently #fafbfc)
- Dark Mode Background (currently #0f172a)

## Best Practices

### Do's
✓ Use `var(--primary)` for brand colors
✓ Use `var(--muted-foreground)` for secondary text
✓ Use `var(--card)` for card backgrounds
✓ Use `var(--border)` for borders
✓ Use `var(--success)` / `var(--destructive)` for status
✓ Use `icon('name', size)` for all icons

### Don'ts
✗ Don't hardcode #2563eb or #0f172a
✗ Don't use rgba(15,23,42,...) — use var(--background-rgb)
✗ Don't mix icon libraries (Lucide only)
✗ Don't specify fonts — use var(--font-*)
✗ Don't hardcode shadows — use var(--shadow-*)

## Troubleshooting

### Colors not changing in dark mode?
- Check `html.dark` class is present on `<html>` element
- Verify CSS variable is defined in `html.dark` block in theme.css
- Ensure no `!important` inline styles override

### Icons not showing?
- Verify `icon()` function is called correctly with valid icon name
- Check `assets/vendor/lucide.min.js` exists and is loading (Network tab)
- Ensure `lucide.createIcons()` runs (included in `includes/head.php`)

### Fonts not loading?
- Check assets/fonts/ has .woff2 files
- Verify font-face is defined in assets/css/fonts.css
- Inspect Network tab for 404s on font files

## File Maintenance

### When adding new colors
1. Add to `:root` in assets/theme.css
2. Add corresponding `html.dark` override
3. Use `var(--new-color)` in code
4. Never hardcode hex values

### When adding new icons
1. Use existing Lucide icon names
2. Call via `icon('icon-name', size)`
3. Don't add custom SVGs

### When changing fonts
1. Update font-face in assets/css/fonts.css
2. Update --font-display, --font-body in theme.css
3. Use typography variables everywhere
