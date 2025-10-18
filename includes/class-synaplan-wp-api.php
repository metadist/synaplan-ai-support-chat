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
     * Supported languages
     */
    const SUPPORTED_LANGUAGES = array(
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
    
    /**
     * Default prompts
     */
    const DEFAULT_PROMPTS = array(
        'general' => 'General Support',
        'sales' => 'Sales Assistant',
        'technical' => 'Technical Support',
        'customer_service' => 'Customer Service'
    );
    
    /**
     * Allowed file types for upload
     */
    const ALLOWED_FILE_TYPES = array(
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'text/plain'
    );
    
    /**
     * Max file size (10MB)
     */
    const MAX_FILE_SIZE = 10485760;
    
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
            Synaplan_WP_Core::log("API Error ($action): " . $response->get_error_message(), 'error');
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($body, true);
        
        return $this->build_response($status_code, $decoded_body, $body);
    }
    
    /**
     * Build standardized API response
     */
    private function build_response($status_code, $decoded_body = null, $raw_body = '') {
        return array(
            'success' => $status_code >= 200 && $status_code < 300,
            'status_code' => $status_code,
            'data' => $decoded_body,
            'raw_body' => $raw_body
        );
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
     * Upload file for RAG (note: files should be uploaded via wpWizardComplete for wizard)
     * This method is for standalone file uploads after wizard completion
     */
    public function upload_file($file_path, $file_name, $file_type) {
        // For RAG uploads, we need to use multipart/form-data with $_FILES
        // This cannot be done via wp_remote_request easily, so we use action=ragUpload
        $data = array(
            'action' => 'ragUpload',
            'file' => '@' . $file_path
        );
        
        return $this->make_request('ragUpload', 'POST', $data);
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
    public static function get_supported_languages() {
        return self::SUPPORTED_LANGUAGES;
    }
    
    /**
     * Detect website language
     */
    public static function detect_website_language() {
        $locale = get_locale();
        $language_code = substr($locale, 0, 2);
        
        return isset(self::SUPPORTED_LANGUAGES[$language_code]) ? $language_code : 'en';
    }
    
    /**
     * Get default prompts (translatable)
     */
    public static function get_default_prompts() {
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
        if (!in_array($file['type'], self::ALLOWED_FILE_TYPES)) {
            return array(
                'valid' => false,
                'error' => __('Only PDF, DOC, DOCX, and TXT files are allowed', 'synaplan-ai-support-chat')
            );
        }
        
        if ($file['size'] > self::MAX_FILE_SIZE) {
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
     * Complete WordPress wizard setup with verification, user creation, API key, files, prompt, and widget
     * 
     * Calls wpWizardComplete action which handles the complete flow:
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
        
        // Use cURL for multipart file uploads (WordPress HTTP API doesn't properly populate $_FILES)
        $ch = curl_init();
        
        // Build form data
        $post_data = array(
            'action' => 'wpWizardComplete',
            // User registration data
            'email' => $wizard_data['email'] ?? '',
            'password' => $wizard_data['password'] ?? '',
            'language' => $wizard_data['language'] ?? 'en',
            // Verification data
            'verification_token' => $verification_token,
            'verification_url' => $verification_url,
            'site_url' => get_site_url(),
            // Widget configuration (matching WordPressWizard.php expected fields)
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
        
        // Add files using CURLFile
        // To create $_FILES['files']['name'][0], $_FILES['files']['name'][1] structure,
        // we must build a custom multipart payload
        $file_count = 0;
        $boundary = '----WebKitFormBoundary' . uniqid();
        $multipart_body = '';
        
        // Add form fields
        foreach ($post_data as $key => $value) {
            $multipart_body .= "--{$boundary}\r\n";
            $multipart_body .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n";
            $multipart_body .= "{$value}\r\n";
        }
        
        // Add files
        if (!empty($uploaded_files)) {
            foreach ($uploaded_files as $file_data) {
                if ($file_count >= 5) break; // Rate limit: max 5 files
                
                if (isset($file_data['file_path']) && file_exists($file_data['file_path'])) {
                    $file_contents = file_get_contents($file_data['file_path']);
                    if ($file_contents !== false) {
                        // Use same field name 'files[]' for all files to create multidimensional $_FILES array
                        $multipart_body .= "--{$boundary}\r\n";
                        $multipart_body .= "Content-Disposition: form-data; name=\"files[]\"; filename=\"{$file_data['name']}\"\r\n";
                        $multipart_body .= "Content-Type: {$file_data['type']}\r\n\r\n";
                        $multipart_body .= $file_contents . "\r\n";
                        $file_count++;
                    }
                }
            }
        }
        
        $multipart_body .= "--{$boundary}--\r\n";
        
        curl_setopt($ch, CURLOPT_URL, $this->api_base_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $multipart_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data; boundary=' . $boundary,
            'Content-Length: ' . strlen($multipart_body)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            Synaplan_WP_Core::log("Wizard completion cURL error: " . $error, 'error');
            return array(
                'success' => false,
                'error' => $error
            );
        }
        
        $decoded_body = json_decode($response, true);
        
        return array(
            'success' => $http_code >= 200 && $http_code < 300 && isset($decoded_body['success']) && $decoded_body['success'],
            'status_code' => $http_code,
            'data' => $decoded_body,
            'raw_body' => $response
        );
    }
}
