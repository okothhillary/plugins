<?php
/**
 * Plugin Name:       Simplify Task Manager
 * Plugin URI:        https://hillary-okoth.sblik.com
 * Description:       A custom plugin to manage internship tasks and customize the sandbox admin.
 * Version:           1.0.0
 * Author:            Open Source
 * Author URI:        https://hillary-okoth.sblik.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simplify-task-manager
 * Domain Path:       /languages
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * A global constant for the plugin's main file.
 * This makes it easier to reference from other files
 */
const STM_PLUGIN_FILE = __FILE__;

/**
 * Loading the plugin's features.
 * Adding a "require_once" line for every new feature I build
 */
// Load Admin Login Customization.
require_once plugin_dir_path(STM_PLUGIN_FILE) . 'includes/admin-login-mods.php';
// Load the Gravity Forms Webhook integration.
require_once plugin_dir_path(STM_PLUGIN_FILE) . 'includes/gravity-forms-webhook.php';
// Load the Task Manager menu and Kanban board page.
require_once plugin_dir_path(STM_PLUGIN_FILE) . 'includes/admin-menu.php';
// Load the custom "Task" post type and "Status" taxonomy.
require_once plugin_dir_path(STM_PLUGIN_FILE) . 'includes/post-type-task.php';