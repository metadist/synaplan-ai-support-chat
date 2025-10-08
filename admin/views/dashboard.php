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

// Get widget embed code
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
    <h1><?php _e('Synaplan AI Dashboard', 'synaplan-wp-ai'); ?></h1>
    
    <div class="synaplan-dashboard-grid">
        <!-- Status Card -->
        <div class="dashboard-card status-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-yes-alt"></span> <?php _e('Setup Status', 'synaplan-wp-ai'); ?></h2>
            </div>
            <div class="card-body">
                <p class="status-message success">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e('Your Synaplan AI chat widget is active and ready!', 'synaplan-wp-ai'); ?>
                </p>
                <p class="help-text">
                    <?php _e('The widget is now displaying on your website. Visitors can use it to chat with your AI assistant.', 'synaplan-wp-ai'); ?>
                </p>
            </div>
        </div>
        
        <!-- API Credentials Card -->
        <div class="dashboard-card credentials-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-admin-network"></span> <?php _e('API Credentials', 'synaplan-wp-ai'); ?></h2>
            </div>
            <div class="card-body">
                <div class="credential-row">
                    <label><?php _e('User ID:', 'synaplan-wp-ai'); ?></label>
                    <code class="credential-value"><?php echo esc_html($user_id); ?></code>
                </div>
                
                <div class="credential-row">
                    <label><?php _e('API Key:', 'synaplan-wp-ai'); ?></label>
                    <div class="api-key-container">
                        <code class="credential-value api-key-hidden" id="api-key-display">
                            <?php echo str_repeat('•', 48); ?>
                        </code>
                        <code class="credential-value api-key-revealed" id="api-key-revealed" style="display: none;">
                            <?php echo esc_html($api_key); ?>
                        </code>
                        <button type="button" class="button button-secondary" id="toggle-api-key">
                            <span class="dashicons dashicons-visibility"></span>
                            <span id="toggle-text"><?php _e('Show API Key', 'synaplan-wp-ai'); ?></span>
                        </button>
                        <button type="button" class="button button-secondary" id="copy-api-key" style="display: none;">
                            <span class="dashicons dashicons-clipboard"></span>
                            <?php _e('Copy', 'synaplan-wp-ai'); ?>
                        </button>
                    </div>
                </div>
                
                <p class="help-text">
                    <span class="dashicons dashicons-info"></span>
                    <?php _e('Keep your API key secure. You can use it to access the full Synaplan platform at', 'synaplan-wp-ai'); ?>
                    <a href="https://app.synaplan.com/" target="_blank">app.synaplan.com</a>
                </p>
            </div>
        </div>
        
        <!-- Widget Embed Code Card -->
        <div class="dashboard-card embed-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-editor-code"></span> <?php _e('Widget Embed Code', 'synaplan-wp-ai'); ?></h2>
            </div>
            <div class="card-body">
                <p><?php _e('Your widget is automatically embedded on all pages. Use this code if you need to manually embed it elsewhere:', 'synaplan-wp-ai'); ?></p>
                <textarea readonly class="embed-code" id="embed-code"><?php echo esc_textarea($embed_code); ?></textarea>
                <button type="button" class="button button-secondary" id="copy-embed-code">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php _e('Copy Code', 'synaplan-wp-ai'); ?>
                </button>
            </div>
        </div>
        
        <!-- Quick Links Card -->
        <div class="dashboard-card links-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-admin-links"></span> <?php _e('Quick Links', 'synaplan-wp-ai'); ?></h2>
            </div>
            <div class="card-body">
                <ul class="quick-links">
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=synaplan-wp-ai-settings'); ?>">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php _e('Widget Settings', 'synaplan-wp-ai'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/" target="_blank">
                            <span class="dashicons dashicons-external"></span>
                            <?php _e('Synaplan Dashboard', 'synaplan-wp-ai'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/index.php/filemanager" target="_blank">
                            <span class="dashicons dashicons-media-document"></span>
                            <?php _e('Manage Knowledge Base', 'synaplan-wp-ai'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="https://app.synaplan.com/index.php/prompts" target="_blank">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php _e('Configure AI Prompts', 'synaplan-wp-ai'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=synaplan-wp-ai-help'); ?>">
                            <span class="dashicons dashicons-sos"></span>
                            <?php _e('Help & Support', 'synaplan-wp-ai'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.synaplan-dashboard {
    margin-top: 20px;
}

.synaplan-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.dashboard-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.dashboard-card .card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.dashboard-card .card-header h2 {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.dashboard-card .card-body {
    padding: 20px;
}

.status-message {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    border-radius: 4px;
    margin: 0 0 15px 0;
}

.status-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.credential-row {
    margin-bottom: 15px;
}

.credential-row label {
    display: block;
    font-weight: 600;
    margin-bottom: 5px;
    color: #555;
}

.credential-value {
    display: inline-block;
    background: #f5f5f5;
    padding: 8px 12px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 13px;
    border: 1px solid #ddd;
}

.api-key-container {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.api-key-container .credential-value {
    flex: 1;
    min-width: 200px;
}

.embed-code {
    width: 100%;
    min-height: 120px;
    font-family: monospace;
    font-size: 12px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f5f5f5;
    margin-bottom: 10px;
}

.help-text {
    color: #666;
    font-size: 13px;
    margin: 10px 0 0 0;
    display: flex;
    align-items: flex-start;
    gap: 5px;
}

.quick-links {
    list-style: none;
    margin: 0;
    padding: 0;
}

.quick-links li {
    margin-bottom: 12px;
}

.quick-links a {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s;
}

.quick-links a:hover {
    background: #f8f9fa;
    border-color: #007cba;
    color: #007cba;
}

@media (max-width: 768px) {
    .synaplan-dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    var apiKeyHidden = $('#api-key-display');
    var apiKeyRevealed = $('#api-key-revealed');
    var toggleBtn = $('#toggle-api-key');
    var copyBtn = $('#copy-api-key');
    var toggleText = $('#toggle-text');
    var isRevealed = false;
    
    // Toggle API key visibility
    toggleBtn.on('click', function() {
        isRevealed = !isRevealed;
        
        if (isRevealed) {
            apiKeyHidden.hide();
            apiKeyRevealed.show();
            copyBtn.show();
            toggleBtn.find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-hidden');
            toggleText.text('<?php _e('Hide API Key', 'synaplan-wp-ai'); ?>');
        } else {
            apiKeyHidden.show();
            apiKeyRevealed.hide();
            copyBtn.hide();
            toggleBtn.find('.dashicons').removeClass('dashicons-hidden').addClass('dashicons-visibility');
            toggleText.text('<?php _e('Show API Key', 'synaplan-wp-ai'); ?>');
        }
    });
    
    // Copy API key
    copyBtn.on('click', function() {
        var apiKey = apiKeyRevealed.text().trim();
        navigator.clipboard.writeText(apiKey).then(function() {
            var originalText = copyBtn.html();
            copyBtn.html('<span class="dashicons dashicons-yes"></span> <?php _e('Copied!', 'synaplan-wp-ai'); ?>');
            setTimeout(function() {
                copyBtn.html(originalText);
            }, 2000);
        });
    });
    
    // Copy embed code
    $('#copy-embed-code').on('click', function() {
        var embedCode = $('#embed-code');
        embedCode.select();
        document.execCommand('copy');
        
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<span class="dashicons dashicons-yes"></span> <?php _e('Copied!', 'synaplan-wp-ai'); ?>');
        setTimeout(function() {
            btn.html(originalText);
        }, 2000);
    });
});
</script>