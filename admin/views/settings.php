<?php
/**
 * Settings page view
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Synaplan AI Settings', 'synaplan-wp-ai'); ?></h1>
    
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'synaplan-wp-ai'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="synaplan-settings-container">
        <div class="synaplan-settings-main">
            <form method="post" action="">
                <?php wp_nonce_field('synaplan_wp_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="integration_type"><?php _e('Integration Type', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <select name="integration_type" id="integration_type">
                                <option value="floating-button" <?php selected($widget_config['integration_type'] ?? 'floating-button', 'floating-button'); ?>>
                                    <?php _e('Floating Button', 'synaplan-wp-ai'); ?>
                                </option>
                                <option value="inline" <?php selected($widget_config['integration_type'] ?? '', 'inline'); ?>>
                                    <?php _e('Inline Widget', 'synaplan-wp-ai'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Choose how the chat widget appears on your site.', 'synaplan-wp-ai'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="color"><?php _e('Widget Color', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <input type="color" name="color" id="color" value="<?php echo esc_attr($widget_config['color'] ?? '#007bff'); ?>" />
                            <p class="description"><?php _e('Choose the primary color for your chat widget.', 'synaplan-wp-ai'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="icon_color"><?php _e('Icon Color', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <input type="color" name="icon_color" id="icon_color" value="<?php echo esc_attr($widget_config['icon_color'] ?? '#ffffff'); ?>" />
                            <p class="description"><?php _e('Choose the color for the chat icon.', 'synaplan-wp-ai'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="position"><?php _e('Position', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <select name="position" id="position">
                                <option value="bottom-right" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-right'); ?>>
                                    <?php _e('Bottom Right', 'synaplan-wp-ai'); ?>
                                </option>
                                <option value="bottom-left" <?php selected($widget_config['position'] ?? '', 'bottom-left'); ?>>
                                    <?php _e('Bottom Left', 'synaplan-wp-ai'); ?>
                                </option>
                                <option value="top-right" <?php selected($widget_config['position'] ?? '', 'top-right'); ?>>
                                    <?php _e('Top Right', 'synaplan-wp-ai'); ?>
                                </option>
                                <option value="top-left" <?php selected($widget_config['position'] ?? '', 'top-left'); ?>>
                                    <?php _e('Top Left', 'synaplan-wp-ai'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Choose where the chat widget appears on your site.', 'synaplan-wp-ai'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="auto_message"><?php _e('Welcome Message', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <textarea name="auto_message" id="auto_message" rows="3" cols="50"><?php echo esc_textarea($widget_config['auto_message'] ?? 'Hello! How can I help you today?'); ?></textarea>
                            <p class="description"><?php _e('The initial message shown when users open the chat.', 'synaplan-wp-ai'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="auto_open"><?php _e('Auto Open', 'synaplan-wp-ai'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="auto_open" id="auto_open" value="1" <?php checked($widget_config['auto_open'] ?? false, true); ?> />
                            <label for="auto_open"><?php _e('Automatically open the chat widget when users visit the site', 'synaplan-wp-ai'); ?></label>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Save Settings', 'synaplan-wp-ai')); ?>
            </form>
        </div>
        
        <div class="synaplan-settings-sidebar">
            <div class="synaplan-widget-preview">
                <h3><?php _e('Widget Preview', 'synaplan-wp-ai'); ?></h3>
                <div id="widget-preview" class="preview-container">
                    <div id="preview-widget" class="preview-widget <?php echo esc_attr($widget_config['position'] ?? 'bottom-right'); ?>">
                        💬
                    </div>
                </div>
                <p class="description"><?php _e('This is how your chat widget will appear on your website.', 'synaplan-wp-ai'); ?></p>
            </div>
            
            <div class="synaplan-status">
                <h3><?php _e('Status', 'synaplan-wp-ai'); ?></h3>
                <p><strong><?php _e('Setup Status:', 'synaplan-wp-ai'); ?></strong> 
                    <?php if (Synaplan_WP_Core::is_setup_completed()): ?>
                        <span style="color: green;"><?php _e('Completed', 'synaplan-wp-ai'); ?></span>
                    <?php else: ?>
                        <span style="color: red;"><?php _e('Not Completed', 'synaplan-wp-ai'); ?></span>
                    <?php endif; ?>
                </p>
                <p><strong><?php _e('API Key:', 'synaplan-wp-ai'); ?></strong> 
                    <?php if (Synaplan_WP_Core::get_api_key()): ?>
                        <span style="color: green;"><?php _e('Configured', 'synaplan-wp-ai'); ?></span>
                    <?php else: ?>
                        <span style="color: red;"><?php _e('Not Configured', 'synaplan-wp-ai'); ?></span>
                    <?php endif; ?>
                </p>
                
                <?php if (Synaplan_WP_Core::is_setup_completed()): ?>
                    <div class="synaplan-account-info">
                        <h4><?php _e('Account Information', 'synaplan-wp-ai'); ?></h4>
                        <?php 
                        $user_id = Synaplan_WP_Core::get_user_id();
                        $widget_config = Synaplan_WP_Core::get_widget_config();
                        $registered_email = $widget_config['email'] ?? '';
                        ?>
                        
                        <?php if ($registered_email): ?>
                            <p><strong><?php _e('Registered Email:', 'synaplan-wp-ai'); ?></strong><br>
                                <code><?php echo esc_html($registered_email); ?></code>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($user_id): ?>
                            <p><strong><?php _e('User ID:', 'synaplan-wp-ai'); ?></strong><br>
                                <code><?php echo esc_html($user_id); ?></code>
                            </p>
                        <?php endif; ?>
                        
                        <p><strong><?php _e('Synaplan Dashboard:', 'synaplan-wp-ai'); ?></strong><br>
                            <a href="https://app.synaplan.com/" target="_blank" class="button button-secondary">
                                <?php _e('Open Synaplan Dashboard', 'synaplan-wp-ai'); ?>
                                <span class="dashicons dashicons-external" style="font-size: 14px; margin-left: 5px;"></span>
                            </a>
                        </p>
                        
                        <p class="description">
                            <?php _e('Manage your AI settings, view analytics, and configure advanced options in your Synaplan dashboard.', 'synaplan-wp-ai'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.synaplan-settings-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.synaplan-settings-main {
    flex: 2;
}

.synaplan-settings-sidebar {
    flex: 1;
}

.synaplan-widget-preview,
.synaplan-status {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 15px;
    margin-bottom: 20px;
}

.synaplan-widget-preview h3,
.synaplan-status h3 {
    margin-top: 0;
}

.synaplan-account-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #e1e1e1;
}

.synaplan-account-info h4 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #23282d;
}

.synaplan-account-info p {
    margin: 8px 0;
}

.synaplan-account-info code {
    background: #f1f1f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 12px;
}

.synaplan-account-info .button {
    margin-top: 5px;
    text-decoration: none;
}

.synaplan-account-info .button:hover {
    text-decoration: none;
}

.preview-container {
    position: relative;
    height: 200px;
    border: 1px solid #ddd;
    background: #f9f9f9;
    border-radius: 8px;
    overflow: hidden;
}

.preview-widget {
    position: absolute;
    width: 60px;
    height: 60px;
    background: <?php echo esc_attr($widget_config['color'] ?? '#007bff'); ?>;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: <?php echo esc_attr($widget_config['icon_color'] ?? '#ffffff'); ?>;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.preview-widget:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

/* Position classes */
.preview-widget.bottom-right {
    bottom: 20px;
    right: 20px;
}

.preview-widget.bottom-left {
    bottom: 20px;
    left: 20px;
}

.preview-widget.top-right {
    top: 20px;
    right: 20px;
}

.preview-widget.top-left {
    top: 20px;
    left: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize preview with current values
    updatePreview();
    
    // Update preview when any setting changes
    $('#color, #icon_color, #position').on('change input', function() {
        updatePreview();
    });
    
    function updatePreview() {
        var bgColor = $('#color').val();
        var iconColor = $('#icon_color').val();
        var position = $('#position').val();
        
        // Update colors
        $('#preview-widget').css({
            'background': bgColor,
            'color': iconColor
        });
        
        // Update position
        $('#preview-widget').removeClass('bottom-right bottom-left top-right top-left');
        $('#preview-widget').addClass(position);
        
        console.log('Preview updated:', {
            bgColor: bgColor,
            iconColor: iconColor,
            position: position
        });
    }
    
    // Add some interactivity to the preview
    $('#preview-widget').on('click', function() {
        $(this).css('transform', 'scale(0.95)');
        setTimeout(function() {
            $('#preview-widget').css('transform', 'scale(1)');
        }, 150);
    });
});
</script>
