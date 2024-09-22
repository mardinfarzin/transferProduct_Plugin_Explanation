<?php
    use PhpOffice\PhpWord\IOFactory as PhpWordIOFactory;
    use PhpOffice\PhpSpreadsheet\IOFactory as PhpExcelIOFactory;
    use PhpOffice\PhpWord\Writer\HTML as WorldToHtml;
    function transferExcelFile($uploadFile) {
        $status = ["status" => false, "row" => 0];
        $titles = [];
        $url = [];
        $data = [];
        $getTransferSettings = getTransferSettingData();
        $url = $getTransferSettings['file_url'];
        $spreadsheet = PhpExcelIOFactory::load($uploadFile['file']);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
    
        // Reading data from each cell
        for ($row = 1; $row <= $highestRow; $row++) {
            if (!empty($getTransferSettings)) {
                foreach ($getTransferSettings as $key => $value) {
                    $cell = "";
                    if ($key == "variable_product") {
                        $cell = $value['cell'];
                    } else {
                        $cell = $value;
                    }
                    if ($key != "file_url") {
                        $cellValue = $worksheet->getCell($cell . $row)->getValue();
                        switch ($key) {
                            case "post_title":
                                $titles[] = $cellValue;
                                $data[$row]["product_data"][$key] = $cellValue;
                                break;
                            case "post_thumbnail":
                                $data[$row]["post_thumbnail"] = $cellValue;
                                break;
                            case "post_categories":
                                $data[$row]["post_categories"] = parseTexts($cellValue);
                                break;
                            case "post_tags":
                                $data[$row]["post_tags"] = parseTexts($cellValue);
                                break;
                            case "images_gallery":
                                $data[$row]["images_gallery"] = parseTexts($cellValue);
                                break;
                            case "wc_product_id":
                                $data[$row]["wc_product_id"] = $cellValue;
                                break;
                            case "UID":
                                $data[$row]["UID"] = $cellValue;
                                break;
                            case "product_excerpt":
                                $data[$row]["product_excerpt"] = $cellValue;
                                break;
                            case "product_sku":
                                $data[$row]["product_sku"] = $cellValue;
                                break;
                            case "product_weight":
                                $data[$row]["product_weight"] = $cellValue;
                                break;
                            case "product_dimensions":
                                $data[$row]["product_dimensions"] = parseTexts($cellValue);
                                break;
                            case "product_qty":
                                $data[$row]["product_qty"] = parseTexts($cellValue);
                                break;
                            case "product_url":
                                $data[$row]["product_url"] = $cellValue;
                                break;
                            case "variable_product":
                                $variable_data = [];
    
                                // Extract attributes from $value['attributes']
                                $attribute_value = stripslashes($value['attributes']);
    
                                // Check attribute values and parse if exists
                                if (!empty($attribute_value)) {
                                    $get_variable_product_json = json_decode($attribute_value, true);
                                    
                                    // Check if JSON has values
                                    if (!empty($get_variable_product_json)) {
                                        foreach ($get_variable_product_json as $variable_product_key => $variable_product_val) {
                                            // Check variable attribute values
                                            if (!empty($variable_product_val)) {
                                                foreach ($variable_product_val as $attribute_key => $attribute_val) {
                                                    // Get variable product values and parse
                                                    $get_variable_products = $worksheet->getCell($cell . $row)->getValue();
                                                    $get_variable_products = parseTexts($get_variable_products);
                                                    
                                                    // Check variable product values
                                                    $j = -1;
                                                    if (!empty($get_variable_products)) {
                                                        foreach ($get_variable_products as $product_val) {
                                                            $variable_product_attribute_json = json_decode($attribute_val, true);
                                                            $j++;
                                                            foreach ($variable_product_attribute_json as $meta_key => $meta_val) {
                                                                $meta_value = $worksheet->getCell($meta_val . $row)->getValue();
                                                                $meta_value = parseTexts($meta_value);
                                                                $variable_data[$variable_product_key][$product_val][$meta_key] = $meta_value[$j];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $data[$row]["variable_product"] = $variable_data;
                                break;
                            default:
                                $data[$row]["product_meta"][$value] = $cellValue;
                                break;
                        }
                    }
                }
            }
        }
        if (!empty($data)) {
            unlink($uploadFile['file']);
            $i = 0;
            foreach ($data as $key => $val) {
                $i++;
                $val["product_data"]["post_type"] = "product";
                if (!empty($url)) {
                    $wordFileName = $i;
                    if (!empty($val['UID'])) {
                        $wordFileName = $val['UID'];
                    }
                    $post_content = readWordFile($url . $wordFileName . ".docx");
                    if (!is_null($post_content)) {
                        $val["product_data"]["post_content"] = $post_content;
                    }
                }
                $productID = wp_insert_post($val["product_data"]);
                
                if (!is_wp_error($productID)) {
                    if (!empty($val["post_thumbnail"])) {
                        $image_id = upload_image_to_post($val["post_thumbnail"]);
                        if (!is_null($image_id)) {
                            set_post_thumbnail($productID, $image_id);
                        }
                    }
                    if (!empty($val["post_categories"])) {
                        set_product_categories($productID, $val["post_categories"]);
                    }
                    if (!empty($val["product_excerpt"])) {
                        update_product_excerpt($productID, $val["product_excerpt"]);
                    }
                    if (!empty($val["post_tags"])) {
                        add_tags_to_product($productID, $val["post_tags"]);
                    }
                    if (!empty($val["wc_product_id"])) {
                        set_wc_product_id($productID, $val["wc_product_id"]);
                    }
                    if (!empty($val["product_sku"])) {
                        set_product_sku($productID, $val["product_sku"]);
                    }
                    if (!empty($val["product_weight"])) {
                        set_product_weight($productID, $val["product_weight"]);
                    }
                    if (!empty($val["product_dimensions"])) {
                        set_product_dimensions($productID, $val["product_dimensions"]);
                    }
                    if (!empty($val["product_qty"])) {
                        set_product_stock_quantity($productID, $val["product_qty"]);
                    }
                    if (!empty($val["product_url"])) {
                        $update_post_data = array(
                            'ID'   => $productID,
                            'guid' => $val["product_qty"],
                        );
                        // Update post with desired GUID
                        wp_update_post($update_post_data);
                    }
                    if (!empty($val["images_gallery"])) {
                        add_gallery_to_product($productID, $val["images_gallery"]);
                    }
                    if (!empty($val['variable_product'])) {
                        create_variable_product($productID, $val['variable_product']);
                    }
                    $caretaker_sale_price = 0;
                    if (!empty($val["product_meta"])) {
                        $regular_price = 0;
                        foreach ($val["product_meta"] as $meta_key => $meta_value) {
                            update_post_meta($productID, $meta_key, $meta_value);
                            if ($meta_key == "_regular_price") {
                                $regular_price = $meta_value;
                            }
                            if ($meta_key == "_sale_price") {
                                $create_sale_price = $meta_value;
                                if (strpos($meta_value, "%") !== false) {
                                    $create_sale_price = ($meta_value / 100) * $regular_price;
                                }
                                update_post_meta($productID, "_price", $create_sale_price);
                                update_post_meta($productID, "_sale_price", $create_sale_price);
                                $caretaker_sale_price = 1;
                            } elseif ($caretaker_sale_price == 0) {
                                update_post_meta($productID, "_price", $meta_value);
                            }
                        }
                    } else {
                        if (!is_null($image_id)) {
                            set_post_thumbnail($productID, $image_id);
                        }
                    }
                    $status['status'] = true;
                    $status['row'] = $highestRow;
                    $status['title'] = $titles;
                }
            }
        }
        return $status;
    }
    
    function get_transfer_product_meta() {
        $data = null;
        global $wpdb;
        $cData = "";
        $transfer_product_setting = $wpdb->prefix . "transfer_product_setting";
        $get_product_setting = $wpdb->get_results("SELECT * FROM `$transfer_product_setting`");
        $id = 0;
        if (!empty($get_product_setting)) {
            foreach ($get_product_setting as $row) {
                $id++;
                if ($row->setting_meta == "file_url") {
                    $cData = $row->setting_value;
                }
                $data .= "<tr setting_id='" . $row->id . "'>";
                    $data .= "<td><input type='checkbox' class='form-check' name='excellSetting_meta_check' value='" . $row->id . "'/></td>";
                    $data .= "<td>$id</td>";
                    $data .= "<td>" . $row->setting_meta . "</td>";
                    $data .= "<td>" . $row->setting_name . "</td>";
                    $data .= "<td>" . $row->setting_value . "</td>";
                    $data .= "<td><button name='deleteExcellData' class='btn btn-danger'><i class='far fa-trash'></i></button></td>";
                $data .= "</tr>"; 
            }
        }
        return ["data" => $data, "c-data" => $cData];
    }
    
    function getTransferSettingData() {
        $data = [];
        global $wpdb;
        $transfer_setting = $wpdb->prefix . "transfer_product_setting";
        $get_transferData = $wpdb->get_results("SELECT * FROM `$transfer_setting`");
        if (!empty($get_transferData)) {
            foreach ($get_transferData as $value) {
                if ($value->setting_meta == "file_url") {
                    $data['file_url'] = $value->setting_value;
                } elseif ($value->setting_value == "variable_product") {
                    $data[$value->setting_value] = ["attributes" => $value->setting_name, "cell" => $value->setting_meta];
                } else {
                    $data[$value->setting_value] = $value->setting_meta;
                }
            }
        }
        return $data;
    }
    
    function uploadWordFile($path) {
        $wp_upload_dir = wp_upload_dir();
        $upload_path = $wp_upload_dir['path'];
        $file_name = basename($path);
        $file_content = file_get_contents($path);
        $upload_file = $upload_path . '/' . $file_name;
        $upload_result = wp_upload_bits($file_name, null, $file_content);
        return $upload_result;
    }
    
    function readWordFile($filePath) {
        $phpWord = PhpWordIOFactory::load($filePath);
    
        // Generate the HTML content
        $htmlWriter = new WorldToHtml($phpWord);
        $htmlContent = $htmlWriter->getContent();
    
        // Parse the HTML content using DOMDocument
        $dom = new DOMDocument();
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $headerContents = '';
        $headerNodes = $dom->getElementsByTagName('body')->item(0)->childNodes;
        foreach ($headerNodes as $header) {
            $headerContents .= $dom->saveHTML($header);
        }
        // Get the body content without the HTML, HEAD, and BODY tags
        $bodyContent = '';
        $bodyNodes = $dom->getElementsByTagName('body')->item(0)->childNodes;
        foreach ($bodyNodes as $node) {
            $bodyContent .= $dom->saveHTML($node);
        }
        $pattern = "/Picture link: (.*?)<\/span>.*?Alt: .*?<\/span><span.*?>(.*?)<\/span>/s";
        $pattern_cama = '/<p><span.*?>(.*?)<\/span><\/p>/';
        $replace_image = preg_replace($pattern, "<img src='$1' alt='$2'/>", $bodyContent);
        $replace_cama = preg_replace($pattern_cama, '$1', $replace_image);
        $result = preg_replace('/(<p><span>)([^<]*)(<\/span><\/p><p><span>)([^<]*)(<\/span><\/p>)/', '$1$2$4$5', $replace_cama);
        return $headerContents;
    }
    

    function parseTexts($inputString) {
        // Split the string by the delimiter |
        $categories = explode('|', $inputString);
        
        // Trim whitespace from the beginning and end of each category
        $categories = array_map('trim', $categories);
        
        // Remove empty categories
        $categories = array_filter($categories);
        
        // Return categories as an array
        return $categories;
    }
    
    function setImageFromUrl($post_id, $image_url) {
        // Set the attachment post ID.
        $attachment_id = wp_insert_attachment(array(
            'guid'           => $image_url,
            'post_mime_type' => 'image/jpeg',
            'post_title'     => 'Featured Image',
            'post_status'    => 'inherit',
        ));
        
        // Set the featured image.
        set_post_thumbnail($post_id, $attachment_id);
    }
    
    function setProductCategories($post_id, $name_arr) {
        // Check if the post (product) exists
        if (empty($post_id) || !get_post($post_id)) {
            return false;
        }
        
        // Convert category names to their respective IDs
        $term_ids = [];
        foreach ($name_arr as $name) {
            $term = term_exists($name, 'product_cat');
            if ($term) {
                $term_ids[] = $term['term_id'];
            } else {
                // If the category does not exist, create it and get its ID
                $term_id = wp_insert_term($name, 'product_cat');
                if (!is_wp_error($term_id)) {
                    $term_ids[] = $term_id['term_id'];
                }
            }
        }
        
        // Assign the product to the desired categories
        wp_set_post_terms($post_id, $term_ids, 'product_cat');
        
        return true;
    }
    
    function addTagsToProduct($product_id, $tags_array) {
        // Check if the product with this ID exists
        if (wc_get_product($product_id)) {
            // Transfer tags to the product object
            $product = wc_get_product($product_id);
            
            // Check the length of the tags array
            if (count($tags_array) > 0) {
                // Add each tag to the product
                foreach ($tags_array as $tag) {
                    wp_set_post_terms($product_id, $tag, 'product_tag', true);
                }
                return true; // Successful operation
            } else {
                return false; // Tags array is empty
            }
        } else {
            return false; // Product with this ID does not exist
        }
    }
    
    function addGalleryToProduct($product_id, $images_url) {
        $image_ids = [];
        // Ensure WooCommerce is active
        if (class_exists('WC_Product')) {
            if (!empty($images_url)) {
                foreach ($images_url as $image_url) {
                    $image_ids[] = uploadImageToPost($image_url);
                }
            }
            // Get the product object by product ID
            $product = wc_get_product($product_id);
            
            // Check the product to ensure it exists
            if ($product) {
                // Set images as the product gallery
                $product->set_gallery_image_ids($image_ids);
                
                // Save the product
                $product->save();
            }
        }
        return $image_ids;
    }
    
    function uploadImageToPost($url) {
        $uploaded_file = null;
        // Get the temporary file path to save the image
        $upload_dir = wp_upload_dir();
        $image_path = $upload_dir['path'] . '/' . basename($url);
        $image_data = file_get_contents($url);
        file_put_contents($image_path, $image_data);
        
        // Upload the image to WordPress
        $file_array = array(
            'name'     => basename($url),
            'tmp_name' => $image_path
        );
        $uploaded_file = media_handle_sideload($file_array, 0);
        
        // Check the success of the image upload
        if (is_wp_error($uploaded_file)) {
            // Handle error as necessary
            echo "Error uploading image: " . $uploaded_file->get_error_message();
            return false;
        }
        
        return $uploaded_file;
    }
    
    function createProductInWooCommerce($data) {
        // Check input data
        if (empty($data) || !is_array($data)) {
            return new WP_Error('invalid_data', __('Invalid data.', 'text-domain'));
        }
        
        // Check if WooCommerce is installed and active
        if (!function_exists('WC')) {
            return new WP_Error('woocommerce_not_installed', __('WooCommerce is not installed or activated.', 'text-domain'));
        }
        
        // Check if a product with the given title exists
        $existing_product = get_page_by_title($data['post_title'], OBJECT, 'product');
        
        if ($existing_product instanceof WP_Post) {
            // The product with the desired title exists, return its ID
            return $existing_product->ID;
        }
        
        // Create a new array for the product
        $product_data = array(
            'name' => isset($data['post_title']) ? sanitize_text_field($data['post_title']) : '',
            'excerpt' => isset($data['post_excerpt']) ? wp_kses_post($data['post_excerpt']) : '',
            'status' => isset($data['status']) ? $data['status'] : 'publish',
            'thumbnail_id' => isset($data['post_thumbnail']) ? intval($data['post_thumbnail']) : 0,
            'slug' => isset($data['post_name']) ? sanitize_title($data['post_name']) : '',
            'regular_price' => isset($data['price']) ? floatval($data['price']) : 0,
            'sale_price' => isset($data['offer']) ? floatval($data['offer']) : 0,
        );
        
        // Create the product
        $product_id = wp_insert_post(array(
            'post_title' => $product_data['name'],
            'post_excerpt' => $product_data['excerpt'],
            'post_status' => $product_data['status'],
            'post_type' => 'product_variation',
            'post_name' => $product_data['slug'],
        ));
        
        // Check the success of product creation
        if (is_wp_error($product_id)) {
            return $product_id;
        }
        
        // Add prices to the product
        update_post_meta($product_id, '_regular_price', $product_data['regular_price']);
        update_post_meta($product_id, '_sale_price', $product_data['sale_price']);
        
        // If a selected image exists, assign it to the product
        if ($product_data['thumbnail_id'] > 0) {
            set_post_thumbnail($product_id, $product_data['thumbnail_id']);
        }
        
        // Return the ID of the new product
        return $product_id;
    }
    
    function createVariableProduct($product_id, $variation_datas) {
        global $wpdb;
        
        if (is_null($product_id) || empty($variation_datas)) {
            return;
        }
        
        foreach ($variation_datas as $attribute_name => $variations) {
            if (empty($variations)) {
                continue;
            }
            
            $taxonomy = 'pa_' . wc_sanitize_taxonomy_name($attribute_name);
            
            wc_create_attribute(array(
                'name' => $attribute_name,
                'slug' => $taxonomy,
                'type' => 'select',
                'order_by' => 'menu_order',
                'has_archives' => true
            ));
            
            foreach ($variations as $variation => $variation_data) {
                $term = term_exists($variation, $taxonomy);
                $term_id = $term ? $term['term_id'] : wp_insert_term($variation, $taxonomy)['term_id'];
                
                wp_set_object_terms($product_id, $variation, $taxonomy, true);
                
                $variation_attributes[$taxonomy] = array(
                    'name' => $taxonomy,
                    'value' => $term_id,
                    'is_visible' => 1,
                    'is_variation' => 1,
                    'is_taxonomy' => '1'
                );
                
                $variation_post = array(
                    'post_title' => get_the_title($product_id),
                    'post_name' => 'product-' . $product_id . '-variation',
                    'post_status' => 'publish',
                    'post_parent' => $product_id,
                    'post_type' => 'product_variation'
                );
                
                $variation_id = wp_insert_post($variation_post);
                
                update_post_meta($variation_id, 'attribute_' . $taxonomy, $variation);
                
                $variation_product = new WC_Product_Variation($variation_id);
                $variation_product->set_sku($variation_data['sku']);
                $variation_product->set_price($variation_data['regular_price']);
                $variation_product->set_regular_price($variation_data['regular_price']);
                $variation_product->set_sale_price($variation_data['sale_price']);
                $variation_product->set_stock_quantity($variation_data['stock_qty']);
                $variation_product->set_manage_stock(!empty($variation_data['stock_qty']));
                $variation_product->set_weight('');
                $variation_product->save();
            }
        }
        if (class_exists('WooCommerce')) {
            // Get product type
            $product_type = 'variable'; // Define the new product type here (e.g., 'variable')
            
            // Set the new product type in term_relationships table
            wp_set_object_terms($product_id, $product_type, 'product_type');
        }
        update_post_meta($product_id, '_product_attributes', $variation_attributes);
    }
    
    function setWCProductId($product_id, $wc_product_id) {
        // Check if the product with the given ID exists
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return "Product not found!";
        }
        
        // Set the product ID in WooCommerce
        $product->set_id($wc_product_id);
        
        // Save changes
        $product->save();
        
        return "WooCommerce product ID has been successfully set!";
    }
    
    function setProductSku($product_id, $sku) {
        // Check if the product with the given ID exists
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return "Product not found!";
        }
        
        // Set SKU for the product
        $product->set_sku($sku);
        
        // Save changes
        $product->save();
        
        return "SKU has been successfully set for the product!";
    }
    
    function setProductWeight($product_id, $weight) {
        // Check if the product with the given ID exists
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return "Product not found!";
        }
        
        // Set weight for the product
        $product->set_weight($weight);
        
        // Save changes
        $product->save();
        
        return "Weight has been successfully set for the product!";
    }
    
    function setProductDimensions($product_id, $dimensions) {
        // Check if the product with the given ID exists
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return "Product not found!";
        }
        
        if (count($dimensions) != 3) {
            return "The dimensions array must contain 3 elements (length, width, height)!";
        }
        
        // Set dimensions for the product
        $length = $dimensions[0];
        $width = $dimensions[1];
        $height = $dimensions[2];
        
        $product->set_length($length);
        $product->set_width($width);
        $product->set_height($height);
        
        // Save changes
        $product->save();
        
        return "Dimensions have been successfully set for the product!";
    }
    
    function setProductStockQuantity($product_id, $stock_quantity) {
        // Check if the product with the given ID exists
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return "Product not found!";
        }
        
        // Set stock quantity for the product
        $product->set_stock_quantity($stock_quantity);
        
        // Save changes
        $product->save();
        
        return "Stock quantity has been successfully set for the product!";
    }
    
    function updateProductExcerpt($product_id, $excerpt) {
        $post_data = array(
            'ID'           => $product_id,
            'post_excerpt' => $excerpt,
        );
    
        wp_update_post($post_data);
    }
    
?>