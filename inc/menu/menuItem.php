<?php
function customDashboardMenu() {
    add_menu_page(
        'Transfer Product',
        'Transfer Product',
        'manage_options',
        'transfer_product',
        'transfer_product_page',
        'dashicons-download',
        6
    );
    add_submenu_page(
        "transfer_product",
        "Settings",
        "Settings",
        "manage_options",
        "transfer_product_setting",
        "transfer_product_setting_theme"
    );
    // add_submenu_page("transfer_product", "Test", "Test", "manage_options", "transfer_product_test", "transfer_product_test");
}
add_action('admin_menu', 'customDashboardMenu');
?>
