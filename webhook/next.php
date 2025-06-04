<?php

function my_post_action_callback($post_id, $post, $update) {
    // Prevent triggering on autosaves and revisions
    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    // Ensure the post has a valid permalink
    $post_url = get_permalink($post_id);
    if (!$post_url) return;
    $frontend_url = rtrim(carbon_get_theme_option('main_website_url'), '/');
    $webhook_url = $frontend_url . "/api/revalidate?secret=12345678";
    

    // Send a POST request to the Next.js revalidation API
    $response = wp_remote_post($webhook_url, array(
        'method'      => 'POST', // Use POST instead of GET
        'timeout'     => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => false, // Non-blocking request for better performance
        'headers'     => array(
            'Content-Type' => 'application/json; charset=utf-8',
        ),
    ));

    // Log errors if any
    if (is_wp_error($response)) {
        error_log("Revalidation failed: " . $response->get_error_message());
    } else {
        error_log("Revalidation successful for: " . $post_url);
    }
}

// Trigger for all post types (including custom post types)
add_action('save_post', 'my_post_action_callback', 10, 3);
