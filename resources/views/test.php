<html>
    <head>
        <title></title>
    </head>
    <body>
        <script>
            function addBulkUploadProjects() {

                var json_caste = document.getElementById('hidden_caste').value;
                var json_department = document.getElementById('hidden_department').value;
                var json_title = document.getElementById('hidden_title').value;
                var json_beneficiary_projects = document.getElementById('hidden_beneficiary_projects').value;
                var json_sanctioned_year = document.getElementById('hidden_sanctioned_year').value;
                var caste = JSON.parse(json_caste);
                var caste_array = [];
                for (var x in caste) {
                    caste_array.push(caste[x]);
                }

                var departments = JSON.parse(json_department);
                var department_array = [];
                for (var x in departments) {
                    department_array.push(departments[x]);
                }
                var titles = JSON.parse(json_title);
                var title_array = [];
                for (var x in titles) {
                    title_array.push(titles[x]);
                }

                var beneficiary_projects = JSON.parse(json_beneficiary_projects);
                var beneficiary_projects_array = [];
                for (var x in beneficiary_projects) {
                    beneficiary_projects_array.push(beneficiary_projects[x]);
                }

                var sanctioned_year = JSON.parse(json_sanctioned_year);
                var sanctioned_year_array = [];
                for (var x in sanctioned_year) {
                    sanctioned_year_array.push(sanctioned_year[x]);
                }


                $('#AddBulkUploadProject').attr('disabled', true);
                $('#AddBulkUploadProject').html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
//                                       
                var count = $('#form_count').val();
                var i = parseInt(count) + 1;
                var limit = parseInt(count) + 4;
                for (i; i <limit; i++) {

                    var new_count = parseInt(count) + (i - 2);
                    alert(new_count);
                    var subview = "";
                    subview += '<div class=" box box-body"><div class = " form-group" ><form method = "post" action = "" id = "addProject_<?php echo $i; ?>" >';
                    subview += '<input type = "hidden" value = "<?php echo csrf_token(); ?>" name = "_token" / >';
                    subview += '<input type = "hidden" name = "index" id = "index" value = "<?php echo $i; ?>" / >';
                    subview += '<div class = "row" >';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಶಿರ್ಷಕೆ: </label></div >';
                    subview += '<div class = "col-md-9" ><select class = "form-control" name = "title" id = "title" ><option value = "" > ಆಯ್ಕೆ ಬದಲಾಯಿಸಿ... </option>';
                    for (var j = 0; j <title_array.length; j++) {
                        subview += '<option value = "' + title_array[j]['_id'] + '" >"' + title_array[j]['calculate_title_name'] + '"</option>';
                    }
                    subview += '</select></div><span class = "help-block" ></span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಗ್ರಾಮ: </label></div >';
                    subview += '<div class = "col-md-9" ><input type = "text" class = "form-control" name = "village" id = "village" value = "" placeholder = " ಗ್ರಾಮ " ></div>';
                    subview += '<span class = "help-block" ></span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಫಲಾನುಭವಿಗಳ ಹೆಸರು: </label></div >';
                    subview += '<div class = "col-md-9" ><input type = "text" class = "form-control" name = "beneficiaries_name" id = "beneficiaries_name" value = "" placeholder = " ಫಲಾನುಭವಿಗಳ ಹೆಸರು " ></div>';
                    subview += '<span class = "help-block" > </span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಜಾತಿ / ಉಪಜಾತಿ: </label></div >';
                    subview += '<div class = "col-md-9" ><select class = "form-control" name = "caste" id = "caste" ><option value = "" > ಆಯ್ಕೆ ಬದಲಾಯಿಸಿ... </option>';
                    for (var j = 0; j <caste_array.length; j++) {
                        subview += '<option value = "' + caste_array[j]['_id'] + '" >"' + caste_array[j]['caste_name'] + '"</option>';
                    }
                    subview += '</select></div><span class = "help-block" ></span></div></div>';
                    subview += '<div class = "row" >';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಮಂಜೂರಾದ ವಷ೵: </label></div >';
                    subview += '<div class = "col-md-9" ><select class = "form-control" name = "sanctioned_year" id = "sanctioned_year" ><option value = "" > ಆಯ್ಕೆ ಬದಲಾಯಿಸಿ... </option>';
                    for (var j = 0; j <sanctioned_year_array.length; j++) {
                        subview += '<option value = "' + sanctioned_year_array[j]['_id'] + '" >"' + sanctioned_year_array[j]['sanctioned_year'] + '"</option>';
                    }

                    subview += '</select></div><span class = "help-block" > </span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಫಲಾನುಭವಿ ವಂತಿಗೆ ಮೊತ್ತ: </label></div >';
                    subview += '<div class = "col-md-9" ><input type = "text" class = "form-control" name = "beneficiary_contribution" id = "beneficiary_contribution" value = "" placeholder = "ಫಲಾನುಭವಿ ವಂತಿಗೆ ಮೊತ್ತ" ></div>';
                    subview += '<span class = "help-block" ></span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಮಂಜೂರಾದ ಸಹಾಯಧನ: </label></div >';
                    subview += '<div class = "col-md-9" > <input type = "text" class = "form-control" name = "approved_subsidy" id = "approved_subsidy" value = "" placeholder = "ಮಂಜೂರಾದ ಸಹಾಯಧನ " > </div>';
                    subview += '<span class = "help-block" > </span></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಒಟ್ಟು ಮೊತ್ತ: </label></div >';
                    subview += '<div class = "col-md-9" > <input type = "text" class = "form-control" name = "total_sum" id = "total_sum" value = "" placeholder = "ಒಟ್ಟು ಮೊತ್ತ" > </div>';
                    subview += '<span class = "help-block" > </span></div></div>';
                    subview += '<div class = "row" >';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಮುಕ್ತಾಯ ದಿನಾಂಕ: </label></div >';
                    subview += '<div class = "col-md-8 " ><input class = "form-control  datepicker" id = "expiration_date"  name = "expiration_date" type = "text" placeholder = "ಮುಕ್ತಾಯ ದಿನಾಂಕ" / ></div>';
                    subview += '<div class = "col-sm-1" ><span class = " help-block" > <i class = "fa fa-calendar" style = "margin-left:-20px" ></i></span></div></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಖರ್ಚು : </label></div >';
                    subview += '<div class = "col-md-9" ><input class = "form-control" name = "expense" id = "expense" value = "" placeholder = "ಖರ್ಚು" >';
                    subview += '<span class = " help-block" ></span></div></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಪ್ರಗತಿ ವಿವರ: </label></div >';
                    subview += '<div class = "col-md-9" > <input class = "form-control" name = "progress_detail" id = "progress_detail" value = "" placeholder = "ಪ್ರಗತಿ ವಿವರ " >';
                    subview += '<span class = "help-block" > </span></div></div>';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಷರಾ: </label></div >';
                    subview += '<div class = "col-md-9" > <input type = "text" class = "form-control" name = "sara" id = "sara" value = "" placeholder = "ಷರಾ" > </div>';
                    subview += '<span class = "help-block" > </span></div></div>';
                    subview += '<div class = "row" >';
                    subview += '<div class = "form-group col-md-3" ><div class = "col-md-3" > <label class = "control-label" > ಇಲಾಖೆ: </label></div >';
                    subview += '<div class = "col-md-9 " ><select class = "form-control" name = "department" id = "department" ><option value = "" > ಆಯ್ಕೆ  ಬದಲಾಯಿಸಿ... </option>';
                    for (var j = 0; j <department_array.length; j++) {
                        subview += '<option value = "' + department_array[j]['_id'] + '" >"' + department_array[j]['department_name'] + '"</option>';
                    }
                    subview += '</select><span class = " help-block" > </span> </div></div></div>';

                    subview += '<div class = "col-md-12" >';
                    subview += '<button class = "btn btn-success add_project" type = "submit" id = "SaveBulkUploadProject_addProject_<?php echo $i; ?>" > <i class = "fa fa-floppy-o" > </i> Save Project</button >';
                    subview += '<div id = "SavedBulkUploadProject_addProject_<?php echo $i; ?>" > </div></div>';

                    subview += '</form></div></div>';


                    $('#form_count').val(parseInt(new_count) + i);
                    $('#bulkProject').append(subview);
                    $('.datepicker').datepicker({
                        format: "dd-mm-yyyy",
                        todayBtn: true,
                    });
                }
                $('#AddBulkUploadProject').attr('disabled', false);
                $('#AddBulkUploadProject').html('Add Few More Projects');
//                                            




            }
        </script>

    </body>
</html>

