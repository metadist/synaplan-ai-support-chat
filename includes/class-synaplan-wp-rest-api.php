<?php
/**
 * REST API endpoints for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * REST API class
 */
class Synaplan_WP_REST_API {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('synaplan-wp/v1', '/verify', array(
            'methods' => 'POST',
            'callback' => array($this, 'verify_token'),
            'permission_callback' => '__return_true', // Public endpoint for API validation
            'args' => array(
                'token' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            ),
        ));
        
        register_rest_route('synaplan-wp/v1', '/site-info', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_site_info'),
            'permission_callback' => '__return_true', // Public endpoint for site validation
        ));
    }
    
    /**
     * Verify token endpoint
     */
    public function verify_token($request) {
        $token = $request->get_param('token');
        
        if (empty($token)) {
            return new WP_Error('missing_token', 'Token is required', array('status' => 400));
        }
        
        $is_valid = Synaplan_WP_Core::validate_verification_token($token);
        
        if ($is_valid) {
            // Get site information
            $site_info = array(
                'site_url' => get_site_url(),
                'site_name' => get_bloginfo('name'),
                'admin_email' => get_option('admin_email'),
                'wp_version' => get_bloginfo('version'),
                'plugin_version' => SYNAPLAN_WP_VERSION,
                'verified_at' => time()
            );
            
            return rest_ensure_response(array(
                'success' => true,
                'verified' => true,
                'site_info' => $site_info,
                'message' => 'WordPress site verified successfully'
            ));
        } else {
            return new WP_Error('invalid_token', 'Invalid or expired token', array('status' => 401));
        }
    }
    
    /**
     * Get site information endpoint
     */
    public function get_site_info($request) {
        $site_info = array(
            'site_url' => get_site_url(),
            'site_name' => get_bloginfo('name'),
            'admin_email' => get_option('admin_email'),
            'wp_version' => get_bloginfo('version'),
            'plugin_version' => SYNAPLAN_WP_VERSION,
            'timestamp' => time()
        );
        
        return rest_ensure_response(array(
            'success' => true,
            'site_info' => $site_info
        ));
    }
}
