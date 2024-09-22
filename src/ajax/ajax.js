$(document).ready(function(){
    var excelData = [];
    $("#tranferExcellUploadData_btn").click(function(){
        $("[name='uploadExcellFile']").click();
    });
    $("[name='uploadExcellFile']").change(function(){
        $("#uploadExcellFileDataFormBTN").click();
    });
    $("#uploadExcellFileDataForm").submit(function(e){
        $("#error_upload").empty().fadeOut(200);
        $("#excelData_progress_percent").attr("class","text-secondary");
        $("#excelData_progress_percent").text("");
        $("#excelData_progress_box").fadeOut(200);
        $("#excelData_progress").width(0);
        e.preventDefault();
        var formData = new FormData();
        formData.append("action","transferExcellData");
        formData.append("excell-file",$("[name='uploadExcellFile']")[0].files[0]);
        $.ajax({
            type:"POST",
            url:ajax_url.url,
            data:formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(event) {
                    if (event.lengthComputable) {
                        var percentComplete = (event.loaded / event.total) * 100;
                        $("#excelData_progress_box").fadeIn(200);
                        $("#excelData_progress_percent").text(percentComplete);
                        $("#excelData_progress").width(percentComplete+"%");
                        if(percentComplete == 100){
                            $("#excelData_progress_percent").text("تکمیل شد");
                            $("#excelData_progress_percent").removeClass("text-secondary").addClass("text-success");
                        }
                    }
                }, false);
                return xhr;
            },
            success:function(e){
                var data_st = e.data;
                $("[name='uploadExcellFile']").val(''); 
                if(data_st.status){
                    var titles = `<li>مجموع محصولات افزوده شده : ${data_st.row}</li>`;
                    for(var i = 0; i < data_st.row;i++){
                        titles += `<li>${data_st.title[i]}</li>`;
                    }
                    $("#error_upload").fadeIn(200).html(`<div class='alert alert-success my-5'><ul>${titles}</ul></div>`);
                }
                /*var class_err = "danger";
                var upload_message = "اپلود نشد"; 
                if(e.data.status){
                    upload_message = e.data.row;
                    class_err = "success";
                }     
                $("#error_upload").append(`<div class='alert alert-${class_err}'>${upload_message} محصول اضافه شد</div>`);*/
            },error:function(err){
                console.log(err);
                $("#excelData_progress").removeClass("bg-success").addClass("bg-danger");
                $("#excelData_progress_percent").removeClass("text-success").addClass("text-danger").text("خطایی به وجود امده است");
                $("#error_upload").fadeIn(200).html("<div class='alert alert-danger my-5'><ul>"
                +"<li>فایل آپلود نشد !!</li>"
                +"<li>ممکن است فرمت فایل نامناسب باشد</li>"
                +"<li>ممکن است حجم فایل از 2 مگابایت بیشتر باشد</li>"
                +"<li>ممکن است در فایل اکسل خطایی به وجود امده باشد</li>"
                +"</ul></div>");
            }
        });
    });
    $("[name='excellData_save']").click(function(){
        $(".transfer_product_meta_data").find(".field_error").empty();
        $(".excellData_err_st_box").empty();
        var excellData_status = true;
        var excellData_titleArr = [];
        var excellData_metaArr = [];
        var excellDataColumnsName = [];
        var wordFileUrl = null;
        var excellData = $(".transfer_product_meta_data");
        if(excellData.length > 0){
            excellData.each(function(){
                var excellData_column = $(this).find("[name='column_name']");
                var excellData_title = $(this).find("[name='excellData_title']");
                var excellData_meta = $(this).find("[name='excellData_meta']");
                if((excellData_column.val() != "") && (excellData_title.val() != "") && (excellData_meta.val() != "")){
                    var excelData_variable_data = $(this).find("[name='variable_data']").val();
                    var attribute_data = excellData_title.val();
                    var excelData_variable_product_data = {};
                    var complete_variable_data = {};
                    var excellData_title_text = "";
                    if(excellData_meta.val() == "variable_product"){
                        excelData_variable_product_data["attributes"] = excelData_variable_data;
                        complete_variable_data[attribute_data] = excelData_variable_product_data;
                        excellData_title_text = JSON.stringify(complete_variable_data);
                    }else{
                        excellData_title_text = excellData_title.val();
                    }
                    excellDataColumnsName.push(excellData_column.val());
                    excellData_titleArr.push(excellData_title_text);
                    excellData_metaArr.push(excellData_meta.val());
                }else{
                    $(this).closest(".transfer_product_meta_data").find(".field_error").append("<div class='alert alert-danger'>filed shoud not be empty</div>");
                    excellData_status = false;
                }
            });
        }
        if($("[name='wordFilesUrl']").val() == ""){
            $("#wordFile_url_err").append("<div class='alert alert-danger'>The address field should not be empty</div>");
            excellData_status = false;
        }else{
            wordFileUrl = $("[name='wordFilesUrl']").val();
        }
        if(excellData_status){
            $.ajax({
                type:"POST",
                url:ajax_url.url,
                data:{
                    action:"SaveDynamicExcellMetaData",
                    excell_title:excellData_titleArr,
                    excell_meta:excellData_metaArr,
                    excell_column:excellDataColumnsName,
                    word_url:wordFileUrl
                },success:function(e){
                    if(e.status){
                        $(".excellData_err_st_box").append("<div class='alert alert-success'>با موفقیت ذخیره شد <i class='fa fa-check'></i></div>");
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }else{
                        $(".excellData_err_st_box").append("<div class='alert alert-danger'>خطایی به وجود امده است</div>");
                    }
                }
            });
        }else{
            $(".excellData_err_st_box").append("<div class='alert alert-danger'>لطفا خطا ها را برطرف کنید</div>");
        }
    });
    $("[name='deleteExcelSetting_meta_group']").click(function(){
        $(".excellData_err_st_box").empty();
        var deleteExcelSettingMeta_st = true;
        var excellSetting_meta_ids = [];
        var excelSettingMeta_check = $('input[name="excellSetting_meta_check"]:checked');
        if(excelSettingMeta_check.length > 0){
            excelSettingMeta_check.each(function(){
                excellSetting_meta_ids.push($(this).val());
            });
        }else{
            $(".excellData_err_st_box").append("<div class='alert alert-danger'>لطفا خطا ها را برطرف کنید</div>");
            deleteExcelSettingMeta_st = false;
        }
        if(deleteExcelSettingMeta_st){
            $.ajax({
                type:"POST",
                url:ajax_url.url,
                data:{
                    action:"deleteExcelSettingMeta_data",
                    setting_ids:excellSetting_meta_ids
                },success:function(e){
                    if(e.status){
                        $(".excellData_err_st_box").append("<div class='alert alert-success'>با موفقیت حذف شد</div>");
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }else{
                        $(".excellData_err_st_box").append("<div class='alert alert-danger'>خطایی به وجود امده است</div>");
                    }
                }
            });
        }
    });
    $("[name='deleteExcellData']").click(function(){
        var id = $(this).closest("tr").attr("setting_id");
        $.ajax({
            type:"POST",
            url:ajax_url.url,
            data:{
                action:"deleteExcelData",
                id:id
            },success:function(e){
                if(e.status){
                    $(".excellData_err_st_box").append("<div class='alert alert-success'>با موفقیت حذف شد</div>");
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }else{
                    $(".excellData_err_st_box").append("<div class='alert alert-danger'>خطایی به وجود امده است</div>");
                }
            }
        });
    });
    $(".transfer_product_add_meta_value").click(function(){
        $("#transfer_product_meta_form_box").empty();
        $.ajax({
            type:"POST",
            url:ajax_url.url,
            data:{
                action:"checkFieldExists_database",
            },success:function(e){
                var i = -1;
                var obj = e.setting_data;
                if(obj != null){
                    Object.keys(obj).forEach(function(key){
                        i++;
                        var setting_title = "";
                        var setting_name = obj[key]["setting_name"];
                        var setting_value = obj[key]["setting_value"];
                        var setting_meta = obj[key]["setting_meta"];
                        if(setting_name != "file_url"){
                            var template = `<div class="col-md-6 mt-3 transfer_product_meta_data" meta-id='${i}'>`;
                            template += `<div class="col-12">`;
                            template += '<div class="field_error"></div>';
                            template += `<span class='delete_transfer_meta_product_btn'><i class='text-danger far fa-times-circle'></i></span>`;
                            template += `<div class="row">`;
                            template += `<div class='col-2 py-4'>${setting_meta} - <input type='hidden' value='${setting_meta}' name='column_name'/></div>`;
                            template += `<div class="col-4">`;
                            template += `<label for="">Title</label>`;
                            template += `<input type="text" name="excellData_title" value="${setting_title}" id="" class="form-control">`;
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
                            var selectBox = $(".transfer_product_meta_data[meta-id='" + i + "'] select[name='transform-excelldata-position']");
                            var valuesArray = [];
                            $('.transfer_product_meta_data[meta-id="' + i + '"] select[name="transform-excelldata-position"] option').each(function() {
                                    valuesArray.push($(this).val());
                            });
                            if (valuesArray.indexOf(setting_value) == -1) {
                                $(`[meta-id='${i}']`).find("[name='excellData_meta']").val(setting_value);
                                setting_name = setting_value;
                                setting_value = "custome_meta";
                            }
                            selectBox.val(setting_value).change();

                            if(setting_value == "variable_product"){
                                var variable_product_option = JSON.parse(setting_name);
                                var keys = Object.keys(variable_product_option);
                                var firstKey = keys[0];
                                var variable_product_attributes = JSON.parse(variable_product_option[firstKey]['attributes']);
                                $(`[meta-id='${i}']`).find("[name='excel_variable_regular_price_meta_data']").val(variable_product_attributes['regular_price']).change();
                                $(`[meta-id='${i}']`).find("[name='excel_variable_sale_price_meta_data']").val(variable_product_attributes['sale_price']).change();
                                $(`[meta-id='${i}']`).find('[name="variable_data"').val(JSON.stringify(variable_product_attributes)).change();
                                console.log(variable_product_option);
                            }
                        }
                    });
                }
                $("#add_new_transfer_product_meta").modal("show");
            }            
        });
    });
});