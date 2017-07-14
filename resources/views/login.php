<!DOCTYPE html>

<?php
echo View::make('includes/header')->render();
?>
<div class="wrapper">
    <?php
    echo View::make('includes/top_menu')->render();
    ?>


    <!-- Full Width Column -->
    <div class="content-wrapper">
        <div class="container">
            <!-- Content Header (Page header) -->
            <section class="content-header">

            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="box box-info">


                            <div class="box-header with-border">
                                <div style="display: none" class='alert alert-danger' id='php_error_msg'></div>
                                <h3 class="box-title"><?php echo trans('login_page_lables.session_start') ?>  </h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form id="login_form" role="form"  method="post"  action="">
                                <input type="hidden" value="<?php echo csrf_token(); ?>" name="_token"/>

                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo trans('login_page_lables.email_address') ?> </label>
                                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Enter email">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1"><?php echo trans('login_page_lables.password') ?> </label>
                                        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
                                    </div>

                                </div><!-- /.box-body -->

                                <div class="box-footer">
                                    <!--<button type="submit" class="btn btn-info pull-right" id="login_button" ><?php echo trans('login_page_lables.sign_in') ?> </button>-->
                                    <button type="button" id="login_button" data-loading-text="Loading.."  onclick="logInFormValidation();" class="btn btn-info pull-right" ><?php echo trans('login_page_lables.sign_in') ?> </button>
                                </div>
                            </form>
                        </div><!-- /.box -->

                    </div>
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo trans('login_page_lables.membership_registration') ?> </h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form  id="register_form" class="form-horizontal" method="post"  action="<?php // echo url('login/register-user')                                                                               ?>"  enctype="multipart/form-data">

                                <input type="hidden" value="<?php echo csrf_token(); ?>" name="_token"/>
                                <?php
                                echo View::make('subviews/registration_subview')->render();
                                ?>

                                <!--</div> /.box-body -->
                                <div class="box-footer">
                                    <button type="button" onclick="regFormValidation();"   data-loading-text="Loading.."       id="registration_btn" class="btn btn-info pull-right"><?php echo trans('login_page_lables.register') ?> </button>
                                </div><!-- /.box-footer -->
                            </form>
                        </div>
                    </div>

                </div>
            </section><!-- /.content -->
        </div><!-- /.container -->
    </div><!-- /.content-wrapper -->
    <?php
    echo View::make('includes/footer')->render();
    ?>

</div><!-- ./wrapper -->
<?php
echo View::make('includes/footer_js');
?>
<script src="<?php echo url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>">
</script>
<script src="<?php echo url('assets/js/ajax_login.js'); ?>">
</script>
<script>


<?php
$js_error_array = array(
    'js_errors' => $js_errors
);
echo View::make('includes/js_translations', $js_error_array);
//echo View::make('includes/custom_js_validation.js');
?>

    //bootstrap WYSIHTML5 - text editor
    $(".textarea").wysihtml5({
        "link": false,
        "image": false,
    });

    // registration button click function call.
    function regFormValidation() {

        if ($('#register_form').valid()) {
            $("#registration_btn").button('loading');
            $('#first_name_error').html("");
            $('#last_name_error').html("");
            $('#contact_number_error').html("");
            $('#email_error').html("");
            $('#password_error').html("");
            $('#retype_password_error').html("");

            call_user_registration();
        }
    }

    //jQuery form validation for candidate registration.
    $(document).ready(function () {
        $('#register_form').validate({
            ignore: [],
            rules: {
                first_name: {
                    required: true,
                    maxlength: 50,
                },
                last_name: {
                    required: true,
                    maxlength: 50,
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: base_url('/login/check-uniq-email'),
                        type: "get",
                        data: {
//                                    user_id: function ()
//                                    {
//                                        return $('#user_id').val();
//                                    },
                        },
                    },
                },
                password: {
                    required: true,
                    minlength: 6
                },
                contact_number: {
                    required: true,
                },
                retype_password: {
                    required: true,
                    equalTo: "#password"
                },
            },
            messages: {
                email: {
                    remote: translationsArray['email_exist_error'][defaultLang],
                },
            },
            submitHandler: function (form) {
                
            }
        });
    });

    // User Registration function
    function call_user_registration() {
        var resume_file = $("#resume_file")[0].files[0];

        var user_info = "";
        var token = '<?php echo csrf_token() ?>';

        var user_info = new FormData();
        user_info.append("first_name", $("#first_name").val());
        user_info.append("last_name", $("#last_name").val());
        user_info.append("email", $("#email").val());
        user_info.append("password", $("#password").val());
        user_info.append("retype_password", $("#retype_password").val());
        user_info.append("contact_number", $("#contact_number").val());
        user_info.append("resume_text", $("#resume_text").val());
        user_info.append("_token", token);
        user_info.append("resume_file", resume_file);

        registration_function(user_info, registration_response, registration_error);
    }

    // AJAX error response function on candidate registration
    function registration_error(result) {
        $("#registration_btn").button('reset');
        if (typeof (result['required_error']) !== "undefined") {
            var error = JSON.parse(result['required_error']);
            if (typeof (error['last_name']) !== "undefined") {
                for (var i = 0; i < (error['last_name']).length; i++) {
                    $("#last_name_error").html(error['last_name'][i]);
                }
            }
            if (typeof (error['first_name']) !== "undefined") {
                for (var i = 0; i < (error['first_name']).length; i++) {
                    $("#first_name_error").html(error['first_name'][i]);
                }
            }
            if (typeof (error['email']) !== "undefined") {
                for (var i = 0; i < (error['email']).length; i++) {
                    $("#email_error").html(error['email'][i]);
                }
            }
            if (typeof (error['password']) !== "undefined") {
                for (var i = 0; i < (error['password']).length; i++) {
                    $("#password_error").html(error['password'][i]);
                }
            }
            if (typeof (error['retype_password']) !== "undefined") {
                for (var i = 0; i < (error['retype_password']).length; i++) {
                    $("#retype_password_error").html(error['retype_password'][i]);
                }
            }
        }

        toastr.warning(result['error']);
    }
    
    // AJAX success response function on candidate registration
    function registration_response(result) {
        $("#registration_btn").button('reset');
        toastr.success(result['success_msg']);
        var redirection_url = base_url('/home');
        window.location = redirection_url;
    }


    // Login form ajax function
    function logInFormValidation() {

        if ($('#login_form').valid()) {
            $('#login_button').button('loading');
            call_user_login();
        }
    }
    
    
    // Login form jQuery Validation.

    $(document).ready(function () {
        $('#login_form').validate({
            rules: {
                inputEmail: {
                    required: true,
                },
                inputPassword: {
                    required: true,
                },
            },
            messages: {
            },
            submitHandler: function (form) {
                

            }
        });
    });


    // on login button click function
    function call_user_login() {

        var user_info = "";
        var token = '<?php echo csrf_token() ?>';
        user_info += 'inputEmail=' + $("#inputEmail").val() +
                '&inputPassword=' + $("#inputPassword").val() + '&_token=' + token;

        login_function(user_info, login_response, login_error);

    }


    // AJAX response function for login form.
    function login_response(result) {
        $('#login_button').button('reset');
        toastr.success(result['success_msg']);
        var redirection_url = base_url('/home');
        window.location = redirection_url;
    }


    // AJAX error reponse function login form.
    function login_error(result) {
        $('#login_button').button('reset');
        $('#php_error_msg').html("");
        $('#php_error_msg').append(result['error']['error_message']);
        $('#php_error_msg').show();
        return false;
    }

</script>
</body>
</html>
