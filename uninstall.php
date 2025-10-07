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

// Remove plugin options
delete_option('synaplan_wp_version');
delete_option('synaplan_wp_setup_completed');
delete_option('synaplan_wp_api_key');
delete_option('synaplan_wp_user_id');
delete_option('synaplan_wp_widget_config');
delete_option('synaplan_wp_wizard_data');

// Remove plugin transients
delete_transient('synaplan_wp_api_status');
delete_transient('synaplan_wp_widget_preview');

// Remove plugin user meta
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'synaplan_wp_%'");

// Remove plugin posts (if any were created)
$wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'synaplan_widget'");

// Remove plugin comments (if any were created)
$wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'synaplan_widget'");

// Clear any cached data
wp_cache_flush();
