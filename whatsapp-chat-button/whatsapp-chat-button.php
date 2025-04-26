<?php
/**
 * Plugin Name: WhatsApp Chat Button
 * Plugin URI: https://yourwebsite.com/whatsapp-chat-button
 * Description: Adds a floating WhatsApp chat button to your WordPress website
 * Version: 1.0.0
 * Author: aparaic
 * Author URI: https://github.com/inioluwa-a
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: whatsapp-chat-button
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('WCB_VERSION', '1.0.0');
define('WCB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCB_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Load plugin textdomain for internationalization
 */
function wcb_load_textdomain() {
    load_plugin_textdomain(
        'whatsapp-chat-button',
        false,
        dirname(WCB_PLUGIN_BASENAME) . '/languages/'
    );
}
add_action('plugins_loaded', 'wcb_load_textdomain');

/**
 * Add menu item to WordPress admin
 */
function wcb_add_admin_menu() {
    add_options_page(
        __('WhatsApp Chat Button Settings', 'whatsapp-chat-button'),
        __('WhatsApp Chat Button', 'whatsapp-chat-button'),
        'manage_options',
        'whatsapp-chat-button',
        'wcb_settings_page'
    );
}
add_action('admin_menu', 'wcb_add_admin_menu');

/**
 * Register plugin settings
 */
function wcb_register_settings() {
    // Register settings
    register_setting('wcb_settings', 'wcb_whatsapp_number', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => '',
    ));
    
    register_setting('wcb_settings', 'wcb_custom_message', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default' => '',
    ));
    
    register_setting('wcb_settings', 'wcb_button_position', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => 'right',
    ));
    
    // Add settings section
    add_settings_section(
        'wcb_settings_section',
        __('WhatsApp Button Configuration', 'whatsapp-chat-button'),
        'wcb_settings_section_callback',
        'wcb_settings'
    );
    
    // Add settings fields
    add_settings_field(
        'wcb_whatsapp_number',
        __('WhatsApp Number', 'whatsapp-chat-button'),
        'wcb_whatsapp_number_callback',
        'wcb_settings',
        'wcb_settings_section'
    );
    
    add_settings_field(
        'wcb_custom_message',
        __('Custom Message', 'whatsapp-chat-button'),
        'wcb_custom_message_callback',
        'wcb_settings',
        'wcb_settings_section'
    );
    
    add_settings_field(
        'wcb_button_position',
        __('Button Position', 'whatsapp-chat-button'),
        'wcb_button_position_callback',
        'wcb_settings',
        'wcb_settings_section'
    );
}
add_action('admin_init', 'wcb_register_settings');

/**
 * Settings section callback
 */
function wcb_settings_section_callback() {
    echo '<p>' . esc_html__('Configure your WhatsApp chat button settings below.', 'whatsapp-chat-button') . '</p>';
}

/**
 * WhatsApp number field callback
 */
function wcb_whatsapp_number_callback() {
    $number = get_option('wcb_whatsapp_number');
    ?>
    <input type="text" name="wcb_whatsapp_number" value="<?php echo esc_attr($number); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('Enter your WhatsApp number with country code (e.g., +1234567890)', 'whatsapp-chat-button'); ?></p>
    <?php
}

/**
 * Custom message field callback
 */
function wcb_custom_message_callback() {
    $message = get_option('wcb_custom_message');
    ?>
    <textarea name="wcb_custom_message" rows="3" class="large-text"><?php echo esc_textarea($message); ?></textarea>
    <p class="description"><?php esc_html_e('Enter the default message that will be sent when users click the button', 'whatsapp-chat-button'); ?></p>
    <?php
}

/**
 * Button position field callback
 */
function wcb_button_position_callback() {
    $position = get_option('wcb_button_position', 'right');
    ?>
    <label>
        <input type="radio" name="wcb_button_position" value="right" <?php checked($position, 'right'); ?> />
        <?php esc_html_e('Right', 'whatsapp-chat-button'); ?>
    </label>
    <br />
    <label>
        <input type="radio" name="wcb_button_position" value="left" <?php checked($position, 'left'); ?> />
        <?php esc_html_e('Left', 'whatsapp-chat-button'); ?>
    </label>
    <?php
}

/**
 * Create the settings page
 */
function wcb_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save settings if data has been posted
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wcb_settings')) {
        // Settings are saved by the Settings API
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wcb_settings');
            do_settings_sections('wcb_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Enqueue styles and scripts
 */
function wcb_enqueue_scripts() {
    // Only enqueue on frontend
    if (is_admin()) {
        return;
    }
    
    // Enqueue styles
    wp_enqueue_style(
        'wcb-styles',
        WCB_PLUGIN_URL . 'assets/css/style.css',
        array(),
        WCB_VERSION
    );
    
    // Enqueue scripts
    wp_enqueue_script(
        'wcb-script',
        WCB_PLUGIN_URL . 'assets/js/script.js',
        array('jquery'),
        WCB_VERSION,
        true
    );
    
    // Pass settings to JavaScript
    wp_localize_script(
        'wcb-script',
        'wcbSettings',
        array(
            'whatsappNumber' => get_option('wcb_whatsapp_number'),
            'customMessage' => get_option('wcb_custom_message'),
            'buttonPosition' => get_option('wcb_button_position', 'right'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wcb-nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'wcb_enqueue_scripts');

/**
 * Add WhatsApp button to footer
 */
function wcb_add_button() {
    // Don't show button if number is not configured
    $number = get_option('wcb_whatsapp_number');
    if (empty($number)) {
        return;
    }
    
    $position = get_option('wcb_button_position', 'right');
    $position_class = $position === 'right' ? 'wcb-right' : 'wcb-left';
    ?>
    <div id="whatsapp-chat-button" class="<?php echo esc_attr($position_class); ?>">
        <a href="#" class="wcb-button" aria-label="<?php esc_attr_e('Chat on WhatsApp', 'whatsapp-chat-button'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.129.332.202.043.073.043.423-.101.828z"/>
            </svg>
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'wcb_add_button');

/**
 * Plugin activation hook
 */
function wcb_activate() {
    // Set default options
    if (!get_option('wcb_button_position')) {
        update_option('wcb_button_position', 'right');
    }
    
    // Clear any existing caches
    wp_cache_flush();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wcb_activate');

/**
 * Plugin deactivation hook
 */
function wcb_deactivate() {
    // Clear any caches
    wp_cache_flush();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wcb_deactivate');

/**
 * Plugin uninstall hook
 */
function wcb_uninstall() {
    // Delete plugin options
    delete_option('wcb_whatsapp_number');
    delete_option('wcb_custom_message');
    delete_option('wcb_button_position');
}
register_uninstall_hook(__FILE__, 'wcb_uninstall'); 