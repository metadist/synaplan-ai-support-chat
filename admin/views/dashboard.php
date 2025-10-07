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

$widget = new Synaplan_WP_Widget();
$widget_preview = $widget->get_widget_preview();
$widget_stats = $widget->get_widget_stats();
?>

<div class="wrap synaplan-dashboard">
    <div class="synaplan-dashboard-header">
        <div class="header-content">
            <h1><?php _e('Synaplan AI Dashboard', 'synaplan-wp-ai'); ?></h1>
            <p><?php _e('Manage your AI chat widget and monitor its performance.', 'synaplan-wp-ai'); ?></p>
        </div>
        <div class="header-actions">
            <button type="button" class="button button-secondary" id="test-widget">
                <?php _e('Test Widget', 'synaplan-wp-ai'); ?>
            </button>
            <a href="<?php echo admin_url('admin.php?page=synaplan-wp-ai-settings'); ?>" class="button button-primary">
                <?php _e('Settings', 'synaplan-wp-ai'); ?>
            </a>
        </div>
    </div>

    <div class="synaplan-dashboard-content">
        <div class="dashboard-grid">
            <!-- Widget Preview -->
            <div class="dashboard-card widget-preview">
                <div class="card-header">
                    <h2><?php _e('Widget Preview', 'synaplan-wp-ai'); ?></h2>
                    <p><?php _e('See how your chat widget appears on your website.', 'synaplan-wp-ai'); ?></p>
                </div>
                <div class="card-content">
                    <?php echo $widget_preview; ?>
                </div>
            </div>

            <!-- Statistics -->
            <div class="dashboard-card widget-stats">
                <div class="card-header">
                    <h2><?php _e('Statistics', 'synaplan-wp-ai'); ?></h2>
                    <p><?php _e('Monitor your widget\'s performance.', 'synaplan-wp-ai'); ?></p>
                </div>
                <div class="card-content">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($widget_stats['total_conversations']); ?></div>
                            <div class="stat-label"><?php _e('Total Conversations', 'synaplan-wp-ai'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($widget_stats['total_messages']); ?></div>
                            <div class="stat-label"><?php _e('Total Messages', 'synaplan-wp-ai'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($widget_stats['avg_response_time']); ?>s</div>
                            <div class="stat-label"><?php _e('Avg Response Time', 'synaplan-wp-ai'); ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($widget_stats['satisfaction_score'], 1); ?>%</div>
                            <div class="stat-label"><?php _e('Satisfaction Score', 'synaplan-wp-ai'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="dashboard-card quick-actions">
                <div class="card-header">
                    <h2><?php _e('Quick Actions', 'synaplan-wp-ai'); ?></h2>
                    <p><?php _e('Common tasks and settings.', 'synaplan-wp-ai'); ?></p>
                </div>
                <div class="card-content">
                    <div class="action-buttons">
                        <a href="<?php echo admin_url('admin.php?page=synaplan-wp-ai-settings'); ?>" class="action-button">
                            <div class="action-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M12 1v6m0 6v6m11-7h-6m-6 0H1" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <h3><?php _e('Widget Settings', 'synaplan-wp-ai'); ?></h3>
                                <p><?php _e('Customize appearance and behavior', 'synaplan-wp-ai'); ?></p>
                            </div>
                        </a>
                        
                        <a href="#" class="action-button" id="preview-widget">
                            <div class="action-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <h3><?php _e('Preview Widget', 'synaplan-wp-ai'); ?></h3>
                                <p><?php _e('See the widget in action', 'synaplan-wp-ai'); ?></p>
                            </div>
                        </a>
                        
                        <a href="#" class="action-button" id="view-analytics">
                            <div class="action-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <h3><?php _e('View Analytics', 'synaplan-wp-ai'); ?></h3>
                                <p><?php _e('Detailed performance metrics', 'synaplan-wp-ai'); ?></p>
                            </div>
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=synaplan-wp-ai-help'); ?>" class="action-button">
                            <div class="action-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" stroke="currentColor" stroke-width="2"/>
                                    <line x1="12" y1="17" x2="12.01" y2="17" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="action-content">
                                <h3><?php _e('Help & Support', 'synaplan-wp-ai'); ?></h3>
                                <p><?php _e('Get help and documentation', 'synaplan-wp-ai'); ?></p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="dashboard-card recent-activity">
                <div class="card-header">
                    <h2><?php _e('Recent Activity', 'synaplan-wp-ai'); ?></h2>
                    <p><?php _e('Latest conversations and interactions.', 'synaplan-wp-ai'); ?></p>
                </div>
                <div class="card-content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php _e('New conversation started', 'synaplan-wp-ai'); ?></div>
                                <div class="activity-time"><?php _e('2 minutes ago', 'synaplan-wp-ai'); ?></div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php _e('Widget configuration updated', 'synaplan-wp-ai'); ?></div>
                                <div class="activity-time"><?php _e('1 hour ago', 'synaplan-wp-ai'); ?></div>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                                    <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php _e('Knowledge base updated', 'synaplan-wp-ai'); ?></div>
                                <div class="activity-time"><?php _e('3 hours ago', 'synaplan-wp-ai'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Test widget functionality
    $('#test-widget').on('click', function() {
        var button = $(this);
        var originalText = button.text();
        
        button.prop('disabled', true).text('<?php _e('Testing...', 'synaplan-wp-ai'); ?>');
        
        $.post(ajaxurl, {
            action: 'synaplan_wp_test_api',
            nonce: synaplan_wp_admin.nonce
        }, function(response) {
            if (response.success) {
                alert('<?php _e('Widget test successful!', 'synaplan-wp-ai'); ?>');
            } else {
                alert('<?php _e('Widget test failed: ', 'synaplan-wp-ai'); ?>' + response.data);
            }
        }).always(function() {
            button.prop('disabled', false).text(originalText);
        });
    });
    
    // Preview widget
    $('#preview-widget').on('click', function(e) {
        e.preventDefault();
        window.open('<?php echo home_url(); ?>', '_blank');
    });
    
    // View analytics
    $('#view-analytics').on('click', function(e) {
        e.preventDefault();
        alert('<?php _e('Analytics feature coming soon!', 'synaplan-wp-ai'); ?>');
    });
});
</script>
