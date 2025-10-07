<?php
/**
 * Widget integration class for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Widget integration class
 */
class Synaplan_WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Only initialize if setup is completed
        if (!Synaplan_WP_Core::is_setup_completed()) {
            return;
        }
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'render_widget'));
        add_shortcode('synaplan_chat', array($this, 'shortcode_handler'));
        add_action('wp_head', array($this, 'add_widget_meta'));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'synaplan-wp-widget-css',
            Synaplan_WP_Core::get_plugin_url('public/css/public.css'),
            array(),
            SYNAPLAN_WP_VERSION
        );
        
        wp_enqueue_script(
            'synaplan-wp-widget-js',
            Synaplan_WP_Core::get_plugin_url('public/js/public.js'),
            array('jquery'),
            SYNAPLAN_WP_VERSION,
            true
        );
        
        // Localize script with widget configuration
        $config = Synaplan_WP_Core::get_widget_config();
        $user_id = Synaplan_WP_Core::get_user_id();
        
        wp_localize_script('synaplan-wp-widget-js', 'synaplan_wp_widget', array(
            'config' => $config,
            'user_id' => $user_id,
            'widget_id' => 1, // Default widget ID
            'api_url' => 'https://api.synaplan.com', // Update with actual API URL
            'strings' => array(
                'loading' => __('Loading...', 'synaplan-wp-ai'),
                'error' => __('An error occurred. Please try again.', 'synaplan-wp-ai'),
                'offline' => __('You appear to be offline. Please check your connection.', 'synaplan-wp-ai')
            )
        ));
    }
    
    /**
     * Render widget in footer
     */
    public function render_widget() {
        $config = Synaplan_WP_Core::get_widget_config();
        $user_id = Synaplan_WP_Core::get_user_id();
        
        if (empty($user_id)) {
            return;
        }
        
        // Generate widget script URL
        $widget_url = $this->get_widget_url($user_id, 1);
        
        ?>
        <script>
        (function() {
            var script = document.createElement('script');
            script.src = '<?php echo esc_url($widget_url); ?>';
            script.async = true;
            document.head.appendChild(script);
        })();
        </script>
        <?php
    }
    
    /**
     * Shortcode handler
     */
    public function shortcode_handler($atts) {
        $atts = shortcode_atts(array(
            'type' => 'inline-box',
            'placeholder' => 'Ask me anything...',
            'button_text' => 'Ask',
            'color' => '#007bff',
            'position' => 'center'
        ), $atts);
        
        $config = Synaplan_WP_Core::get_widget_config();
        $user_id = Synaplan_WP_Core::get_user_id();
        
        if (empty($user_id)) {
            return '<p>' . __('Synaplan widget is not configured.', 'synaplan-wp-ai') . '</p>';
        }
        
        // Generate widget script URL with inline mode
        $widget_url = $this->get_widget_url($user_id, 1, 'inline-box');
        
        ob_start();
        ?>
        <div class="synaplan-widget-shortcode" data-type="<?php echo esc_attr($atts['type']); ?>">
            <script>
            (function() {
                var script = document.createElement('script');
                script.src = '<?php echo esc_url($widget_url); ?>';
                script.async = true;
                document.head.appendChild(script);
            })();
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add widget meta tags
     */
    public function add_widget_meta() {
        $config = Synaplan_WP_Core::get_widget_config();
        
        echo '<meta name="synaplan-widget-enabled" content="true" />' . "\n";
        echo '<meta name="synaplan-widget-type" content="' . esc_attr($config['integration_type']) . '" />' . "\n";
        echo '<meta name="synaplan-widget-color" content="' . esc_attr($config['color']) . '" />' . "\n";
    }
    
    /**
     * Get widget URL
     */
    private function get_widget_url($user_id, $widget_id, $mode = '') {
        $base_url = 'https://widget.synaplan.com'; // Update with actual widget URL
        $url = $base_url . '/widget.php?uid=' . $user_id . '&widgetid=' . $widget_id;
        
        if (!empty($mode)) {
            $url .= '&mode=' . $mode;
        }
        
        return $url;
    }
    
    /**
     * Get widget configuration for JavaScript
     */
    public function get_widget_config() {
        return Synaplan_WP_Core::get_widget_config();
    }
    
    /**
     * Update widget configuration
     */
    public function update_widget_config($config) {
        return Synaplan_WP_Core::update_widget_config($config);
    }
    
    /**
     * Check if widget should be displayed
     */
    public function should_display_widget() {
        // Check if setup is completed
        if (!Synaplan_WP_Core::is_setup_completed()) {
            return false;
        }
        
        // Check if user ID exists
        if (empty(Synaplan_WP_Core::get_user_id())) {
            return false;
        }
        
        // Check if widget is enabled
        $config = Synaplan_WP_Core::get_widget_config();
        if (empty($config)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get widget preview HTML
     */
    public function get_widget_preview() {
        $config = Synaplan_WP_Core::get_widget_config();
        
        if (empty($config)) {
            return '<p>' . __('No widget configuration found.', 'synaplan-wp-ai') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="synaplan-widget-preview">
            <div class="preview-header">
                <h3><?php _e('Widget Preview', 'synaplan-wp-ai'); ?></h3>
                <p><?php _e('This is how your chat widget will appear on your website.', 'synaplan-wp-ai'); ?></p>
            </div>
            
            <div class="preview-container">
                <div class="preview-widget" style="background-color: <?php echo esc_attr($config['color']); ?>;">
                    <div class="widget-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 4.75C4 3.7835 4.7835 3 5.75 3H18.25C19.2165 3 20 3.7835 20 4.75V14.25C20 15.2165 19.2165 16 18.25 16H8.41421L5.70711 18.7071C5.07714 19.3371 4 18.8898 4 17.9929V4.75Z" fill="white"/>
                        </svg>
                    </div>
                    <div class="widget-label"><?php _e('Chat', 'synaplan-wp-ai'); ?></div>
                </div>
                
                <div class="preview-info">
                    <div class="info-item">
                        <strong><?php _e('Type:', 'synaplan-wp-ai'); ?></strong>
                        <span><?php echo esc_html($config['integration_type'] === 'inline-box' ? __('Inline Box', 'synaplan-wp-ai') : __('Floating Button', 'synaplan-wp-ai')); ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php _e('Position:', 'synaplan-wp-ai'); ?></strong>
                        <span><?php echo esc_html(ucfirst(str_replace('-', ' ', $config['position']))); ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php _e('Color:', 'synaplan-wp-ai'); ?></strong>
                        <span style="color: <?php echo esc_attr($config['color']); ?>;"><?php echo esc_html($config['color']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong><?php _e('Auto-open:', 'synaplan-wp-ai'); ?></strong>
                        <span><?php echo $config['auto_open'] ? __('Yes', 'synaplan-wp-ai') : __('No', 'synaplan-wp-ai'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get widget statistics
     */
    public function get_widget_stats() {
        $api = new Synaplan_WP_API();
        
        // This would typically fetch stats from the API
        // For now, return placeholder data
        return array(
            'total_conversations' => 0,
            'total_messages' => 0,
            'avg_response_time' => 0,
            'satisfaction_score' => 0
        );
    }
    
    /**
     * Test widget functionality
     */
    public function test_widget() {
        $config = Synaplan_WP_Core::get_widget_config();
        $user_id = Synaplan_WP_Core::get_user_id();
        
        if (empty($user_id)) {
            return array('success' => false, 'error' => __('No user ID configured.', 'synaplan-wp-ai'));
        }
        
        if (empty($config)) {
            return array('success' => false, 'error' => __('No widget configuration found.', 'synaplan-wp-ai'));
        }
        
        // Test API connection
        $api = new Synaplan_WP_API();
        $test_result = $api->test_connection();
        
        if (!$test_result['success']) {
            return $test_result;
        }
        
        return array('success' => true, 'message' => __('Widget test successful.', 'synaplan-wp-ai'));
    }
}
