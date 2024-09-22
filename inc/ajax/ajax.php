<?php


    function transferExcellData(){
        $data = [];
        if(!empty($_FILES['excell-file'])){
            $uploadFile = wp_handle_upload($_FILES['excell-file'], array('test_form' => false));
            $data = transferExcelFile($uploadFile);
        }
        wp_send_json(["data"=>$data]);
    }
    add_action( 'wp_ajax_transferExcellData', 'transferExcellData' );
    add_action( 'wp_ajax_nopriv_transferExcellData', 'transferExcellData' );

    function SaveDynamicExcellMetaData(){
        $data = ["status"=>false,"data"=>[]];
        global $wpdb;
        $transferExcellData = $wpdb->prefix."transfer_product_setting";
        $excell_title = $_POST['excell_title'];
        $excell_meta = $_POST['excell_meta'];
        $excell_column = $_POST['excell_column'];
        $word_url = $_POST['word_url'];
        if(!empty($excell_title) && !empty($excell_meta) && !empty($excell_column) && !empty($word_url)){
            $wpdb->query("TRUNCATE TABLE `$transferExcellData`");
            $wpdb->insert($transferExcellData,[
                "setting_name"=>"file_url",
                "setting_meta"=>"file_url",
                "setting_value"=>$word_url
            ]);
            for($i = 0;$i<count($excell_title);$i++){
                $insertExcellMeta = $wpdb->insert($transferExcellData,[
                    "setting_name"=>$excell_title[$i],
                    "setting_value"=>$excell_meta[$i],
                    "setting_meta"=>$excell_column[$i]
                ]);
                if(!is_wp_error( $insertExcellMeta )){
                    $data['status'] = true;
                }
            }
        }
        wp_send_json($data);
    }
    add_action( 'wp_ajax_SaveDynamicExcellMetaData', 'SaveDynamicExcellMetaData' );
    add_action( 'wp_ajax_nopriv_SaveDynamicExcellMetaData', 'SaveDynamicExcellMetaData' );

    function deleteExcelSettingMeta_data(){
        global $wpdb;
        $data = ["status"=>false,"data"=>[]];
        $setting_table = $wpdb->prefix."transfer_product_setting";
        $setting_ids = $_POST['setting_ids'];
        if(!empty($setting_ids)){
            foreach($setting_ids as $setting_id){
                $delete_excel_setting = $wpdb->delete($setting_table,[
                    "id"=>$setting_id
                ]);
                if(!is_wp_error($delete_excel_setting)){
                    $data['status'] = true;
                }else{
                    $data['data'] = "have error";
                    $data['status'] = false;
                }
            }
        }
        wp_send_json($data);
    }
    add_action( 'wp_ajax_deleteExcelSettingMeta_data', 'deleteExcelSettingMeta_data' );
    add_action( 'wp_ajax_nopriv_deleteExcelSettingMeta_data', 'deleteExcelSettingMeta_data' );

    function deleteExcelData(){
        global $wpdb;
        $data = ["status"=>false,"data"=>[]];
        $setting_table = $wpdb->prefix."transfer_product_setting";
        $id = $_POST['id'];
        if(!is_null($id)){
            $delete = $wpdb->delete($setting_table,[
                "id"=>$id
            ]);
            if(!is_wp_error($delete)){
                $data['status'] = true;
            }else{
                $data['data'] = "have error";
            }
        }
        wp_send_json( $data );
    }
    add_action( 'wp_ajax_deleteExcelData', 'deleteExcelData' );
    add_action( 'wp_ajax_nopriv_deleteExcelData', 'deleteExcelData' );

    function checkFieldExists_database(){
        global $wpdb;
        $data = ["status"=>false];
        $setting_data = [];
        $setting_table = $wpdb->prefix."transfer_product_setting";
        $get_product_setting_datas = $wpdb->get_results("SELECT * FROM `$setting_table`");
        if(!is_wp_error($get_product_setting_datas)){
            $i = 0;
            foreach($get_product_setting_datas as $get_product_setting_data_val){
                $i++;
                $setting_data[$i] = ["setting_name"=>($get_product_setting_data_val->setting_value == "variable_product")?stripslashes($get_product_setting_data_val->setting_name):$get_product_setting_data_val->setting_name,"setting_meta"=>$get_product_setting_data_val->setting_meta,"setting_value"=>$get_product_setting_data_val->setting_value];
            }
        }
        if(!empty($setting_data)){
            $data['status'] = true;
            $data['setting_data'] = $setting_data;
        }
        wp_send_json($data);
    }
    add_action( 'wp_ajax_checkFieldExists_database', 'checkFieldExists_database' );
    add_action( 'wp_ajax_nopriv_checkFieldExists_database', 'checkFieldExists_database' );
?>