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
        
        // Debug logging
        Synaplan_WP_Core::log("Making API request to: $url");
        Synaplan_WP_Core::log("Action: $action");
        Synaplan_WP_Core::log("Data: " . wp_json_encode($args['body']));
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            Synaplan_WP_Core::log("API Error: " . $response->get_error_message());
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body, true);
        
        Synaplan_WP_Core::log("API Response Status: $status_code");
        Synaplan_WP_Core::log("API Response Body: $body");
        
        return array(
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'data' => $decoded_body,
            'raw_body' => $body
        );
    }
    
    /**
     * Register new user - DEPRECATED: Use complete_wizard_setup instead
     * This method is kept for backward compatibility
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
                'error' => __('No API key configured', 'synaplan-ai-support-chat')
            );
        }
        
        $result = $this->get_profile();
        
        if ($result['success']) {
            return array(
                'success' => true,
                'message' => __('API connection successful', 'synaplan-ai-support-chat')
            );
        } else {
            return array(
                'success' => false,
                'error' => $result['data']['error'] ?? __('API connection failed', 'synaplan-ai-support-chat')
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
            'general' => __('General Support', 'synaplan-ai-support-chat'),
            'sales' => __('Sales Assistant', 'synaplan-ai-support-chat'),
            'technical' => __('Technical Support', 'synaplan-ai-support-chat'),
            'customer_service' => __('Customer Service', 'synaplan-ai-support-chat')
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
                'error' => __('Only PDF and DOCX files are allowed', 'synaplan-ai-support-chat')
            );
        }
        
        if ($file['size'] > $max_size) {
            return array(
                'valid' => false,
                'error' => __('File size must be less than 10MB', 'synaplan-ai-support-chat')
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
                'message' => __('File uploaded successfully', 'synaplan-ai-support-chat')
            );
        } else {
            return array(
                'success' => false,
                'error' => $result['data']['error'] ?? __('File upload failed', 'synaplan-ai-support-chat')
            );
        }
    }
    
    /**
     * Upload file for RAG vectorization
     */
    public function upload_file_for_rag($file_path, $file_name, $file_type, $user_id, $api_key) {
        // Security validation
        if (!file_exists($file_path)) {
            return array(
                'success' => false,
                'error' => 'File not found'
            );
        }
        
        // Validate file size (max 10MB for RAG)
        $max_size = 10 * 1024 * 1024; // 10MB
        if (filesize($file_path) > $max_size) {
            return array(
                'success' => false,
                'error' => 'File size exceeds 10MB limit'
            );
        }
        
        // Validate file type
        $allowed_types = array('application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        if (!in_array($file_type, $allowed_types)) {
            return array(
                'success' => false,
                'error' => 'Only PDF and DOCX files are allowed for RAG'
            );
        }
        
        // Build multipart form data for file upload using WordPress HTTP API
        $boundary = wp_generate_password(24, false);
        $file_content = file_get_contents($file_path);
        
        $body = '';
        
        // Add regular form fields
        $fields = array(
            'action' => 'ragUpload',
            'user_id' => $user_id,
            'groupKey' => 'WORDPRESS_WIZARD'
        );
        
        foreach ($fields as $name => $value) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
            $body .= "{$value}\r\n";
        }
        
        // Add file
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Disposition: form-data; name=\"files[]\"; filename=\"{$file_name}\"\r\n";
        $body .= "Content-Type: {$file_type}\r\n\r\n";
        $body .= $file_content . "\r\n";
        $body .= "--{$boundary}--\r\n";
        
        Synaplan_WP_Core::log("Uploading file for RAG: " . $file_name);
        
        $response = wp_remote_post($this->api_base_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary
            ),
            'body' => $body,
            'timeout' => 120,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            Synaplan_WP_Core::log("RAG upload error: " . $response->get_error_message());
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $http_code = wp_remote_retrieve_response_code($response);
        $body_response = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body_response, true);
        
        Synaplan_WP_Core::log("RAG upload response: " . $body_response);
        
        return array(
            'success' => $http_code >= 200 && $http_code < 300,
            'status_code' => $http_code,
            'data' => $decoded_body,
            'raw_body' => $body_response
        );
    }
    
    /**
     * Complete WordPress wizard setup with verification, user creation, API key, files, prompt, and widget
     * 
     * NEW: This endpoint handles the complete flow:
     * 1. Verify WordPress site via callback
     * 2. Create user with status 'NEW' (no email confirmation needed)
     * 3. Create API key
     * 4. Upload files to RAG system
     * 5. Enable file search on general prompt
     * 6. Save widget configuration
     *
     * @param array $wizard_data Wizard configuration data including email, password, and widget settings
     * @param array $uploaded_files Array of uploaded file paths
     * @return array Result of the operation with user_id and api_key
     */
    public function complete_wizard_setup($wizard_data, $uploaded_files = array()) {
        // Create verification token for WordPress site validation
        $verification_token = Synaplan_WP_Core::create_verification_token();
        $verification_url = Synaplan_WP_Core::get_verification_endpoint_url();
        
        // Build multipart form data using WordPress HTTP API
        $boundary = wp_generate_password(24, false);
        $body = '';
        
        // Add regular form fields
        $fields = array(
            'action' => 'wpWizardComplete',
            'email' => $wizard_data['email'] ?? '',
            'password' => $wizard_data['password'] ?? '',
            'language' => $wizard_data['language'] ?? 'en',
            'verification_token' => $verification_token,
            'verification_url' => $verification_url,
            'site_url' => get_site_url(),
            'widgetId' => $wizard_data['widget_id'] ?? 1,
            'widgetColor' => $wizard_data['widget_color'] ?? '#007bff',
            'widgetIconColor' => $wizard_data['icon_color'] ?? '#ffffff',
            'widgetPosition' => $wizard_data['widget_position'] ?? 'bottom-right',
            'autoMessage' => $wizard_data['intro_message'] ?? 'Hello! How can I help you today?',
            'widgetPrompt' => $wizard_data['prompt'] ?? 'general',
            'autoOpen' => '0',
            'integrationType' => 'floating-button',
            'inlinePlaceholder' => 'Ask me anything...',
            'inlineButtonText' => 'Ask',
            'inlineFontSize' => '18',
            'inlineTextColor' => '#212529',
            'inlineBorderRadius' => '8'
        );
        
        foreach ($fields as $name => $value) {
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n";
            $body .= "{$value}\r\n";
        }
        
        // Add files if any (max 5 for wizard)
        if (!empty($uploaded_files)) {
            $file_count = 0;
            foreach ($uploaded_files as $index => $file_data) {
                if ($file_count >= 5) break; // Rate limit: max 5 files
                if (isset($file_data['file_path']) && file_exists($file_data['file_path'])) {
                    $file_content = file_get_contents($file_data['file_path']);
                    $body .= "--{$boundary}\r\n";
                    $body .= "Content-Disposition: form-data; name=\"files[]\"; filename=\"{$file_data['name']}\"\r\n";
                    $body .= "Content-Type: {$file_data['type']}\r\n\r\n";
                    $body .= $file_content . "\r\n";
                    $file_count++;
                }
            }
        }
        
        $body .= "--{$boundary}--\r\n";
        
        Synaplan_WP_Core::log("Completing wizard setup with verification for: " . $wizard_data['email']);
        Synaplan_WP_Core::log("Files to upload: " . count($uploaded_files));
        
        $response = wp_remote_post($this->api_base_url, array(
            'headers' => array(
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary
            ),
            'body' => $body,
            'timeout' => 180,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            Synaplan_WP_Core::log("Wizard completion error: " . $response->get_error_message());
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $http_code = wp_remote_retrieve_response_code($response);
        $body_response = wp_remote_retrieve_body($response);
        
        Synaplan_WP_Core::log("Wizard completion response code: " . $http_code);
        Synaplan_WP_Core::log("Wizard completion response: " . $body_response);
        
        $decoded_body = json_decode($body_response, true);
        
        return array(
            'success' => $http_code >= 200 && $http_code < 300 && isset($decoded_body['success']) && $decoded_body['success'],
            'status_code' => $http_code,
            'data' => $decoded_body,
            'raw_body' => $body_response
        );
    }
}
