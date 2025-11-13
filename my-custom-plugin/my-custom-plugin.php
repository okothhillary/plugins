<?php
/*
Plugin Name: My Custom Plugin
Description: A plugin to add custom CSS, JS, and Gravity Forms webhook functionality.
Version: 1.2
Author: Hillary Okoth
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/*
 Enqueue Custom CSS
*/
function my_custom_plugin_enqueue_styles() {
    $css_file = plugin_dir_path(__FILE__) . 'css/custom-style.css';

    // Use time() alone to force browser to always load latest version
    wp_enqueue_style(
        'my-custom-style',
        plugin_dir_url(__FILE__) . 'css/custom-style.css',
        array(),
        time() // development cache-busting
    );
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_styles');

/*
 Enqueue Custom JS
*/
function my_custom_plugin_enqueue_scripts() {
    $js_file = plugin_dir_path(__FILE__) . 'js/custom-script.js';

    // Force browser to load latest JS every page load
    wp_enqueue_script(
        'my-custom-script',
        plugin_dir_url(__FILE__) . 'js/custom-script.js',
        array('jquery'),
        time(), // prevent browser from loading cached version
        true
    );
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

/*
 Debug log for CSS/JS versioning
*/
add_action('wp_enqueue_scripts', function() {
    error_log('Custom plugin enqueued CSS version: ' . time());
    error_log('Custom plugin enqueued JS version: ' . time());
});

/*
 Gravity Forms After Submission Hook
*/
add_action('gform_after_submission', 'my_custom_plugin_gravity_webhook', 10, 2);

function my_custom_plugin_gravity_webhook($entry, $form) {
    $target_form_id = 1; // Replace with your form ID
    if ($form['id'] != $target_form_id) {
        return;
    }

    // Map all fields with their actual IDs as in WordPress
    $data = array(
        'business_name'     => rgar($entry, '1'),
        'address'           => rgar($entry, '4'),
        'preferred_contact' => rgar($entry, '11'),
        'email'             => rgar($entry, '2'),
        'phone'             => rgar($entry, '5'),
        'best_time'         => rgar($entry, '12'),
        'subject'           => rgar($entry, '3'),
    );

    // Convert to JSON
    $body = wp_json_encode($data);

    // Send to Webhook.site
    $response = wp_remote_post('https://webhook.site/e2dbc0c0-2ac0-460b-84d9-a3994ff14b0e', array(
        'method'    => 'POST',
        'headers'   => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'      => $body,
        'timeout'   => 15,
    ));

    // Logging for debugging
    if (is_wp_error($response)) {
        error_log('Gravity Form webhook error: ' . $response->get_error_message());
    } else {
        error_log('Gravity Form webhook response: ' . print_r($response, true));
    }
}
