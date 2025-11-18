<?php
/**
 * This is the "view" file for the Kanban Board page
 * It contains all HTML and WP_Query loop
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if(!defined('WPINC')) {
    die;
}

// Get all the "Status" terms from the custom taxonomy.
$status_terms = get_terms(
    array(
        'taxonomy' => 'stm_task_status',
        'orderby' => 'term_order',
        'order' => 'ASC',
        'hide_empty' => false,
    )
);


?>



<div class="wrap">

    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div id="kanban-board-wrapper" class="stm-kanban-board">

        <?php
        // Check if there's any status terms (columns) to display.
        if (!empty($status_terms) && !is_wp_error($status_terms)) :

            // Loop each through status to create a column.
        foreach ($status_terms as $status_term) :
            $term_id = $status_term->term_id;
            $term_name = $status_term->name;
            $term_slug = $status_term->slug;
            ?>

            <div
                class="kanban-column <?php echo esc_attr__('kanban-column-' . $status_term->slug); ?>)
                data-term-id="<?php echo esc_attr__($term_id) ?>">

                <div class="kanban-column-header">
                    <h2><?php echo esc_html($term_name)?> SimplifyBiz</h2>
                </div>

                <div class="kanban-column-body">

                    <?php
                    // WP_Query - Get tasks for this specific column/term.

                    // Define the query arguments.
                    $task_query_args = array(
                        'post_type'      => 'stm_task',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'stm_task_status',
                                'field' => 'term_id',
                                'terms' => $term_id,
                            ),
                        ),
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    );

                    // Create query object.
                    $task_query = new WP_Query($task_query_args);

                    // Check if query found any tasks.
                    if ($task_query->have_posts()) :

                        while ($task_query->have_posts()) :
                            $task_query->the_post();

                        // Get task's data.
                        $task_id = get_the_ID();
                        $task_title = get_the_title();
                        $task_content = get_the_content();

                        ?>
                            <div class="kanban-card" data-task-id="<?php echo esc_attr($task_id); ?>">
                                <div class="kanban-card-header">
                                    <h4><?php echo esc_html($task_title); ?></h4>
                                </div>
                                <div class="kanban-card-body">
                                    <p><?php echo esc_html(wp_trim_words($task_content, 20, '...')); ?></p>
                                </div>
                            </div>

                        <?php
                        endwhile;

                        else:
                            // Show this message if a column is empty.
                            echo '<p class="kanban-no-tasks">No tasks in this column.</p>';
                        endif;

                    // Reset post data a custom loop (always).
                    wp_reset_postdata();

                    ?>
                </div>
            </div>
        <?php
        endforeach;

        else:
            // This message shows if no "Status" terms (To Do, etc.) were found.
            echo '<p>No task statuses found. Please add some in the "Task Manager" > "Task Statuses" menu.</p>';
        endif; // End if !empty($status_terms).
        ?>
        
    </div>


