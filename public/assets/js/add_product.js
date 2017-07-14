var deletedImages = new Array();

$(".select2").select2();

$(function () {
    //bootstrap WYSIHTML5 - text editor
    $(".textarea").wysihtml5();
});

$("#text_cutomize_checkbox").on("ifChanged", cutomize_text);
$("#image_cutomize_checkbox").on("ifChanged", cutomize_image);
$("#image_text_allowed_checkbox").on("ifChanged", cutomize_image_text);
$("#is_in_stock").on("ifChanged", display_stock_qty);

function display_stock_qty() {
    var is_checked = document.getElementById('is_in_stock').checked;
    if (is_checked) {
        $("#stock_qty_content").slideDown("slow");
    } else {
        $("#stock_qty_content").slideUp("slow");
    }
}

var count = 2;
var img_rule_count = 2;

function add_rule(type) {
    var result_string = '<div class="well clearfix" id="rule_' + count + '">';

    result_string += '<div class="row">';
    result_string += '<div class="col-md-12">';
    result_string += "<i class='fa fa-close pull-right' style='border: 2px solid #F70000;color: #0A0A0A;padding-left: 2px;padding-right: 2px;' onclick='clear_rule(\"" + count + "\",\"text\")'></i>";
    result_string += '</div>';
    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Label<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="label[]" id="label_' + count + '" class="form-control text_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Min text length<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_text_length[]" id="min_text_length' + count + '" class="form-control text_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max text length<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_text_length[]" id="max_text_length_' + count + '" class="form-control text_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max No of rows<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_no_rows[]" id="max_no_rows_' + count + '" class="form-control text_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div></div>';

    $('#text_cutomize_content').append(result_string);
    count++;

//    $("#add_product_form").validate().resetForm();

    $('.text_rules').each(function () {
        $(this).rules("add", {
            required: true
        });
    });

}


