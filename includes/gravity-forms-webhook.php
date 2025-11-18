<?php
/**
 * This file contains the logic for sending Gravity Forms data to an
 * external site. This fulfils the "gravity_after_submission hook" task.
 *
 * @package Simplify Task Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


/**
 * The WP "Hook" that listens for any Gravity Form submission.
 *
 * This function will run *after* a form is successfully submitted,
 * allowing us to hook in and perform our custom action.
 *
 * @param array $entry The form entry object, containing all submitted data.
 * @param array $form The form object, containing details about the form structure.
 */
function stm_send_to_webhook(array $entry, array $form):void {

    // External URL - from Webhook.site.
    $webhook_url = 'https://webhook.site/8bda6ada-87fb-44d9-a8ca-f87e591eeecc';

    // Grab all form fields and add them to this array.
    $data_to_send = array();

    // Loop through all the fields in the form.
    foreach ($form['fields'] as $field) {
        $label = $field->label;

        // `rgar()` is a safe Gravity Forms function to get entry data.
        $value = rgar($entry, (string) $field->id);

        // Add it to the array.
        $data_to_send[$label] = $value;
    }

    // Adding useful info to the package.
    $data_to_send['form_title'] = $form['title'];
    $data_to_send['entry_id'] = $entry['id'];
    $data_to_send['user_ip'] = $entry['ip'];

    // Sending the data - using a WP function.
    $args = array(
        'body' => json_encode($data_to_send),
        'headers' => array(
            'Content-Type' => 'application/json; charset=utf-8',
        ),
    );

    // This sends the data.
    $response = wp_remote_post($webhook_url, $args);

    // If an error occur.
    if (is_wp_error($response)) {
        error_log('Webhook failed: '. $response->get_error_message());
    } else {

        // The request succeeded, the $response is an Array.
        // Check the HTTP response code from the server.
        $response_code = wp_remote_retrieve_response_code( $response );

        // Check if the code is NOT a "success" code (2xx range).
        if ( $response_code < 200 || $response_code >= 300 ) {
            error_log( 'Webhook HTTP Error: Server responded with code ' . $response_code );
        }
    }
}

add_action('gform_after_submission', 'stm_send_to_webhook', 10, 2);