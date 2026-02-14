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
    ?>

    <div class="wrap">
        <h1>Instagram Post Requests</h1>
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
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>

            <tbody>
                <?php if ( empty($rows) ) : ?>
                    <tr><td colspan="9" style="text-align:center;">No records found.</td></tr>
                <?php else: ?>
                    <?php foreach ( $rows as $row ) : ?>
                        <tr>
                            <td><?php echo esc_html($row->id); ?></td>
                            <td><?php echo esc_html($row->username); ?></td>
                            <td><?php echo esc_html($row->user_email); ?></td>
                            <td><?php echo esc_html($row->selected_category); ?></td>
                            <td><?php echo esc_html(str_replace(['[',']','"'], '', $row->selected_styles)); ?></td>
                            <td><?php echo esc_html($row->no_of_posts); ?></td>
                            <td><?php echo esc_html($row->additional_notes); ?></td>
                            <td><?php echo esc_html($row->status); ?></td>
                            <td><?php echo esc_html($row->created_at); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

    <?php
}



/**
 * SUBMENU PAGES (EMPTY FOR NOW)
 */
function ipg_admin_settings_page() {
    echo "<div class='wrap'><h1>Settings</h1><p>Coming soon...</p></div>";
}

function ipg_admin_generated_images_page() {
    echo "<div class='wrap'><h1>Generated Images</h1><p>Coming soon...</p></div>";
}
