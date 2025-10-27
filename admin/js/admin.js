/**
 * Admin JavaScript for Synaplan WP AI
 *
 * @package Synaplan_WP_AI
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Wizard functionality
    var SynaplanWizard = {
        debugMode: false,
        
        init: function() {
            this.bindEvents();
            this.initPasswordStrength();
            this.initFileUpload();
            this.initDebugMode();
        },
        
        initDebugMode: function() {
            var self = this;
            // Check if debug mode toggle exists and is checked
            $('#debug_mode').on('change', function() {
                self.debugMode = $(this).is(':checked');
                self.debugLog('🔧 Debug Mode ' + (self.debugMode ? 'ENABLED' : 'DISABLED'), {});
                if (self.debugMode) {
                    console.log('%c🔧 SYNAPLAN DEBUG MODE ACTIVE 🔧', 'background: #00c853; color: #fff; padding: 5px 10px; font-size: 14px; font-weight: bold;');
                    console.log('%cAll wizard steps will be logged to console with detailed information', 'color: #00c853; font-size: 12px;');
                }
            });
        },
        
        debugLog: function(message, data) {
            if (!this.debugMode) return;
            
            var timestamp = new Date().toLocaleTimeString();
            console.group('%c[' + timestamp + '] ' + message, 'color: #2196F3; font-weight: bold;');
            if (data && Object.keys(data).length > 0) {
                console.table(data);
            }
            console.groupEnd();
        },
        
        debugLogStep: function(step, phase, data) {
            if (!this.debugMode) return;
            
            var colors = {
                'collecting': '#FF9800',
                'sanitizing': '#9C27B0', 
                'validating': '#3F51B5',
                'sending': '#2196F3',
                'response': '#4CAF50',
                'error': '#F44336'
            };
            
            var color = colors[phase] || '#607D8B';
            var timestamp = new Date().toLocaleTimeString();
            
            console.group('%c[' + timestamp + '] STEP ' + step + ' - ' + phase.toUpperCase(), 'color: ' + color + '; font-weight: bold; font-size: 13px;');
            console.log(data);
            console.groupEnd();
        },

        bindEvents: function() {
            // Form submission
            $('#synaplan-wizard-form').on('submit', this.handleFormSubmit);
            
            // Step navigation
            $('#prev-step').on('click', this.goToPreviousStep);
            $('#next-step').on('click', this.goToNextStep);
            
            // Password strength checking
            $('#password').on('input', this.checkPasswordStrength);
            
            // File upload
            $('#select-files').on('click', this.selectFiles);
            $('#file-input').on('change', this.handleFileSelect);
            
            // Drag and drop
            $('#file-upload-area').on('dragover', this.handleDragOver);
            $('#file-upload-area').on('dragleave', this.handleDragLeave);
            $('#file-upload-area').on('drop', this.handleDrop);
        },

        handleFormSubmit: function(e) {
            e.preventDefault();
            
            var form = $(this);
            var step = parseInt(form.find('input[name="step"]').val());
            
            // PHASE 1: COLLECTING DATA
            var collectedData = {};
            form.serializeArray().forEach(function(item) {
                if (item.name !== 'password' && item.name !== 'nonce') {
                    collectedData[item.name] = item.value;
                } else if (item.name === 'password') {
                    collectedData[item.name] = '[REDACTED - length: ' + item.value.length + ']';
                }
            });
            SynaplanWizard.debugLogStep(step, 'collecting', collectedData);
            
            // Validate terms checkbox for step 1
            if (step === 1) {
                if (!form.find('input[name="terms"]').is(':checked')) {
                    SynaplanWizard.debugLogStep(step, 'error', { error: 'Terms not accepted' });
                    SynaplanWizard.showError('Please accept the Terms of Service and Privacy Policy to continue.');
                    return;
                }
            }
            
            var formData = new FormData(this);
            
            // Add step to form data
            formData.append('step', step);
            
            // PHASE 2: SANITIZING (happens server-side, but we log what we're sending)
            var sanitizationInfo = {
                email: 'sanitize_email()',
                password: 'raw (validated only)',
                language: 'sanitize_text_field()',
                terms: 'rest_sanitize_boolean()',
                intro_message: 'sanitize_textarea_field()',
                widget_color: 'sanitize_hex_color()',
                widget_position: 'sanitize_text_field()',
                prompt: 'sanitize_text_field()'
            };
            
            var sanitizingData = {};
            Object.keys(collectedData).forEach(function(key) {
                if (sanitizationInfo[key]) {
                    sanitizingData[key] = sanitizationInfo[key];
                }
            });
            SynaplanWizard.debugLogStep(step, 'sanitizing', sanitizingData);
            
            // Show loading state
            SynaplanWizard.showLoading();
            
            // Submit step data
            formData.append('action', 'synaplan_wp_wizard_step');
            formData.append('nonce', synaplan_wp_admin.nonce);
            
            // PHASE 3: SENDING TO SERVER
            SynaplanWizard.debugLogStep(step, 'sending', {
                url: synaplan_wp_admin.ajax_url,
                action: 'synaplan_wp_wizard_step',
                step: step,
                field_count: form.serializeArray().length
            });
            
            $.ajax({
                url: synaplan_wp_admin.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // PHASE 4: RESPONSE RECEIVED
                    SynaplanWizard.debugLogStep(step, 'response', response);
                    
                    // Log debug info from server if available
                    if (response.debug) {
                        console.group('%c📊 SERVER DEBUG INFO - STEP ' + step, 'color: #00BCD4; font-weight: bold; font-size: 13px;');
                        console.log('Collected Data:', response.debug.collected_data);
                        console.log('Sanitized Data:', response.debug.sanitized_data);
                        console.log('Validation:', response.debug.validation);
                        console.log('Message:', response.debug.message);
                        console.groupEnd();
                    }
                    
                    // Log debug_log from complete wizard if available
                    if (response.debug_log) {
                        console.group('%c🔍 COMPLETE WIZARD DEBUG LOG', 'background: #673AB7; color: #fff; padding: 5px 10px; font-size: 14px; font-weight: bold;');
                        console.log('Full API Communication Log:', response.debug_log);
                        console.groupEnd();
                    }
                    
                    if (response.success) {
                        if (response.next_step) {
                            SynaplanWizard.goToStep(response.next_step);
                        } else if (response.completed) {
                            SynaplanWizard.debugLog('✅ WIZARD COMPLETED SUCCESSFULLY', {});
                            SynaplanWizard.showCompletion();
                        }
                    } else {
                        SynaplanWizard.debugLogStep(step, 'error', { error: response.error });
                        SynaplanWizard.showError(response.error || 'An error occurred');
                    }
                },
                error: function(xhr, status, error) {
                    SynaplanWizard.debugLogStep(step, 'error', {
                        xhr_status: xhr.status,
                        error_status: status,
                        error_message: error
                    });
                    SynaplanWizard.showError('Network error. Please try again.');
                },
                complete: function() {
                    SynaplanWizard.hideLoading();
                }
            });
        },

        goToStep: function(step) {
            window.location.href = 'admin.php?page=synaplan-ai-support-chat&step=' + step;
        },

        goToPreviousStep: function() {
            var currentStep = parseInt($('input[name="step"]').val());
            if (currentStep > 1) {
                SynaplanWizard.goToStep(currentStep - 1);
            }
        },

        goToNextStep: function() {
            $('#synaplan-wizard-form').submit();
        },

        showLoading: function() {
            $('.wizard-actions').addClass('loading');
            $('#next-step').prop('disabled', true).text(synaplan_wp_admin.strings.loading);
        },

        hideLoading: function() {
            $('.wizard-actions').removeClass('loading');
            $('#next-step').prop('disabled', false).text('Next');
        },

        showError: function(message) {
            // Remove existing error messages
            $('.wizard-error').remove();
            
            // Add new error message
            var errorHtml = '<div class="wizard-error notice notice-error"><p>' + message + '</p></div>';
            $('.synaplan-wizard-content').prepend(errorHtml);
            
            // Scroll to top
            $('html, body').animate({ scrollTop: 0 }, 300);
        },

        showCompletion: function() {
            // Redirect to dashboard
            window.location.href = 'admin.php?page=synaplan-ai-support-chat';
        },

        initPasswordStrength: function() {
            // Password strength checker will be implemented here
        },

        checkPasswordStrength: function() {
            var password = $(this).val();
            var strengthBar = $('#password-strength');
            
            if (password.length === 0) {
                strengthBar.removeClass('weak medium strong');
                return;
            }
            
            var strength = 0;
            
            // Length check
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            
            // Character type checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update strength bar
            strengthBar.removeClass('weak medium strong');
            
            if (strength < 3) {
                strengthBar.addClass('weak');
            } else if (strength < 5) {
                strengthBar.addClass('medium');
            } else {
                strengthBar.addClass('strong');
            }
        },

        initFileUpload: function() {
            // File upload functionality will be implemented here
        },

        selectFiles: function() {
            $('#file-input').click();
        },

        handleFileSelect: function() {
            var files = this.files;
            SynaplanWizard.processFiles(files);
        },

        handleDragOver: function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        },

        handleDragLeave: function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        },

        handleDrop: function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            var files = e.originalEvent.dataTransfer.files;
            SynaplanWizard.processFiles(files);
        },

        processFiles: function(files) {
            var fileList = $('#file-list');
            var uploadedFiles = $('#uploaded-files');
            
            // DEBUG: Log file selection
            SynaplanWizard.debugLog('📁 FILES SELECTED', {
                count: files.length,
                maxAllowed: 5
            });
            
            // Clear existing files
            fileList.empty();
            
            // Process each file
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                // DEBUG: Log individual file details
                var fileDebugInfo = {
                    name: file.name,
                    size: SynaplanWizard.formatFileSize(file.size),
                    type: file.type,
                    lastModified: new Date(file.lastModified).toLocaleString()
                };
                
                // Validate file type
                if (!SynaplanWizard.isValidFileType(file)) {
                    fileDebugInfo.validation = '❌ INVALID TYPE';
                    SynaplanWizard.debugLog('❌ File Validation Failed', fileDebugInfo);
                    continue;
                }
                
                // Validate file size
                if (!SynaplanWizard.isValidFileSize(file)) {
                    fileDebugInfo.validation = '❌ TOO LARGE (max 10MB)';
                    SynaplanWizard.debugLog('❌ File Validation Failed', fileDebugInfo);
                    continue;
                }
                
                fileDebugInfo.validation = '✅ VALID';
                SynaplanWizard.debugLog('✅ File Validated', fileDebugInfo);
                
                // Add file to list
                var fileItem = $('<li></li>');
                fileItem.html(
                    '<span>' + file.name + '</span>' +
                    '<span class="file-size">' + SynaplanWizard.formatFileSize(file.size) + '</span>'
                );
                fileList.append(fileItem);
            }
            
            // Show uploaded files section if there are files
            if (fileList.children().length > 0) {
                uploadedFiles.show();
                SynaplanWizard.debugLog('📋 Valid files ready for upload', {
                    validCount: fileList.children().length
                });
            }
        },

        isValidFileType: function(file) {
            var validTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            return validTypes.includes(file.type);
        },

        isValidFileSize: function(file) {
            var maxSize = 10 * 1024 * 1024; // 10MB
            return file.size <= maxSize;
        },

        formatFileSize: function(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    };

    // Dashboard functionality
    var SynaplanDashboard = {
        init: function() {
            this.bindEvents();
            this.loadStats();
        },

        bindEvents: function() {
            // Test widget button
            $('#test-widget').on('click', this.testWidget);
            
            // Preview widget button
            $('#preview-widget').on('click', this.previewWidget);
            
            // View analytics button
            $('#view-analytics').on('click', this.viewAnalytics);
        },

        testWidget: function() {
            var button = $(this);
            var originalText = button.text();
            
            button.prop('disabled', true).text(synaplan_wp_admin.strings.loading);
            
            $.post(synaplan_wp_admin.ajax_url, {
                action: 'synaplan_wp_test_api',
                nonce: synaplan_wp_admin.nonce
            }, function(response) {
                if (response.success) {
                    alert(synaplan_wp_admin.strings.success);
                } else {
                    alert(synaplan_wp_admin.strings.error + ': ' + response.data);
                }
            }).always(function() {
                button.prop('disabled', false).text(originalText);
            });
        },

        previewWidget: function() {
            window.open(window.location.origin, '_blank');
        },

        viewAnalytics: function() {
            alert('Analytics feature coming soon!');
        },

        loadStats: function() {
            // Load statistics from API
            // This would typically make an AJAX call to get real stats
        }
    };

    // Settings functionality
    var SynaplanSettings = {
        init: function() {
            this.bindEvents();
            this.initColorPickers();
        },

        bindEvents: function() {
            // Save settings
            $('#synaplan-settings-form').on('submit', this.saveSettings);
            
            // Test API connection
            $('#test-api').on('click', this.testApiConnection);
        },

        saveSettings: function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = form.serialize();
            
            $.post(synaplan_wp_admin.ajax_url, {
                action: 'synaplan_wp_save_config',
                nonce: synaplan_wp_admin.nonce,
                config: formData
            }, function(response) {
                if (response.success) {
                    SynaplanSettings.showMessage(response.data, 'success');
                } else {
                    SynaplanSettings.showMessage(response.data, 'error');
                }
            });
        },

        testApiConnection: function() {
            var button = $(this);
            var originalText = button.text();
            
            button.prop('disabled', true).text(synaplan_wp_admin.strings.loading);
            
            $.post(synaplan_wp_admin.ajax_url, {
                action: 'synaplan_wp_test_api',
                nonce: synaplan_wp_admin.nonce
            }, function(response) {
                if (response.success) {
                    SynaplanSettings.showMessage(response.data, 'success');
                } else {
                    SynaplanSettings.showMessage(response.data, 'error');
                }
            }).always(function() {
                button.prop('disabled', false).text(originalText);
            });
        },

        initColorPickers: function() {
            // Initialize color pickers if available
            if ($.fn.wpColorPicker) {
                $('.color-picker').wpColorPicker();
            }
        },

        showMessage: function(message, type) {
            // Remove existing messages
            $('.synaplan-message').remove();
            
            // Add new message
            var messageHtml = '<div class="synaplan-message notice notice-' + type + '"><p>' + message + '</p></div>';
            $('.wrap').prepend(messageHtml);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.synaplan-message').fadeOut();
            }, 5000);
        }
    };

    // Initialize based on current page
    $(document).ready(function() {
        if ($('.synaplan-wizard').length) {
            SynaplanWizard.init();
        }
        
        if ($('.synaplan-dashboard').length) {
            SynaplanDashboard.init();
        }
        
        if ($('.synaplan-settings').length) {
            SynaplanSettings.init();
        }
    });

})(jQuery);
