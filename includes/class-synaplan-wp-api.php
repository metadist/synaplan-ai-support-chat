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
     * STEP 1: Verify WordPress site and create user
     * 
     * @param array $wizard_data Wizard configuration data
     * @return array Result with user_id
     */
    public function wizard_step1_create_user($wizard_data) {
        // Create verification token for WordPress site validation
        $verification_token = Synaplan_WP_Core::create_verification_token();
        $verification_url = Synaplan_WP_Core::get_verification_endpoint_url();
        
        // Don't include 'action' here - make_request adds it
        $data = array(
            'email' => $wizard_data['email'] ?? '',
            'password' => $wizard_data['password'] ?? '',
            'language' => $wizard_data['language'] ?? 'en',
            'verification_token' => $verification_token,
            'verification_url' => $verification_url,
            'site_url' => get_site_url()
        );
        
        return $this->make_request('wpStep1VerifyAndCreateUser', 'POST', $data);
    }
    
    /**
     * STEP 2: Create API key for user
     * 
     * @param int $user_id User ID from step 1
     * @return array Result with api_key
     */
    public function wizard_step2_create_api_key($user_id) {
        // Don't include 'action' here - make_request adds it
        $data = array(
            'user_id' => $user_id
        );
        
        return $this->make_request('wpStep2CreateApiKey', 'POST', $data);
    }
    
    /**
     * STEP 3: Upload single file
     * 
     * @param int $user_id User ID
     * @param array $file_data File information (file_path, name, type)
     * @return array Result with file info
     */
    public function wizard_step3_upload_file($user_id, $file_data) {
        if (!isset($file_data['file_path']) || !file_exists($file_data['file_path'])) {
            return array(
                'success' => false,
                'error' => 'File not found'
            );
        }
        
        // Read file contents
        $file_contents = file_get_contents($file_data['file_path']);
        if ($file_contents === false) {
            return array(
                'success' => false,
                'error' => 'Failed to read file'
            );
        }
        
        // Build boundary and multipart body for single file
        $boundary = '----WebKitFormBoundary' . wp_generate_password(16, false);
        $multipart_body = '';
        
        // Add user_id field
        $multipart_body .= "--{$boundary}\r\n";
        $multipart_body .= "Content-Disposition: form-data; name=\"action\"\r\n\r\n";
        $multipart_body .= "wpStep3UploadFile\r\n";
        
        $multipart_body .= "--{$boundary}\r\n";
        $multipart_body .= "Content-Disposition: form-data; name=\"user_id\"\r\n\r\n";
        $multipart_body .= "{$user_id}\r\n";
        
        // Add single file
        $multipart_body .= "--{$boundary}\r\n";
        $multipart_body .= "Content-Disposition: form-data; name=\"file\"; filename=\"{$file_data['name']}\"\r\n";
        $multipart_body .= "Content-Type: {$file_data['type']}\r\n\r\n";
        $multipart_body .= $file_contents . "\r\n";
        
        $multipart_body .= "--{$boundary}--\r\n";
        
        // Use WordPress HTTP API
        $response = wp_remote_post($this->api_base_url, array(
            'timeout' => 180,
            'body' => $multipart_body,
            'headers' => array(
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary
            ),
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message()
            );
        }
        
        $http_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        $decoded_body = json_decode($response_body, true);
        
        return array(
            'success' => $http_code >= 200 && $http_code < 300 && isset($decoded_body['success']) && $decoded_body['success'],
            'status_code' => $http_code,
            'data' => $decoded_body,
            'raw_body' => $response_body
        );
    }
    
    /**
     * STEP 4: Enable file search on general prompt
     * 
     * @param int $user_id User ID
     * @return array Result
     */
    public function wizard_step4_enable_file_search($user_id) {
        // Don't include 'action' here - make_request adds it
        $data = array(
            'user_id' => $user_id
        );
        
        return $this->make_request('wpStep4EnableFileSearch', 'POST', $data);
    }
    
    /**
     * STEP 5: Save widget configuration
     * 
     * @param int $user_id User ID
     * @param array $wizard_data Widget configuration
     * @return array Result
     */
    public function wizard_step5_save_widget($user_id, $wizard_data) {
        // Don't include 'action' here - make_request adds it
        $data = array(
            'user_id' => $user_id,
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
        
        return $this->make_request('wpStep5SaveWidget', 'POST', $data);
    }
    
    /**
     * Complete WordPress wizard setup - NEW STEP-BY-STEP VERSION
     * 
     * This method orchestrates the complete wizard flow using individual API calls
     * This is more reliable with WordPress HTTP API than the monolithic approach
     *
     * @param array $wizard_data Wizard configuration data including email, password, and widget settings
     * @param array $uploaded_files Array of uploaded file paths
     * @return array Result of the operation with user_id and api_key
     */
    public function complete_wizard_setup($wizard_data, $uploaded_files = array()) {
        $debug_mode = isset($wizard_data['debug_mode']) && $wizard_data['debug_mode'];
        $debug_log = array();
        
        // STEP 1: Verify site and create user
        if ($debug_mode) {
            $debug_log['step_1_request'] = array(
                'action' => 'wpStep1VerifyAndCreateUser',
                'email' => $wizard_data['email'] ?? '',
                'password' => '[REDACTED]',
                'language' => $wizard_data['language'] ?? 'en',
                'site_url' => get_site_url()
            );
        }
        
        $step1_result = $this->wizard_step1_create_user($wizard_data);
        
        if ($debug_mode) {
            $debug_log['step_1_response'] = array(
                'http_success' => $step1_result['success'],
                'status_code' => $step1_result['status_code'] ?? null,
                'full_response' => $step1_result['data'] ?? null
            );
        }
        
        // Extract API response from HTTP wrapper
        $step1_api_response = $step1_result['data'] ?? array();
        $step1_api_success = $step1_api_response['success'] ?? false;
        $step1_api_data = $step1_api_response['data'] ?? array();
        
        // Check both HTTP-level and API-level success
        if (!$step1_result['success'] || !$step1_api_success) {
            $error_msg = $step1_api_response['error'] ?? $step1_result['error'] ?? 'User creation failed';
            Synaplan_WP_Core::log("Step 1 failed: " . $error_msg, 'error');
            
            $result = array(
                'success' => false,
                'error' => $error_msg,
                'step' => 1
            );
            if ($debug_mode) {
                $result['debug_log'] = $debug_log;
                $result['debug_info'] = 'HTTP success: ' . ($step1_result['success'] ? 'true' : 'false') . ', API success: ' . ($step1_api_success ? 'true' : 'false');
            }
            return $result;
        }
        
        // Get user_id from the clean API data structure
        $user_id = $step1_api_data['user_id'] ?? null;
        
        if (empty($user_id)) {
            $error_details = 'User ID not found in API response';
            Synaplan_WP_Core::log($error_details . ': ' . wp_json_encode($step1_api_response), 'error');
            
            $result = array(
                'success' => false,
                'error' => 'User ID not returned from step 1. Please check your API configuration.',
                'step' => 1
            );
            if ($debug_mode) {
                $result['debug_log'] = $debug_log;
                $result['api_response_structure'] = $step1_api_response;
                $result['api_data_structure'] = $step1_api_data;
            }
            return $result;
        }
        
        // STEP 2: Create API key
        if ($debug_mode) {
            $debug_log['step_2_request'] = array(
                'action' => 'wpStep2CreateApiKey',
                'user_id' => $user_id
            );
        }
        
        $step2_result = $this->wizard_step2_create_api_key($user_id);
        
        // Extract API response from HTTP wrapper
        $step2_api_response = $step2_result['data'] ?? array();
        $step2_api_success = $step2_api_response['success'] ?? false;
        $step2_api_data = $step2_api_response['data'] ?? array();
        
        if ($debug_mode) {
            $debug_log['step_2_response'] = array(
                'http_success' => $step2_result['success'],
                'api_success' => $step2_api_success,
                'status_code' => $step2_result['status_code'] ?? null,
                'api_key' => isset($step2_api_data['api_key']) ? '[REDACTED - length: ' . strlen($step2_api_data['api_key']) . ']' : 'NOT FOUND'
            );
        }
        
        // Check both HTTP-level and API-level success
        if (!$step2_result['success'] || !$step2_api_success) {
            $error_msg = $step2_api_response['error'] ?? $step2_result['error'] ?? 'API key creation failed';
            Synaplan_WP_Core::log("Step 2 failed: " . $error_msg, 'error');
            
            $result = array(
                'success' => false,
                'error' => $error_msg,
                'step' => 2
            );
            if ($debug_mode) {
                $result['debug_log'] = $debug_log;
            }
            return $result;
        }
        
        // Get api_key from the clean API data structure
        $api_key = $step2_api_data['api_key'] ?? null;
        
        if (empty($api_key)) {
            $error_details = 'API key not found in API response';
            Synaplan_WP_Core::log($error_details . ': ' . wp_json_encode($step2_api_response), 'error');
            
            $result = array(
                'success' => false,
                'error' => 'API key not returned from step 2. Please check your API configuration.',
                'step' => 2
            );
            if ($debug_mode) {
                $result['debug_log'] = $debug_log;
                $result['api_response_structure'] = $step2_api_response;
            }
            return $result;
        }
        
        // STEP 3: Upload files (one at a time for better compatibility)
        $uploaded_files_count = 0;
        if ($debug_mode) {
            $debug_log['step_3_files'] = array();
        }
        
        if (!empty($uploaded_files)) {
            foreach ($uploaded_files as $index => $file_data) {
                if ($uploaded_files_count >= 5) break; // Rate limit: max 5 files
                
                if ($debug_mode) {
                    $debug_log['step_3_files'][$index] = array(
                        'request' => array(
                            'action' => 'wpStep3UploadFile',
                            'user_id' => $user_id,
                            'filename' => $file_data['name'] ?? 'unknown',
                            'filesize' => isset($file_data['size']) ? number_format($file_data['size']) . ' bytes' : 'unknown',
                            'filetype' => $file_data['type'] ?? 'unknown'
                        )
                    );
                }
                
                $step3_result = $this->wizard_step3_upload_file($user_id, $file_data);
                
                // Extract API response from HTTP wrapper
                $step3_api_response = $step3_result['data'] ?? array();
                $step3_api_success = $step3_api_response['success'] ?? false;
                $step3_api_data = $step3_api_response['data'] ?? array();
                
                if ($debug_mode) {
                    $debug_log['step_3_files'][$index]['response'] = array(
                        'http_success' => $step3_result['success'],
                        'api_success' => $step3_api_success,
                        'status_code' => $step3_result['status_code'] ?? null,
                        'file_id' => $step3_api_data['file_id'] ?? null,
                        'error' => $step3_api_response['error'] ?? $step3_result['error'] ?? null
                    );
                }
                
                if ($step3_result['success'] && $step3_api_success) {
                    $uploaded_files_count++;
                } else {
                    // Log error but continue with other files
                    $error_msg = $step3_api_response['error'] ?? $step3_result['error'] ?? 'Unknown error';
                    Synaplan_WP_Core::log("File upload failed: " . $error_msg, 'warning');
                }
            }
        }
        
        // STEP 4: Enable file search if files were uploaded
        if ($uploaded_files_count > 0) {
            if ($debug_mode) {
                $debug_log['step_4_request'] = array(
                    'action' => 'wpStep4EnableFileSearch',
                    'user_id' => $user_id
                );
            }
            
            $step4_result = $this->wizard_step4_enable_file_search($user_id);
            
            // Extract API response from HTTP wrapper
            $step4_api_response = $step4_result['data'] ?? array();
            $step4_api_success = $step4_api_response['success'] ?? false;
            
            if ($debug_mode) {
                $debug_log['step_4_response'] = array(
                    'http_success' => $step4_result['success'],
                    'api_success' => $step4_api_success,
                    'status_code' => $step4_result['status_code'] ?? null,
                    'error' => $step4_api_response['error'] ?? $step4_result['error'] ?? null
                );
            }
            
            if (!$step4_result['success'] || !$step4_api_success) {
                // Log error but don't fail the entire setup
                $error_msg = $step4_api_response['error'] ?? $step4_result['error'] ?? 'Unknown error';
                Synaplan_WP_Core::log("Step 4 failed: " . $error_msg, 'warning');
            }
        } else {
            if ($debug_mode) {
                $debug_log['step_4_skipped'] = 'No files uploaded, skipping file search configuration';
            }
        }
        
        // STEP 5: Save widget configuration
        if ($debug_mode) {
            $debug_log['step_5_request'] = array(
                'action' => 'wpStep5SaveWidget',
                'user_id' => $user_id,
                'widgetColor' => $wizard_data['widget_color'] ?? '#007bff',
                'widgetPosition' => $wizard_data['widget_position'] ?? 'bottom-right',
                'autoMessage' => $wizard_data['intro_message'] ?? 'Hello! How can I help you today?',
                'widgetPrompt' => $wizard_data['prompt'] ?? 'general'
            );
        }
        
        $step5_result = $this->wizard_step5_save_widget($user_id, $wizard_data);
        
        // Extract API response from HTTP wrapper
        $step5_api_response = $step5_result['data'] ?? array();
        $step5_api_success = $step5_api_response['success'] ?? false;
        
        if ($debug_mode) {
            $debug_log['step_5_response'] = array(
                'http_success' => $step5_result['success'],
                'api_success' => $step5_api_success,
                'status_code' => $step5_result['status_code'] ?? null,
                'error' => $step5_api_response['error'] ?? $step5_result['error'] ?? null
            );
        }
        
        if (!$step5_result['success'] || !$step5_api_success) {
            // Log error but don't fail the entire setup
            $error_msg = $step5_api_response['error'] ?? $step5_result['error'] ?? 'Unknown error';
            Synaplan_WP_Core::log("Step 5 failed: " . $error_msg, 'warning');
        }
        
        // Return success with all data
        $result = array(
            'success' => true,
            'status_code' => 200,
            'data' => array(
                'success' => true,
                'data' => array(
                    'user_id' => $user_id,
                    'api_key' => $api_key,
                    'filesProcessed' => $uploaded_files_count,
                    'widget_configured' => $step5_result['success'] ?? false
                )
            )
        );
        
        if ($debug_mode) {
            $debug_log['summary'] = array(
                'total_steps' => 5,
                'step_1_create_user' => 'SUCCESS',
                'step_2_create_api_key' => 'SUCCESS',
                'step_3_upload_files' => $uploaded_files_count . ' files uploaded',
                'step_4_enable_file_search' => $uploaded_files_count > 0 ? (($step4_result['success'] ?? false) && ($step4_api_success ?? false) ? 'SUCCESS' : 'FAILED') : 'SKIPPED',
                'step_5_save_widget' => ($step5_result['success'] ?? false) && ($step5_api_success ?? false) ? 'SUCCESS' : 'FAILED',
                'overall_status' => 'SUCCESS',
                'user_id' => $user_id,
                'api_key_length' => strlen($api_key)
            );
            $result['debug_log'] = $debug_log;
        }
        
        return $result;
    }
}
