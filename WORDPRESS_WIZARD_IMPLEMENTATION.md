# WordPress Wizard Complete Integration - Implementation Summary

## Overview

This document describes the complete implementation of the WordPress wizard integration with Synaplan, specifically addressing the three missing pieces that were previously incomplete:

1. **RAG File Upload** - Upload and vectorize files during wizard setup
2. **Prompt Configuration** - Enable file search tool on general prompt with WORDPRESS_WIZARD filter
3. **Widget Configuration** - Save widget settings to the Synaplan database

## Changes Made

### 1. Synaplan Backend (Main Application)

#### New API Endpoint: `wpWizardComplete`

**File**: `/wwwroot/synaplan/app/inc/integrations/wordpresswizard.php` (NEW)

- Created new `WordPressWizard` class to handle wizard completion
- Implements `completeWizardSetup()` method that:
  - Processes uploaded RAG files with group key "WORDPRESS_WIZARD"
  - Enables file search tool on the general prompt with group filter
  - Saves widget configuration to database
  - Handles errors gracefully without failing the entire process

**Key Features**:
- Rate limiting: Maximum 5 files per wizard session
- File size limit: 10MB per file
- Supported file types: PDF, DOCX, TXT, JPG, JPEG, PNG, MP3, MP4
- Automatic cleanup of temporary files
- Comprehensive logging for debugging

#### API Configuration Updates

**File**: `/wwwroot/synaplan/app/inc/api/apiauthenticator.php`
- Added `wpWizardComplete` to authenticated endpoints list

**File**: `/wwwroot/synaplan/app/inc/api/_api-restcalls.php`
- Added route handler for `wpWizardComplete` action

**File**: `/wwwroot/synaplan/app/inc/_coreincludes.php`
- Included new WordPressWizard class in core includes

### 2. WordPress Plugin Updates

#### Enhanced API Client

**File**: `/wwwroot/synaplan-wp/includes/class-synaplan-wp-api.php`

Added new method `complete_wizard_setup()`:
- Calls the new `wpWizardComplete` endpoint
- Handles file uploads via cURL with multipart form data
- Sends widget configuration parameters
- Timeout: 180 seconds (3 minutes) for file processing
- Returns structured response with success/error information

#### Updated Wizard Process

**File**: `/wwwroot/synaplan-wp/includes/class-synaplan-wp-wizard.php`

Modified `process_step_4()`:
- Removed separate calls to `upload_file_for_rag()` and `save_widget()`
- Now calls unified `complete_wizard_setup()` endpoint
- Improved error handling and logging
- Better cleanup of temporary files
- Removed obsolete `process_uploaded_files_for_rag()` method

## How It Works

### Workflow Sequence

1. **User Registration**
   - WordPress plugin calls `userRegister` API
   - Creates new user account
   - Generates API key automatically
   - Returns user ID and API key to plugin

2. **Wizard Completion** (NEW UNIFIED ENDPOINT)
   - Plugin calls `wpWizardComplete` with:
     - Widget configuration (color, position, message, etc.)
     - Uploaded files (if any)
     - API key for authentication
   
3. **Backend Processing**
   - **Step 1**: Process uploaded files
     - Move files to user directory
     - Create database entries in BMESSAGES table
     - Process through RAG vectorization system
     - Store with group key "WORDPRESS_WIZARD"
   
   - **Step 2**: Enable file search (if files uploaded)
     - Get current general prompt configuration
     - Enable `tool_files` setting
     - Set `tool_files_keyword` to "WORDPRESS_WIZARD"
     - Update prompt in database
   
   - **Step 3**: Save widget configuration
     - Validate widget parameters
     - Save to BCONFIG table with widget_1 group
     - Store all widget settings (colors, position, messages, etc.)

4. **Cleanup**
   - Delete temporary files
   - Clear wizard session data
   - Mark setup as completed

## API Endpoint Details

### Request: `wpWizardComplete`

**Method**: POST  
**Authentication**: Bearer token (API key)  
**Content-Type**: multipart/form-data

**Parameters**:
```php
[
    'action' => 'wpWizardComplete',
    'widgetId' => 1,                    // Widget ID (1-9)
    'widgetColor' => '#007bff',         // Widget button color
    'widgetIconColor' => '#ffffff',     // Icon color
    'widgetPosition' => 'bottom-right', // Position on page
    'autoMessage' => 'Hello!...',       // Welcome message
    'widgetPrompt' => 'general',        // Prompt type
    'autoOpen' => '0',                  // Auto-open flag
    'integrationType' => 'floating-button',
    'files[]' => [file1, file2, ...]    // Optional uploaded files
]
```

**Response**:
```json
{
    "success": true,
    "message": "WordPress wizard setup completed successfully",
    "filesProcessed": 2,
    "widget": {
        "widgetId": 1,
        "saved": true
    }
}
```

**Error Response**:
```json
{
    "success": false,
    "error": "Error message description"
}
```

## Database Changes

### Tables Modified

1. **BMESSAGES**
   - Stores uploaded file entries
   - Links to RAG system via BRAG table

2. **BRAG**
   - Stores vectorized file content
   - Uses group key "WORDPRESS_WIZARD" for filtering

3. **BCONFIG**
   - Stores widget configuration
   - Group: `widget_1` (or widget_2, widget_3, etc.)
   - Settings: color, iconColor, position, autoMessage, prompt, etc.

