<?php

if ( ! defined('ABSPATH') ) exit;

add_action("wp_ajax_ipg_save_user_request", "ipg_save_user_request");
add_action("wp_ajax_nopriv_ipg_save_user_request", "ipg_save_user_request");

function ipg_save_user_request() {

    if ( ! is_user_logged_in() ) {
        wp_send_json_error([ "message" => "Please log in first." ]);
    }

    global $wpdb;
    $table = $wpdb->prefix . "ipg_requests";

    $user = wp_get_current_user();

    $category  = sanitize_text_field($_POST['selected_category']);
    $styles    = isset($_POST['styles']) ? json_encode($_POST['styles']) : json_encode([]);
    $notes     = sanitize_textarea_field($_POST['additional_notes']);
    $no_posts  = intval($_POST['no_of_posts']);

    // 1. SAVE INTO DATABASE
    $wpdb->insert($table, [
        'user_id' => $user->ID,
        'username' => $user->user_login,
        'user_email' => $user->user_email,
        'selected_category' => $category,
        'selected_styles' => $styles,
        'additional_notes' => $notes,
        'admin_prompt' => '',
        'created_at' => current_time('mysql'),
        'status' => 'pending',
        'is_free_user' => 'yes',
        'free_images_generated' => 0,
        'first_image_url' => null,
        'second_image_url' => null,
        'no_of_posts' => $no_posts
    ]);

    // REMOVE THIS → it causes the error
    // ipg_append_to_excel([...]);

    wp_send_json_success([
        "message" => "Your request was submitted successfully."
    ]);
}
