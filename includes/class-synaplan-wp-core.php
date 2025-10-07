<?php
/**
 * Core plugin class for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class Synaplan_WP_Core {
    
    /**
     * Plugin version
     */
    const VERSION = '1.0.0';
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Load text domain
        add_action('init', array($this, 'load_textdomain'));
        
        // Initialize admin interface
        if (is_admin()) {
            new Synaplan_WP_Admin();
        }
        
        // Initialize widget integration
        new Synaplan_WP_Widget();
        
        // Initialize API client
        new Synaplan_WP_API();
        
        // Add activation hooks
        register_activation_hook(SYNAPLAN_WP_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(SYNAPLAN_WP_PLUGIN_FILE, array($this, 'deactivate'));
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'synaplan-wp-ai',
            false,
            dirname(plugin_basename(SYNAPLAN_WP_PLUGIN_FILE)) . '/languages'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Create wizard sessions table
        $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            step_data longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY session_id (session_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Update database version
        update_option('synaplan_wp_db_version', '1.0');
    }
    
    /**
     * Drop database tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
        
        delete_option('synaplan_wp_db_version');
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        // Plugin version
        if (!get_option('synaplan_wp_version')) {
            add_option('synaplan_wp_version', self::VERSION);
        }
        
        // Setup completion status
        if (!get_option('synaplan_wp_setup_completed')) {
            add_option('synaplan_wp_setup_completed', false);
        }
        
        // Default widget configuration
        if (!get_option('synaplan_wp_widget_config')) {
            $default_config = array(
                'integration_type' => 'floating-button',
                'color' => '#007bff',
                'icon_color' => '#ffffff',
                'position' => 'bottom-right',
                'auto_message' => 'Hello! How can I help you today?',
                'auto_open' => false,
                'prompt' => 'general'
            );
            add_option('synaplan_wp_widget_config', $default_config);
        }
    }
    
    /**
     * Get plugin option with default value
     */
    public static function get_option($option_name, $default = false) {
        return get_option('synaplan_wp_' . $option_name, $default);
    }
    
    /**
     * Update plugin option
     */
    public static function update_option($option_name, $value) {
        return update_option('synaplan_wp_' . $option_name, $value);
    }
    
    /**
     * Delete plugin option
     */
    public static function delete_option($option_name) {
        return delete_option('synaplan_wp_' . $option_name);
    }
    
    /**
     * Check if setup is completed
     */
    public static function is_setup_completed() {
        return (bool) self::get_option('setup_completed', false);
    }
    
    /**
     * Mark setup as completed
     */
    public static function mark_setup_completed() {
        self::update_option('setup_completed', true);
    }
    
    /**
     * Get API key
     */
    public static function get_api_key() {
        return self::get_option('api_key', '');
    }
    
    /**
     * Set API key
     */
    public static function set_api_key($api_key) {
        return self::update_option('api_key', $api_key);
    }
    
    /**
     * Get user ID
     */
    public static function get_user_id() {
        return self::get_option('user_id', '');
    }
    
    /**
     * Set user ID
     */
    public static function set_user_id($user_id) {
        return self::update_option('user_id', $user_id);
    }
    
    /**
     * Get widget configuration
     */
    public static function get_widget_config() {
        return self::get_option('widget_config', array());
    }
    
    /**
     * Update widget configuration
     */
    public static function update_widget_config($config) {
        return self::update_option('widget_config', $config);
    }
    
    /**
     * Validate email address
     */
    public static function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate password strength
     */
    public static function validate_password($password) {
        // Minimum 6 characters, must contain numbers and special characters
        if (strlen($password) < 6) {
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate random session ID
     */
    public static function generate_session_id() {
        return wp_generate_password(32, false);
    }
    
    /**
     * Log debug information
     */
    public static function log($message, $level = 'info') {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Synaplan WP AI] ' . $level . ': ' . $message);
        }
    }
    
    /**
     * Get plugin URL
     */
    public static function get_plugin_url($path = '') {
        return SYNAPLAN_WP_PLUGIN_URL . $path;
    }
    
    /**
     * Get plugin path
     */
    public static function get_plugin_path($path = '') {
        return SYNAPLAN_WP_PLUGIN_DIR . $path;
    }
}
