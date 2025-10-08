<?php
/**
 * Settings view for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap synaplan-settings">
    <h1><?php _e('Synaplan AI Settings', 'synaplan-ai-support-chat'); ?></h1>
    
    <?php settings_errors('synaplan_wp_settings'); ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('synaplan_wp_settings'); ?>
        
        <div class="settings-card">
            <h2><?php _e('Widget Configuration', 'synaplan-ai-support-chat'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="integration_type"><?php _e('Integration Type', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="integration_type" id="integration_type" class="regular-text">
                            <option value="floating-button" <?php selected($widget_config['integration_type'] ?? 'floating-button', 'floating-button'); ?>>
                                <?php _e('Floating Button', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="inline-box" <?php selected($widget_config['integration_type'] ?? 'floating-button', 'inline-box'); ?>>
                                <?php _e('Inline Box', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description"><?php _e('Choose how the widget appears on your site', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="color"><?php _e('Primary Color', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="color" name="color" id="color" value="<?php echo esc_attr($widget_config['color'] ?? '#007bff'); ?>" />
                        <p class="description"><?php _e('Color for the chat button', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="icon_color"><?php _e('Icon Color', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="color" name="icon_color" id="icon_color" value="<?php echo esc_attr($widget_config['icon_color'] ?? '#ffffff'); ?>" />
                        <p class="description"><?php _e('Color for the icon inside the button', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="position"><?php _e('Position', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="position" id="position" class="regular-text">
                            <option value="bottom-right" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-right'); ?>>
                                <?php _e('Bottom Right', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="bottom-left" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-left'); ?>>
                                <?php _e('Bottom Left', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="bottom-center" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-center'); ?>>
                                <?php _e('Bottom Center', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description"><?php _e('Position of the chat button on your site', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="auto_message"><?php _e('Welcome Message', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <textarea name="auto_message" id="auto_message" rows="3" class="large-text"><?php echo esc_textarea($widget_config['auto_message'] ?? 'Hello! How can I help you today?'); ?></textarea>
                        <p class="description"><?php _e('First message shown to visitors', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="auto_open"><?php _e('Auto-open', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_open" id="auto_open" value="1" <?php checked($widget_config['auto_open'] ?? false, true); ?> />
                            <?php _e('Automatically open chat widget after a few seconds', 'synaplan-ai-support-chat'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="prompt"><?php _e('AI Assistant Type', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="prompt" id="prompt" class="regular-text">
                            <option value="general" <?php selected($widget_config['prompt'] ?? 'general', 'general'); ?>>
                                <?php _e('General Support', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="sales" <?php selected($widget_config['prompt'] ?? 'general', 'sales'); ?>>
                                <?php _e('Sales Assistant', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="technical" <?php selected($widget_config['prompt'] ?? 'general', 'technical'); ?>>
                                <?php _e('Technical Support', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="customer_service" <?php selected($widget_config['prompt'] ?? 'general', 'customer_service'); ?>>
                                <?php _e('Customer Service', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('You can customize prompts further on the', 'synaplan-ai-support-chat'); ?>
                            <a href="https://app.synaplan.com/index.php/prompts" target="_blank"><?php _e('Synaplan Dashboard', 'synaplan-ai-support-chat'); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'synaplan-ai-support-chat'); ?>" />
        </p>
    </form>
    
    <div class="settings-info">
        <h3><?php _e('Advanced Configuration', 'synaplan-ai-support-chat'); ?></h3>
        <p>
            <?php _e('For advanced settings like managing your knowledge base, configuring AI prompts, and accessing analytics, visit the', 'synaplan-ai-support-chat'); ?>
            <a href="https://app.synaplan.com/" target="_blank"><?php _e('Synaplan Dashboard', 'synaplan-ai-support-chat'); ?></a>.
        </p>
    </div>
</div>

<style>
.synaplan-settings {
    margin-top: 20px;
}

.settings-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.settings-card h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.settings-info {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.settings-info h3 {
    margin-top: 0;
}
</style>