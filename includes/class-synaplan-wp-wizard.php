<?php
/**
 * Setup wizard class for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Setup wizard class
 */
class Synaplan_WP_Wizard {
    
    /**
     * Current step
     */
    private $current_step = 1;
    
    /**
     * Total steps
     */
    private $total_steps = 4;
    
    /**
     * Wizard data
     */
    private $wizard_data = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->load_wizard_data();
    }
    
    /**
     * Load wizard data from session
     */
    private function load_wizard_data() {
        $user_id = get_current_user_id();
        $wizard_data = get_transient('synaplan_wizard_data_' . $user_id);
        
        if ($wizard_data) {
            $this->wizard_data = $wizard_data;
        }
    }
    
    /**
     * Save wizard data to session
     */
    private function save_wizard_data() {
        $user_id = get_current_user_id();
        set_transient('synaplan_wizard_data_' . $user_id, $this->wizard_data, HOUR_IN_SECONDS);
    }
    
    /**
     * Render wizard
     */
    public function render() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Step navigation via GET parameter
        $this->current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
        
        if ($this->current_step < 1 || $this->current_step > $this->total_steps) {
            $this->current_step = 1;
        }
        
        ?>
        <div class="wrap synaplan-wizard">
            <div class="synaplan-wizard-header">
                <div class="synaplan-wizard-logo">
                    <img src="<?php echo esc_url(Synaplan_WP_Core::get_plugin_url('assets/images/logo.svg')); ?>" alt="Synaplan" />
                </div>
                <h1><?php esc_html_e('Welcome to Synaplan AI', 'synaplan-ai-support-chat'); ?></h1>
                <p><?php esc_html_e('Let\'s set up your AI chat widget in just a few steps.', 'synaplan-ai-support-chat'); ?></p>
            </div>
            
            <div class="synaplan-wizard-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo esc_attr(($this->current_step / $this->total_steps) * 100); ?>%"></div>
                </div>
                <div class="progress-steps">
                    <?php for ($i = 1; $i <= $this->total_steps; $i++): ?>
                        <div class="step <?php echo absint($i) <= absint($this->current_step) ? 'active' : ''; ?>">
                            <span class="step-number"><?php echo absint($i); ?></span>
                            <span class="step-label"><?php echo esc_html($this->get_step_label($i)); ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="synaplan-wizard-content">
                <form id="synaplan-wizard-form" method="post">
                    <?php wp_nonce_field('synaplan_wizard', 'wizard_nonce'); ?>
                    <input type="hidden" name="step" value="<?php echo absint($this->current_step); ?>" />
                    
                    <?php $this->render_step($this->current_step); ?>
                    
                    <div class="wizard-actions">
                        <?php if ($this->current_step > 1): ?>
                            <button type="button" class="button button-secondary" id="prev-step">
                                <?php esc_html_e('Previous', 'synaplan-ai-support-chat'); ?>
                            </button>
                        <?php endif; ?>
                        
                        <button type="submit" class="button button-primary" id="next-step">
                            <?php echo absint($this->current_step) === absint($this->total_steps) ? esc_html__('Complete Setup', 'synaplan-ai-support-chat') : esc_html__('Next', 'synaplan-ai-support-chat'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get step label
     */
    private function get_step_label($step) {
        $labels = array(
            1 => __('Account', 'synaplan-ai-support-chat'),
            2 => __('Settings', 'synaplan-ai-support-chat'),
            3 => __('Knowledge', 'synaplan-ai-support-chat'),
            4 => __('Complete', 'synaplan-ai-support-chat')
        );
        
        return $labels[$step] ?? '';
    }
    
    /**
     * Render specific step
     */
    private function render_step($step) {
        switch ($step) {
            case 1:
                $this->render_step_1();
                break;
            case 2:
                $this->render_step_2();
                break;
            case 3:
                $this->render_step_3();
                break;
            case 4:
                $this->render_step_4();
                break;
        }
    }
    
    /**
     * Render step 1: Account creation
     */
    private function render_step_1() {
        $email = $this->wizard_data['email'] ?? get_option('admin_email');
        $password = $this->wizard_data['password'] ?? '';
        $language = $this->wizard_data['language'] ?? '';
        $debug_mode = $this->wizard_data['debug_mode'] ?? false;
        
        ?>
        <div class="wizard-step step-1">
            <h2><?php esc_html_e('Create Your Account', 'synaplan-ai-support-chat'); ?></h2>
            <p><?php esc_html_e('Enter your email and password to create your Synaplan account.', 'synaplan-ai-support-chat'); ?></p>
            
            <div class="form-group">
                <label for="email"><?php esc_html_e('Email Address', 'synaplan-ai-support-chat'); ?> <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" required />
                <small class="form-help"><?php esc_html_e('We\'ll use this to send you confirmation and updates.', 'synaplan-ai-support-chat'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="password"><?php esc_html_e('Password', 'synaplan-ai-support-chat'); ?> <span class="required">*</span></label>
                <input type="password" id="password" name="password" value="<?php echo esc_attr($password); ?>" required />
                <small class="form-help"><?php esc_html_e('Minimum 6 characters. Must include:', 'synaplan-ai-support-chat'); ?> <strong><?php esc_html_e('numbers', 'synaplan-ai-support-chat'); ?></strong> <?php esc_html_e('and', 'synaplan-ai-support-chat'); ?> <strong><?php esc_html_e('special characters', 'synaplan-ai-support-chat'); ?></strong> <?php esc_html_e('(e.g., !@#$%^&*)', 'synaplan-ai-support-chat'); ?></small>
                <div class="password-strength" id="password-strength"></div>
            </div>
            
            <div class="form-group">
                <label for="language"><?php esc_html_e('Website Language', 'synaplan-ai-support-chat'); ?></label>
                <select id="language" name="language">
                    <?php
                    $languages = Synaplan_WP_API::get_supported_languages();
                    $detected_language = Synaplan_WP_API::detect_website_language();
                    
                    foreach ($languages as $code => $name) {
                        $selected = ($code === $language) || ($code === $detected_language && empty($language)) ? 'selected' : '';
                        echo '<option value="' . esc_attr($code) . '" ' . esc_attr($selected) . '>' . esc_html($name) . '</option>';
                    }
                    ?>
                </select>
                <small class="form-help"><?php esc_html_e('This will be used for the AI responses.', 'synaplan-ai-support-chat'); ?></small>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required />
                    <?php esc_html_e('I agree to the', 'synaplan-ai-support-chat'); ?> <a href="#" target="_blank"><?php esc_html_e('Terms of Service', 'synaplan-ai-support-chat'); ?></a> <?php esc_html_e('and', 'synaplan-ai-support-chat'); ?> <a href="#" target="_blank"><?php esc_html_e('Privacy Policy', 'synaplan-ai-support-chat'); ?></a>
                </label>
            </div>
            
            <div class="form-group debug-toggle">
                <label class="checkbox-label debug-label">
                    <input type="checkbox" name="debug_mode" id="debug_mode" <?php echo $debug_mode ? 'checked' : ''; ?> />
                    <span class="debug-icon">🔧</span>
                    <?php esc_html_e('Debug Mode - Developer Console Logging', 'synaplan-ai-support-chat'); ?>
                </label>
                <small class="form-help debug-help">
                    <strong><?php esc_html_e('For Developers:', 'synaplan-ai-support-chat'); ?></strong>
                    <?php esc_html_e('Enable extensive logging in browser console to track data collection, sanitization, and API transfers for each wizard step.', 'synaplan-ai-support-chat'); ?>
                </small>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render step 2: Basic settings
     */
    private function render_step_2() {
        $intro_message = $this->wizard_data['intro_message'] ?? 'Hello! How can I help you today?';
        $prompt = $this->wizard_data['prompt'] ?? 'general';
        
        ?>
        <div class="wizard-step step-2">
            <h2><?php esc_html_e('Configure Your Chat Widget', 'synaplan-ai-support-chat'); ?></h2>
            <p><?php esc_html_e('Set up how your AI assistant will interact with visitors.', 'synaplan-ai-support-chat'); ?></p>
            
            <div class="form-group">
                <label for="intro_message"><?php esc_html_e('Welcome Message', 'synaplan-ai-support-chat'); ?></label>
                <textarea id="intro_message" name="intro_message" rows="3"><?php echo esc_textarea($intro_message); ?></textarea>
                <small class="form-help"><?php esc_html_e('This message will be shown to visitors when they first open the chat.', 'synaplan-ai-support-chat'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="prompt"><?php esc_html_e('AI Assistant Type', 'synaplan-ai-support-chat'); ?></label>
                <select id="prompt" name="prompt">
                    <?php
                    $prompts = Synaplan_WP_API::get_default_prompts();
                    
                    foreach ($prompts as $key => $name) {
                        $selected = $key === $prompt ? 'selected' : '';
                        echo '<option value="' . esc_attr($key) . '" ' . esc_attr($selected) . '>' . esc_html($name) . '</option>';
                    }
                    ?>
                </select>
                <small class="form-help"><?php esc_html_e('Choose the type of assistant that best fits your needs.', 'synaplan-ai-support-chat'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="widget_color"><?php esc_html_e('Widget Color', 'synaplan-ai-support-chat'); ?></label>
                <input type="color" id="widget_color" name="widget_color" value="#007bff" />
                <small class="form-help"><?php esc_html_e('Choose a color that matches your website design.', 'synaplan-ai-support-chat'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="widget_position"><?php esc_html_e('Widget Position', 'synaplan-ai-support-chat'); ?></label>
                <select id="widget_position" name="widget_position">
                    <option value="bottom-right"><?php esc_html_e('Bottom Right', 'synaplan-ai-support-chat'); ?></option>
                    <option value="bottom-left"><?php esc_html_e('Bottom Left', 'synaplan-ai-support-chat'); ?></option>
                    <option value="bottom-center"><?php esc_html_e('Bottom Center', 'synaplan-ai-support-chat'); ?></option>
                </select>
                <small class="form-help"><?php esc_html_e('Choose where the chat button will appear on your site.', 'synaplan-ai-support-chat'); ?></small>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render step 3: File upload
     */
    private function render_step_3() {
        ?>
        <div class="wizard-step step-3">
            <h2><?php esc_html_e('Add Knowledge Base (Optional)', 'synaplan-ai-support-chat'); ?></h2>
            <p><?php esc_html_e('Upload documents to help your AI assistant answer questions about your business.', 'synaplan-ai-support-chat'); ?></p>
            
            <div class="file-upload-area" id="file-upload-area">
                <div class="upload-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="10,9 9,9 8,9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h3><?php esc_html_e('Drop files here or click to upload', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php esc_html_e('Supported formats: PDF, DOC, DOCX, TXT (Max 10MB each)', 'synaplan-ai-support-chat'); ?></p>
                <input type="file" id="file-input" name="files[]" multiple accept=".pdf,.doc,.docx,.txt" style="display: none;" />
                <button type="button" class="button button-secondary" id="select-files">
                    <?php esc_html_e('Select Files', 'synaplan-ai-support-chat'); ?>
                </button>
            </div>
            
            <div class="uploaded-files" id="uploaded-files" style="display: none;">
                <h4><?php esc_html_e('Uploaded Files', 'synaplan-ai-support-chat'); ?></h4>
                <ul id="file-list"></ul>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="skip_files" />
                    <?php esc_html_e('Skip this step - I\'ll add files later', 'synaplan-ai-support-chat'); ?>
                </label>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render step 4: Confirmation
     */
    private function render_step_4() {
        ?>
        <div class="wizard-step step-4">
            <h2><?php esc_html_e('Review Your Settings', 'synaplan-ai-support-chat'); ?></h2>
            <p><?php esc_html_e('Please review your configuration before completing the setup.', 'synaplan-ai-support-chat'); ?></p>
            
            <div class="review-summary">
                <div class="summary-item">
                    <strong><?php esc_html_e('Email:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-email"><?php echo esc_html($this->wizard_data['email'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php esc_html_e('Language:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-language"><?php echo esc_html($this->wizard_data['language'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php esc_html_e('Welcome Message:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-intro"><?php echo esc_html($this->wizard_data['intro_message'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php esc_html_e('AI Type:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-prompt"><?php echo esc_html($this->wizard_data['prompt'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php esc_html_e('Widget Color:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-color"><?php echo esc_html($this->wizard_data['widget_color'] ?? '#007bff'); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php esc_html_e('Position:', 'synaplan-ai-support-chat'); ?></strong>
                    <span id="review-position"><?php echo esc_html($this->wizard_data['widget_position'] ?? 'bottom-right'); ?></span>
                </div>
            </div>
            
            <div class="completion-notice">
                <div class="notice-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="notice-content">
                    <h4><?php esc_html_e('Almost Done!', 'synaplan-ai-support-chat'); ?></h4>
                    <p><?php esc_html_e('After clicking "Complete Setup", we\'ll create your account and send you a confirmation email.', 'synaplan-ai-support-chat'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Process wizard step
     */
    public function process_step($step, $data) {
        switch ($step) {
            case 1:
                return $this->process_step_1($data);
            case 2:
                return $this->process_step_2($data);
            case 3:
                return $this->process_step_3($data);
            case 4:
                return $this->process_step_4($data);
            default:
                return array('success' => false, 'error' => 'Invalid step');
        }
    }
    
    /**
     * Process step 1: Account validation
     */
    private function process_step_1($data) {
        $email = sanitize_email($data['email']);
        $password = $data['password'];
        $language = sanitize_text_field($data['language']);
        $terms = isset($data['terms']) ? $data['terms'] : false;
        
        // Validate terms acceptance
        if (!$terms) {
            return array('success' => false, 'error' => __('Please accept the Terms of Service and Privacy Policy to continue.', 'synaplan-ai-support-chat'));
        }
        
        // Validate email
        if (!Synaplan_WP_Core::validate_email($email)) {
            return array('success' => false, 'error' => __('Please enter a valid email address.', 'synaplan-ai-support-chat'));
        }
        
        // Validate password
        if (!Synaplan_WP_Core::validate_password($password)) {
            return array('success' => false, 'error' => __('Password must be at least 6 characters with numbers and special characters.', 'synaplan-ai-support-chat'));
        }
        
        // Save debug mode setting
        $debug_mode = isset($data['debug_mode']) ? rest_sanitize_boolean($data['debug_mode']) : false;
        
        // Save step data
        $this->wizard_data['email'] = $email;
        $this->wizard_data['password'] = $password;
        $this->wizard_data['language'] = $language;
        $this->wizard_data['debug_mode'] = $debug_mode;
        $this->save_wizard_data();
        
        $response = array('success' => true, 'next_step' => 2);
        
        // Add debug info if enabled
        if ($debug_mode) {
            $response['debug'] = array(
                'step' => 1,
                'collected_data' => array(
                    'email' => $email,
                    'password' => '[REDACTED]',
                    'language' => $language,
                    'terms_accepted' => $terms ? 'yes' : 'no'
                ),
                'sanitized_data' => array(
                    'email' => $email,
                    'language' => $language
                ),
                'validation' => array(
                    'email_valid' => true,
                    'password_valid' => true,
                    'terms_accepted' => $terms
                ),
                'message' => 'Step 1 validation completed successfully'
            );
        }
        
        return $response;
    }
    
    /**
     * Process step 2: Settings validation
     */
    private function process_step_2($data) {
        $intro_message = sanitize_textarea_field($data['intro_message']);
        $prompt = sanitize_text_field($data['prompt']);
        $widget_color = sanitize_hex_color($data['widget_color']);
        $widget_position = sanitize_text_field($data['widget_position']);
        
        // Save step data
        $this->wizard_data['intro_message'] = $intro_message;
        $this->wizard_data['prompt'] = $prompt;
        $this->wizard_data['widget_color'] = $widget_color;
        $this->wizard_data['widget_position'] = $widget_position;
        $this->save_wizard_data();
        
        return array('success' => true, 'next_step' => 3);
    }
    
    /**
     * Process step 3: File upload using WordPress media library
     */
    private function process_step_3($data) {
        // Handle file uploads if any - use WordPress media library
        // phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified in handle_wizard_step() via check_ajax_referer()
        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            // phpcs:disable WordPress.Security.ValidatedSanitizedInput -- Files handled by WordPress wp_handle_upload()
            // Rate limiting: max 5 files per wizard session
            $file_count = count($_FILES['files']['name']);
            if ($file_count > 5) {
                return array(
                    'success' => false, 
                    'error' => __('Maximum 5 files allowed per wizard session', 'synaplan-ai-support-chat')
                );
            }
            
            $uploaded_files = array();
            
            // WordPress requires this for wp_handle_upload()
            if (!function_exists('wp_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }
            
            // Process each file
            for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
                // Skip if no file
                if (empty($_FILES['files']['tmp_name'][$i])) {
                    continue;
                }
                
                // Create a temporary $_FILES array for this single file (wp_handle_upload expects single file format)
                $file = array(
                    'name' => isset($_FILES['files']['name'][$i]) ? sanitize_file_name(wp_unslash($_FILES['files']['name'][$i])) : '',
                    'type' => isset($_FILES['files']['type'][$i]) ? sanitize_text_field(wp_unslash($_FILES['files']['type'][$i])) : '',
                    'tmp_name' => isset($_FILES['files']['tmp_name'][$i]) ? sanitize_text_field(wp_unslash($_FILES['files']['tmp_name'][$i])) : '',
                    'error' => isset($_FILES['files']['error'][$i]) ? absint($_FILES['files']['error'][$i]) : UPLOAD_ERR_NO_FILE,
                    'size' => isset($_FILES['files']['size'][$i]) ? absint($_FILES['files']['size'][$i]) : 0
                );
                // phpcs:enable WordPress.Security.ValidatedSanitizedInput
                // phpcs:enable WordPress.Security.NonceVerification.Missing
                
                // Verify file type from file itself, not from client
                $file_check = wp_check_filetype($file['name']);
                if ($file_check['type']) {
                    $file['type'] = $file_check['type'];
                }
                
                // Skip if upload error
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                // Validate file before uploading
                $api = new Synaplan_WP_API();
                $validation = $api->validate_file_upload($file);
                
                if (!$validation['valid']) {
                    return array(
                        'success' => false,
                        'error' => $validation['error']
                    );
                }
                
                // Use WordPress's built-in file upload handler
                $upload_overrides = array(
                    'test_form' => false,
                    'mimes' => array(
                        'pdf' => 'application/pdf',
                        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'txt' => 'text/plain'
                    )
                );
                
                $uploaded_file = wp_handle_upload($file, $upload_overrides);
                
                if (isset($uploaded_file['error'])) {
                    return array(
                        'success' => false,
                        'error' => $uploaded_file['error']
                    );
                }
                
                if (isset($uploaded_file['file'])) {
                    // Create attachment post in media library
                    $attachment = array(
                        'post_mime_type' => $uploaded_file['type'],
                        'post_title' => sanitize_file_name(pathinfo($file['name'], PATHINFO_FILENAME)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
                    
                    $attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);
                    
                    if (!is_wp_error($attachment_id) && $attachment_id > 0) {
                        $uploaded_files[] = array(
                            'name' => $file['name'],
                            'type' => $file['type'],
                            'file_path' => $uploaded_file['file'],
                            'size' => $file['size'],
                            'attachment_id' => $attachment_id,
                            'url' => $uploaded_file['url']
                        );
                    }
                }
            }
            
            $this->wizard_data['uploaded_files'] = $uploaded_files;
        }
        
        $this->save_wizard_data();
        
        return array('success' => true, 'next_step' => 4);
    }
    
    /**
     * Process step 4: Complete setup with new unified flow
     */
    private function process_step_4($data) {
        
        $api = new Synaplan_WP_API();
        
        // Prepare wizard data for complete setup
        $wizard_data = array(
            'email' => $this->wizard_data['email'],
            'password' => $this->wizard_data['password'],
            'language' => $this->wizard_data['language'],
            'widget_id' => 1,
            'widget_color' => $this->wizard_data['widget_color'],
            'icon_color' => '#ffffff',
            'widget_position' => $this->wizard_data['widget_position'],
            'intro_message' => $this->wizard_data['intro_message'],
            'prompt' => $this->wizard_data['prompt'],
            'debug_mode' => $this->wizard_data['debug_mode'] ?? false
        );
        
        // Get uploaded files if any
        $uploaded_files = $this->wizard_data['uploaded_files'] ?? array();
        
        
        // Call the new unified endpoint
        $result = $api->complete_wizard_setup($wizard_data, $uploaded_files);
        
        
        if (!$result['success']) {
            $error_message = 'Setup failed';
            if (isset($result['data']['error'])) {
                $error_message = $result['data']['error'];
            } elseif (isset($result['error'])) {
                $error_message = $result['error'];
            }
            
            Synaplan_WP_Core::log("Wizard setup failed: " . $error_message, 'error');
            return array('success' => false, 'error' => $error_message);
        }
        
        // Extract data from successful response
        $response_data = $result['data']['data'] ?? $result['data'];
        
        $api_key = $response_data['api_key'] ?? '';
        $user_id = $response_data['user_id'] ?? '';
        
        if (empty($api_key) || empty($user_id)) {
            Synaplan_WP_Core::log("Missing API key or user ID in response", 'error');
            return array('success' => false, 'error' => __('Setup succeeded but missing credentials', 'synaplan-ai-support-chat'));
        }
        
        // Save API credentials
        // Save credentials (logging removed for security)
        
        Synaplan_WP_Core::set_api_key($api_key);
        Synaplan_WP_Core::set_user_id($user_id);
        
        // Update widget configuration with user's settings
        $widget_config = array(
            'integration_type' => 'floating-button',
            'color' => $this->wizard_data['widget_color'],
            'icon_color' => '#ffffff',
            'position' => $this->wizard_data['widget_position'],
            'auto_message' => $this->wizard_data['intro_message'],
            'auto_open' => false,
            'prompt' => $this->wizard_data['prompt'],
            'email' => $this->wizard_data['email'],
            'language' => $this->wizard_data['language']
        );
        
        Synaplan_WP_Core::update_widget_config($widget_config);
        
        // Mark setup as completed
        Synaplan_WP_Core::mark_setup_completed();
        
        // Clean up wizard data and temp files
        $this->cleanup_wizard_data();
        
        return array(
            'success' => true,
            'completed' => true,
            'message' => 'Setup completed successfully'
        );
    }
    
    /**
     * Clean up wizard data and uploaded files
     */
    private function cleanup_wizard_data() {
        // Clean up uploaded files from media library
        if (!empty($this->wizard_data['uploaded_files'])) {
            foreach ($this->wizard_data['uploaded_files'] as $file_data) {
                if (isset($file_data['attachment_id']) && $file_data['attachment_id'] > 0) {
                    wp_delete_attachment($file_data['attachment_id'], true);
                }
            }
        }
        
        // Delete wizard session data
        $user_id = get_current_user_id();
        delete_transient('synaplan_wizard_data_' . $user_id);
    }
}
