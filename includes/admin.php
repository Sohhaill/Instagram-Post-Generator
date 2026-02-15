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
        <tr>
            <th>Admin Prompt</th>
            <td>
                <textarea id="ipg-pe-prompt" style="width:100%; height:80px;"></textarea>
            </td>
        </tr>
    </table>

    <button class="button button-primary" id="ipg-generate-image-btn">Generate Image</button>

    <div id="ipg-generated-img" style="margin-top:20px;"></div>
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


function ipg_admin_generated_images_page() {
    echo "<div class='wrap'><h1>Generated Images</h1><p>Coming soon...</p></div>";
}
