<?php
    function load_custom_wp_admin_style_js() {
        // چک کردن آدرس صفحه
        if(isset($_GET['page'])){
            $page_slug = $_GET['page'];
            if ( $page_slug === 'transfer_product' || $page_slug == "transfer_product_setting" ) {
                wp_enqueue_style("bootstrap",plugin_dir_url( __FILE__ )."css/bootstrap.css",[],"1.0.0");
                wp_enqueue_script( "JQ",plugin_dir_url( __FILE__ )."js/jquery.min.js", [], true );
                wp_enqueue_script( "transferCustomJS",plugin_dir_url( __FILE__ )."js/custome.js", [], true );
                wp_enqueue_script( "bootstrap",plugin_dir_url( __FILE__ )."js/bootstrap.js", [], true );
                wp_enqueue_script( "fontIconAwesome",plugin_dir_url( __FILE__ )."font/all.js", [], true );
                wp_enqueue_script("transferAjax",plugin_dir_url( __FILE__ )."ajax/ajax.js",[],"1.0.0",true);
                wp_localize_script('transferAjax', 'ajax_url', array(
                    'url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ajaxnonce')
                ));
            }
        }
    }
    add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style_js' );    
?>