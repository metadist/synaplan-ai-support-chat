<?php
/**
 * Signed visitor identity for the Synaplan widget
 *
 * @package Synaplan_WP_AI
 * @since 1.0.11
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Tells the widget which WordPress user is signed in, in a way a visitor
 * cannot forge: the user ID is signed on this server with the widget's
 * signing key. The key never leaves the server.
 */
class Synaplan_WP_Visitor {

    /**
     * How long a signature is valid. Synaplan accepts at most 24 hours.
     */
    const SIGNATURE_LIFETIME = 12 * HOUR_IN_SECONDS;

    /**
     * Longest user ID Synaplan accepts.
     */
    const MAX_ID_LENGTH = 255;

    /**
     * Widget ID from the Synaplan dashboard (wdg_...), '' when not set.
     */
    public static function get_widget_id() {
        return (string) get_option('synaplan_wp_widget_public_id', '');
    }

    /**
     * Signing key from the Synaplan dashboard, '' when not set.
     */
    public static function get_signing_key() {
        return (string) get_option('synaplan_wp_visitor_signing_key', '');
    }

    /**
     * Whether a signing key is stored.
     */
    public static function has_signing_key() {
        return '' !== self::get_signing_key();
    }

    /**
     * Store the widget ID. Returns false when it is not a widget ID.
     */
    public static function set_widget_id($widget_id) {
        $widget_id = trim((string) $widget_id);
        if ('' !== $widget_id && !preg_match('/^wdg_[A-Za-z0-9]{1,64}$/', $widget_id)) {
            return false;
        }

        update_option('synaplan_wp_widget_public_id', $widget_id, false);

        return true;
    }

    /**
     * Store the signing key. Returns false when it is not a signing key.
     */
    public static function set_signing_key($key) {
        $key = strtolower(trim((string) $key));
        if (!preg_match('/^[0-9a-f]{64}$/', $key)) {
            return false;
        }

        // Not autoloaded: only pages for signed-in users need it.
        update_option('synaplan_wp_visitor_signing_key', $key, false);

        return true;
    }

    /**
     * Remove the signing key.
     */
    public static function delete_signing_key() {
        delete_option('synaplan_wp_visitor_signing_key');
    }

    /**
     * Signature Synaplan checks: "<expiry>.<hex HMAC-SHA256>" over
     * "widgetId\nuserId\nexpiry".
     */
    public static function sign($widget_id, $external_user_id, $expiry, $key) {
        $mac = hash_hmac('sha256', $widget_id . "\n" . $external_user_id . "\n" . $expiry, $key);

        return $expiry . '.' . $mac;
    }

    /**
     * The signed-in visitor for the widget, or null when there is none to
     * name: nobody is signed in, or the widget ID or key is missing.
     *
     * @return array|null array('externalUserId' => ..., 'externalUserIdSignature' => ...)
     */
    public static function current() {
        $widget_id = self::get_widget_id();
        $key = self::get_signing_key();
        if ('' === $widget_id || '' === $key || !is_user_logged_in()) {
            return null;
        }

        $user = wp_get_current_user();

        /**
         * The ID your user-data API knows the visitor by. Default: the
         * WordPress user ID. Return '' to send no ID for this visitor.
         *
         * @param string  $external_user_id
         * @param WP_User $user
         */
        $external_user_id = trim((string) apply_filters('synaplan_wp_external_user_id', (string) $user->ID, $user));
        if ('' === $external_user_id || strlen($external_user_id) > self::MAX_ID_LENGTH) {
            return null;
        }

        return array(
            'externalUserId' => $external_user_id,
            'externalUserIdSignature' => self::sign($widget_id, $external_user_id, time() + self::SIGNATURE_LIFETIME, $key),
        );
    }

    /**
     * Script that hands the signed visitor to the widget loader, '' when
     * there is no visitor to name.
     */
    public static function inline_script() {
        $visitor = self::current();
        if (null === $visitor) {
            return '';
        }

        return 'window.SynaplanWidgetVisitor = ' . wp_json_encode($visitor) . ';';
    }
}
