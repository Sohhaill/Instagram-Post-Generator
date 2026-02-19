<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function ipg_admin_menu() {

    // MAIN MENU
    add_menu_page(
        "Instagram Post Generator",
        "Instagram Post Generator",
        "manage_options",
        "ipg-dashboard",
        "ipg_admin_main_page",
        "dashicons-format-image",
        26
    );

    // SUBMENU 1
    add_submenu_page(
        "ipg-dashboard",
        "Settings",
        "Settings",
        "manage_options",
        "ipg-settings",
        "ipg_admin_settings_page"
    );

    // SUBMENU 2
    add_submenu_page(
        "ipg-dashboard",
        "Generated Images",
        "Generated Images",
        "manage_options",
        "ipg-generated-images",
        "ipg_admin_generated_images_page"
    );
}
add_action("admin_menu", "ipg_admin_menu");



/**
 * MAIN MENU PAGE → SHOW DATABASE TABLE
 */
function ipg_admin_main_page() {
    global $wpdb;
    $table = $wpdb->prefix . "ipg_requests";
    $rows  = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    // GET API KEY STATUS
    $saved_api_key = get_option('ipg_chatgpt_api_key', '');

    if (!empty($saved_api_key)) {
        $status_msg = '<span style="color: #0a8a0a; font-size:14px; font-weight:bold;">✓ Your API key is registered.</span>';
    } else {
        $status_msg = '<span style="color: #cc0000; font-size:14px; font-weight:bold;">✕ No API key found. Please register your API key from <a href="admin.php?page=ipg-settings">Settings</a>.</span>';
    }
    ?>

    <div class="wrap">
  <p><?php echo $status_msg; ?></p>
        <h1>Instagram Post Requests</h1>

        <!-- API KEY STATUS MESSAGE -->
      
        <br>

        <p>Below is the list of all submitted requests.</p>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th>Styles</th>
                    <th>No. of Posts</th>
                    <th>Admin Prompt</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ( empty($rows) ) : ?>
                    <tr><td colspan="11" style="text-align:center;">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ( $rows as $row ) : ?>
                        <tr id="ipg-row-<?php echo $row->id; ?>">
                            <td><?php echo esc_html($row->id); ?></td>
                            <td><?php echo esc_html($row->username); ?></td>
                            <td><?php echo esc_html($row->user_email); ?></td>
                            <td><?php echo esc_html($row->selected_category); ?></td>
                            <td><?php echo esc_html(str_replace(['[',']','"'], '', $row->selected_styles)); ?></td>
                            <td><?php echo esc_html($row->no_of_posts); ?></td>
                            <td><?php echo esc_html($row->admin_prompt); ?></td>
                            <td><?php echo esc_html($row->additional_notes); ?></td>
                            <td><?php echo esc_html($row->status); ?></td>
                            <td><?php echo esc_html($row->created_at); ?></td>

                            <td>
                                <button class="button button-primary ipg-edit-btn" data-id="<?php echo $row->id; ?>">Edit</button>
                                <button class="button ipg-prompt-btn" data-id="<?php echo $row->id; ?>">Edit Prompt</button>
                                <button class="button button-danger ipg-delete-btn" data-id="<?php echo $row->id; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div id="ipg-prompt-editor" style="display:none; margin-top:30px; padding:20px; border:1px solid #ddd; background:#fff;">

   <h2>Edit Prompt & Generate Image</h2>

<table class="form-table">
    <tr>
        <th>User</th>
        <td id="ipg-pe-user"></td>
    </tr>
    <tr>
        <th>Email</th>
        <td id="ipg-pe-email"></td>
    </tr>
    <tr>
        <th>Category</th>
        <td id="ipg-pe-category"></td>
    </tr>
    <tr>
        <th>Styles</th>
        <td id="ipg-pe-styles"></td>
    </tr>

    <!-- IMAGE 1 PROMPT -->
    <tr>
        <th>Image 1 Prompt</th>
        <td>
            <textarea id="ipg-pe-prompt1" style="width:100%; height:80px;" placeholder="Write prompt for image 1..."></textarea>
            <button class="button button-primary" id="ipg-generate-image1-btn" style="margin-top:8px;">
                Generate Image 1
            </button>
            <span id="ipg-spinner1" style="display:none;">
                <span class="spinner is-active" style="float:none;vertical-align:middle;"></span> Generating...
            </span>
        </td>
    </tr>

    <!-- IMAGE 1 RESULT -->
    <tr id="ipg-image1-result-row" style="display:none;">
        <th>Image 1</th>
        <td id="ipg-image1-result"></td>
    </tr>

    <!-- IMAGE 2 PROMPT — hidden until image 1 is done -->
    <tr id="ipg-prompt2-row" style="display:none;">
        <th>Image 2 Prompt</th>
        <td>
            <textarea id="ipg-pe-prompt2" style="width:100%; height:80px;" placeholder="Write prompt for image 2..."></textarea>
            <button class="button button-primary" id="ipg-generate-image2-btn" style="margin-top:8px;">
                Generate Image 2
            </button>
            <span id="ipg-spinner2" style="display:none;">
                <span class="spinner is-active" style="float:none;vertical-align:middle;"></span> Generating...
            </span>
        </td>
    </tr>

    <!-- IMAGE 2 RESULT -->
    <tr id="ipg-image2-result-row" style="display:none;">
        <th>Image 2</th>
        <td id="ipg-image2-result"></td>
    </tr>

</table>

</div>
    </div>

    <?php
}




