<?php
/**
 * Admin interface class for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin interface class
 */
class Synaplan_WP_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_synaplan_wp_wizard_step', array($this, 'handle_wizard_step'));
        add_action('wp_ajax_synaplan_wp_save_config', array($this, 'handle_save_config'));
        add_action('wp_ajax_synaplan_wp_test_api', array($this, 'handle_test_api'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('Synaplan AI Support Chat', 'synaplan-ai-support-chat'),
            __('Synaplan AI Support Chat', 'synaplan-ai-support-chat'),
            'manage_options',
            'synaplan-ai-support-chat',
            array($this, 'admin_page'),
            'data:image/svg+xml;base64,' . base64_encode($this->get_menu_icon()),
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'synaplan-ai-support-chat',
            __('Dashboard', 'synaplan-ai-support-chat'),
            __('Dashboard', 'synaplan-ai-support-chat'),
            'manage_options',
            'synaplan-ai-support-chat',
            array($this, 'admin_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'synaplan-ai-support-chat',
            __('Settings', 'synaplan-ai-support-chat'),
            __('Settings', 'synaplan-ai-support-chat'),
            'manage_options',
            'synaplan-ai-support-chat-settings',
            array($this, 'settings_page')
        );
        
        // Help submenu
        add_submenu_page(
            'synaplan-ai-support-chat',
            __('Help & Support', 'synaplan-ai-support-chat'),
            __('Help & Support', 'synaplan-ai-support-chat'),
            'manage_options',
            'synaplan-ai-support-chat-help',
            array($this, 'help_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin pages
        if (strpos($hook, 'synaplan-ai-support-chat') === false) {
            return;
        }
        
        wp_enqueue_style(
            'synaplan-wp-admin-css',
            Synaplan_WP_Core::get_plugin_url('admin/css/admin.css'),
            array(),
            SYNAPLAN_WP_VERSION
        );
        
        wp_enqueue_script(
            'synaplan-wp-admin-js',
            Synaplan_WP_Core::get_plugin_url('admin/js/admin.js'),
            array('jquery'),
            SYNAPLAN_WP_VERSION,
            true
        );
        
        // Add page-specific styles
        $this->enqueue_page_specific_styles($hook);
        
        // Add page-specific scripts
        $this->enqueue_page_specific_scripts($hook);
        
        // Localize script
        wp_localize_script('synaplan-wp-admin-js', 'synaplan_wp_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('synaplan_wp_admin_nonce'),
            'strings' => array(
                'loading' => __('Loading...', 'synaplan-ai-support-chat'),
                'error' => __('An error occurred. Please try again.', 'synaplan-ai-support-chat'),
                'success' => __('Success!', 'synaplan-ai-support-chat'),
                'confirm' => __('Are you sure?', 'synaplan-ai-support-chat')
            )
        ));
    }
    
    /**
     * Enqueue page-specific styles as separate files
     */
    private function enqueue_page_specific_styles($hook) {
        // Dashboard styles
        if (strpos($hook, 'toplevel_page_synaplan-ai-support-chat') !== false) {
            $css_file = Synaplan_WP_Core::get_plugin_path('admin/views/dashboard-inline.css');
            if (file_exists($css_file)) {
                wp_enqueue_style(
                    'synaplan-wp-dashboard-css',
                    Synaplan_WP_Core::get_plugin_url('admin/views/dashboard-inline.css'),
                    array('synaplan-wp-admin-css'),
                    SYNAPLAN_WP_VERSION
                );
            }
        }
        // Settings styles
        elseif (strpos($hook, 'synaplan-ai-support-chat-settings') !== false) {
            $css_file = Synaplan_WP_Core::get_plugin_path('admin/views/settings-inline.css');
            if (file_exists($css_file)) {
                wp_enqueue_style(
                    'synaplan-wp-settings-css',
                    Synaplan_WP_Core::get_plugin_url('admin/views/settings-inline.css'),
                    array('synaplan-wp-admin-css'),
                    SYNAPLAN_WP_VERSION
                );
            }
        }
        // Help styles
        elseif (strpos($hook, 'synaplan-ai-support-chat-help') !== false) {
            $css_file = Synaplan_WP_Core::get_plugin_path('admin/views/help-inline.css');
            if (file_exists($css_file)) {
                wp_enqueue_style(
                    'synaplan-wp-help-css',
                    Synaplan_WP_Core::get_plugin_url('admin/views/help-inline.css'),
                    array('synaplan-wp-admin-css'),
                    SYNAPLAN_WP_VERSION
                );
            }
        }
    }
    
    /**
     * Enqueue page-specific scripts as separate files
     */
    private function enqueue_page_specific_scripts($hook) {
        // Dashboard scripts
        if (strpos($hook, 'toplevel_page_synaplan-ai-support-chat') !== false) {
            $js_file = Synaplan_WP_Core::get_plugin_path('admin/views/dashboard-inline.js');
            if (file_exists($js_file)) {
                wp_enqueue_script(
                    'synaplan-wp-dashboard-js',
                    Synaplan_WP_Core::get_plugin_url('admin/views/dashboard-inline.js'),
                    array('jquery', 'synaplan-wp-admin-js'),
                    SYNAPLAN_WP_VERSION,
                    true
                );
            }
        }
    }
    
    /**
     * Main admin page
     */
    public function admin_page() {
        $setup_completed = Synaplan_WP_Core::is_setup_completed();
        
        if (!$setup_completed) {
            $this->render_wizard();
        } else {
            $this->render_dashboard();
        }
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        $this->render_settings();
    }
    
    /**
     * Help page
     */
    public function help_page() {
        $this->render_help();
    }
    
    /**
     * Render setup wizard
     */
    private function render_wizard() {
        $wizard = new Synaplan_WP_Wizard();
        $wizard->render();
    }
    
    /**
     * Render dashboard
     */
    private function render_dashboard() {
        $widget_config = Synaplan_WP_Core::get_widget_config();
        $api_key = Synaplan_WP_Core::get_api_key();
        $user_id = Synaplan_WP_Core::get_user_id();
        
        include Synaplan_WP_Core::get_plugin_path('admin/views/dashboard.php');
    }
    
    /**
     * Render settings page
     */
    private function render_settings() {
        $widget_config = Synaplan_WP_Core::get_widget_config();
        
        if (isset($_POST['submit']) && isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'synaplan_wp_settings')) {
            $this->save_settings();
        }
        
        include Synaplan_WP_Core::get_plugin_path('admin/views/settings.php');
    }
    
    /**
     * Render help page
     */
    private function render_help() {
        include Synaplan_WP_Core::get_plugin_path('admin/views/help.php');
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in render_settings()
        $config = array(
            'integration_type' => isset($_POST['integration_type']) ? sanitize_text_field(wp_unslash($_POST['integration_type'])) : 'floating-button',
            'color' => isset($_POST['color']) ? sanitize_hex_color(wp_unslash($_POST['color'])) : '#007bff',
            'icon_color' => isset($_POST['icon_color']) ? sanitize_hex_color(wp_unslash($_POST['icon_color'])) : '#ffffff',
            'position' => isset($_POST['position']) ? sanitize_text_field(wp_unslash($_POST['position'])) : 'bottom-right',
            'auto_message' => isset($_POST['auto_message']) ? sanitize_textarea_field(wp_unslash($_POST['auto_message'])) : '',
            'auto_open' => isset($_POST['auto_open']) ? true : false,
            'prompt' => isset($_POST['prompt']) ? sanitize_text_field(wp_unslash($_POST['prompt'])) : 'general'
        );
        // phpcs:enable WordPress.Security.NonceVerification.Missing
        
        Synaplan_WP_Core::update_widget_config($config);
        
        add_settings_error(
            'synaplan_wp_settings',
            'settings_saved',
            __('Settings saved successfully!', 'synaplan-ai-support-chat'),
            'updated'
        );
    }
    
    /**
     * Handle wizard step AJAX request
     */
    public function handle_wizard_step() {
        check_ajax_referer('synaplan_wp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Insufficient permissions.', 'synaplan-ai-support-chat'));
        }
        
        $step = isset($_POST['step']) ? intval($_POST['step']) : 1;
        
        // Sanitize and validate only the specific fields we need based on step
        $data = array();
        
        // Common fields across steps
        if (isset($_POST['email'])) {
            $data['email'] = sanitize_email(wp_unslash($_POST['email']));
        }
        if (isset($_POST['password'])) {
            // Password should not be sanitized but validated
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Password intentionally not sanitized to preserve special characters
            $data['password'] = wp_unslash($_POST['password']);
        }
        if (isset($_POST['language'])) {
            $data['language'] = sanitize_text_field(wp_unslash($_POST['language']));
        }
        if (isset($_POST['terms'])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via rest_sanitize_boolean
            $data['terms'] = rest_sanitize_boolean(wp_unslash($_POST['terms']));
        }
        if (isset($_POST['intro_message'])) {
            $data['intro_message'] = sanitize_textarea_field(wp_unslash($_POST['intro_message']));
        }
        if (isset($_POST['prompt'])) {
            $data['prompt'] = sanitize_text_field(wp_unslash($_POST['prompt']));
        }
        if (isset($_POST['widget_color'])) {
            $data['widget_color'] = sanitize_hex_color(wp_unslash($_POST['widget_color']));
        }
        if (isset($_POST['widget_position'])) {
            $data['widget_position'] = sanitize_text_field(wp_unslash($_POST['widget_position']));
        }
        if (isset($_POST['skip_files'])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via rest_sanitize_boolean
            $data['skip_files'] = rest_sanitize_boolean(wp_unslash($_POST['skip_files']));
        }
        if (isset($_POST['debug_mode'])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via rest_sanitize_boolean
            $data['debug_mode'] = rest_sanitize_boolean(wp_unslash($_POST['debug_mode']));
        }
        
        $wizard = new Synaplan_WP_Wizard();
        $result = $wizard->process_step($step, $data);
        
        wp_send_json($result);
    }
    
    /**
     * Handle save config AJAX request
     */
    public function handle_save_config() {
        check_ajax_referer('synaplan_wp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Insufficient permissions.', 'synaplan-ai-support-chat'));
        }
        
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array elements sanitized individually below
        $config = isset($_POST['config']) ? wp_unslash($_POST['config']) : array();
        
        // Sanitize config data
        $sanitized_config = array(
            'integration_type' => isset($config['integration_type']) ? sanitize_text_field($config['integration_type']) : 'floating-button',
            'color' => isset($config['color']) ? sanitize_hex_color($config['color']) : '#007bff',
            'icon_color' => isset($config['icon_color']) ? sanitize_hex_color($config['icon_color']) : '#ffffff',
            'position' => isset($config['position']) ? sanitize_text_field($config['position']) : 'bottom-right',
            'auto_message' => isset($config['auto_message']) ? sanitize_textarea_field($config['auto_message']) : '',
            'auto_open' => isset($config['auto_open']) ? true : false,
            'prompt' => isset($config['prompt']) ? sanitize_text_field($config['prompt']) : 'general'
        );
        
        Synaplan_WP_Core::update_widget_config($sanitized_config);
        
        wp_send_json_success(esc_html__('Configuration saved successfully!', 'synaplan-ai-support-chat'));
    }
    
    /**
     * Handle test API AJAX request
     */
    public function handle_test_api() {
        check_ajax_referer('synaplan_wp_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Insufficient permissions.', 'synaplan-ai-support-chat'));
        }
        
        $api = new Synaplan_WP_API();
        $result = $api->test_connection();
        
        wp_send_json($result);
    }
    
    /**
     * Get menu icon SVG
     */
    private function get_menu_icon() {
        return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4.75C4 3.7835 4.7835 3 5.75 3H18.25C19.2165 3 20 3.7835 20 4.75V14.25C20 15.2165 19.2165 16 18.25 16H8.41421L5.70711 18.7071C5.07714 19.3371 4 18.8898 4 17.9929V4.75Z" fill="#007bff"/>
        </svg>';
    }
}
