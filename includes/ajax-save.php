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



add_action("wp_ajax_ipg_delete_entry", "ipg_delete_entry");

function ipg_delete_entry() {

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => "Permission denied"]);
    }

    global $wpdb;

    $table = $wpdb->prefix . "ipg_requests";
    $id    = intval($_POST['id']);

    if ($id <= 0) {
        wp_send_json_error(["message" => "Invalid ID"]);
    }

    $deleted = $wpdb->delete($table, ['id' => $id], ['%d']);

    if ($deleted) {
        wp_send_json_success(["message" => "Row deleted", "id" => $id]);
    } else {
        wp_send_json_error(["message" => "Could not delete entry"]);
    }
}

add_action("wp_ajax_ipg_generate_image", "ipg_generate_image");
function ipg_generate_image() {

    if (!current_user_can("manage_options")) {
        wp_send_json_error(["message" => "Permission denied"]);
    }

    global $wpdb;
    $table = $wpdb->prefix . "ipg_requests";

    $id = intval($_POST['id']);
    $prompt = sanitize_textarea_field($_POST['prompt']);
    $api_key = get_option('ipg_chatgpt_api_key');

    if (empty($api_key)) {
        wp_send_json_error(["message" => "API key missing. Add API key in Settings."]);
    }

    // DEBUG START
    error_log("🔥 Sending request to OpenAI...");
    error_log("📝 Prompt: " . $prompt);
    error_log("🔑 API KEY EXISTS: " . (!empty($api_key) ? "YES" : "NO"));
    // DEBUG END

    $response = wp_remote_post("https://api.openai.com/v1/images/generations", [
        "headers" => [
            "Content-Type"  => "application/json",
            "Authorization" => "Bearer " . $api_key,
        ],
        "body" => json_encode([
            "model"  => "dall-e-3",
            "prompt" => $prompt,
            "size"   => "1024x1024",
        ]),
        "timeout" => 60, // important
    ]);

    if (is_wp_error($response)) {
        error_log("❌ WP REQUEST ERROR: " . $response->get_error_message());
        wp_send_json_error(["message" => "API request failed: " . $response->get_error_message()]);
    }

    $raw = wp_remote_retrieve_body($response);
    error_log("📩 RAW RESPONSE: " . $raw);

    // Check HTTP status
    $http_code = wp_remote_retrieve_response_code($response);
    error_log("🌐 HTTP CODE: " . $http_code);

    if ($http_code !== 200) {
        wp_send_json_error(["message" => "OpenAI Error: " . $raw]);
    }

    $body = json_decode($raw, true);

    if (!$body) {
        wp_send_json_error(["message" => "Invalid JSON from OpenAI"]);
    }

    $image_base64 = $body["data"][0]["b64_json"] ?? "";

    if (empty($image_base64)) {
        wp_send_json_error(["message" => "No image returned"]);
    }

    $image_url = "data:image/png;base64," . $image_base64;

    $wpdb->update(
        $table,
        [
            "admin_prompt"    => $prompt,
            "first_image_url" => $image_url
        ],
        ["id" => $id],
        ["%s", "%s"],
        ["%d"]
    );

    wp_send_json_success([
        "image_url" => $image_url,
        "message"   => "Image generated successfully"
    ]);
}
