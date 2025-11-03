<?php
/**
 * Plugin Name: Synaplan AI Support Chat
 * Plugin URI: https://github.com/metadist/synaplan-ai-support-chat
 * Description: Integrate Synaplan AI support chat widget into your WordPress site with a wizard-style setup procedure. Provide instant AI-powered customer support with knowledge base integration.
 * Version: 1.0.10
 * Author: metadist GmbH
 * Author URI: https://www.synaplan.com
 * License: Apache-2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: synaplan-ai-support-chat
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.3
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// PHP version check - Require 7.3 or higher
if (version_compare(PHP_VERSION, '7.3.0', '<')) {
    // Deactivate the plugin
    add_action('admin_init', 'synaplan_wp_deactivate_self');
    add_action('admin_notices', 'synaplan_wp_php_version_notice');
    
    function synaplan_wp_deactivate_self() {
        deactivate_plugins(plugin_basename(__FILE__));
    }
    
    function synaplan_wp_php_version_notice() {
        echo '<div class="error"><p>';
        printf(
            // translators: %1$s: Current PHP version, %2$s: Required PHP version
            esc_html__('Synaplan AI Support Chat requires PHP version %2$s or higher. You are currently running PHP version %1$s. Please upgrade your PHP version or contact your hosting provider.', 'synaplan-ai-support-chat'),
            esc_html(PHP_VERSION),
            '7.3.0'
        );
        echo '</p></div>';
    }
    
    // Prevent further execution
    return;
}

// Define plugin constants
define('SYNAPLAN_WP_VERSION', '1.0.10');
define('SYNAPLAN_WP_PLUGIN_FILE', __FILE__);
define('SYNAPLAN_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SYNAPLAN_WP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SYNAPLAN_WP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-core.php';
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-admin.php';
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-api.php';
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-wizard.php';
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-widget.php';
require_once SYNAPLAN_WP_PLUGIN_DIR . 'includes/class-synaplan-wp-rest-api.php';

/**
 * Initialize the plugin
 */
function synaplan_wp_init() {
    $core = new Synaplan_WP_Core();
    $core->init();
    
    // Initialize REST API
    new Synaplan_WP_REST_API();
}
add_action('plugins_loaded', 'synaplan_wp_init');

/**
 * Plugin activation hook
 */
function synaplan_wp_activate() {
    // Create database tables if needed
    Synaplan_WP_Core::create_tables();
    
    // Set default options
    add_option('synaplan_wp_version', SYNAPLAN_WP_VERSION);
    add_option('synaplan_wp_setup_completed', false);
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'synaplan_wp_activate');

/**
 * Plugin deactivation hook
 */
function synaplan_wp_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Clean up transients and temporary data
    delete_transient('synaplan_wp_api_status');
    delete_transient('synaplan_wp_widget_preview');
    
    // Clean up user-specific transients
    global $wpdb;
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Cleanup operation for transients
    $users = $wpdb->get_col("SELECT ID FROM {$wpdb->users} LIMIT 100");
    foreach ($users as $user_id) {
        delete_transient('synaplan_wizard_data_' . $user_id);
    }
}
register_deactivation_hook(__FILE__, 'synaplan_wp_deactivate');

/**
 * Manual cleanup function (can be called if uninstall fails)
 */
function synaplan_wp_manual_cleanup() {
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
    $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Schema change during cleanup, table name is prefixed
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
    
    // Clear cache
    wp_cache_flush();
    
    return true;
}
