<?php
/**
 * Initialize default pricing comparison table data
 * This script safely inserts default pricing table data without removing existing data
 */

require_once 'includes/db.php';

// Default pricing table data
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

try {
    // Check if data already exists
    $existing = queryOne("SELECT setting_val FROM site_settings WHERE setting_key=?", ['pricing_comparison_table']);
    
    if (empty($existing['setting_val']) || $existing['setting_val'] === '[]') {
        // Only insert if empty or doesn't exist
        $table_json = json_encode($default_table_data, JSON_UNESCAPED_UNICODE);
        saveSetting('pricing_comparison_table', $table_json);
        echo "✓ Pricing table data initialized successfully\n";
    } else {
        echo "✓ Pricing table data already exists. Skipping initialization.\n";
    }
} catch (\Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
?>