4. **BPROMPTS** & **BPROMPTMETA**
   - Updates general prompt configuration
   - Enables file search tool
   - Sets group filter for RAG searches

## Testing Instructions

### Prerequisites
- WordPress site with Synaplan WP AI plugin installed
- Access to Synaplan backend logs
- Test files (PDF, DOCX) for upload testing

### Test Procedure

1. **Start Wizard**
   - Navigate to WordPress admin → Synaplan AI → Setup Wizard
   - Verify wizard loads without errors

2. **Step 1: Account Creation**
   - Enter email, password, and language
   - Accept terms
   - Click "Next"
   - **Expected**: User created in BUSER table, API key generated in BAPIKEYS table

3. **Step 2: Widget Settings**
   - Configure welcome message
   - Choose AI assistant type (general)
   - Select widget color and position
   - Click "Next"
   - **Expected**: Settings saved to wizard session

4. **Step 3: File Upload** (CRITICAL TEST)
   - Upload 1-2 test files (PDF or DOCX)
   - Verify file preview appears
   - Click "Next"
   - **Expected**: Files saved to temporary directory

5. **Step 4: Complete Setup**
   - Review settings
   - Click "Complete Setup"
   - **Expected**: 
     - Success message appears
     - Redirected to widget management page
     - Check backend logs for:
       - "Calling wpWizardComplete endpoint"
       - "Wizard completion successful"
       - "Processing RAG files"
       - File vectorization logs

### Verification Checklist

- [ ] User account created (check BUSER table)
- [ ] API key generated (check BAPIKEYS table)
- [ ] Files uploaded and processed (check BMESSAGES table, BDIRECT='IN', BFILE=1)
- [ ] RAG entries created (check BRAG table, BGROUPKEY='WORDPRESS_WIZARD')
- [ ] General prompt updated (check BPROMPTMETA for tool_files='1' and tool_files_keyword='WORDPRESS_WIZARD')
- [ ] Widget configuration saved (check BCONFIG table, BGROUP='widget_1')
- [ ] Widget displays on WordPress site
- [ ] Chat widget works correctly
- [ ] File search enabled in chat (test by asking about uploaded document content)

### Test Queries for File Search

After completing wizard with uploaded files, test the file search functionality:

1. Open the chat widget on your WordPress site
2. Ask questions related to uploaded file content
3. The AI should reference the uploaded documents
4. Check backend logs for RAG search queries with WORDPRESS_WIZARD filter

Example queries:
- "What information do you have about [topic in uploaded document]?"
- "Can you summarize the document I uploaded?"
- "Tell me about [specific section in uploaded file]"

## Troubleshooting

### Common Issues

1. **Files not uploading**
   - Check file size (max 10MB)
   - Verify file type is supported
   - Check PHP upload limits in WordPress
   - Review WordPress error logs

2. **Widget not appearing**
   - Verify BCONFIG entries exist for widget_1
   - Check widget embed code in WordPress theme
   - Ensure user ID and widget ID are correct

3. **File search not working**
   - Verify BRAG entries exist with WORDPRESS_WIZARD group
   - Check BPROMPTMETA for tool_files='1'
   - Verify tool_files_keyword='WORDPRESS_WIZARD'
   - Check if RAG vectorization completed successfully

4. **API authentication errors**
   - Verify API key is saved in WordPress options
   - Check BAPIKEYS table for active key
   - Ensure Bearer token is sent in request headers

### Debug Logging

Enable debug logging in WordPress:
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check logs:
- WordPress: `/wp-content/debug.log`
- Synaplan: Check error_log() calls in WordPressWizard class

## Security Considerations

1. **File Upload Security**
   - File type validation (whitelist only)
   - File size limits enforced
   - Files stored in user-specific directories
   - Filenames sanitized and made unique

2. **Rate Limiting**
   - Maximum 5 files per wizard session
   - User registration rate limited (5 per hour per IP)
   - API endpoints require authentication

3. **Data Validation**
   - All input sanitized via db::EscString()
   - Widget parameters validated
   - Color codes validated with regex
   - Position values whitelisted

4. **Authentication**
   - API key required for all authenticated endpoints
   - Session-based authentication for widget users
   - Bearer token validation

## Future Enhancements

Potential improvements for future releases:

1. **Progress Indicator**
   - Show real-time file upload progress
   - Display vectorization status
   - Provide estimated completion time

2. **File Management**
   - Allow users to manage uploaded files
   - Add file deletion capability
   - Show file processing status

3. **Advanced Configuration**
   - Multiple widget support
   - Custom RAG group names
   - Per-widget file associations

4. **Error Recovery**
   - Retry failed file uploads
   - Resume interrupted vectorization
   - Better error messages for users

## Conclusion

The WordPress wizard integration is now complete with all three missing pieces implemented:

✅ **RAG File Upload** - Files are uploaded, stored, and vectorized with proper group filtering  
✅ **Prompt Configuration** - File search tool automatically enabled on general prompt  
✅ **Widget Configuration** - Widget settings saved and persisted to database  

The implementation uses a unified `wpWizardComplete` endpoint that handles all setup steps in a single, atomic operation, ensuring consistency and reliability.

---

**Implementation Date**: 2025-10-07  
**Version**: 1.0.0  
**Status**: ✅ Complete and Ready for Testing

