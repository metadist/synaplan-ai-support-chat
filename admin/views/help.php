<?php
/**
 * Help view for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap synaplan-help">
    <h1><?php _e('Synaplan AI Help & Support', 'synaplan-ai-support-chat'); ?></h1>
    
    <div class="help-grid">
        <div class="help-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-book"></span> <?php _e('Getting Started', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <h3><?php _e('How to use your AI chat widget', 'synaplan-ai-support-chat'); ?></h3>
                <ol>
                    <li><?php _e('Your widget is automatically displayed on all pages of your website', 'synaplan-ai-support-chat'); ?></li>
                    <li><?php _e('Visitors can click the chat button to start a conversation', 'synaplan-ai-support-chat'); ?></li>
                    <li><?php _e('The AI will respond based on your knowledge base and configuration', 'synaplan-ai-support-chat'); ?></li>
                    <li><?php _e('You can customize settings anytime from the Settings page', 'synaplan-ai-support-chat'); ?></li>
                </ol>
            </div>
        </div>
        
        <div class="help-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-admin-generic"></span> <?php _e('Common Tasks', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <h3><?php _e('Managing Your Knowledge Base', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Visit the', 'synaplan-ai-support-chat'); ?> <a href="https://app.synaplan.com/index.php/filemanager" target="_blank"><?php _e('File Manager', 'synaplan-ai-support-chat'); ?></a> <?php _e('to upload documents, PDFs, or other files that your AI can reference when answering questions.', 'synaplan-ai-support-chat'); ?></p>
                
                <h3><?php _e('Customizing AI Responses', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Use the', 'synaplan-ai-support-chat'); ?> <a href="https://app.synaplan.com/index.php/prompts" target="_blank"><?php _e('Prompts Manager', 'synaplan-ai-support-chat'); ?></a> <?php _e('to define how your AI responds to different types of questions.', 'synaplan-ai-support-chat'); ?></p>
                
                <h3><?php _e('Changing Widget Appearance', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Go to', 'synaplan-ai-support-chat'); ?> <a href="<?php echo admin_url('admin.php?page=synaplan-ai-support-chat-settings'); ?>"><?php _e('Settings', 'synaplan-ai-support-chat'); ?></a> <?php _e('to change colors, position, and welcome messages.', 'synaplan-ai-support-chat'); ?></p>
            </div>
        </div>
        
        <div class="help-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-editor-help"></span> <?php _e('Frequently Asked Questions', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <h3><?php _e('Where is my API key?', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('You can find your API key on the', 'synaplan-ai-support-chat'); ?> <a href="<?php echo admin_url('admin.php?page=synaplan-ai-support-chat'); ?>"><?php _e('Dashboard', 'synaplan-ai-support-chat'); ?></a>. <?php _e('Click "Show API Key" to reveal it.', 'synaplan-ai-support-chat'); ?></p>
                
                <h3><?php _e('How do I add more documents to my knowledge base?', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Visit the', 'synaplan-ai-support-chat'); ?> <a href="https://app.synaplan.com/index.php/filemanager" target="_blank"><?php _e('Synaplan File Manager', 'synaplan-ai-support-chat'); ?></a> <?php _e('and upload your documents. They will automatically be processed and made available to your AI.', 'synaplan-ai-support-chat'); ?></p>
                
                <h3><?php _e('Can I customize the AI\'s personality?', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Yes! Use the', 'synaplan-ai-support-chat'); ?> <a href="https://app.synaplan.com/index.php/prompts" target="_blank"><?php _e('Prompts Manager', 'synaplan-ai-support-chat'); ?></a> <?php _e('to define how your AI should respond, its tone, and behavior.', 'synaplan-ai-support-chat'); ?></p>
                
                <h3><?php _e('Is my data secure?', 'synaplan-ai-support-chat'); ?></h3>
                <p><?php _e('Yes. All data is encrypted and transmitted over HTTPS. Your conversations and documents are private and secure.', 'synaplan-ai-support-chat'); ?></p>
            </div>
        </div>
        
        <div class="help-card">
            <div class="card-header">
                <h2><span class="dashicons dashicons-email"></span> <?php _e('Contact Support', 'synaplan-ai-support-chat'); ?></h2>
            </div>
            <div class="card-body">
                <p><?php _e('Need more help? Contact our support team:', 'synaplan-ai-support-chat'); ?></p>
                <ul class="contact-list">
                    <li>
                        <span class="dashicons dashicons-email"></span>
                        <a href="mailto:support@synaplan.com">support@synaplan.com</a>
                    </li>
                    <li>
                        <span class="dashicons dashicons-admin-site"></span>
                        <a href="https://synaplan.com" target="_blank">synaplan.com</a>
                    </li>
                    <li>
                        <span class="dashicons dashicons-admin-home"></span>
                        <a href="https://app.synaplan.com/" target="_blank">Synaplan Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.synaplan-help {
    margin-top: 20px;
}

.help-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.help-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.help-card .card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
}

.help-card .card-header h2 {
    margin: 0;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.help-card .card-body {
    padding: 20px;
}

.help-card h3 {
    font-size: 14px;
    margin-top: 15px;
    margin-bottom: 8px;
    color: #333;
}

.help-card h3:first-child {
    margin-top: 0;
}

.help-card ol, .help-card ul {
    margin-left: 20px;
}

.help-card li {
    margin-bottom: 8px;
}

.contact-list {
    list-style: none;
    margin: 15px 0 0 0;
    padding: 0;
}

.contact-list li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border: 1px solid #eee;
    border-radius: 4px;
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .help-grid {
        grid-template-columns: 1fr;
    }
}
</style>