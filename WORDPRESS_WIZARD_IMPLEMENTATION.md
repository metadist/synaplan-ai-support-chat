# WordPress Wizard Implementation - Complete Flow

## Overview
This implementation provides a secure, complete WordPress plugin setup flow that:
1. Verifies WordPress site via callback
2. Creates user with status 'NEW' (no email confirmation needed)
3. Generates API key automatically
4. Uploads files to RAG system
5. Configures prompts with file search
6. Saves widget configuration to BCONFIG table

## Key Features

### 1. Secure WordPress Verification
- Plugin generates a random verification token on activation
- Token is sent to app.synaplan.com during registration
- App calls back to WordPress site's REST API to verify the token
- Only verified WordPress sites can create users

### 2. Automatic User Creation
- User is created with `BUSERLEVEL = 'NEW'` (not `PIN:xxxx`)
- No email confirmation required
- User is immediately active and can use the platform

### 3. API Key Management
- API key is generated automatically during wizard completion
- Stored securely in WordPress options
- Shown in dashboard with "click to reveal" security feature
- Can be copied with one click

### 4. Complete Widget Configuration
- Widget settings saved to BCONFIG table (not just WordPress options)
- Uses same format as manual configuration via app.synaplan.com
- All inputs sanitized with `db::EscString` for security

## Files Modified

### Main Application (synaplan/)
1. **app/inc/integrations/wordpresswizard.php** - Complete rewrite
   - `completeWizardSetup()` - Main entry point
   - `verifyWordPressSite()` - Secure callback verification
   - `createWordPressUser()` - User creation with status 'NEW'
   - `createUserApiKey()` - API key generation
   - `saveWidgetConfiguration()` - Widget config to BCONFIG
   - `processWizardRAGFiles()` - File uploads (unchanged)
   - `enableFileSearchOnGeneralPrompt()` - Prompt config (unchanged)

2. **app/inc/api/_api-restcalls.php**
   - Added `case 'wpWizardComplete'` route

### WordPress Plugin (synaplan-wp/)
1. **includes/class-synaplan-wp-api.php**
   - Updated `complete_wizard_setup()` method
   - Now sends verification token and complete user data
   - Handles file uploads with multipart form data

2. **includes/class-synaplan-wp-wizard.php**
   - Updated `process_step_4()` to use new unified flow
   - Properly extracts API key and user ID from response
   - Saves credentials to WordPress options

3. **includes/class-synaplan-wp-rest-api.php**
   - Unchanged - already has verification endpoint

4. **includes/class-synaplan-wp-core.php**
   - Unchanged - already has token generation and validation

5. **admin/views/dashboard.php** - NEW
   - Shows API key with click-to-reveal feature
   - Copy buttons for API key and embed code
   - Links to full Synaplan platform

6. **admin/views/settings.php** - NEW
   - Widget configuration interface
   - Links to advanced settings on app.synaplan.com

7. **admin/views/help.php** - NEW
   - Getting started guide
   - FAQ section
   - Support contact information

## Security Features

### Input Sanitization
All user inputs are sanitized using `db::EscString()`:
- Email addresses
- Passwords (hashed with MD5)
- Widget colors (hex codes)
- Text fields (messages, placeholders, etc.)
- File paths and names

### WordPress Verification Flow
```
1. Plugin activation → Generate random token → Store in wp_options
2. User submits wizard → Token sent to app.synaplan.com
3. App calls back to WordPress: POST /wp-json/synaplan-wp/v1/verify
4. WordPress validates token (15-minute expiry)
5. If valid, app creates user with status 'NEW'
6. API key returned to WordPress plugin
```

### API Key Storage
- Stored in `wp_options` table as `synaplan_wp_api_key`
- Not displayed by default - requires click to reveal
- Can be copied but not modified through UI

## Testing Steps

### Prerequisites
1. WordPress installation (5.0+)
2. PHP 8.0+
3. Synaplan plugin uploaded to `/wp-content/plugins/synaplan-wp-ai/`

### Test Procedure

#### 1. Plugin Activation
```bash
# Activate plugin
wp plugin activate synaplan-wp-ai

# Verify options created
wp option get synaplan_wp_version
wp option get synaplan_wp_verification_token
```

#### 2. Run Wizard
1. Go to WordPress Admin → Synaplan AI
2. Fill in wizard step 1:
   - Email: test@example.com
   - Password: Test123!@#
   - Language: English
   - Accept terms
3. Fill in wizard step 2:
   - Welcome message: "Hello! How can I help?"
   - AI type: General Support
   - Color: #007bff
   - Position: Bottom Right
4. Fill in wizard step 3 (optional):
   - Upload 1-5 PDF or DOCX files
5. Review and complete step 4

#### 3. Verify User Creation
```sql
-- Check user was created with status 'NEW'
SELECT BID, BMAIL, BUSERLEVEL, BUSERDETAILS 
FROM BUSER 
WHERE BMAIL = 'test@example.com';

-- Should show:
-- BUSERLEVEL = 'NEW'
-- BUSERDETAILS contains emailConfirmed: true, wordpressVerified: true
```

#### 4. Verify API Key
```sql
-- Check API key was created
SELECT BOWNERID, BNAME, BKEY, BSTATUS 
FROM BAPIKEYS 
WHERE BNAME = 'WordPress Plugin' 
ORDER BY BCREATED DESC LIMIT 1;

-- Should show:
-- BKEY starting with 'sk_live_'
-- BSTATUS = 'active'
```

