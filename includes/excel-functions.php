<?php

if (!defined('ABSPATH')) exit;

function ipg_append_to_excel($row_data) {

    $upload_dir = wp_upload_dir();
    $file = $upload_dir['basedir'] . "/ipg-requests.xlsx";

    require_once ABSPATH . 'wp-admin/includes/file.php';
    WP_Filesystem();

    // CREATE NEW EXCEL IF NOT EXISTS
    if (!file_exists($file)) {

        $headers = [
            'Username',
            'User Email',
            'Category',
            'Styles',
            'Notes',
            'Timestamp',
            'User Type',
            'Status',
            'Entry ID'
        ];

        $content = implode("\t", $headers) . "\n";
        file_put_contents($file, $content);
    }

    // APPEND NEW ROW
    $line = implode("\t", $row_data) . "\n";
    file_put_contents($file, $line, FILE_APPEND);
}
