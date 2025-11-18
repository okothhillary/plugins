<?php
/**
 * This file contains the logic for creating the admin menu page for
 * the Task Manager.
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 * Creates the top-level "Task Manager" menu page.
 * This function is hooked into the 'admin_menu' action.
 */
function stm_add_admin_menu_page():void {

    // The URL to the custom icon.
    $icon_url = plugin_dir_url(STM_PLUGIN_FILE) . 'assets/images/stm-icon-1.svg';

    // The Parent Menu.
    add_menu_page(
        'Task Manager',
        'Task Manager',
        'manage_options',
        'stm-task-manager',
        'stm_render_kanban_page',
        $icon_url,
        20
    );

    // Submenu 1: Kanban Board - the main page.
    // Using the same plug as the parent make this page
    // the default page when user click on the parent "Task Manager".
    add_submenu_page(
        'stm-task-manager',
        'Kanban Board',
        'Kanban Board',
        'manage_options',
        'stm_task_manager',
        'stm_render_kanban_page'
    );

    // Submenu 2: Add a New Task.
    add_submenu_page(
        'stm-task-manager',
        'Add New Task',
        'Add New Task',
        'manage_options',
        'post-new.php?post_type=stm_task',

    );

    // Submenu 4: Add a Task Status menu
    add_submenu_page(
            'stm-task-manager',
            'Task Statuses',
            'Task Statuses',
            'manage_options',
            'edit.php?taxonomy=stm_task_status&post_type=stm_task',
    );


    // Submenu 3: Settings page.
    add_submenu_page(
        'stm-task-manager',
        'Task Manager Settings',
        'Settings',
        'manage_options',
        'stm_task_manager-settings',
        'stm_render_settings_page'
    );
}
// This hook calls the custom menu page.
add_action('admin_menu', 'stm_add_admin_menu_page');



/**
 * The "Callback Function" that builds the HTML for the Task Manager Page.
 *
 * This function acts as a simple loader for the "view" file.
 */
function stm_render_kanban_page():void {
    require_once plugin_dir_path(STM_PLUGIN_FILE) . 'includes/views/kanban-board-page.php';
}


/**
 * Callback Function that builds the HTML for the Settings page.
 */
function stm_render_settings_page() {
    ?>
        <div class="wrap">
            <h1>Task Manager Settings</h1>
            <p>This page will hold the settings, using the Options API</p>
        </div>
    <?php
}


/**
 * Redirects the user back to the Kanban board after they save a new task.
 */
function stm_redirect_after_saving_task($location, $post_id):string {

    // Get saved post object.
    $post = get_post($post_id);

    // Check if post type is in Task CPT.
    if ('stm_task' === $post->post_type) {
        // If it is, ignore the default WP location and send
        // user back to main Kanban board page.
        $location = admin_url('admin.php?page=stm_task_manager');
    }

    return $location;
}
// This filter hook intercept the "save' redirect.
add_filter('redirect_post_location', 'stm_redirect_after_saving_task', 10, 2);


/**
 * Enqueuing custom CSS stylesheet for the admin area.
 *
 * This function is hooked into the 'admin_enqueue_scripts' action.
 */
function stm_load_admin_styles():void {
    // Get URL to the admin CSS file.
    $css_file_url = plugin_dir_url(STM_PLUGIN_FILE) . 'assets/css/admin-kanban.css';

    // Queue the stylesheet.
    wp_enqueue_style('simplify-kanban-styles', $css_file_url);
}
add_action('admin_enqueue_scripts', 'stm_load_admin_styles');


/**
 * This function runs on the 'admin_init' hook, a safe place
 * to check the current screen.
 */
function stm_conditionally_add_gettext_filter():void {

    // Check if user is on an admin screen.
    if ( is_admin() ) {

        // Getting the current screen.
        $screen = get_current_screen();

        // Check if the screen exists AND if it's for the CPT.
        if ( $screen && 'stm_task' === $screen->post_type ) {

            // All check pass! - add the text-changing filter.
            add_filter( 'gettext', 'stm_change_publish_button_text', 10, 2 );
        }
    }
}
// The "check" function run on 'admin_init' hook.
add_action( 'admin_init', 'stm_conditionally_add_gettext_filter' );


/**
 * This function will only be run on the correct page.
 *
 * @param string $translated_text The translated text.
 * @param string $untranslated_text The original, untranslated text.
 */
function stm_change_publish_button_text( string $translated_text, string $untranslated_text):string {

    // Check for "Publish" and also the "Publishing..." text.
    if ( 'Publish' === $untranslated_text || 'Publishing...' === $untranslated_text ) {
        // This will change "Publish" to "Save Task"
        // and "Publishing..." to "Saving...".
        $translated_text = str_replace( 'Publish', 'Save Task', $untranslated_text );
    }

    return $translated_text;
}