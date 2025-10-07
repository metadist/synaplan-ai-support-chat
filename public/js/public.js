/**
 * Frontend JavaScript for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Widget functionality
    var SynaplanWidget = {
        init: function() {
            this.bindEvents();
            this.checkWidgetStatus();
        },

        bindEvents: function() {
            // Widget initialization
            $(document).ready(this.initializeWidget);
            
            // Error handling
            $(document).on('synaplan-widget-error', this.handleError);
            
            // Success handling
            $(document).on('synaplan-widget-success', this.handleSuccess);
        },

        initializeWidget: function() {
            console.log('[Synaplan Widget] Initializing widget...');
            
            // Check if widget configuration is available
            if (typeof synaplan_wp_widget === 'undefined') {
                console.error('[Synaplan Widget] Widget configuration not found');
                SynaplanWidget.showError('Widget configuration not found');
                return;
            }

            var config = synaplan_wp_widget.config;
            var userId = synaplan_wp_widget.user_id;
            var widgetId = synaplan_wp_widget.widget_id;

            console.log('[Synaplan Widget] Config:', config);
            console.log('[Synaplan Widget] User ID:', userId);
            console.log('[Synaplan Widget] Widget ID:', widgetId);

            if (!userId || !widgetId) {
                console.error('[Synaplan Widget] Widget not properly configured');
                SynaplanWidget.showError('Widget not properly configured');
                return;
            }

            // Initialize widget based on configuration
            SynaplanWidget.loadWidget(config, userId, widgetId);
        },

        loadWidget: function(config, userId, widgetId) {
            var widgetUrl = SynaplanWidget.buildWidgetUrl(config, userId, widgetId);
            
            console.log('[Synaplan Widget] Loading widget from URL:', widgetUrl);
            
            // Create and load widget script
            var script = document.createElement('script');
            script.src = widgetUrl;
            script.async = true;
            script.onload = function() {
                console.log('[Synaplan Widget] Widget script loaded successfully');
                SynaplanWidget.onWidgetLoaded();
            };
            script.onerror = function() {
                console.error('[Synaplan Widget] Failed to load widget script from:', widgetUrl);
                SynaplanWidget.showError('Failed to load widget script');
            };
            
            document.head.appendChild(script);
        },

        buildWidgetUrl: function(config, userId, widgetId) {
            var baseUrl = synaplan_wp_widget.api_url || 'https://app.synaplan.com';
            
            // Extract numeric user ID from wp_user_XXX format
            var numericUserId = SynaplanWidget.extractNumericUserId(userId);
            
            var url = baseUrl + '/widget.php?uid=' + numericUserId + '&widgetid=' + widgetId;
            
            // Add mode parameter for inline widgets
            if (config.integration_type === 'inline-box') {
                url += '&mode=inline-box';
            }
            
            return url;
        },
        
        extractNumericUserId: function(userId) {
            // If it's already numeric, return as is
            if (!isNaN(userId)) {
                return userId;
            }
            
            // Extract number from wp_user_XXX format
            var match = userId.match(/wp_user_(\d+)/);
            if (match) {
                return match[1];
            }
            
            // If no pattern matches, return the original value
            return userId;
        },

        onWidgetLoaded: function() {
            // Widget loaded successfully
            $(document).trigger('synaplan-widget-success');
            
            // Add loaded class to body
            $('body').addClass('synaplan-widget-loaded');
        },

        checkWidgetStatus: function() {
            // Check if widget is properly loaded
            setTimeout(function() {
                if (!$('body').hasClass('synaplan-widget-loaded')) {
                    SynaplanWidget.showError('Widget failed to load');
                }
            }, 5000);
        },

        handleError: function(event, error) {
            SynaplanWidget.showError(error);
        },

        handleSuccess: function(event, message) {
            SynaplanWidget.showSuccess(message);
        },

        showError: function(message) {
            // Remove existing error messages
            $('.synaplan-widget-error').remove();
            
            // Add error message
            var errorHtml = '<div class="synaplan-widget-error">' + message + '</div>';
            $('body').prepend(errorHtml);
            
            // Log error
            console.error('[Synaplan Widget]', message);
        },

        showSuccess: function(message) {
            // Remove existing success messages
            $('.synaplan-widget-success').remove();
            
            // Add success message
            var successHtml = '<div class="synaplan-widget-success">' + message + '</div>';
            $('body').prepend(successHtml);
            
            // Auto-hide after 3 seconds
            setTimeout(function() {
                $('.synaplan-widget-success').fadeOut();
            }, 3000);
        }
    };

    // Shortcode functionality
    var SynaplanShortcode = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Handle shortcode widgets
            $('.synaplan-widget-shortcode').each(this.initializeShortcode);
        },

        initializeShortcode: function() {
            var container = $(this);
            var type = container.data('type') || 'inline-box';
            
            // Get shortcode-specific configuration
            var shortcodeConfig = {
                integration_type: type,
                placeholder: container.data('placeholder') || 'Ask me anything...',
                button_text: container.data('button-text') || 'Ask',
                color: container.data('color') || '#007bff',
                position: container.data('position') || 'center'
            };
            
            // Initialize shortcode widget
            SynaplanShortcode.loadShortcodeWidget(container, shortcodeConfig);
        },

        loadShortcodeWidget: function(container, shortcodeConfig) {
            // Check if widget configuration is available
            if (typeof synaplan_wp_widget === 'undefined') {
                SynaplanShortcode.showError(container, 'Widget configuration not found');
                return;
            }

            var config = synaplan_wp_widget.config;
            var userId = synaplan_wp_widget.user_id;
            var widgetId = synaplan_wp_widget.widget_id;

            if (!userId || !widgetId) {
                SynaplanShortcode.showError(container, 'Widget not properly configured');
                return;
            }

            // Merge shortcode config with main config
            var mergedConfig = $.extend({}, config, shortcodeConfig);

            // Build widget URL for shortcode with inline-box mode
            var widgetUrl = SynaplanWidget.buildWidgetUrl(mergedConfig, userId, widgetId);
            
            // Create and load widget script
            var script = document.createElement('script');
            script.src = widgetUrl;
            script.async = true;
            script.onload = function() {
                SynaplanShortcode.onShortcodeLoaded(container);
            };
            script.onerror = function() {
                SynaplanShortcode.showError(container, 'Failed to load widget script');
            };
            
            document.head.appendChild(script);
        },

        onShortcodeLoaded: function(container) {
            // Shortcode widget loaded successfully
            container.addClass('synaplan-widget-loaded');
        },

        showError: function(container, message) {
            // Remove existing error messages
            container.find('.synaplan-widget-error').remove();
            
            // Add error message
            var errorHtml = '<div class="synaplan-widget-error">' + message + '</div>';
            container.prepend(errorHtml);
            
            // Log error
            console.error('[Synaplan Shortcode]', message);
        }
    };

    // Performance monitoring
    var SynaplanPerformance = {
        init: function() {
            this.monitorPerformance();
        },

        monitorPerformance: function() {
            // Monitor widget load time
            var startTime = performance.now();
            
            $(document).on('synaplan-widget-success', function() {
                var loadTime = performance.now() - startTime;
                console.log('[Synaplan Widget] Load time:', loadTime + 'ms');
                
                // Send performance data to analytics if available
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'widget_load_time', {
                        'value': Math.round(loadTime),
                        'event_category': 'performance'
                    });
                }
            });
        }
    };

    // Error reporting
    var SynaplanErrorReporting = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Global error handler
            window.addEventListener('error', this.handleGlobalError);
            
            // Unhandled promise rejection handler
            window.addEventListener('unhandledrejection', this.handlePromiseRejection);
        },

        handleGlobalError: function(event) {
            // Check if error is related to Synaplan widget
            if (event.filename && event.filename.includes('synaplan')) {
                SynaplanErrorReporting.reportError({
                    type: 'javascript_error',
                    message: event.message,
                    filename: event.filename,
                    lineno: event.lineno,
                    colno: event.colno,
                    stack: event.error ? event.error.stack : null
                });
            }
        },

        handlePromiseRejection: function(event) {
            // Check if rejection is related to Synaplan widget
            if (event.reason && event.reason.toString().includes('synaplan')) {
                SynaplanErrorReporting.reportError({
                    type: 'promise_rejection',
                    message: event.reason.toString(),
                    stack: event.reason.stack || null
                });
            }
        },

        reportError: function(errorData) {
            // Log error locally
            console.error('[Synaplan Widget Error]', errorData);
            
            // Send error to analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'exception', {
                    'description': errorData.message,
                    'fatal': false,
                    'event_category': 'error'
                });
            }
        }
    };

    // Initialize all components
    $(document).ready(function() {
        SynaplanWidget.init();
        SynaplanShortcode.init();
        SynaplanPerformance.init();
        SynaplanErrorReporting.init();
    });

})(jQuery);
