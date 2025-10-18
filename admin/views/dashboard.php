<?php
/**
 * Dashboard view for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get widget embed code (this is shown to users, not executed inline)
$user_id = Synaplan_WP_Core::get_user_id();
$widget_url = 'https://app.synaplan.com/widget.php?uid=' . $user_id . '&widgetid=1';
$embed_code = '<script>
(function() {
    var script = document.createElement(\'script\');
    script.src = \'' . $widget_url . '\';
    script.async = true;
    document.head.appendChild(script);
})();
</script>';
?>

<div class="wrap synaplan-dashboard">
    <h1><?php esc_html_e('Synaplan AI Dashboard', 'synaplan-ai-support-chat'); ?></h1>
    
    <div class="synaplan-dashboard-grid">
        <!-- Status Card -->
        <div class="dashboard-card status-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Setup Status', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <p class="status-message success">
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e('Your Synaplan AI chat widget is active and ready!', 'synaplan-ai-support-chat'); ?>
                </p>
                <p class="help-text">
                    <?php esc_html_e('The widget is now displaying on your website. Visitors can use it to chat with your AI assistant.', 'synaplan-ai-support-chat'); ?>
                </p>
            </div>
        </div>
        
        <!-- API Credentials Card -->
        <div class="dashboard-card credentials-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-admin-network"></span> <?php esc_html_e('API Credentials', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <div class="credential-row">
                    <label><?php esc_html_e('User ID:', 'synaplan-ai-support-chat'); ?></label>
                    <code class="credential-value"><?php echo esc_html($user_id); ?></code>
                </div>
                
                <div class="credential-row">
                    <label><?php esc_html_e('API Key:', 'synaplan-ai-support-chat'); ?></label>
                    <div class="api-key-container">
                        <code class="credential-value api-key-hidden" id="api-key-display">
                            <?php echo esc_html(str_repeat('•', 48)); ?>
                        </code>
                        <code class="credential-value api-key-revealed" id="api-key-revealed" style="display: none;">
                            <?php echo esc_html($api_key); ?>
                        </code>
                        <button type="button" class="button button-secondary" id="toggle-api-key"
                            data-show-text="<?php echo esc_attr__('Show API Key', 'synaplan-ai-support-chat'); ?>"
                            data-hide-text="<?php echo esc_attr__('Hide API Key', 'synaplan-ai-support-chat'); ?>">
                            <span class="dashicons dashicons-visibility"></span>
                            <span id="toggle-text"><?php esc_html_e('Show API Key', 'synaplan-ai-support-chat'); ?></span>
                        </button>
                        <button type="button" class="button button-secondary" id="copy-api-key" style="display: none;"
                            data-copied-text="<?php echo esc_attr__('Copied!', 'synaplan-ai-support-chat'); ?>">
                            <span class="dashicons dashicons-clipboard"></span>
                            <?php esc_html_e('Copy', 'synaplan-ai-support-chat'); ?>
                        </button>
                    </div>
                </div>
                
                <p class="help-text">
                    <span class="dashicons dashicons-info"></span>
                    <?php esc_html_e('Keep your API key secure. You can use it to access the full Synaplan platform at', 'synaplan-ai-support-chat'); ?>
                    <a href="https://app.synaplan.com/" target="_blank">app.synaplan.com</a>
                </p>
            </div>
        </div>
        
        <!-- Widget Embed Code Card -->
        <div class="dashboard-card embed-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-editor-code"></span> <?php esc_html_e('Widget Embed Code', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <p><?php esc_html_e('Your widget is automatically embedded on all pages. Use this code if you need to manually embed it elsewhere:', 'synaplan-ai-support-chat'); ?></p>
                <textarea readonly class="embed-code" id="embed-code"><?php echo esc_textarea($embed_code); ?></textarea>
                <button type="button" class="button button-secondary" id="copy-embed-code"
                    data-copied-text="<?php echo esc_attr__('Copied!', 'synaplan-ai-support-chat'); ?>">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php esc_html_e('Copy Code', 'synaplan-ai-support-chat'); ?>
                </button>
            </div>
        </div>
        
        <!-- Quick Links Card -->
        <div class="dashboard-card links-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-admin-links"></span> <?php esc_html_e('Quick Links', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <ul class="quick-links">
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=synaplan-ai-support-chat-settings')); ?>">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Widget Settings', 'synaplan-ai-support-chat'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/" target="_blank">
                            <span class="dashicons dashicons-external"></span>
                            <?php esc_html_e('Synaplan Dashboard', 'synaplan-ai-support-chat'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/index.php/filemanager" target="_blank">
                            <span class="dashicons dashicons-media-document"></span>
                            <?php esc_html_e('Manage Knowledge Base', 'synaplan-ai-support-chat'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/index.php/prompts" target="_blank">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php esc_html_e('Configure AI Prompts', 'synaplan-ai-support-chat'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=synaplan-ai-support-chat-help')); ?>">
                            <span class="dashicons dashicons-sos"></span>
                            <?php esc_html_e('Help & Support', 'synaplan-ai-support-chat'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>