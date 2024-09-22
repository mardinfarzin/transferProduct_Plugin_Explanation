<?php
/*
    Plugin Name: TransferProduct
    Author: Mardin
    Version: 1.0.0
    Description: This plugin is a personal plugin for transferring products from an Excel file to WooCommerce
*/
include(plugin_dir_path(__FILE__) . "inc.php");

// Define function to create or update the table
function createTransferProductSettingTable() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'transfer_product_setting';

    $charset_collate = $wpdb->get_charset_collate();

    // Define table structure
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        setting_name VARCHAR(255) NOT NULL,
        setting_meta VARCHAR(255) NOT NULL,
        setting_value TEXT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Execute SQL operation
    dbDelta($sql);
}

// Call function to create or update the table
register_activation_hook(__FILE__, 'createTransferProductSettingTable');
?>
