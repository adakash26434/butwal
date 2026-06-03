<?php
$pageTitle = 'Company Settings';
require_once '../includes/admin-layout.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        try {
            saveSetting('company_name', trim($_POST['company_name'] ?? 'Ankur Infotech Pvt. Ltd.'));
            saveSetting('company_phone', trim($_POST['company_phone'] ?? ''));
            saveSetting('company_address', trim($_POST['company_address'] ?? ''));
            saveSetting('company_email', trim($_POST['company_email'] ?? ''));
            saveSetting('company_website', trim($_POST['company_website'] ?? ''));
            
            saveSetting('footer_tagline', trim($_POST['footer_tagline'] ?? ''));
            
            saveSetting('developed_by_name', trim($_POST['developed_by_name'] ?? 'Ankur Infotech Pvt. Ltd.'));
            saveSetting('developed_by_url', trim($_POST['developed_by_url'] ?? ''));
            
            $success = 'Company settings updated successfully.';
        } catch (\Throwable $e) {
            $error = 'Save failed: ' . $e->getMessage();
        }
    }
}

// Load current settings
$s = siteSettings();
?>

<?php if($success):?><div class="alert alert-success mb-1"><?=e($success)?></div><?php endif;?>
<?php if($error):?><div class="alert alert-error mb-1"><?=e($error)?></div><?php endif;?>

<div style="margin-bottom:2rem;">
    <h2 class="h-eyebrow-flat">Company Settings</h2>
    <p style="font-size:var(--text-sm);color:var(--muted-foreground);margin:0.5rem 0 0;">Manage company information displayed across the website.</p>
</div>

<div class="st-card p-tile">
    <form method="POST" class="col-1-tight">
        <?=csrfField()?>
        <input type="hidden" name="action" value="save">

        <!-- Company Details Section -->
        <div>
            <h3 style="font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted-foreground);margin-bottom:1rem;">Company Details</h3>
        </div>

        <div>
            <label class="form-label fs-2xs2">Company Name</label>
            <input type="text" name="company_name" required class="form-input fs-sm2" value="<?=e($s['company_name']??'Ankur Infotech Pvt. Ltd.')?>" maxlength="150" placeholder="Company name">
            <span class="form-hint">The official name of your company.</span>
        </div>

        <div>
            <label class="form-label fs-2xs2">Phone Number</label>
            <input type="text" name="company_phone" class="form-input fs-sm2" value="<?=e($s['company_phone']??'')?>" maxlength="100" placeholder="+977-071-438585, 071-437612">
            <span class="form-hint">Phone number(s) displayed in footer and contact pages.</span>
        </div>

        <div>
            <label class="form-label fs-2xs2">Email Address</label>
            <input type="email" name="company_email" class="form-input fs-sm2" value="<?=e($s['company_email']??'')?>" maxlength="100" placeholder="contact@company.com">
            <span class="form-hint">Company email for general inquiries.</span>
        </div>

        <div>
            <label class="form-label fs-2xs2">Address</label>
            <input type="text" name="company_address" class="form-input fs-sm2" value="<?=e($s['company_address']??'')?>" maxlength="200" placeholder="Butwal, Rupandehi, Nepal">
            <span class="form-hint">Company headquarters address.</span>
        </div>

        <div>
            <label class="form-label fs-2xs2">Website URL</label>
            <input type="url" name="company_website" class="form-input fs-sm2" value="<?=e($s['company_website']??'')?>" maxlength="200" placeholder="https://example.com">
            <span class="form-hint">Main company website URL.</span>
        </div>

        <!-- Footer Section -->
        <div style="margin-top:2rem;">
            <h3 style="font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted-foreground);margin-bottom:1rem;">Footer Settings</h3>
        </div>

        <div>
            <label class="form-label fs-2xs2">Footer Tagline</label>
            <textarea name="footer_tagline" class="form-input fs-sm-r" rows="3" maxlength="500" placeholder="Trusted software & IT solutions partner based in Nepal."><?=e($s['footer_tagline']??'Trusted software & IT solutions partner based in Butwal, Rupandehi, Nepal.')?></textarea>
            <span class="form-hint">Short description displayed in footer (max 500 chars).</span>
        </div>

        <!-- Developer Attribution Section -->
        <div style="margin-top:2rem;">
            <h3 style="font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted-foreground);margin-bottom:1rem;">Developer Attribution</h3>
        </div>

        <div>
            <label class="form-label fs-2xs2">Developed By Name</label>
            <input type="text" name="developed_by_name" required class="form-input fs-sm2" value="<?=e($s['developed_by_name']??'Ankur Infotech Pvt. Ltd.')?>" maxlength="150" placeholder="Company name">
            <span class="form-hint">Name displayed in footer: "Developed by [Name]".</span>
        </div>

        <div>
            <label class="form-label fs-2xs2">Developer URL</label>
            <input type="url" name="developed_by_url" required class="form-input fs-sm2" value="<?=e($s['developed_by_url']??'')?>" maxlength="200" placeholder="https://example.com">
            <span class="form-hint">URL to developer/company website (linked in footer).</span>
        </div>

        <button type="submit" class="btn btn-primary w-100" style="margin-top:1.5rem;">Save Settings</button>
    </form>
</div>

<?php require_once '../includes/admin-layout-close.php'; ?>
