<?php
/**
 * This file handles all customizations for the WordPress login page.
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 * Enqueuing the custom stylesheet for the login page.
 * This function loads the CSS for the login page
 */
function stm_login_styles():void {
    // Getting the URL to the CSS file. Using the global constant.
    $css_file_url = plugin_dir_url(STM_PLUGIN_FILE) . 'assets/css/admin-login.css';

    // Telling WP to "enqueue" the stylesheet.
    wp_enqueue_style('simplify-login-styles', $css_file_url);
}
// This hook runs the function at the right time.
add_action('login_enqueue_scripts', 'stm_login_styles');


/**
 * Change the logo's "href" attribute
 */
function stm_login_logo_url():string {
    // The company's url.
    return home_url('https://simplifybiz.com/');
}
add_filter('login_headerurl', 'stm_login_logo_url');


/**
 * Change the logo's "title" attribute (the hover text)
 */
function stm_login_logo_title():string {
    return 'Sign In - Access your internship sandbox';
}
add_filter('login_headertext', 'stm_login_logo_title');


/**
 * Adding a custom message (title and subtitle) above the login form
 */
function stm_login_custom_message():string {
    // Using HTML here to create the custom message.
    return '
        <div class="stm-login-header">
            <h2 class="stm-login-title">Interns Sign In</h2>
            <p class="stm-login-subtitle">Access your Internship WordPress Sandbox.</p>
	    </div>
	';
}
// This filter hook adds the custom message.
add_filter('login_message', 'stm_login_custom_message');