/**
 * SUBMENU PAGES (EMPTY FOR NOW)
 */
function ipg_admin_settings_page() {

    // SAVE API KEY WHEN FORM SUBMITS
    if ( isset($_POST['ipg_save_settings']) && check_admin_referer('ipg_settings_nonce') ) {

        $api_key = sanitize_text_field($_POST['ipg_chatgpt_api_key']);

        update_option('ipg_chatgpt_api_key', $api_key);

        echo '<div class="updated notice"><p>Settings saved successfully.</p></div>';
    }

    // GET EXISTING API KEY
    $saved_api_key = get_option('ipg_chatgpt_api_key', '');
    ?>

    <div class="wrap">
        <h1>Instagram Post Generator – Settings</h1>
        <p>Enter your ChatGPT API Secret Key below. This key will be used for generating images and posts.</p>

        <form method="post">
            <?php wp_nonce_field('ipg_settings_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="ipg_chatgpt_api_key">ChatGPT API Secret Key</label></th>
                    <td>
                        <input 
                            type="text" 
                            id="ipg_chatgpt_api_key" 
                            name="ipg_chatgpt_api_key" 
                            value="<?php echo esc_attr($saved_api_key); ?>" 
                            class="regular-text" 
                            style="width: 400px;"
                        >
                        <p class="description">Enter your OpenAI/ChatGPT API secret key.</p>
                    </td>
                </tr>
            </table>

            <p>
                <input type="submit" name="ipg_save_settings" class="button button-primary" value="Save Settings">
            </p>
        </form>
    </div>

    <?php
}

// ── IMAGE 1 HANDLER ─────────────────────────────────────────────────────────
function ipg_generate_image1_handler() {

    check_ajax_referer('ipg_ajax_nonce', 'nonce');

    if ( empty($_POST['prompt']) ) {
        wp_send_json_error(['message' => 'Prompt is required.']);
    }

    $prompt  = sanitize_text_field($_POST['prompt']);
    $id      = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $api_key = get_option('ipg_chatgpt_api_key', '');

    if ( empty($api_key) ) {
        wp_send_json_error(['message' => 'API key is missing.']);
    }
    if ( $id <= 0 ) {
        wp_send_json_error(['message' => 'Invalid request ID.']);
    }

    $result = ipg_call_dalle($prompt, $api_key);

    if ( isset($result['error']) ) {
        wp_send_json_error(['message' => $result['error']]);
    }

    // Save image 1 URL + prompt to DB
    global $wpdb;
    $wpdb->update(
        $wpdb->prefix . 'ipg_requests',
        [
            'first_image_url' => $result['url'],
            'admin_prompt'    => $prompt,
        ],
        ['id' => $id],
        ['%s', '%s'],
        ['%d']
    );

    wp_send_json_success(['image_url' => $result['url']]);
}
add_action('wp_ajax_ipg_generate_image1', 'ipg_generate_image1_handler');


// ── IMAGE 2 HANDLER ─────────────────────────────────────────────────────────
function ipg_generate_image2_handler() {

    check_ajax_referer('ipg_ajax_nonce', 'nonce');

    if ( empty($_POST['prompt']) ) {
        wp_send_json_error(['message' => 'Prompt is required.']);
    }

    $prompt  = sanitize_text_field($_POST['prompt']);
    $id      = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $api_key = get_option('ipg_chatgpt_api_key', '');

    if ( empty($api_key) ) {
        wp_send_json_error(['message' => 'API key is missing.']);
    }
    if ( $id <= 0 ) {
        wp_send_json_error(['message' => 'Invalid request ID.']);
    }

    $result = ipg_call_dalle($prompt, $api_key);

    if ( isset($result['error']) ) {
        wp_send_json_error(['message' => $result['error']]);
    }

    // Save image 2 URL to DB
    global $wpdb;
    $wpdb->update(
        $wpdb->prefix . 'ipg_requests',
        ['second_image_url' => $result['url']],
        ['id' => $id],
        ['%s'],
        ['%d']
    );

    wp_send_json_success(['image_url' => $result['url']]);
}
add_action('wp_ajax_ipg_generate_image2', 'ipg_generate_image2_handler');


// ── SHARED DALL·E HELPER ────────────────────────────────────────────────────
function ipg_call_dalle( $prompt, $api_key ) {

    $response = wp_remote_post('https://api.openai.com/v1/images/generations', [
        'timeout' => 60,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode([
            'model'   => 'dall-e-3',
            'prompt'  => $prompt,
            'n'       => 1,
            'size'    => '1024x1024',
            'quality' => 'standard',
        ]),
    ]);

    if ( is_wp_error($response) ) {
        return ['error' => 'Request failed: ' . $response->get_error_message()];
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if ( isset($body['error']) ) {
        return ['error' => 'OpenAI Error: ' . $body['error']['message']];
    }

    if ( isset($body['data'][0]['url']) ) {
        return ['url' => $body['data'][0]['url']];
    }

    return ['error' => 'No image returned from OpenAI.'];
}


function ipg_admin_generated_images_page() {
    echo "<div class='wrap'><h1>Generated Images</h1><p>Coming soon...</p></div>";
}
