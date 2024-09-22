<?php
function transfer_product_page() {
    $template = '';
    $template .= '<div class="container my-4 bg-white rounded">';
    $template .= '<div class="col-12 py-2" id="transferData_header">';
    $template .= '<div method="post" class="row" enctype="multipart/form-data">';
    $template .= '<div class="col-2">';
    $template .= '<button class="btn btn-primary" id="tranferExcellUploadData_btn">Upload File</button>';
    $template .= '<form id="uploadExcellFileDataForm" method="post" style="display:none;">';
    $template .= '<input type="file" name="uploadExcellFile">';
    $template .= '<input type="submit" id="uploadExcellFileDataFormBTN" >';
    $template .= '</form>';
    $template .= '</div>';
    $template .= '<div class="col-10" style="display:none;" id="excelData_progress_box">';
    $template .= '<div class="col-12">';
    $template .= '<p class="text-secondary" id=\'excelData_progress_percent\'>100%</p>';
    $template .= '<div class="progress">';
    $template .= '<div class="bg-success progress-bar" id="excelData_progress" style="width:100%;"></div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '<div class="col-12 my-3" id="error_upload"></div>';
    $template .= '</div>';

    $template .= '<div class="modal" id="excellFileDataBox">';
    $template .= '<div class="modal-dialog modal-lg">';
    $template .= '<div class="modal-content">';
    $template .= '<div class="modal-body">';
    $template .= '<div class="col-12 my-3">';
    $template .= '<div class="row" id="excellFileDataSelectionBox">';
    //$template .= '<div class="col-md-6 my-3"><div class="row"><div class="col-1"><input class="form-control excellFileDataSelectionToAdd" product-id="1" type="checkbox" name="" id=""></div><div class="col-11"><p>1-Northern Fragrance Product</p></div></div></div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';

    echo $template;
}

function transfer_product_setting_theme() {
    $template = '';
    $template .= '<div class="container bg-white my-5 py-3 rounded">';
    $template .= '<div class="col-12 my-2">';
    $template .= '<div class="row">';
    $template .= '<div class="col-6">';
    $template .= '<input class="form-control" placeholder="Search" type="text" name="excelDataFilter_text" id="">';
    $template .= '</div>';
    $template .= '<div class="col-2">';
    $template .= '<button class="btn btn-danger" name=\'deleteExcelSetting_meta_group\'>Delete Group</button>';
    $template .= '</div>';
    $template .= '<div class="col-3">';
    $template .= '<div class="transfer_product_add_meta_value mt-2" style="cursor:pointer;">';
    $template .= '<span class="text-primary"><i class="far fa-plus-circle"></i> Add New</span>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';

    $template .= '<div class="col-12 border-top my-3">';
    $transfer_meta_data = get_transfer_product_meta();
    if (!is_null($transfer_meta_data['data'])) {
        $template .= '<table class="table table-striped">';
        $template .= '<thead class="table-dark">';
        $template .= '<tr>';
        $template .= '<th></th>';
        $template .= '<th>ID</th>';
        $template .= '<th>Position</th>';
        $template .= '<th>Title</th>';
        $template .= '<th>Meta</th>';
        $template .= '<th></th>';
        $template .= '</tr>';
        $template .= '</thead>';
        $template .= '<tbody id="excelData_body_data">';
        $template .= $transfer_meta_data['data'];
        $template .= '</tbody>';
        $template .= '</table>';
    } else {
        $template .= '<p class="text-secondary my-3 text-center">Nothing to display</p>';
    }
    $template .= '</div>';
    $template .= '</div>';

    $template .= '<div class="modal" id="add_new_transfer_product_meta">';
    $template .= '<div class="modal-dialog modal-lg">';
    $template .= '<div class="modal-content">';
    $template .= '<div class="modal-body">';
    $template .= '<div class="col-12">';
    $template .= '<div class="row">';
    $template .= '<div class="col-2">';
    $template .= '<button id="add_new_transfer_product_meta_btn" class="btn btn-outline-primary rounded-circle"><i class="far fa-plus"></i></button>';
    $template .= '</div>';
    $template .= '<div class="col-10">';
    $wordFile_url = !empty($transfer_meta_data['c-data']) ? $transfer_meta_data['c-data'] : '';
    $template .= '<div id="wordFile_url_err"></div>';
    $template .= '<input class="form-control" type="text" name="wordFilesUrl" value="' . $wordFile_url . '">';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '<div class="col-12 border-top mt-5">';
    $template .= '<div class="excellData_err_st_box"></div>';
    $template .= '<div class="row" id="transfer_product_meta_form_box"></div>';
    $template .= '<div class="col-12">';
    $template .= '<button name="excellData_save" class="btn btn-primary mx-auto d-block my-3">Save</button>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';

    echo $template;
}
?>
