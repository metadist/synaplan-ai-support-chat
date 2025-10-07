<?php
/**
 * Plugin Name: Synaplan WP AI
 * Plugin URI: https://github.com/synaplan/synaplan-wp-ai
 * Description: Integrate Synaplan AI chat widget into your WordPress site with a wizard-style setup procedure.
 * Version: 1.0.0
 * Author: Synaplan
 * Author URI: https://synaplan.com
 * License: Apache-2.0
 * License URI: https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain: synaplan-wp-ai
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 8.0
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SYNAPLAN_WP_VERSION', '1.0.1');
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

/**
 * Initialize the plugin
 */
function synaplan_wp_init() {
    $core = new Synaplan_WP_Core();
    $core->init();
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
}
register_deactivation_hook(__FILE__, 'synaplan_wp_deactivate');

/**
 * Plugin uninstall hook
 */
function synaplan_wp_uninstall() {
    // Remove options
    delete_option('synaplan_wp_version');
    delete_option('synaplan_wp_setup_completed');
    delete_option('synaplan_wp_api_key');
    delete_option('synaplan_wp_user_id');
    delete_option('synaplan_wp_widget_config');
    
    // Remove database tables
    Synaplan_WP_Core::drop_tables();
}
register_uninstall_hook(__FILE__, 'synaplan_wp_uninstall');
