# Plugin Rename Changelog

## Summary
The WordPress plugin has been renamed from **"Synaplan WP AI"** to **"Synaplan AI Support Chat"**.

## Files Changed

### Main Plugin File
- **Deleted:** `synaplan-wp-ai.php`
- **Created:** `synaplan-ai-support-chat.php`
  - Updated Plugin Name header to "Synaplan AI Support Chat"
  - Updated Plugin URI to `https://github.com/synaplan/synaplan-ai-support-chat`
  - Updated Text Domain to `synaplan-ai-support-chat`
  - Updated description to emphasize customer support features

### Text Domain Updates
All translation strings updated from `'synaplan-wp-ai'` to `'synaplan-ai-support-chat'` in:
- `includes/class-synaplan-wp-core.php`
- `includes/class-synaplan-wp-admin.php`
- `includes/class-synaplan-wp-wizard.php`
- `includes/class-synaplan-wp-api.php`
- `includes/class-synaplan-wp-widget.php`
- `admin/views/dashboard.php`
- `admin/views/settings.php`
- `admin/views/help.php`
- `admin/js/admin.js`

### Admin Menu Updates
- Menu slug changed from `synaplan-wp-ai` to `synaplan-ai-support-chat`
- Submenu slugs updated:
  - `synaplan-wp-ai-settings` → `synaplan-ai-support-chat-settings`
  - `synaplan-wp-ai-help` → `synaplan-ai-support-chat-help`
- Admin menu title changed to "Synaplan AI Support Chat"

### Admin Page Hook
- Updated hook check from `synaplan-wp-ai` to `synaplan-ai-support-chat`

### Documentation Updates
- `README.md` - All references updated
- `IMPLEMENTATION_PLAN.md` - All references updated
- `WORDPRESS_WIZARD_IMPLEMENTATION.md` - All references updated
  - Plugin path: `/wp-content/plugins/synaplan-ai-support-chat/`
  - Activation command: `wp plugin activate synaplan-ai-support-chat`

### Admin URL References
All `admin_url()` references updated in views:
- Dashboard quick links
- Help page references
- Settings page references

## What Stayed the Same

### Database Options
All database option names remain unchanged:
- `synaplan_wp_version`
- `synaplan_wp_setup_completed`
- `synaplan_wp_api_key`
- `synaplan_wp_user_id`
- `synaplan_wp_widget_config`
- etc.

**Reason:** Preserves existing user data and settings during plugin update.

### PHP Function Names
All function names remain unchanged:
- `synaplan_wp_init()`
- `synaplan_wp_activate()`
- `synaplan_wp_deactivate()`
- `synaplan_wp_manual_cleanup()`

**Reason:** Maintains backward compatibility and avoids breaking existing installations.

### PHP Class Names
All class names remain unchanged:
- `Synaplan_WP_Core`
- `Synaplan_WP_Admin`
- `Synaplan_WP_API`
- `Synaplan_WP_Wizard`
- `Synaplan_WP_Widget`
- `Synaplan_WP_REST_API`

**Reason:** Internal structure remains consistent; only user-facing names changed.

### PHP Constants
All constants remain unchanged:
- `SYNAPLAN_WP_VERSION`
- `SYNAPLAN_WP_PLUGIN_FILE`
- `SYNAPLAN_WP_PLUGIN_DIR`
- `SYNAPLAN_WP_PLUGIN_URL`
- `SYNAPLAN_WP_PLUGIN_BASENAME`

**Reason:** Internal constants don't need to match user-facing names.

### Directory Structure
The plugin directory name can remain `synaplan-wp` as requested.

## Installation Notes

### Fresh Installation
Simply upload the plugin to:
```
/wp-content/plugins/synaplan-ai-support-chat/
```

Or keep the directory name as:
```
/wp-content/plugins/synaplan-wp/
```
(WordPress reads the plugin name from the header, not the directory name)

### Upgrading from Old Version
Users upgrading from "Synaplan WP AI" will:
1. See the new name "Synaplan AI Support Chat" in their plugin list
2. Keep all existing settings and data (options remain unchanged)
3. Keep existing API keys and widget configuration
4. Menu will appear as "Synaplan AI Support Chat" in WordPress admin

## Version
- Version: 1.0.3 (unchanged)
- Date: 2025-10-08

## Summary of Changes
- **Plugin Name:** "Synaplan WP AI" → "Synaplan AI Support Chat"
- **Text Domain:** `synaplan-wp-ai` → `synaplan-ai-support-chat`
- **Menu Slugs:** All updated to new naming convention
- **Documentation:** All updated to reflect new name
- **GitHub:** Repository name should be updated to `synaplan-ai-support-chat`

All user-facing strings and branding have been updated while maintaining backward compatibility for existing installations.