#### 5. Verify Widget Configuration
```sql
-- Check widget config in BCONFIG
SELECT BOWNERID, BGROUP, BSETTING, BVALUE 
FROM BCONFIG 
WHERE BGROUP = 'widget_1' 
AND BOWNERID = [USER_ID_FROM_STEP_3];

-- Should show multiple rows with settings:
-- color, iconColor, position, autoMessage, prompt, etc.
```

#### 6. Verify Files (if uploaded)
```sql
-- Check files were uploaded
SELECT BID, BTEXT, BFILEPATH, BFILETYPE 
FROM BMESSAGES 
WHERE BUSERID = [USER_ID] 
AND BMESSTYPE = 'RAG' 
AND BFILE = 1;

-- Check RAG entries
SELECT BMID, BGROUPKEY, BTEXT 
FROM BRAG 
WHERE BUID = [USER_ID] 
AND BGROUPKEY = 'WORDPRESS_WIZARD';
```

#### 7. Verify Prompt Configuration (if files uploaded)
```sql
-- Check file search tool is enabled on general prompt
SELECT p.BTOPIC, ps.BTOKEN, ps.BVALUE 
FROM BPROMPTS p
JOIN BPROMPTSETTINGS ps ON ps.BPROMPTID = p.BID
WHERE p.BTOPIC = 'general' 
AND (ps.BTOKEN = 'tool_files' OR ps.BTOKEN = 'tool_files_keyword');

-- Should show:
-- tool_files = '1'
-- tool_files_keyword = 'WORDPRESS_WIZARD'
```

#### 8. Test Dashboard
1. Go to WordPress Admin → Synaplan AI → Dashboard
2. Verify API key is hidden (shows dots)
3. Click "Show API Key"
4. Verify API key is revealed
5. Click "Copy" button
6. Verify API key was copied to clipboard

#### 9. Test Widget on Frontend
1. Visit any page on the WordPress site
2. Verify chat widget button appears (bottom right by default)
3. Click widget button
4. Verify chat interface opens
5. Send a test message
6. Verify AI responds

#### 10. Test Login to App
1. Go to https://app.synaplan.com/
2. Login with:
   - Email: test@example.com
   - Password: Test123!@#
3. Verify login succeeds (no email confirmation needed)
4. Navigate to File Manager
5. Verify uploaded files appear (if any were uploaded)
6. Navigate to Prompts
7. Verify general prompt has file search enabled

### Expected Results

✅ User created with status 'NEW'
✅ API key generated and stored
✅ Widget configuration saved to BCONFIG
✅ Files uploaded and vectorized (if provided)
✅ Prompt configured with file search (if files uploaded)
✅ Dashboard shows API key with click-to-reveal
✅ Widget appears on frontend
✅ User can login to app.synaplan.com without email confirmation

## Debugging

### Enable Debug Logging
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Check Logs
```bash
# WordPress logs
tail -f /var/www/html/wp-content/debug.log

# App logs
tail -f /var/wwwroot/synaplan/app/logs/error.log
```

### Common Issues

#### Issue: "WordPress site verification failed"
- Check that REST API is accessible: `/wp-json/synaplan-wp/v1/verify`
- Verify token hasn't expired (15-minute limit)
- Check SSL certificate is valid

#### Issue: "Missing API key or user ID in response"
- Check app logs for errors
- Verify `wpWizardComplete` route is registered
- Check response structure matches expected format

#### Issue: Widget not appearing
- Clear browser cache
- Check console for JavaScript errors
- Verify user ID is correct in embed code

## API Endpoint Details

### POST /api.php?action=wpWizardComplete

**Request Parameters:**
```json
{
  "action": "wpWizardComplete",
  "email": "user@example.com",
  "password": "password123",
  "language": "en",
  "verification_token": "abc123...",
  "verification_url": "https://site.com/wp-json/synaplan-wp/v1/verify",
  "site_url": "https://site.com",
  "widgetId": 1,
  "widgetColor": "#007bff",
  "widgetIconColor": "#ffffff",
  "widgetPosition": "bottom-right",
  "autoMessage": "Hello!",
  "widgetPrompt": "general",
  "files[]": [File objects]
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "WordPress wizard setup completed successfully",
  "data": {
    "user_id": 123,
    "email": "user@example.com",
    "api_key": "sk_live_abc123...",
    "filesProcessed": 2,
    "widget_configured": true,
    "site_verified": true
    }
}
```

**Response (Error):**
```json
{
    "success": false,
  "error": "Error message here"
}
```

## Maintenance

### Cleanup Old Verification Tokens
Verification tokens expire after 15 minutes but remain in the database. Consider adding a cron job to clean up old tokens:

```php
// Add to WordPress cron
wp_schedule_event(time(), 'daily', 'synaplan_cleanup_tokens');
add_action('synaplan_cleanup_tokens', function() {
    delete_option('synaplan_wp_verification_token');
});
```

### Update API Key
If the API key needs to be regenerated:
1. Delete from WordPress: `wp option delete synaplan_wp_api_key`
2. User must re-run wizard or manually create API key on app.synaplan.com
3. Save new API key: `wp option set synaplan_wp_api_key "sk_live_new_key"`

## Future Enhancements

- [ ] Support for multiple widgets per site
- [ ] Analytics dashboard in WordPress admin
- [ ] Bulk file upload with progress indicator
- [ ] Webhook integration for real-time updates
- [ ] White-label customization options

## Support

For issues or questions:
- Email: support@synaplan.com
- Website: https://synaplan.com
- Dashboard: https://app.synaplan.com/

---

**Version:** 1.0.3
**Last Updated:** 2025-10-08
**Author:** Synaplan Development Team