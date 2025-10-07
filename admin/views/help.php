<?php
/**
 * Help page view
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
    <h1><?php _e('Synaplan AI - Help & Support', 'synaplan-wp-ai'); ?></h1>
    
    <div class="synaplan-help-container">
        <div class="synaplan-help-main">
            <h2><?php _e('Getting Started', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Welcome to Synaplan AI! Follow these steps to get your AI chat widget up and running:', 'synaplan-wp-ai'); ?></p>
            
            <ol>
                <li><strong><?php _e('Complete the Setup Wizard', 'synaplan-wp-ai'); ?></strong> - Go to the Dashboard and follow the 4-step setup process</li>
                <li><strong><?php _e('Configure Your Widget', 'synaplan-wp-ai'); ?></strong> - Customize colors, position, and messages in Settings</li>
                <li><strong><?php _e('Test Your Widget', 'synaplan-wp-ai'); ?></strong> - Visit your website to see the chat widget in action</li>
            </ol>
            
            <h2><?php _e('Widget Configuration', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Customize your chat widget to match your website\'s design:', 'synaplan-wp-ai'); ?></p>
            
            <ul>
                <li><strong><?php _e('Integration Type', 'synaplan-wp-ai'); ?></strong> - Choose between floating button or inline widget</li>
                <li><strong><?php _e('Colors', 'synaplan-wp-ai'); ?></strong> - Set widget and icon colors to match your brand</li>
                <li><strong><?php _e('Position', 'synaplan-wp-ai'); ?></strong> - Place the widget in any corner of your site</li>
                <li><strong><?php _e('Welcome Message', 'synaplan-wp-ai'); ?></strong> - Customize the initial message users see</li>
                <li><strong><?php _e('Auto Open', 'synaplan-wp-ai'); ?></strong> - Optionally open the chat automatically</li>
            </ul>
            
            <h2><?php _e('Troubleshooting', 'synaplan-wp-ai'); ?></h2>
            
            <h3><?php _e('Widget Not Appearing', 'synaplan-wp-ai'); ?></h3>
            <ul>
                <li><?php _e('Ensure the setup wizard is completed', 'synaplan-wp-ai'); ?></li>
                <li><?php _e('Check that your API key is configured', 'synaplan-wp-ai'); ?></li>
                <li><?php _e('Clear your browser cache', 'synaplan-wp-ai'); ?></li>
                <li><?php _e('Check for JavaScript errors in browser console', 'synaplan-wp-ai'); ?></li>
            </ul>
            
            <h3><?php _e('Setup Issues', 'synaplan-wp-ai'); ?></h3>
            <ul>
                <li><?php _e('Make sure you have a valid email address', 'synaplan-wp-ai'); ?></li>
                <li><?php _e('Check your internet connection', 'synaplan-wp-ai'); ?></li>
                <li><?php _e('Try refreshing the page and starting over', 'synaplan-wp-ai'); ?></li>
            </ul>
            
            <h2><?php _e('API Information', 'synaplan-wp-ai'); ?></h2>
            <p><?php _e('Your Synaplan AI integration uses the following information:', 'synaplan-wp-ai'); ?></p>
            
            <table class="widefat">
                <tr>
                    <td><strong><?php _e('API Endpoint', 'synaplan-wp-ai'); ?></strong></td>
                    <td>https://app.synaplan.com/api.php</td>
                </tr>
                <tr>
                    <td><strong><?php _e('User ID', 'synaplan-wp-ai'); ?></strong></td>
                    <td><?php echo esc_html(Synaplan_WP_Core::get_user_id() ?: __('Not configured', 'synaplan-wp-ai')); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('API Key Status', 'synaplan-wp-ai'); ?></strong></td>
                    <td><?php echo Synaplan_WP_Core::get_api_key() ? __('Configured', 'synaplan-wp-ai') : __('Not configured', 'synaplan-wp-ai'); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Setup Status', 'synaplan-wp-ai'); ?></strong></td>
                    <td><?php echo Synaplan_WP_Core::is_setup_completed() ? __('Completed', 'synaplan-wp-ai') : __('Not completed', 'synaplan-wp-ai'); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="synaplan-help-sidebar">
            <div class="synaplan-support-box">
                <h3><?php _e('Need Help?', 'synaplan-wp-ai'); ?></h3>
                <p><?php _e('If you\'re experiencing issues or need assistance:', 'synaplan-wp-ai'); ?></p>
                <ul>
                    <li><a href="https://synaplan.com/support" target="_blank"><?php _e('Visit our Support Center', 'synaplan-wp-ai'); ?></a></li>
                    <li><a href="https://synaplan.com/contact" target="_blank"><?php _e('Contact Support', 'synaplan-wp-ai'); ?></a></li>
                    <li><a href="https://github.com/synaplan/synaplan-wp-ai" target="_blank"><?php _e('View Documentation', 'synaplan-wp-ai'); ?></a></li>
                </ul>
            </div>
            
            <div class="synaplan-status-box">
                <h3><?php _e('Current Status', 'synaplan-wp-ai'); ?></h3>
                <p><strong><?php _e('Plugin Version:', 'synaplan-wp-ai'); ?></strong> <?php echo SYNAPLAN_WP_VERSION; ?></p>
                <p><strong><?php _e('WordPress Version:', 'synaplan-wp-ai'); ?></strong> <?php echo get_bloginfo('version'); ?></p>
                <p><strong><?php _e('PHP Version:', 'synaplan-wp-ai'); ?></strong> <?php echo PHP_VERSION; ?></p>
                
                <h4><?php _e('System Requirements', 'synaplan-wp-ai'); ?></h4>
                <ul>
                    <li><?php _e('WordPress 5.0+', 'synaplan-wp-ai'); ?> <?php echo version_compare(get_bloginfo('version'), '5.0', '>=') ? '✅' : '❌'; ?></li>
                    <li><?php _e('PHP 8.0+', 'synaplan-wp-ai'); ?> <?php echo version_compare(PHP_VERSION, '8.0', '>=') ? '✅' : '❌'; ?></li>
                    <li><?php _e('cURL Extension', 'synaplan-wp-ai'); ?> <?php echo extension_loaded('curl') ? '✅' : '❌'; ?></li>
                    <li><?php _e('JSON Extension', 'synaplan-wp-ai'); ?> <?php echo extension_loaded('json') ? '✅' : '❌'; ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.synaplan-help-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.synaplan-help-main {
    flex: 2;
}

.synaplan-help-sidebar {
    flex: 1;
}

.synaplan-support-box,
.synaplan-status-box {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 15px;
    margin-bottom: 20px;
}

.synaplan-support-box h3,
.synaplan-status-box h3 {
    margin-top: 0;
}

.synaplan-support-box ul {
    margin: 10px 0;
}

.synaplan-support-box li {
    margin: 5px 0;
}

.synaplan-status-box h4 {
    margin-bottom: 10px;
}

.synaplan-status-box ul {
    margin: 10px 0;
}

.synaplan-status-box li {
    margin: 5px 0;
}
</style>
