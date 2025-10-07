<?php
/**
 * Uninstall script for Synaplan WP AI plugin
 * 
 * This file is executed when the plugin is uninstalled.
 * It removes all plugin data from the database.
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Prevent any errors from breaking the uninstall process
error_reporting(0);
ini_set('display_errors', 0);

try {
    // Remove plugin options
    $options_to_remove = [
        'synaplan_wp_version',
        'synaplan_wp_setup_completed',
        'synaplan_wp_api_key',
        'synaplan_wp_user_id',
        'synaplan_wp_widget_config',
        'synaplan_wp_wizard_data',
        'synaplan_wp_db_version',
        'synaplan_wp_verification_token'
    ];
    
    foreach ($options_to_remove as $option) {
        delete_option($option);
    }
    
    // Remove plugin transients
    $transients_to_remove = [
        'synaplan_wp_api_status',
        'synaplan_wp_widget_preview'
    ];
    
    foreach ($transients_to_remove as $transient) {
        delete_transient($transient);
    }
    
    // Remove user-specific transients
    global $wpdb;
    $users = $wpdb->get_col("SELECT ID FROM {$wpdb->users}");
    foreach ($users as $user_id) {
        delete_transient('synaplan_wizard_data_' . $user_id);
    }
    
    // Remove plugin user meta
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'synaplan_wp_%'");
    
    // Remove plugin posts (if any were created)
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'synaplan_widget'");
    
    // Remove plugin comments (if any were created)
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'synaplan_widget'");
    
    // Remove plugin database tables
    $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Clear any cached data
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
} catch (Exception $e) {
    // Silently continue even if there are errors
    // This ensures the uninstall process completes
}