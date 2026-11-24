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
    <h1><?php esc_html_e('Synaplan AI Settings', 'synaplan-ai-support-chat'); ?></h1>
    
    <?php settings_errors('synaplan_wp_settings'); ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('synaplan_wp_settings'); ?>
        
        <div class="settings-card">
            <h2><?php esc_html_e('Widget Configuration', 'synaplan-ai-support-chat'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="integration_type"><?php esc_html_e('Integration Type', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="integration_type" id="integration_type" class="regular-text">
                            <option value="floating-button" <?php selected($widget_config['integration_type'] ?? 'floating-button', 'floating-button'); ?>>
                                <?php esc_html_e('Floating Button', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="inline-box" <?php selected($widget_config['integration_type'] ?? 'floating-button', 'inline-box'); ?>>
                                <?php esc_html_e('Inline Box', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('Choose how the widget appears on your site', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="color"><?php esc_html_e('Primary Color', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="color" name="color" id="color" value="<?php echo esc_attr($widget_config['color'] ?? '#007bff'); ?>" />
                        <p class="description"><?php esc_html_e('Color for the chat button', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="icon_color"><?php esc_html_e('Icon Color', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="color" name="icon_color" id="icon_color" value="<?php echo esc_attr($widget_config['icon_color'] ?? '#ffffff'); ?>" />
                        <p class="description"><?php esc_html_e('Color for the icon inside the button', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="position"><?php esc_html_e('Position', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="position" id="position" class="regular-text">
                            <option value="bottom-right" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-right'); ?>>
                                <?php esc_html_e('Bottom Right', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="bottom-left" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-left'); ?>>
                                <?php esc_html_e('Bottom Left', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="bottom-center" <?php selected($widget_config['position'] ?? 'bottom-right', 'bottom-center'); ?>>
                                <?php esc_html_e('Bottom Center', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description"><?php esc_html_e('Position of the chat button on your site', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="auto_message"><?php esc_html_e('Welcome Message', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <textarea name="auto_message" id="auto_message" rows="3" class="large-text"><?php echo esc_textarea($widget_config['auto_message'] ?? 'Hello! How can I help you today?'); ?></textarea>
                        <p class="description"><?php esc_html_e('First message shown to visitors', 'synaplan-ai-support-chat'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="auto_open"><?php esc_html_e('Auto-open', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="auto_open" id="auto_open" value="1" <?php checked($widget_config['auto_open'] ?? false, true); ?> />
                            <?php esc_html_e('Automatically open chat widget after a few seconds', 'synaplan-ai-support-chat'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="prompt"><?php esc_html_e('AI Assistant Type', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <select name="prompt" id="prompt" class="regular-text">
                            <option value="general" <?php selected($widget_config['prompt'] ?? 'general', 'general'); ?>>
                                <?php esc_html_e('General Support', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="sales" <?php selected($widget_config['prompt'] ?? 'general', 'sales'); ?>>
                                <?php esc_html_e('Sales Assistant', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="technical" <?php selected($widget_config['prompt'] ?? 'general', 'technical'); ?>>
                                <?php esc_html_e('Technical Support', 'synaplan-ai-support-chat'); ?>
                            </option>
                            <option value="customer_service" <?php selected($widget_config['prompt'] ?? 'general', 'customer_service'); ?>>
                                <?php esc_html_e('Customer Service', 'synaplan-ai-support-chat'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('You can customize prompts further on the', 'synaplan-ai-support-chat'); ?>
                            <a href="https://app.synaplan.com/index.php/prompts" target="_blank"><?php esc_html_e('Synaplan Dashboard', 'synaplan-ai-support-chat'); ?></a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="settings-card">
            <h2><?php esc_html_e('Signed-in Visitors', 'synaplan-ai-support-chat'); ?></h2>
            <p>
                <?php esc_html_e('If your widget looks up user data ("User Data Integration" in the Synaplan dashboard), the plugin can tell it which WordPress user is signed in. The plugin signs the user ID on this server, so a visitor cannot pretend to be someone else. Copy both values from the widget settings in the Synaplan dashboard. Leave them empty if you do not use this.', 'synaplan-ai-support-chat'); ?>
            </p>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="synaplan_widget_public_id"><?php esc_html_e('Widget ID', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="synaplan_widget_public_id" id="synaplan_widget_public_id" class="regular-text code" value="<?php echo esc_attr(Synaplan_WP_Visitor::get_widget_id()); ?>" placeholder="wdg_..." autocomplete="off" />
                        <p class="description">
                            <?php esc_html_e('Starts with "wdg_". You find it in the embed code of your widget.', 'synaplan-ai-support-chat'); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="synaplan_visitor_signing_key"><?php esc_html_e('Signing key', 'synaplan-ai-support-chat'); ?></label>
                    </th>
                    <td>
                        <input type="password" name="synaplan_visitor_signing_key" id="synaplan_visitor_signing_key" class="regular-text code" value="" placeholder="<?php echo esc_attr(Synaplan_WP_Visitor::has_signing_key() ? __('A key is saved. Paste a new one to replace it.', 'synaplan-ai-support-chat') : ''); ?>" autocomplete="new-password" />
                        <p class="description">
                            <?php
                            if (Synaplan_WP_Visitor::has_signing_key()) {
                                esc_html_e('A signing key is saved. It is never shown again and never sent to visitors.', 'synaplan-ai-support-chat');
                            } else {
                                esc_html_e('No signing key saved yet. It stays on this server and is never sent to visitors.', 'synaplan-ai-support-chat');
                            }
                            ?>
                        </p>
                        <?php if (Synaplan_WP_Visitor::has_signing_key()) : ?>
                            <label for="synaplan_visitor_signing_key_remove">
                                <input type="checkbox" name="synaplan_visitor_signing_key_remove" id="synaplan_visitor_signing_key_remove" value="1" />
                                <?php esc_html_e('Remove the saved signing key', 'synaplan-ai-support-chat'); ?>
                            </label>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e('Save Changes', 'synaplan-ai-support-chat'); ?>" />
        </p>
    </form>
    
    <div class="settings-info">
        <h3><?php esc_html_e('Advanced Configuration', 'synaplan-ai-support-chat'); ?></h3>
        <p>
            <?php esc_html_e('For advanced settings like managing your knowledge base, configuring AI prompts, and accessing analytics, visit the', 'synaplan-ai-support-chat'); ?>
            <a href="https://app.synaplan.com/" target="_blank"><?php esc_html_e('Synaplan Dashboard', 'synaplan-ai-support-chat'); ?></a>.
        </p>
    </div>
</div>