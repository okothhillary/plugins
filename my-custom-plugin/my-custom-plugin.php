<?php
/*
Plugin Name: My Custom Plugin
Description: A plugin to add custom CSS, JS, and Gravity Forms webhook functionality.
Version: 1.3
Author: Hillary Okoth
*/
if (!defined('ABSPATH')) {
    exit;
}

/*
 Enqueue Custom CSS with filemtime()
*/
function my_custom_plugin_enqueue_styles() {
    $css_path = plugin_dir_path(__FILE__) . 'css/custom-style.css';
    $css_url  = plugin_dir_url(__FILE__) . 'css/custom-style.css';
    $version  = file_exists($css_path) ? filemtime($css_path) : '1.0';

    wp_enqueue_style('my-custom-style', $css_url, array(), $version);
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_styles');

/*
 Enqueue Custom JS with filemtime()
*/
function my_custom_plugin_enqueue_scripts() {
    $js_path = plugin_dir_path(__FILE__) . 'js/custom-script.js';
    $js_url  = plugin_dir_url(__FILE__) . 'js/custom-script.js';
    $version = file_exists($js_path) ? filemtime($js_path) : '1.0';

    wp_enqueue_script('my-custom-script', $js_url, array('jquery'), $version, true);
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

/*
 Debug: Show file timestamps in footer (admin only)
*/
function my_custom_plugin_debug_info() {
    if (!current_user_can('administrator') || !is_user_logged_in()) return;

    $css_path = plugin_dir_path(__FILE__) . 'css/custom-style.css';
    $js_path  = plugin_dir_path(__FILE__) . 'js/custom-script.js';

    $css_time = file_exists($css_path) ? date('Y-m-d H:i:s', filemtime($css_path)) : 'Not found';
    $js_time  = file_exists($js_path) ? date('Y-m-d H:i:s', filemtime($js_path)) : 'Not found';

    echo '<div style="position:fixed;bottom:10px;right:10px;background:#fff;padding:10px;border:2px solid #000;font-family:monospace;font-size:12px;z-index:99999;box-shadow:0 0 10px rgba(0,0,0,0.3);">
            <strong>Plugin Debug</strong><br>
            CSS: ' . esc_html($css_time) . '<br>
            JS:  ' . esc_html($js_time) . '
          </div>';
}
//add_action('wp_footer', 'my_custom_plugin_debug_info');

/*
 Gravity Forms After Submission Hook
*/
add_action('gform_after_submission', 'my_custom_plugin_gravity_webhook', 10, 2);
function my_custom_plugin_gravity_webhook($entry, $form) {
    $target_form_id = 1;
    if ($form['id'] != $target_form_id) {
        return;
    }

    $data = array(
        'business_name'     => rgar($entry, '1'),
        'address'           => rgar($entry, '4'),
        'preferred_contact' => rgar($entry, '11'),
        'email'             => rgar($entry, '2'),
        'phone'             => rgar($entry, '5'),
        'best_time'         => rgar($entry, '12'),
        'subject'           => rgar($entry, '3'),
    );

    $body = wp_json_encode($data);

    $response = wp_remote_post('https://webhook.site/e2dbc0c0-2ac0-460b-84d9-a3994ff14b0e', array(
        'method'      => 'POST',
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => $body,
        'timeout'     => 15,
        'sslverify'   => true,
    ));

    if (is_wp_error($response)) {
        error_log('Gravity Webhook Error: ' . $response->get_error_message());
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        error_log("Gravity Webhook Sent (Code: $code): " . substr($body, 0, 200));
    }
}