<?php
$pageTitle = 'Pricing Comparison Table';
require_once '../includes/admin-layout.php';

$success = $error = '';

// Fetch all pricing plans for column headers
$plans = [];
$plans_error = '';
try { 
    $plans = query("SELECT id, name FROM pricing_plans WHERE active=1 ORDER BY position, id"); 
    
    // If no plans exist, create default ones
    if (empty($plans)) {
        try {
            execute("INSERT INTO pricing_plans (name, tag, price_label, period, cta_label, cta_url, is_popular, active, position, created_at) VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()),
            (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()),
            (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                'Starter', 'starter', 'Custom', 'month', 'Get Started', '/contact', 0, 1, 1,
                'Growth', 'growth', 'Custom', 'month', 'Get Started', '/contact', 1, 1, 2,
                'Enterprise', 'enterprise', 'Custom', 'month', 'Contact Sales', '/contact', 0, 1, 3
            ]);
            // Fetch the newly created plans
            $plans = query("SELECT id, name FROM pricing_plans WHERE active=1 ORDER BY position, id");
            $success = 'Default pricing plans created. You can now edit the pricing table.';
        } catch(\Throwable $e) {
            $plans_error = 'Could not create default plans. Please create pricing plans manually in the Pricing Plans section.';
        }
    }
}
catch(\Throwable $e) { 
    $plans_error = 'Could not fetch plans: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'save-table') {
        // Save entire table structure
        try {
            // Get all features (rows)
            $features_raw = trim($_POST['features'] ?? '');
            $features_list = array_values(array_filter(array_map('trim', explode("\n", $features_raw))));
            
            // Build table structure: each feature becomes a row with values for each plan
            $table_data = [];
            
            foreach ($features_list as $feature_name) {
                $row = ['feature' => $feature_name, 'values' => []];
                
                // Get value for each plan column
                foreach ($plans as $plan) {
                    $key = 'plan_' . $plan['id'] . '_' . md5($feature_name);
                    $value = trim($_POST[$key] ?? '');
                    $row['values'][$plan['id']] = $value;
                }
                
                $table_data[] = $row;
            }
            
            // Save to site_settings as JSON
            $table_json = json_encode($table_data, JSON_UNESCAPED_UNICODE);
            saveSetting('pricing_comparison_table', $table_json);
            
            $success = 'Pricing table updated successfully.';
        } catch(\Throwable $e) {
            $error = 'Save failed: ' . $e->getMessage();
        }
    }
}

// Load current table data
$table_data = [];
$table_json = '';
try {
    $setting = queryOne("SELECT setting_val FROM site_settings WHERE setting_key=?", ['pricing_comparison_table']);
    $table_json = $setting['setting_val'] ?? '[]';
    $table_data = json_decode($table_json, true) ?: [];
    
    // Auto-initialize with default data if empty
    if (empty($table_data)) {
        $default_table_data = [
            ['feature' => 'Core Software Module', 'values' => [1 => '✓', 2 => '✓', 3 => '✓']],
            ['feature' => 'Members limit', 'values' => [1 => '500', 2 => '5,000', 3 => 'Unlimited']],
            ['feature' => 'Branches', 'values' => [1 => '1', 2 => '5', 3 => 'Unlimited']],
            ['feature' => 'Mobile Banking App', 'values' => [1 => '—', 2 => '✓', 3 => '✓']],
            ['feature' => 'Document Management (DMS)', 'values' => [1 => '—', 2 => '✓', 3 => '✓']],
            ['feature' => 'HR & Payroll', 'values' => [1 => '—', 2 => '—', 3 => '✓']],
            ['feature' => 'Priority support (<2 hr)', 'values' => [1 => '—', 2 => '✓', 3 => '✓']],
            ['feature' => 'On-site visits', 'values' => [1 => '—', 2 => 'Quarterly', 3 => 'Dedicated']],
            ['feature' => 'Custom reports', 'values' => [1 => '✓', 2 => '✓', 3 => '✓']],
            ['feature' => 'BS Calendar native', 'values' => [1 => '✓', 2 => '✓', 3 => '✓']],
            ['feature' => 'Custom branding', 'values' => [1 => '—', 2 => '✓', 3 => '✓']],
            ['feature' => 'Uptime SLA', 'values' => [1 => '99%', 2 => '99.9%', 3 => '99.95%']],
        ];
        $table_json = json_encode($default_table_data, JSON_UNESCAPED_UNICODE);
        saveSetting('pricing_comparison_table', $table_json);
        $table_data = $default_table_data;
    }
} catch(\Throwable $e) {
    $table_data = [];
}
?>

