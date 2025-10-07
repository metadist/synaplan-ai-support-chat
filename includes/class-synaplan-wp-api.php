<?php
/**
 * API client class for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * API client class
 */
class Synaplan_WP_API {
    
    /**
     * API base URL
     */
    private $api_base_url;
    
    /**
     * API key
     */
    private $api_key;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_base_url = 'https://app.synaplan.com/api.php'; // Correct API URL
        $this->api_key = Synaplan_WP_Core::get_api_key();
    }
    
    /**
     * Set API key
     */
    public function set_api_key($api_key) {
        $this->api_key = $api_key;
    }
    
    /**
     * Make API request
     */
    private function make_request($action, $method = 'POST', $data = null) {
        $url = $this->api_base_url; // Always use api.php
        
        $args = array(
            'method' => $method,
            'timeout' => 30
        );
        
        // Add Authorization header only if API key is available
        if (!empty($this->api_key)) {
            $args['headers'] = array(
                'Authorization' => 'Bearer ' . $this->api_key
            );
        }
        
        if ($data && in_array($method, array('POST', 'PUT', 'PATCH'))) {
            // Add action parameter and merge with data
            $post_data = array_merge(array('action' => $action), $data);
            $args['body'] = $post_data;
        } else {
            $args['body'] = array('action' => $action);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body, true);
        
        return array(
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'data' => $decoded_body,
            'raw_body' => $body
        );
    }
    
    /**
     * Register new user
     */
    public function register_user($email, $password, $language = 'en') {
        // Create verification token for WordPress site validation
        $verification_token = Synaplan_WP_Core::create_verification_token();
        $verification_url = Synaplan_WP_Core::get_verification_endpoint_url();
        
        $data = array(
            'email' => $email,
            'password' => $password,
            'language' => $language,
            'source' => 'wordpress_plugin',
            'verification_token' => $verification_token,
            'verification_url' => $verification_url,
            'site_url' => get_site_url()
        );
        
        return $this->make_request('userRegister', 'POST', $data);
    }
    
    /**
     * Create API key
     */
    public function create_api_key($name = 'WordPress Plugin') {
        $data = array(
            'name' => $name
        );
        
        return $this->make_request('createApiKey', 'POST', $data);
    }
    
    /**
     * Save widget configuration
     */
    public function save_widget($config) {
        return $this->make_request('saveWidget', 'POST', $config);
    }
    
    /**
     * Upload file for RAG
     */
    public function upload_file($file_path, $file_name, $file_type) {
        $url = $this->api_base_url . '/ragUpload';
        
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key
            ),
            'timeout' => 60,
            'body' => array(
                'file' => new CURLFile($file_path, $file_type, $file_name)
            )
        );
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body, true);
        
        return array(
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'data' => $decoded_body
        );
    }
    
    /**
     * Update prompt
     */
    public function update_prompt($prompt_data) {
        return $this->make_request('promptUpdate', 'POST', $prompt_data);
    }
    
    /**
     * Get user profile
     */
    public function get_profile() {
        return $this->make_request('getProfile', 'GET');
    }
    
    /**
     * Get widgets
     */
    public function get_widgets() {
        return $this->make_request('getWidgets', 'GET');
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        if (empty($this->api_key)) {
            return array(
                'success' => false,
                'error' => __('No API key configured', 'synaplan-wp-ai')
            );
        }
        
        $result = $this->get_profile();
        
        if ($result['success']) {
            return array(
                'success' => true,
                'message' => __('API connection successful', 'synaplan-wp-ai')
            );
        } else {
            return array(
                'success' => false,
                'error' => $result['data']['error'] ?? __('API connection failed', 'synaplan-wp-ai')
            );
        }
    }
    
    /**
     * Send confirmation email
     */
    public function send_confirmation_email($email) {
        $data = array(
            'email' => $email,
            'type' => 'confirmation'
        );
        
        return $this->make_request('sendEmail', 'POST', $data);
    }
    
    /**
     * Verify email confirmation
     */
    public function verify_email($token) {
        $data = array(
            'token' => $token
        );
        
        return $this->make_request('verifyEmail', 'POST', $data);
    }
    
    /**
     * Get supported languages
     */
    public function get_supported_languages() {
        return array(
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'it' => 'Italiano',
            'pt' => 'Português',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'ru' => 'Русский',
            'ja' => '日本語',
            'ko' => '한국어',
            'zh' => '中文'
        );
    }
    
    /**
     * Detect website language
     */
    public function detect_website_language() {
        $locale = get_locale();
        $language_code = substr($locale, 0, 2);
        
        $supported_languages = $this->get_supported_languages();
        
        return isset($supported_languages[$language_code]) ? $language_code : 'en';
    }
    
    /**
     * Get default prompts
     */
    public function get_default_prompts() {
        return array(
            'general' => __('General Support', 'synaplan-wp-ai'),
            'sales' => __('Sales Assistant', 'synaplan-wp-ai'),
            'technical' => __('Technical Support', 'synaplan-wp-ai'),
            'customer_service' => __('Customer Service', 'synaplan-wp-ai')
        );
    }
    
    /**
     * Validate file upload
     */
    public function validate_file_upload($file) {
        $allowed_types = array('application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $max_size = 10 * 1024 * 1024; // 10MB
        
        if (!in_array($file['type'], $allowed_types)) {
            return array(
                'valid' => false,
                'error' => __('Only PDF and DOCX files are allowed', 'synaplan-wp-ai')
            );
        }
        
        if ($file['size'] > $max_size) {
            return array(
                'valid' => false,
                'error' => __('File size must be less than 10MB', 'synaplan-wp-ai')
            );
        }
        
        return array('valid' => true);
    }
    
    /**
     * Process uploaded file
     */
    public function process_file_upload($file) {
        $validation = $this->validate_file_upload($file);
        
        if (!$validation['valid']) {
            return $validation;
        }
        
        // Upload file to Synaplan API
        $result = $this->upload_file($file['tmp_name'], $file['name'], $file['type']);
        
        if ($result['success']) {
            return array(
                'success' => true,
                'file_id' => $result['data']['file_id'] ?? null,
                'message' => __('File uploaded successfully', 'synaplan-wp-ai')
            );
        } else {
            return array(
                'success' => false,
                'error' => $result['data']['error'] ?? __('File upload failed', 'synaplan-wp-ai')
            );
        }
    }
}
