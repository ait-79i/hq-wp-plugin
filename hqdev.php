<?php
/**
 * Plugin Name: HQDev Top Text
 * Description: Add custom text to the top of your website
 * Version: 1.1.0
 * Author: HQDev
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// PART 1: PLUGIN INITIALIZATION
// ========================================
// This section registers all WordPress hooks
// You can comment/uncomment each line to test individual features

// Admin menu hooks
add_action('admin_menu', 'hqdev_add_admin_menu');           // Settings page in Settings menu
add_action('admin_menu', 'hqdev_add_simple_admin_page');    // Main dashboard page

// Settings initialization
add_action('admin_init', 'hqdev_settings_init');            // Register plugin settings

// Front-end display hooks
add_action('wp_head', 'hqdev_add_top_text_styles');         // Add CSS styles
add_action('wp_body_open', 'hqdev_display_top_text');       // Display text at top
add_action('wp_footer', 'hqdev_display_top_text_fallback'); // Fallback display method

// Shortcode registration
add_shortcode('hqdev_hello', 'hqdev_hello_shortcode');       // [hqdev_hello] shortcode
add_shortcode('hqdev_button', 'hqdev_button_shortcode');     // [hqdev_button] shortcode
// ========================================
// PART 2: ADMIN MENU REGISTRATION
// ========================================
// These functions create menu items in WordPress admin

// Function 2A: Add settings page under Settings menu
function hqdev_add_admin_menu() {
    add_options_page(
        'HQDev Top Text Settings',   // Page title (shown in browser tab)
        'Top Text',                  // Menu title (shown in menu)
        'manage_options',            // Required capability
        'hqdev-top-text',           // Menu slug (unique identifier)
        'hqdev_options_page'        // Function to display the page
    );
}

// Function 2B: Add main dashboard page in sidebar
function hqdev_add_simple_admin_page() {
    add_menu_page(
        'HQ Dashboard',           // Page title
        'HQ Dash',                     // Menu title
        'manage_options',            // Capability
        'hqdev-dashboard',           // Menu slug
        'hqdev_simple_admin_page',   // Function to display page
        'dashicons-admin-tools',     // Icon
        10                           // Position (lower = higher in menu)
    );
}
// ========================================
// PART 3: SETTINGS REGISTRATION
// ========================================
// This function registers all plugin settings with WordPress

// Function 3A: Initialize all plugin settings
function hqdev_settings_init() {
    // Register the settings group
    register_setting('hqdev_top_text', 'hqdev_top_text_settings');
    
    // Add settings section
    add_settings_section(
        'hqdev_top_text_section',           // Section ID
        'Top Text Settings',                // Section title
        'hqdev_settings_section_callback',  // Callback function
        'hqdev_top_text'                   // Page slug
    );
    
    // Add individual settings fields
    add_settings_field(
        'top_text',                    // Field ID
        'Text to Display',             // Field label
        'hqdev_top_text_render',      // Render function
        'hqdev_top_text',             // Page slug
        'hqdev_top_text_section'      // Section ID
    );
    
    add_settings_field(
        'text_color',
        'Text Color',
        'hqdev_text_color_render',
        'hqdev_top_text',
        'hqdev_top_text_section'
    );
    
    add_settings_field(
        'background_color',
        'Background Color',
        'hqdev_background_color_render',
        'hqdev_top_text',
        'hqdev_top_text_section'
    );
    
    add_settings_field(
        'enable_text',
        'Enable Top Text',
        'hqdev_enable_text_render',
        'hqdev_top_text',
        'hqdev_top_text_section'
    );
}
// ========================================
// PART 4: FORM FIELD RENDER FUNCTIONS
// ========================================
// These functions create the HTML for each settings field

// Function 4A: Render textarea for top text
function hqdev_top_text_render() {
    $options = get_option('hqdev_top_text_settings');
    $text = isset($options['top_text']) ? $options['top_text'] : '';
    ?>
    <textarea name='hqdev_top_text_settings[top_text]' rows='4' cols='50' placeholder='Enter the text you want to display at the top of your site...'><?php echo esc_textarea($text); ?></textarea>
    <p class="description">Enter the text you want to display at the top of your website. HTML is allowed.</p>
    <?php
}
// Function 4B: Render color picker for text color
function hqdev_text_color_render() {
    $options = get_option('hqdev_top_text_settings');
    $color = isset($options['text_color']) ? $options['text_color'] : '#ffffff';
    ?>
    <input type='color' name='hqdev_top_text_settings[text_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Choose the text color for the top banner.</p>
    <?php
}
// Function 4C: Render color picker for background color
function hqdev_background_color_render() {
    $options = get_option('hqdev_top_text_settings');
    $color = isset($options['background_color']) ? $options['background_color'] : '#333333';
    ?>
    <input type='color' name='hqdev_top_text_settings[background_color]' value='<?php echo esc_attr($color); ?>'>
    <p class="description">Choose the background color for the top banner.</p>
    <?php
}
// Function 4D: Render checkbox to enable/disable feature
function hqdev_enable_text_render() {
    $options = get_option('hqdev_top_text_settings');
    $enabled = isset($options['enable_text']) ? $options['enable_text'] : 0;
    ?>
    <input type='checkbox' name='hqdev_top_text_settings[enable_text]' <?php checked($enabled, 1); ?> value='1'>
    <label>Check to enable the top text display</label>
    <p class="description">Turn this on to show the text at the top of your website.</p>
    <?php
}
// ========================================
// PART 5: ADMIN PAGE DISPLAY FUNCTIONS
// ========================================
// These functions create the HTML for admin pages

// Function 5A: Settings section description
function hqdev_settings_section_callback() {
    echo 'Configure the text that will appear at the top of your website.';
}

// Function 5B: Settings page content (under Settings menu)
function hqdev_options_page() {
    ?>
    <div class="wrap">
        <h1>HQDev Top Text Settings</h1>
        <form action='options.php' method='post'>
            <?php
            settings_fields('hqdev_top_text');      // Security fields
            do_settings_sections('hqdev_top_text'); // Display all sections
            submit_button();                        // Save button
            ?>
        </form>
    </div>
    <?php
}

// Function 5C: Main dashboard page content (in sidebar menu)
function hqdev_simple_admin_page() {
    // STEP 1: Handle form submission (save custom message)
    if (isset($_POST['hqdev_submit']) && wp_verify_nonce($_POST['hqdev_nonce'], 'hqdev_action')) {
        $message = sanitize_text_field($_POST['hqdev_message']);
        update_option('hqdev_admin_message', $message);
        echo '<div class="notice notice-success"><p>Message saved successfully!</p></div>';
    }
    
    // STEP 2: Get data to display
    $saved_message = get_option('hqdev_admin_message', 'Welcome to HQDev Dashboard!');
    $options = get_option('hqdev_top_text_settings', array());
    $top_text_enabled = isset($options['enable_text']) && $options['enable_text'] ? 'Yes' : 'No';
    $top_text_content = isset($options['top_text']) ? $options['top_text'] : 'Not set';
    ?>
    <div class="wrap">
        <h1>HQDev Dashboard12</h1>
        
        <div style="display: flex; gap: 20px; margin-top: 20px;">
            <!-- Main Content -->
            <div style="flex: 2;">
                <div class="card" style="padding: 20px;">
                    <h2>Plugin Information</h2>
                    <p><?PHP echo esc_html($saved_message)  ?>. This is a simple admin page example.</p>
                    
                    <h3>Current Settings</h3>
                    <table class="widefat">
                        <tr>
                            <td><strong>Top Text Enabled:</strong></td>
                            <td><?php echo esc_html($top_text_enabled); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Top Text Content:</strong></td>
                            <td><?php echo esc_html(wp_trim_words($top_text_content, 10)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Available Shortcodes:</strong></td>
                            <td>[hqdev_hello], [hqdev_button]</td>
                        </tr>
                    </table>
                </div>
                
                <div class="card" style="padding: 20px; margin-top: 20px;">
                    <h2>Custom Message</h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('hqdev_action', 'hqdev_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">Your Message</th>
                                <td>
                                    <textarea name="hqdev_message" rows="4" cols="50" class="large-text"><?php echo esc_textarea($saved_message); ?></textarea>
                                    <p class="description">Enter a custom message to save.</p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save Message', 'primary', 'hqdev_submit'); ?>
                    </form>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div style="flex: 1;">
                <div class="card" style="padding: 20px;">
                    <h3>Quick Actions</h3>
                    <p><a href="<?php echo admin_url('options-general.php?page=hqdev-top-text'); ?>" class="button button-secondary">Configure Top Text</a></p>
                    <p><a href="<?php echo admin_url('plugins.php'); ?>" class="button button-secondary">Manage Plugins</a></p>
                </div>
                
                <div class="card" style="padding: 20px; margin-top: 20px;">
                    <h3>Shortcode Examples</h3>
                    <p><strong>Hello Shortcode:</strong></p>
                    <code>[hqdev_hello name="Admin"]</code>
                    
                    <p style="margin-top: 15px;"><strong>Button Shortcode:</strong></p>
                    <code>[hqdev_button url="#"]Click Me[/hqdev_button]</code>
                </div>
                
                <div class="card" style="padding: 20px; margin-top: 20px;">
                    <h3>Saved Message</h3>
                    <div style="background: #f9f9f9; padding: 10px; border-left: 4px solid #0073aa;">
                        <?php echo esc_html($saved_message); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// ========================================
// PART 6: FRONT-END DISPLAY FUNCTIONS
// ========================================
// These functions display content on the website front-end

// Function 6A: Add CSS styles to website head
function hqdev_add_top_text_styles() {
    $options = get_option('hqdev_top_text_settings');
    $enabled = isset($options['enable_text']) ? $options['enable_text'] : 0;
    
    // Exit if feature is disabled
    if (!$enabled) {
        return;
    }
    
    // Get color settings
    $text_color = isset($options['text_color']) ? $options['text_color'] : '#ffffff';
    $bg_color = isset($options['background_color']) ? $options['background_color'] : '#333333';
    ?>
    <style type="text/css">
        .hqdev-top-text {
            background-color: <?php echo esc_attr($bg_color); ?>;
            color: <?php echo esc_attr($text_color); ?>;
            padding: 10px 20px;
            text-align: center;
            position: relative;
            z-index: 9999;
            width: 100%;
            box-sizing: border-box;
            margin: 0;
            font-size: 14px;
            line-height: 1.4;
        }
        .hqdev-top-text p {
            margin: 0;
            padding: 0;
        }
        body.hqdev-top-text-enabled {
            padding-top: 0 !important;
        }
    </style>
    <?php
}
// Function 6B: Display the top text banner
function hqdev_display_top_text() {
    $options = get_option('hqdev_top_text_settings');
    $enabled = isset($options['enable_text']) ? $options['enable_text'] : 0;
    $text = isset($options['top_text']) ? $options['top_text'] : '';
    
    // Only display if enabled and text exists
    if ($enabled && !empty($text)) {
        echo '<div class="hqdev-top-text">' . wp_kses_post($text) . '</div>';
        echo '<script>document.body.classList.add("hqdev-top-text-enabled");</script>';
    }
}
// Function 6C: Fallback method for older themes
function hqdev_display_top_text_fallback() {
    // Only run if wp_body_open hook didn't fire
    if (!did_action('wp_body_open')) {
        $options = get_option('hqdev_top_text_settings');
        $enabled = isset($options['enable_text']) ? $options['enable_text'] : 0;
        $text = isset($options['top_text']) ? $options['top_text'] : '';
        
        if ($enabled && !empty($text)) {
            ?>
            <script>
            // Wait for page to load, then add the banner
            document.addEventListener('DOMContentLoaded', function() {
                var topText = document.createElement('div');
                topText.className = 'hqdev-top-text';
                topText.innerHTML = <?php echo json_encode(wp_kses_post($text)); ?>;
                document.body.insertBefore(topText, document.body.firstChild);
                document.body.classList.add('hqdev-top-text-enabled');
            });
            </script>
            <?php
        }
    }
}
// ========================================
// PART 7: SHORTCODE FUNCTIONS
// ========================================
// These functions handle shortcodes that users can add to posts/pages

// Function 7A: Hello shortcode - [hqdev_hello name="John" color="#red" size="20px"]
function hqdev_hello_shortcode($atts) {
    // Set default values for attributes
    $atts = shortcode_atts(array(
        'name' => 'World',           // Default name
        'color' => '#333333',        // Default color
        'size' => '16px'            // Default font size
    ), $atts);
    
    // Clean and validate the input
    $name = sanitize_text_field($atts['name']);
    $color = sanitize_hex_color($atts['color']) ? $atts['color'] : '#333333';
    $size = sanitize_text_field($atts['size']);
    
    // Return HTML output
    return '<div class="hqdev-hello" style="color: ' . esc_attr($color) . '; font-size: ' . esc_attr($size) . '; padding: 10px; border: 2px solid ' . esc_attr($color) . '; border-radius: 5px; display: inline-block; margin: 10px 0;">
                <strong>Hello, ' . esc_html($name) . '!</strong>
            </div>';
}
// Function 7B: Button shortcode - [hqdev_button url="https://example.com" size="large"]Click Me[/hqdev_button]
function hqdev_button_shortcode($atts, $content = null) {
    // Set default values for attributes
    $atts = shortcode_atts(array(
        'url' => '#',                // Default link
        'color' => '#ffffff',        // Text color
        'bg_color' => '#007cba',     // Background color
        'size' => 'medium',          // Button size
        'target' => '_self'          // Link target
    ), $atts);
    
    // Clean and validate inputs
    $url = esc_url($atts['url']);
    $color = sanitize_hex_color($atts['color']) ? $atts['color'] : '#ffffff';
    $bg_color = sanitize_hex_color($atts['bg_color']) ? $atts['bg_color'] : '#007cba';
    $target = in_array($atts['target'], array('_self', '_blank')) ? $atts['target'] : '_self';
    
    // Set button size based on attribute
    $padding = '10px 20px';
    $font_size = '16px';
    
    switch($atts['size']) {
        case 'small':
            $padding = '5px 10px';
            $font_size = '14px';
            break;
        case 'large':
            $padding = '15px 30px';
            $font_size = '18px';
            break;
        default: // medium
            $padding = '10px 20px';
            $font_size = '16px';
    }
    
    // Get button text (content between shortcode tags)
    $button_text = $content ? do_shortcode($content) : 'Click Here';
    
    // Return button HTML
    return '<a href="' . $url . '" target="' . esc_attr($target) . '" class="hqdev-button" style="
                background-color: ' . esc_attr($bg_color) . ';
                color: ' . esc_attr($color) . ';
                padding: ' . esc_attr($padding) . ';
                font-size: ' . esc_attr($font_size) . ';
                text-decoration: none;
                border-radius: 5px;
                display: inline-block;
                margin: 5px;
                border: none;
                cursor: pointer;
                transition: opacity 0.3s ease;
            " onmouseover="this.style.opacity=\'0.8\'" onmouseout="this.style.opacity=\'1\'">
                ' . esc_html($button_text) . '
            </a>';
}

// ========================================
// END OF PLUGIN
// ========================================
?>