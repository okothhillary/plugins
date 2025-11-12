<?php
/*
Plugin Name: My Custom Plugin
Description: A plugin to add custom CSS and JS to your WordPress site.
Version: 1.0
Author: Hillary Okoth
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue custom CSS with versioning
function my_custom_plugin_enqueue_styles() {
    wp_enqueue_style(
        'my-custom-style',
        plugin_dir_url(__FILE__) . 'css/custom-style.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'css/custom-style.css') // version = last modified time
    );
}
add_action('wp_enqueue_scripts', 'my_custom_plugin_enqueue_styles');

// Enqueue custom JS with versioning
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
