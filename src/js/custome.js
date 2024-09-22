$(document).ready(function(){
    var delete_columns_arr = [];
    var delete_columns_num = [];
    var variable_sale_excell_meta_data = {};
    var variable_qty_excell_meta_data = {};
    var variable_waite_excell_meta_data = {};
    var columns_variable_product_usage = [];
    function printExcelAlphabet() {
        const startCharCode = 'A'.charCodeAt(0);
        const endCharCode = 'Z'.charCodeAt(0);
        var result = [];

        for (let i = startCharCode; i <= endCharCode; i++) {
            result[result.length] = String.fromCharCode(i);
        }

        for (let i = startCharCode; i <= endCharCode; i++) {
            for (let j = startCharCode; j <= endCharCode; j++) {
                result[result.length] = String.fromCharCode(i) + String.fromCharCode(j);
            }
        }
        return result;
    }
    var transferSettingMeta_id = 0;

    ////////////////////////////////////////////////////
    $("#add_new_transfer_product_meta_btn").click(function(){
        if($("#transfer_product_meta_form_box").children().length > 0){
            transferSettingMeta_id = $("#transfer_product_meta_form_box").children().length;
        }
        console.log(variable_sale_excell_meta_data);
        var column_name = null;
        var column_num = null;
        if(delete_columns_arr.length > 0){
            column_name = delete_columns_arr[delete_columns_arr.length - 1];
            column_num = delete_columns_num[delete_columns_num.length - 1];
            delete_columns_arr.pop(); // حذف آخرین المان از delete_columns_arr
            delete_columns_num.pop();
        }else{
            var excellColumn = printExcelAlphabet();
            column_num = transferSettingMeta_id++;
            column_name = excellColumn[transferSettingMeta_id - 1];
        }
            var template = `<div class="col-md-6 mt-3 transfer_product_meta_data" meta-id='${column_num}'>`;
            template += `<div class="col-12">`;
            template += '<div class="field_error"></div>';
            template += `<span class='delete_transfer_meta_product_btn'><i class='text-danger far fa-times-circle'></i></span>`;
            template += `<div class="row">`;
            template += `<div class='col-2 py-4'>${column_name} - <input type='hidden' value='${column_name}' name='column_name'/></div>`;
            template += `<div class="col-4">`;
            template += `<label for="">Title</label>`;
            template += `<input type="text" name="excellData_title" value="Title" id="" class="form-control">`;
            template += `</div>`;
            template += `<div class="col-4">`;
            template += `<label for="">Type</label>`;
            template += `<select name="transform-excelldata-position" class="form-control">`;
            template += `<option value="post_title">Title</option>`;
            template += `<option value="post_thumbnail">Main Image</option>`;
            template += `<option value="post_categories">Category</option>`;
            template += `<option value="post_tags">Tags</option>`;
            template += `<option value="images_gallery">Gallery Image</option>`;
            template += `<option value="_regular_price">Price</option>`;
            template += `<option value="_sale_price">Discount</option>`;
            template += `<option value="product_url">Product URL</option>`;
            template += `<option value="product_sku">Product SKU</option>`;
            template += `<option value="product_qty">Product Quantity</option>`;
            template += `<option value="product_weight">Product Weight</option>`;
            template += `<option value="product_dimensions">Product Dimensions</option>`;
            template += `<option value="product_excerpt">Short Description</option>`;
            template += `<option value="variable_product">Variable Product</option>`;
            template += `<option value="custome_meta">Custom</option>`;
            template += `</select>`;
            template += `<input type='hidden' name='variable_data'/>`;
            template += `<input type='text' name="excellData_meta" value="post_title" class='form-control transform_custom_meta_data' style='display:none;' placeholder='Meta Data'/>`;
            template += `<label style='display:none' for='excel_variable_regular_price_meta_data'>Regular Price</label>`;
            template += `<select name='excel_variable_regular_price_meta_data' class='form-control' style='display:none'><option>--Product Price--</option></select>`;
            template += `<label style='display:none' for='excel_variable_sale_price_meta_data'>Discount Price</label>`;
            template += `<select name='excel_variable_sale_price_meta_data' class='form-control' style='display:none'><option>--Product Discount--</option></select>`;
            template += `</div>`;
            template += `</div>`;
            template += `</div>`;
            template += `</div>`;
            $("#transfer_product_meta_form_box").append(template);
    });
    ////////////////////////////////////////////////////////////













    ////////////////////////////////////////////////////////////////////////////////////
    $(document).on("change", "[name='excel_variable_sale_price_meta_data']", function() {
        var select_sale_variable_data = $(this).val();
        var obj_variable = $(this).closest(".transfer_product_meta_data").find("[name='variable_data']").attr("value");
        var dataObj = JSON.parse(obj_variable);
        dataObj.sale_price = select_sale_variable_data;
        var newDataStr = JSON.stringify(dataObj);
        $(this).closest(".transfer_product_meta_data").find('[name="variable_data"]').attr("value",newDataStr);
        var get_top_of_box_alphabet = $(this).closest(".transfer_product_meta_data").find("[name='column_name']").val();
        variable_sale_excell_meta_data[get_top_of_box_alphabet] = select_sale_variable_data;
    });
    ////////////////////////////////////////////////////////////////////////////////////









    
    ////////////////////////////////////////////////////////////////////////////////////
    $(document).on("change","[name='excel_variable_regular_price_meta_data']",function(){
        var select_regular_variable_data = $(this).val();
        var obj_variable = $(this).closest(".transfer_product_meta_data").find("[name='variable_data']").attr("value");
        var dataObj = JSON.parse(obj_variable);
        dataObj.regular_price = select_regular_variable_data;
        var newDataStr = JSON.stringify(dataObj);
        $(this).closest(".transfer_product_meta_data").find('[name="variable_data"]').val(newDataStr);
        var get_top_of_box_alphabet = $(this).closest(".transfer_product_meta_data").find("[name='column_name']").val();
        variable_sale_excell_meta_data[get_top_of_box_alphabet] = select_regular_variable_data;
    });
    ////////////////////////////////////////////////////////////////////////////////////







    /////////////////////////////////////////////////////////////////////
    $(document).on("click",".delete_transfer_meta_product_btn",function(){
        var parent = $(this).closest(".transfer_product_meta_data");
        var get_column_alphabet = parent.find("[name='column_name']").val();
        delete variable_sale_excell_meta_data.get_column_alphabet;
        delete_columns_arr.push(get_column_alphabet);
        delete_columns_num.push(parent.attr("meta-id"));
        parent.remove();
    });
    ////////////////////////////////////////////////////////////////////////














    /////////////////////////////////////////////////////////////////////////////
    $(document).on("change", "[name='transform-excelldata-position']", function() {
        var transform_excellData_position = $(this).val();
        var parentTransferProductMeta = $(this).closest(".transfer_product_meta_data").attr("meta-id");
        var get_column_alphabet = $(this).closest(".transfer_product_meta_data").find("[name='column_name']").val();
        var text_select = $(this).find("option:selected").text();
        var add_to_regular_variable_meta_box_parent = $("[name='excel_variable_regular_price_meta_data']");
        var add_to_sale_variable_meta_box_parent = $("[name='excel_variable_sale_price_meta_data']");

        $(this).closest(".transfer_product_meta_data").find("[name='excellData_title']").val(text_select);


        if((transform_excellData_position == "_regular_price") || (transform_excellData_position == "_sale_price")){
            columns_variable_product_usage.push(get_column_alphabet);
        }
        if(transform_excellData_position == "variable_product"){
            $(this).closest(".transfer_product_meta_data").find("[name='transform-excelldata-position']").fadeOut(200);
            $(this).closest(".transfer_product_meta_data").find("[name='excel_variable_regular_price_meta_data']").fadeIn(200);
            $(this).closest(".transfer_product_meta_data").find("[for='excel_variable_regular_price_meta_data']").fadeIn(200);
            $(this).closest(".transfer_product_meta_data").find("[name='excel_variable_sale_price_meta_data']").fadeIn(200);
            $(this).closest(".transfer_product_meta_data").find("[for='excel_variable_sale_price_meta_data']").fadeIn(200);
            $(this).closest(".transfer_product_meta_data").find("[name='variable_data']").attr("value", JSON.stringify({regular_price: $('[name="excel_variable_regular_price_meta_data"] option:first').val(), sale_price: $('[name="excel_variable_sale_price_meta_data"] option:first').val()}));
        }else{
            add_to_regular_variable_meta_box_parent.empty();
            add_to_sale_variable_meta_box_parent.empty();
            $(this).closest(".transfer_product_meta_data").find("[name='transform-excelldata-position']").fadeIn(200);
            $(this).closest(".transfer_product_meta_data").find("[name='excel_variable_regular_price_meta_data']").fadeOut(200);
            $(this).closest(".transfer_product_meta_data").find("[for='excel_variable_regular_price_meta_data']").fadeOut(200);
            $(this).closest(".transfer_product_meta_data").find("[name='excel_variable_sale_price_meta_data']").fadeOut(200);
            $(this).closest(".transfer_product_meta_data").find("[for='excel_variable_sale_price_meta_data']").fadeOut(200);
        }
        if(columns_variable_product_usage.length > 0){
            add_to_regular_variable_meta_box_parent.empty();
            add_to_sale_variable_meta_box_parent.empty();
            columns_variable_product_usage.forEach(element => {
                add_to_regular_variable_meta_box_parent.append(`<option value='${element}'>${element}</option>`);
                add_to_sale_variable_meta_box_parent.append(`<option value='${element}'>${element}</option>`);
            });
        }
        

        switch (transform_excellData_position) {
            case "post_title":
            case "post_thumbnail":
            case "post_categories":
            case "post_tags":
            case "images_gallery":
            case "_regular_price":
            case "_sale_price":
            case "product_url":
            case "variable_product":
            case "wc_product_id":
            case "product_sku":
            case "product_qty":
            case "product_weight":
            case "product_dimensions":
                $(`[meta-id='${parentTransferProductMeta}']`).find(".transform_custom_meta_data").attr("value",transform_excellData_position);
            break;
            case "custome_meta":
                $(`[meta-id='${parentTransferProductMeta}']`).find(".transform_custom_meta_data").fadeIn(200);
                $(this).fadeOut(100);
            break;
        }
    });
    //////////////////////////////////////////////////////////////////////////////






















    $("[name='excelDataFilter_text']").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#excelData_body_data tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
});