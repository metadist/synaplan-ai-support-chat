<?php
/**
 * PHP Compatibility Test Script for Synaplan AI Support Chat
 * 
 * This script tests PHP compatibility without requiring WordPress.
 * Run this on your server to check if your PHP version is compatible.
 * 
 * Usage:
 *   1. Upload this file to your server
 *   2. Access it via browser: https://yourdomain.com/test-php-compatibility.php
 *   3. Or run via CLI: php test-php-compatibility.php
 * 
 * @package Synaplan_WP_AI
 * @version 1.0.4
 */

// Prevent caching
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// HTML output if accessed via browser
$is_cli = php_sapi_name() === 'cli';

if (!$is_cli) {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synaplan AI Support Chat - PHP Compatibility Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            margin-top: 0;
        }
        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #ddd;
            background: #f8f9fa;
        }
        .pass {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .fail {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .status {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .details {
            font-size: 14px;
            color: #666;
        }
        .summary {
            margin-top: 30px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 4px;
        }
        .icon {
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 PHP Compatibility Test</h1>
        <p>Testing compatibility for <strong>Synaplan AI Support Chat WordPress Plugin v1.0.4</strong></p>
        <hr>';
}

// Test results array
$results = array();
$overall_compatible = true;

// Helper function to add test result
function add_test($name, $passed, $details = '', $required = true) {
    global $results, $overall_compatible, $is_cli;
    
    $results[] = array(
        'name' => $name,
        'passed' => $passed,
        'details' => $details,
        'required' => $required
    );
    
    if ($required && !$passed) {
        $overall_compatible = false;
    }
    
    if ($is_cli) {
        $status = $passed ? '✓ PASS' : '✗ FAIL';
        echo sprintf("[%s] %s\n", $status, $name);
        if ($details) {
            echo "       " . $details . "\n";
        }
    }
}

// Test 1: PHP Version
$php_version = PHP_VERSION;
$min_version = '7.3.0';
$version_check = version_compare($php_version, $min_version, '>=');

add_test(
    'PHP Version',
    $version_check,
    sprintf('Current: %s | Required: %s or higher', $php_version, $min_version),
    true
);

// Test 2: cURL Extension
$curl_check = extension_loaded('curl') && function_exists('curl_init');
add_test(
    'cURL Extension',
    $curl_check,
    $curl_check ? 'cURL is available' : 'cURL extension is required for API communication',
    true
);

// Test 3: JSON Extension
$json_check = extension_loaded('json') && function_exists('json_encode');
add_test(
    'JSON Extension',
    $json_check,
    $json_check ? 'JSON support is available' : 'JSON extension is required',
    true
);

// Test 4: Filter Extension
$filter_check = extension_loaded('filter') && function_exists('filter_var');
add_test(
    'Filter Extension',
    $filter_check,
    $filter_check ? 'Input filtering is available' : 'Filter extension is required',
    true
);

// Test 5: OpenSSL (for HTTPS)
$openssl_check = extension_loaded('openssl');
add_test(
    'OpenSSL Extension',
    $openssl_check,
    $openssl_check ? 'SSL/TLS support is available' : 'OpenSSL is recommended for secure API communication',
    false
);

// Test 6: mbstring Extension
$mbstring_check = extension_loaded('mbstring');
add_test(
    'Mbstring Extension',
    $mbstring_check,
    $mbstring_check ? 'Multibyte string support is available' : 'Mbstring is recommended for international characters',
    false
);

// Test 7: Memory Limit
$memory_limit = ini_get('memory_limit');
$memory_value = intval($memory_limit);
$memory_check = $memory_value >= 64 || $memory_limit === '-1';
add_test(
    'Memory Limit',
    $memory_check,
    sprintf('Current: %s | Recommended: 128M or higher', $memory_limit),
    false
);

// Test 8: Max Upload Size
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$upload_check = intval($upload_max) >= 10;
add_test(
    'Upload File Size',
    $upload_check,
    sprintf('upload_max_filesize: %s, post_max_size: %s | Recommended: 10M or higher for document uploads', $upload_max, $post_max),
    false
);

// Test 9: allow_url_fopen (alternative to cURL)
$allow_url_fopen = ini_get('allow_url_fopen');
$url_fopen_check = $curl_check || $allow_url_fopen;
add_test(
    'URL Access',
    $url_fopen_check,
    $url_fopen_check ? 'Remote URL access is enabled' : 'Either cURL or allow_url_fopen must be enabled',
    true
);

// Test 10: Write permissions (if WordPress is detected)
$write_check = true;
$write_details = 'Cannot test without WordPress installation';
if (defined('ABSPATH')) {
    $uploads_dir = wp_upload_dir();
    $write_check = wp_is_writable($uploads_dir['basedir']);
    $write_details = $write_check ? 'WordPress uploads directory is writable' : 'WordPress uploads directory is not writable';
}
add_test(
    'File Write Permissions',
    $write_check,
    $write_details,
    false
);

// Output results in HTML format
if (!$is_cli) {
    foreach ($results as $result) {
        $class = $result['passed'] ? 'pass' : ($result['required'] ? 'fail' : 'warning');
        $icon = $result['passed'] ? '✓' : ($result['required'] ? '✗' : '⚠');
        $status_text = $result['passed'] ? 'PASS' : ($result['required'] ? 'FAIL' : 'WARNING');
        
        echo sprintf(
            '<div class="test-item %s">
                <div class="status"><span class="icon">%s</span> %s: %s</div>
                <div class="details">%s</div>
            </div>',
            $class,
            $icon,
            $result['name'],
            $status_text,
            htmlspecialchars($result['details'])
        );
    }
    
    // Summary
    echo '<div class="summary">';
    if ($overall_compatible) {
        echo '<h2 style="color: #28a745;">✓ Compatible</h2>';
        echo '<p>Your server meets all required specifications for running Synaplan AI Support Chat plugin!</p>';
        echo '<p><strong>Next Steps:</strong></p>';
        echo '<ol>';
        echo '<li>Install WordPress (if not already installed)</li>';
        echo '<li>Download and install the Synaplan AI Support Chat plugin</li>';
        echo '<li>Follow the setup wizard to configure your AI chat widget</li>';
        echo '</ol>';
    } else {
        echo '<h2 style="color: #dc3545;">✗ Not Compatible</h2>';
        echo '<p>Your server does not meet the minimum requirements. Please address the failed checks above.</p>';
        echo '<p><strong>Required Actions:</strong></p>';
        echo '<ul>';
        
        foreach ($results as $result) {
            if ($result['required'] && !$result['passed']) {
                echo '<li>' . htmlspecialchars($result['name']) . ': ' . htmlspecialchars($result['details']) . '</li>';
            }
        }
        
        echo '</ul>';
        echo '<p>Contact your hosting provider if you need assistance upgrading PHP or enabling required extensions.</p>';
    }
    echo '</div>';
    
    // PHP Info Link
    echo '<hr>';
    echo '<p style="text-align: center; color: #666; font-size: 14px;">';
    echo 'For detailed PHP configuration, <a href="?phpinfo=1">view phpinfo()</a> | ';
    echo '<a href="https://www.synaplan.com/support">Get Support</a>';
    echo '</p>';
    
    echo '</div></body></html>';
    
    // Show phpinfo if requested
    if (isset($_GET['phpinfo']) && $_GET['phpinfo'] === '1') {
        phpinfo();
    }
} else {
    // CLI Summary
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "SUMMARY\n";
    echo str_repeat('=', 60) . "\n";
    
    if ($overall_compatible) {
        echo "✓ COMPATIBLE: Your server meets all required specifications!\n";
    } else {
        echo "✗ NOT COMPATIBLE: Please address the failed checks above.\n";
    }
    
    echo "\nTest Results:\n";
    $passed = count(array_filter($results, function($r) { return $r['passed']; }));
    $failed = count(array_filter($results, function($r) { return !$r['passed'] && $r['required']; }));
    $warnings = count(array_filter($results, function($r) { return !$r['passed'] && !$r['required']; }));
    
    echo sprintf("  Passed:   %d\n", $passed);
    echo sprintf("  Failed:   %d\n", $failed);
    echo sprintf("  Warnings: %d\n", $warnings);
    echo sprintf("  Total:    %d\n", count($results));
    
    echo "\nPHP Version: " . PHP_VERSION . "\n";
    echo "Plugin Version: 1.0.4\n";
    echo "\n";
}

// Exit with appropriate code for CLI
if ($is_cli) {
    exit($overall_compatible ? 0 : 1);
}

