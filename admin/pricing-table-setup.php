<?php
$pageTitle = 'Pricing Setup';
require_once '../includes/admin-layout.php';

$success = $error = '';

// Auto-create default pricing plans if they don't exist
try {
    $existing_plans = query("SELECT COUNT(*) as count FROM pricing_plans");
    
    if ($existing_plans[0]['count'] == 0) {
        // Insert default plans
        execute("INSERT INTO pricing_plans (name, tag, price_label, period, cta_label, cta_url, is_popular, active, position, created_at) VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()),
        (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()),
        (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
        [
            'Starter', 'starter', 'Custom', 'month', 'Get Started', '/contact', 0, 1, 1,
            'Growth', 'growth', 'Custom', 'month', 'Get Started', '/contact', 1, 1, 2,
            'Enterprise', 'enterprise', 'Custom', 'month', 'Contact Sales', '/contact', 0, 1, 3
        ]);
        $success = 'Default pricing plans created successfully! You can now edit the pricing table.';
    } else {
        $success = 'Pricing plans already exist. You can now use the Pricing Table editor.';
    }
} catch(\Throwable $e) {
    $error = 'Setup error: ' . $e->getMessage();
}
?>

<?php if($success):?><div class="alert alert-success mb-2"><?=e($success)?></div><?php endif;?>
<?php if($error):?><div class="alert alert-error mb-2"><?=e($error)?></div><?php endif;?>

<div style="margin-bottom:2rem;">
    <h2 class="h-eyebrow-flat" style="margin-bottom:1.5rem;">Pricing Setup</h2>
    <p style="margin-bottom:1rem;color:var(--muted-foreground);">Default pricing plans have been initialized. You can now:</p>
    <ul style="margin:1rem 0;padding-left:1.5rem;color:var(--muted-foreground);">
        <li>Go to <strong>Pricing Plans</strong> to customize plan names, prices, and features</li>
        <li>Go to <strong>Pricing Table</strong> to edit the comparison table features and values</li>
    </ul>
    
    <div style="display:flex;gap:0.75rem;margin-top:2rem;">
        <a href="pricing.php" class="btn btn-primary">Pricing Plans</a>
        <a href="pricing-table.php" class="btn btn-outline">Pricing Table</a>
    </div>
</div>
