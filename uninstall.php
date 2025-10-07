<?php
/**
 * Uninstall script for Synaplan WP AI plugin
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove all plugin options
delete_option('synaplan_wp_version');
delete_option('synaplan_wp_setup_completed');
delete_option('synaplan_wp_api_key');
delete_option('synaplan_wp_user_id');
delete_option('synaplan_wp_widget_config');
delete_option('synaplan_wp_wizard_data');
delete_option('synaplan_wp_db_version');
delete_option('synaplan_wp_verification_token');

// Remove transients
delete_transient('synaplan_wp_api_status');
delete_transient('synaplan_wp_widget_preview');

// Remove database table
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}synaplan_wizard_sessions");

// Clear cache
wp_cache_flush();
