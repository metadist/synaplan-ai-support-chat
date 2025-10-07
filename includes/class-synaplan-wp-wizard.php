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
        $session_id = $this->get_session_id();
        
        if ($session_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
            
            $result = $wpdb->get_row($wpdb->prepare(
                "SELECT step_data FROM $table_name WHERE session_id = %s",
                $session_id
            ));
            
            if ($result) {
                $this->wizard_data = json_decode($result->step_data, true) ?: array();
            }
        }
    }
    
    /**
     * Save wizard data to session
     */
    private function save_wizard_data() {
        $session_id = $this->get_session_id();
        
        if (!$session_id) {
            $session_id = Synaplan_WP_Core::generate_session_id();
            $this->set_session_id($session_id);
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
        
        $wpdb->replace(
            $table_name,
            array(
                'session_id' => $session_id,
                'step_data' => json_encode($this->wizard_data)
            ),
            array('%s', '%s')
        );
    }
    
    /**
     * Get session ID
     */
    private function get_session_id() {
        if (!session_id()) {
            session_start();
        }
        
        return $_SESSION['synaplan_wizard_session_id'] ?? null;
    }
    
    /**
     * Set session ID
     */
    private function set_session_id($session_id) {
        if (!session_id()) {
            session_start();
        }
        
        $_SESSION['synaplan_wizard_session_id'] = $session_id;
    }
    
    /**
     * Render wizard
     */
    public function render() {
        $this->current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
        
        if ($this->current_step < 1 || $this->current_step > $this->total_steps) {
            $this->current_step = 1;
        }
        
        ?>
        <div class="wrap synaplan-wizard">
            <div class="synaplan-wizard-header">
                <div class="synaplan-wizard-logo">
                    <img src="<?php echo Synaplan_WP_Core::get_plugin_url('assets/images/logo.svg'); ?>" alt="Synaplan" />
                </div>
                <h1><?php _e('Welcome to Synaplan AI', 'synaplan-wp-ai'); ?></h1>
                <p><?php _e('Let\'s set up your AI chat widget in just a few steps.', 'synaplan-wp-ai'); ?></p>
            </div>
            
            <div class="synaplan-wizard-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo ($this->current_step / $this->total_steps) * 100; ?>%"></div>
                </div>
                <div class="progress-steps">
                    <?php for ($i = 1; $i <= $this->total_steps; $i++): ?>
                        <div class="step <?php echo $i <= $this->current_step ? 'active' : ''; ?>">
                            <span class="step-number"><?php echo $i; ?></span>
                            <span class="step-label"><?php echo $this->get_step_label($i); ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="synaplan-wizard-content">
                <form id="synaplan-wizard-form" method="post">
                    <?php wp_nonce_field('synaplan_wizard', 'wizard_nonce'); ?>
                    <input type="hidden" name="step" value="<?php echo $this->current_step; ?>" />
                    
                    <?php $this->render_step($this->current_step); ?>
                    
                    <div class="wizard-actions">
                        <?php if ($this->current_step > 1): ?>
                            <button type="button" class="button button-secondary" id="prev-step">
                                <?php _e('Previous', 'synaplan-wp-ai'); ?>
                            </button>
                        <?php endif; ?>
                        
                        <button type="submit" class="button button-primary" id="next-step">
                            <?php echo $this->current_step === $this->total_steps ? __('Complete Setup', 'synaplan-wp-ai') : __('Next', 'synaplan-wp-ai'); ?>
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
            1 => __('Account', 'synaplan-wp-ai'),
            2 => __('Settings', 'synaplan-wp-ai'),
            3 => __('Knowledge', 'synaplan-wp-ai'),
            4 => __('Complete', 'synaplan-wp-ai')
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
        $email = $this->wizard_data['email'] ?? '';
        $password = $this->wizard_data['password'] ?? '';
        $language = $this->wizard_data['language'] ?? '';
        
        ?>
        <div class="wizard-step step-1">
            <h2><?php _e('Create Your Account', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Enter your email and password to create your Synaplan account.', 'synaplan-wp-ai'); ?></p>
            
            <div class="form-group">
                <label for="email"><?php _e('Email Address', 'synaplan-wp-ai'); ?> <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo esc_attr($email); ?>" required />
                <small class="form-help"><?php _e('We\'ll use this to send you confirmation and updates.', 'synaplan-wp-ai'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="password"><?php _e('Password', 'synaplan-wp-ai'); ?> <span class="required">*</span></label>
                <input type="password" id="password" name="password" value="<?php echo esc_attr($password); ?>" required />
                <small class="form-help"><?php _e('Minimum 6 characters with numbers and special characters.', 'synaplan-wp-ai'); ?></small>
                <div class="password-strength" id="password-strength"></div>
            </div>
            
            <div class="form-group">
                <label for="language"><?php _e('Website Language', 'synaplan-wp-ai'); ?></label>
                <select id="language" name="language">
                    <?php
                    $api = new Synaplan_WP_API();
                    $languages = $api->get_supported_languages();
                    $detected_language = $api->detect_website_language();
                    
                    foreach ($languages as $code => $name) {
                        $selected = ($code === $language) || ($code === $detected_language && empty($language)) ? 'selected' : '';
                        echo "<option value=\"$code\" $selected>$name</option>";
                    }
                    ?>
                </select>
                <small class="form-help"><?php _e('This will be used for the AI responses.', 'synaplan-wp-ai'); ?></small>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required />
                    <?php _e('I agree to the', 'synaplan-wp-ai'); ?> <a href="#" target="_blank"><?php _e('Terms of Service', 'synaplan-wp-ai'); ?></a> <?php _e('and', 'synaplan-wp-ai'); ?> <a href="#" target="_blank"><?php _e('Privacy Policy', 'synaplan-wp-ai'); ?></a>
                </label>
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
            <h2><?php _e('Configure Your Chat Widget', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Set up how your AI assistant will interact with visitors.', 'synaplan-wp-ai'); ?></p>
            
            <div class="form-group">
                <label for="intro_message"><?php _e('Welcome Message', 'synaplan-wp-ai'); ?></label>
                <textarea id="intro_message" name="intro_message" rows="3"><?php echo esc_textarea($intro_message); ?></textarea>
                <small class="form-help"><?php _e('This message will be shown to visitors when they first open the chat.', 'synaplan-wp-ai'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="prompt"><?php _e('AI Assistant Type', 'synaplan-wp-ai'); ?></label>
                <select id="prompt" name="prompt">
                    <?php
                    $api = new Synaplan_WP_API();
                    $prompts = $api->get_default_prompts();
                    
                    foreach ($prompts as $key => $name) {
                        $selected = $key === $prompt ? 'selected' : '';
                        echo "<option value=\"$key\" $selected>$name</option>";
                    }
                    ?>
                </select>
                <small class="form-help"><?php _e('Choose the type of assistant that best fits your needs.', 'synaplan-wp-ai'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="widget_color"><?php _e('Widget Color', 'synaplan-wp-ai'); ?></label>
                <input type="color" id="widget_color" name="widget_color" value="#007bff" />
                <small class="form-help"><?php _e('Choose a color that matches your website design.', 'synaplan-wp-ai'); ?></small>
            </div>
            
            <div class="form-group">
                <label for="widget_position"><?php _e('Widget Position', 'synaplan-wp-ai'); ?></label>
                <select id="widget_position" name="widget_position">
                    <option value="bottom-right"><?php _e('Bottom Right', 'synaplan-wp-ai'); ?></option>
                    <option value="bottom-left"><?php _e('Bottom Left', 'synaplan-wp-ai'); ?></option>
                    <option value="bottom-center"><?php _e('Bottom Center', 'synaplan-wp-ai'); ?></option>
                </select>
                <small class="form-help"><?php _e('Choose where the chat button will appear on your site.', 'synaplan-wp-ai'); ?></small>
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
            <h2><?php _e('Add Knowledge Base (Optional)', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Upload documents to help your AI assistant answer questions about your business.', 'synaplan-wp-ai'); ?></p>
            
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
                <h3><?php _e('Drop files here or click to upload', 'synaplan-wp-ai'); ?></h3>
                <p><?php _e('Supported formats: PDF, DOCX (Max 10MB each)', 'synaplan-wp-ai'); ?></p>
                <input type="file" id="file-input" name="files[]" multiple accept=".pdf,.docx" style="display: none;" />
                <button type="button" class="button button-secondary" id="select-files">
                    <?php _e('Select Files', 'synaplan-wp-ai'); ?>
                </button>
            </div>
            
            <div class="uploaded-files" id="uploaded-files" style="display: none;">
                <h4><?php _e('Uploaded Files', 'synaplan-wp-ai'); ?></h4>
                <ul id="file-list"></ul>
            </div>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="skip_files" />
                    <?php _e('Skip this step - I\'ll add files later', 'synaplan-wp-ai'); ?>
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
            <h2><?php _e('Review Your Settings', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Please review your configuration before completing the setup.', 'synaplan-wp-ai'); ?></p>
            
            <div class="review-summary">
                <div class="summary-item">
                    <strong><?php _e('Email:', 'synaplan-wp-ai'); ?></strong>
                    <span id="review-email"><?php echo esc_html($this->wizard_data['email'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php _e('Language:', 'synaplan-wp-ai'); ?></strong>
                    <span id="review-language"><?php echo esc_html($this->wizard_data['language'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php _e('Welcome Message:', 'synaplan-wp-ai'); ?></strong>
                    <span id="review-intro"><?php echo esc_html($this->wizard_data['intro_message'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php _e('AI Type:', 'synaplan-wp-ai'); ?></strong>
                    <span id="review-prompt"><?php echo esc_html($this->wizard_data['prompt'] ?? ''); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php _e('Widget Color:', 'synaplan-wp-ai'); ?></strong>
                    <span id="review-color"><?php echo esc_html($this->wizard_data['widget_color'] ?? '#007bff'); ?></span>
                </div>
                <div class="summary-item">
                    <strong><?php _e('Position:', 'synaplan-wp-ai'); ?></strong>
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
                    <h4><?php _e('Almost Done!', 'synaplan-wp-ai'); ?></h4>
                    <p><?php _e('After clicking "Complete Setup", we\'ll create your account and send you a confirmation email.', 'synaplan-wp-ai'); ?></p>
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
        
        // Validate email
        if (!Synaplan_WP_Core::validate_email($email)) {
            return array('success' => false, 'error' => __('Please enter a valid email address.', 'synaplan-wp-ai'));
        }
        
        // Validate password
        if (!Synaplan_WP_Core::validate_password($password)) {
            return array('success' => false, 'error' => __('Password must be at least 6 characters with numbers and special characters.', 'synaplan-wp-ai'));
        }
        
        // Save step data
        $this->wizard_data['email'] = $email;
        $this->wizard_data['password'] = $password;
        $this->wizard_data['language'] = $language;
        $this->save_wizard_data();
        
        return array('success' => true, 'next_step' => 2);
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
     * Process step 3: File upload
     */
    private function process_step_3($data) {
        // Handle file uploads if any
        if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
            $uploaded_files = array();
            
            for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
                $file = array(
                    'name' => $_FILES['files']['name'][$i],
                    'type' => $_FILES['files']['type'][$i],
                    'tmp_name' => $_FILES['files']['tmp_name'][$i],
                    'size' => $_FILES['files']['size'][$i]
                );
                
                $api = new Synaplan_WP_API();
                $result = $api->process_file_upload($file);
                
                if ($result['success']) {
                    $uploaded_files[] = array(
                        'name' => $file['name'],
                        'file_id' => $result['file_id']
                    );
                }
            }
            
            $this->wizard_data['uploaded_files'] = $uploaded_files;
        }
        
        $this->save_wizard_data();
        
        return array('success' => true, 'next_step' => 4);
    }
    
    /**
     * Process step 4: Complete setup
     */
    private function process_step_4($data) {
        $api = new Synaplan_WP_API();
        
        // Register user
        $register_result = $api->register_user(
            $this->wizard_data['email'],
            $this->wizard_data['password'],
            $this->wizard_data['language']
        );
        
        if (!$register_result['success']) {
            return array('success' => false, 'error' => $register_result['data']['error'] ?? __('Registration failed.', 'synaplan-wp-ai'));
        }
        
        // Create API key
        $api_key_result = $api->create_api_key('WordPress Plugin');
        
        if (!$api_key_result['success']) {
            return array('success' => false, 'error' => $api_key_result['data']['error'] ?? __('API key creation failed.', 'synaplan-wp-ai'));
        }
        
        // Save API credentials
        Synaplan_WP_Core::set_api_key($api_key_result['data']['key']);
        Synaplan_WP_Core::set_user_id($register_result['data']['user_id']);
        
        // Save widget configuration
        $widget_config = array(
            'integration_type' => 'floating-button',
            'color' => $this->wizard_data['widget_color'],
            'icon_color' => '#ffffff',
            'position' => $this->wizard_data['widget_position'],
            'auto_message' => $this->wizard_data['intro_message'],
            'auto_open' => false,
            'prompt' => $this->wizard_data['prompt']
        );
        
        Synaplan_WP_Core::update_widget_config($widget_config);
        
        // Save widget to Synaplan
        $api->set_api_key($api_key_result['data']['key']);
        $save_widget_result = $api->save_widget($widget_config);
        
        // Mark setup as completed
        Synaplan_WP_Core::mark_setup_completed();
        
        // Clean up wizard data
        $this->cleanup_wizard_data();
        
        return array('success' => true, 'completed' => true);
    }
    
    /**
     * Clean up wizard data
     */
    private function cleanup_wizard_data() {
        $session_id = $this->get_session_id();
        
        if ($session_id) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'synaplan_wizard_sessions';
            
            $wpdb->delete($table_name, array('session_id' => $session_id), array('%s'));
        }
        
        unset($_SESSION['synaplan_wizard_session_id']);
    }
}
