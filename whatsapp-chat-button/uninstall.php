<?php
/**
 * Uninstall WhatsApp Chat Button
 *
 * Deletes all plugin data when the plugin is uninstalled.
 *
 * @package WhatsApp_Chat_Button
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('wcb_whatsapp_number');
delete_option('wcb_custom_message');
delete_option('wcb_button_position');

// Clear any cached data
wp_cache_flush();

// Flush rewrite rules
flush_rewrite_rules(); 