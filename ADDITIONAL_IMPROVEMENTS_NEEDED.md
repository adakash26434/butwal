# Additional Issues Found in Audit

## 1. **Inline Styles in Forms** (VISUAL CONSISTENCY)
- **Issue**: Many admin forms still use `style="..."` attributes
- **Impact**: Forms don't follow the new unified CSS system I created
- **Files**: banners.php, careers.php, faqs.php, and 10+ more
- **Affected**: 200+ inline style declarations throughout admin

## 2. **Form Field Missing Attributes**
- **Issue**: Input fields lack helpful attributes like `maxlength`, `placeholder`, `title`
- **Impact**: Poor UX, users don't know field requirements
- **Example**: Title fields missing maxlength, text areas without placeholder hints
- **Priority**: Medium

## 3. **Missing Form Layout Consistency**
- **Issue**: Form sections use different div structures instead of `.form-section` class
- **Impact**: Inconsistent spacing and visual hierarchy
- **Example**: Some forms use `<div style="...">` instead of `class="form-section"`

## 4. **Table Display Issues**
- **Issue**: Field values don't truncate when too long
- **Impact**: Text wraps awkwardly or overflows in lists
- **Files**: All list views across admin

## 5. **No Mobile-Specific Form Testing**
- **Issue**: Haven't tested forms on actual mobile devices
- **Impact**: Forms may not be truly responsive as intended
- **Priority**: Testing needed

## 6. **Form Validation Messages Missing**
- **Issue**: No visible error messages when form submission fails
- **Impact**: Users don't know what went wrong
- **Files**: All forms

## 7. **Accessible Labels**
- **Issue**: Some form fields don't have proper `for` attribute on labels
- **Impact**: Accessibility issues - screen readers can't link labels to inputs
- **Priority**: Important for accessibility

## 8. **Readonly Fields Not Styled**
- **Issue**: Readonly/disabled fields don't have visual distinction
- **Impact**: Users don't know field is not editable
- **Priority**: Medium

## Recommendations:
1. **Convert inline styles** → Use CSS classes from admin-forms.css
2. **Add form attributes** → maxlength, placeholder, title, required
3. **Improve accessibility** → Add proper label `for` attributes
4. **Add validation feedback** → Visual error states
5. **Test on mobile** → Verify responsive breakpoints work
6. **Refactor one form as example** → banners.php could be the template

---
Total Forms Needing Improvement: 52
Total Inline Styles Found: 200+
Estimated Time to Full Refactor: 4-6 hours for one person