<?php if($success):?><div class="alert alert-success mb-1"><?=e($success)?></div><?php endif;?>
<?php if($error):?><div class="alert alert-error mb-1"><?=e($error)?></div><?php endif;?>

<div style="margin-bottom:2rem;">
    <div class="row-between-mb">
        <h2 class="h-eyebrow-flat">Pricing Comparison Table</h2>
    </div>
    
    <?php if($plans_error):?>
        <div style="border:2px dashed var(--border);border-radius:1rem;padding:3rem;text-align:center;color:var(--muted-foreground);">
            <p><?=e($plans_error)?></p>
            <a href="pricing.php" class="btn btn-primary btn-sm" style="margin-top:1rem;">Go to Pricing Plans</a>
        </div>
    <?php elseif(empty($plans)):?>
        <div style="border:2px dashed var(--border);border-radius:1rem;padding:3rem;text-align:center;color:var(--muted-foreground);">
            <p>No active pricing plans found. Please create pricing plans first.</p>
            <a href="pricing.php" class="btn btn-primary btn-sm" style="margin-top:1rem;">Go to Pricing Plans</a>
        </div>
    <?php else:?>
    
    <form method="POST" style="display:flex;flex-direction:column;gap:1.5rem;">
        <?= csrf() ?>
        <input type="hidden" name="action" value="save-table">
        
        <!-- Features List -->
        <div class="af-form-group">
            <label class="af-label">Features (one per line)</label>
            <textarea name="features" rows="15" class="af-textarea" placeholder="Core Software Module&#10;Members limit&#10;Branches&#10;Mobile Banking App&#10;Document Management (DMS)&#10;HR & Payroll&#10;Priority support (<2 hr)&#10;On-site visits&#10;Custom reports&#10;BS Calendar native&#10;Custom branding&#10;Uptime SLA"><?php
                echo e(implode("\n", array_map(fn($row) => $row['feature'], $table_data)));
            ?></textarea>
            <small style="color:var(--muted-foreground);">List all features that should appear in the comparison table. Edit values for each pricing plan below.</small>
        </div>
        
        <!-- Comparison Table Preview -->
        <div style="overflow-x:auto;">
            <table class="st-table" style="min-width:560px;border:1px solid var(--border);border-radius:0.75rem;overflow:hidden;">
                <thead>
                    <tr>
                        <th style="width:40%;">Feature</th>
                        <?php foreach($plans as $plan):?>
                        <th class="text-center" style="<?=$plan['id']===2?'background:rgba(37,99,235,0.06);color:var(--primary);':''?>">
                            <?=e($plan['name'])?>
                        </th>
                        <?php endforeach;?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $features_raw = trim($_POST['features'] ?? implode("\n", array_map(fn($row) => $row['feature'], $table_data)));
                    $features_list = array_values(array_filter(array_map('trim', explode("\n", $features_raw))));
                    
                    foreach($features_list as $idx => $feature_name):
                        $current_row = $table_data[$idx] ?? ['feature' => $feature_name, 'values' => []];
                    ?>
                    <tr>
                        <td style="font-weight:500;"><?=e($feature_name)?></td>
                        <?php foreach($plans as $plan):?>
                        <td style="text-align:center;<?=$plan['id']===2?'background:rgba(37,99,235,0.04);':''?>">
                            <input type="text" 
                                name="plan_<?=$plan['id']?>_<?=md5($feature_name)?>" 
                                value="<?=e($current_row['values'][$plan['id']] ?? '')?>"
                                class="af-input" 
                                style="text-align:center;width:100%;padding:0.5rem;border:1px solid var(--border);"
                                placeholder="✓ or value">
                        </td>
                        <?php endforeach;?>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
        
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" style="width:14px;height:14px;display:inline;margin-right:0.35rem;"></i>
                Save Table
            </button>
            <a href="pricing.php" class="btn btn-outline">Manage Plans</a>
        </div>
    </form>
    
    <?php endif;?>
</div>

<style>
    .af-table-note {
        background:rgba(59,130,246,0.08);
        border:1px solid rgba(59,130,246,0.2);
        border-radius:0.75rem;
        padding:0.875rem 1.1rem;
        margin-bottom:1.5rem;
        display:flex;
        align-items:flex-start;
        gap:0.75rem;
    }
    .af-table-note-icon {
        font-size:1.4rem;
        flex-shrink:0;
    }
    .af-table-note-content {
        font-size:0.8125rem;
        color:var(--muted-foreground);
    }
    .af-table-note-title {
        font-weight:700;
        font-size:0.875rem;
        color:var(--foreground);
        margin-bottom:0.2rem;
    }
</style>
