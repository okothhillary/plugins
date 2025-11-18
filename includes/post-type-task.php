<?php
/**
 * This file registers the Custom Post Type (CPT) for "Tasks"
 * and the Custom Taxonomy for "Status".
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 * The main function to register the "Task" CPT
 * and "Status" taxonomy.
 */
function stm_register_task_cpt_and_taxonomy():void {

    // Register the "Status" taxonomy. It is created first
    // to make it available for the CPT.
    $taxonomy_labels = array(
        'name' => 'Statuses',
        'singular_name' => 'Status',
        'menu_name' => 'Status',
    );

    $taxonomy_args = array(
        'labels' => $taxonomy_labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'query_var' => false,
        'rewrite' => false,
    );
    // Registers the "Status" taxonomy and links it to the "Task" CPT.
    register_taxonomy('stm_task_status', array('stm_task'), $taxonomy_args);

    // Register the "Task" post type.
    $cpt_labels = array(
        'name' => 'Tasks',
        'singular_name' => 'Task',
        'add_new_item' => 'Add New Task',
        'edit_item' => 'Edit Task',
        'new_item' => 'New Task',
        'view_item' => 'View Task',
        'search_items' => 'Search Tasks',
        'not_found' => 'No Tasks found',
    );

    $cpt_args = array(
        'labels' => $cpt_labels,
        'public' => false,
        'publicly_queryable' => false,
        'has_archive' => false,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => array('title', 'editor'),
        'hierarchical' => false,
        'menu_position' => 21,
        'menu_icon' => 'dashicons-pressthis',
        'show_in_rest' => true,
        'exclude_from_search' => true,
        'taxonomies' => array('stm_task_status'),
        'rewrite' => false,
        'query_var' => false,
    );
    // Officially registers the "Task" post type.
    register_post_type('stm_task', $cpt_args);
}
// This hook runs the function at the right time - when WP
// is initializing.
add_action('init', 'stm_register_task_cpt_and_taxonomy');