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
    wp_enqueue_style(
        'my-custom-style',
        plugin_dir_url(__FILE__) . 'css/custom-style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'css/custom-style.css') // version = last modified time
    );
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_styles');

/*
 Enqueue Custom JS
*/
function my_custom_plugin_enqueue_scripts() {
    wp_enqueue_script(
        'my-custom-script',
        plugin_dir_url(__FILE__) . 'js/custom-script.js',
        array('jquery'),
        filemtime(plugin_dir_path(__FILE__) . 'js/custom-script.js'), // version = last modified time
        true
    );
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_scripts');

/*
 Gravity Forms After Submission Hook
*/
add_action('gform_after_submission', 'my_custom_plugin_gravity_webhook', 10, 2);

function my_custom_plugin_gravity_webhook($entry, $form) {
    $target_form_id = 1; // Replace with your form ID
    if ($form['id'] != $target_form_id) {
        return;
    }

    // Map all fields with their actual IDs as in wordpress )
    $data = array(
        'business_name'   => rgar($entry, '1'),
        'address'         => rgar($entry, '4'),
        'preferred_contact' => rgar($entry, '11'),
        'email'           => rgar($entry, '2'),
        'phone'           => rgar($entry, '5'),
        'best_time'       => rgar($entry, '12'),
        'subject'         => rgar($entry, '3'),
    );

    // Convert to JSON
    $body = wp_json_encode($data);

    // Send to Webhook.site (replace with your actual webhook URL)
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