function add_image_rule() {
    var result_string = '<div class="well clearfix" id="img_rule_' + img_rule_count + '">';

    result_string += '<div class="row">';
    result_string += '<div class="col-md-12">';
    result_string += "<i class='fa fa-close pull-right' style='border: 2px solid #F70000;color: #0A0A0A;padding-left: 2px;padding-right: 2px;' onclick='clear_rule(\"" + img_rule_count + "\",\"img\")'></i>";
    result_string += '</div>';
    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label class="control-label">Min Size (MB)<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_image_size[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max Size (MB)<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_image_size[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Min Height<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_image_height[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max Height<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_image_height[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Min Width<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_image_width[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max Width<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_image_width[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Min no of Images<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_no_of_images[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Max no of Images<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_no_of_images[]" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Label<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="image_label[]" class="form-control text_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '<div class="row">';
    result_string += '<div class="col-md-9 form-group">';
    result_string += '<label><input id="image_text_allowed_checkbox_' + img_rule_count + '" name="image_text_allowed_checkbox_' + img_rule_count + '" type="checkbox" onclick="cutomize_image_text(' + img_rule_count + ')" value="true"> Is text allowed</label>';
    result_string += '</div>';
    result_string += '</div>';

    result_string += '<div class="row" id="image_text_cutomize_content_' + img_rule_count + '" style="display: none;">';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="title" class="control-label">Min Character Length<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="min_image_text_length_' + img_rule_count + '" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '<div class="col-md-6 form-group">';
    result_string += '<label for="max_image_text_length">Max Character Length<span style="color:red">*</span></label>';
    result_string += '<input type="text" name="max_image_text_length_' + img_rule_count + '" class="form-control img_rules" value="" />';
    result_string += '<span style="color:red"></span>';
    result_string += '</div>';

    result_string += '</div>';

    result_string += '</div>';


    $('#image_cutomize_content').append(result_string);
    img_rule_count++;

//    $("#add_product_form").validate().resetForm();

    $(".img_rules").each(function () {
        $(this).rules("add", {
            required: true
        });
    });

}

function clear_rule(id, type) {

    if (type == 'text') {
        $("#rule_" + id).remove();
    }

    if (type == 'img') {
        $("#img_rule_" + id).remove();
        
        
        alert(img_rule_count);
        alert(id);
        id = parseInt(parseInt(id) + parseInt(1));
        for (var i = id; i < img_rule_count; i++) {
            new_id = parseInt(parseInt(i) - parseInt(1));
            if (document.getElementById("image_text_allowed_checkbox_" + i)) {
                var content = document.getElementById("image_text_cutomize_content_" + i);
                content.id = "image_text_cutomize_content_" + new_id;

                var field = document.getElementById("image_text_allowed_checkbox_" + i);
                field.name = "image_text_allowed_checkbox_" + new_id;
                field.id = "image_text_allowed_checkbox_" + new_id;

                document.getElementById("image_text_allowed_checkbox_" + new_id).onclick = function () {
                    
                    cutomize_image_text(new_id);
                    
                    // Set all future clicks to subscribe s
                    this.onclick = function () {
                        cutomize_image_text(new_id);
                    };
                };

            }
        }
        
        img_rule_count--;
        
    }

}

function cutomize_text() {
    var is_checked = document.getElementById('text_cutomize_checkbox').checked;
    if (is_checked) {
        $("#text_cutomize_content").slideDown("slow");
    } else {
        $("#text_cutomize_content").slideUp("slow");
    }
}

function cutomize_image() {
    var is_checked = document.getElementById('image_cutomize_checkbox').checked;
    if (is_checked) {
        $("#image_cutomize_content").slideDown("slow");
    } else {
        $("#image_cutomize_content").slideUp("slow");
    }
}

function cutomize_image_text(id) {
    alert(id);
    var is_checked = document.getElementById('image_text_allowed_checkbox_' + id).checked;
    if (is_checked) {
        $("#image_text_cutomize_content_" + id).slideDown("slow");
    } else {
        $("#image_text_cutomize_content_" + id).slideUp("slow");
    }
}

//iCheck for checkbox and radio inputs
$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
    checkboxClass: 'icheckbox_minimal-blue',
    radioClass: 'iradio_minimal-blue'
});

$(document).ready(function () {
    $("#add_product_form").validate({
        ignore: ":hidden:not(textarea)",
        rules: {
            product_name: {
                required: true
            },
            valid_for: {
                required: true
            },
            occasion: {
                required: true
            },
            price: {
                required: true
            },
            short_description: {
                required: true
            },
            description: {
                required: true
            },
            'product_image[]': {
                required: true
            },
            stock_quantity: {
                required: true
            }
        },
        errorPlacement: function (error, element) {
            console.log(element.attr("name"));
            if (element.attr("name") == 'valid_for[]') {
                error.insertAfter("#valid_for_error");
            }
            else if (element.attr("name") == 'occasion[]') {
                error.insertAfter("#occasion_error");
            }
            else if (element.attr("name") == 'product_image[]') {
                error.insertAfter("#image_error");
            }
            else {
                error.insertAfter(element);
            }
        },
        messages: {
//                                                                product_name: {
//                                                                    required: ""
//                                                                }
        },
        submitHandler: function (form) {
            if (document.getElementById('hidden_operation').value == 'UPDATE') {
                if (deletedImages.length > 0) {
                    document.getElementById('hidden_deleted_images').value = deletedImages;
                }
            }
//            console.log(deletedImages);
//            return false;
            form.submit();
        }

    });

    $(".text_rules").each(function () {
        $(this).rules("add", {
            required: true
        });
    });

    $(".img_rules").each(function () {
        $(this).rules("add", {
            required: true
        });
    });

});

jQuery(document).ready(function () {
    $('.fileinput').on('clear.bs.fileinput', function (event) {
        if (event.target.id != "" && event.target.id != null && event.target.id != 'undefined') {
            if (!inArray(event.target.id, deletedImages)) {
                deletedImages.push(event.target.id);
            }
        }
    });
});

jQuery(document).ready(function () {
    $('.fileinput').on('change.bs.fileinput', function (event) {
        var id = event.target.id;

        if (id) {

            var idLength = "path".length;
            var image_id = id.substring(idLength);
            var element = document.getElementById('category_image_' + image_id);
            var size_in_bytes = element.files[0].size;
            var size_in_kb = parseFloat(size_in_bytes / 1024);
            if (size_in_kb > 1024) {
                show_error("Maximum upload size is 1MB");
                $('#' + id).fileinput('reset');
            } else {
                if (!inArray(event.target.id, deletedImages)) {
                    deletedImages.push(event.target.id);
                }
            }

        }
    });
});

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}