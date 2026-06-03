<?php
/**
 * Client Import Template Excel Generator
 * Downloads a sample .CSV file with proper headers for bulk client imports
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="client-import-template_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Get output buffer
$output = fopen('php://output', 'w');

// BOM for UTF-8 (Excel compatibility)
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Headers
fputcsv($output, [
    'Company Name',
    'Contact Person',
    'Email',
    'Phone',
    'City',
    'Type',
    'Logo URL',
    'Description',
    'Status'
], ',');

// Sample rows
$sampleData = [
    ['Acme Corp', 'John Doe', 'john@acme.com', '+977-1-1234567', 'Kathmandu', 'client', '', 'Leading IT Solutions Provider', 'Active'],
    ['Tech Solutions Ltd', 'Jane Smith', 'jane@techsol.com', '+977-1-7654321', 'Pokhara', 'partner', '', 'Technology Partner', 'Active'],
    ['Digital Innovations', 'Ram Sharma', 'ram@digital.com', '+977-56-123456', 'Lalitpur', 'channel', '', 'Channel Partner', 'Active'],
];

foreach ($sampleData as $row) {
    fputcsv($output, $row, ',');
}

// Example with notes
fputcsv($output, [], ',');
fputcsv($output, ['NOTES:'], ',');
fputcsv($output, ['Type: client, partner, channel, solution, or investor'], ',');
fputcsv($output, ['Status: Active or Inactive'], ',');
fputcsv($output, ['Leave Logo URL blank to use company name only'], ',');

fclose($output);
exit;
