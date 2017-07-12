<input type="hidden" id="hidden_base_url" value="<?php echo url(); ?>" />
<!-- jQuery 2.1.4 -->
<script src="<?php echo url('assets/plugins/jQuery/jQuery-2.1.4.min.js'); ?>"></script>
<!-- Bootstrap 3.3.5 -->
<script src="<?php echo url('assets/js/bootstrap.min.js'); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo url('assets/js/app.min.js'); ?>"></script>
<!-- Custom JS for toastr -->
<script src="<?php echo url('assets/js/custom_js.js'); ?>"></script>
<script src="<?php echo url('assets/plugins/toastr/toastr.js'); ?>"></script>
<!-- Slimscroll -->
<script src="<?php echo url('assets/plugins/slimScroll/jquery.slimscroll.min.js'); ?>"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo url('assets/plugins/jasny-bootstrap/js/jasny-bootstrap.min.js'); ?>"></script>
<script src="<?php echo url('assets/plugins/bootstrap/js/bootbox.min.js'); ?>"></script>

<script src="<?php echo url('assets/js/validation.js'); ?>"></script>
<script src="<?php echo url('assets/js/custom_validation.js'); ?>"></script>

<script>



    function base_url(arg)
    {
        var base_url = document.getElementById('hidden_base_url').value;
        return base_url + arg;
    }

    $(document).ready(function () {
        var msg = "<?php echo Session::get('success'); ?>";
        if (msg != "") {
            show_success(msg);
        }
    });


    $(document).ready(function () {
        var error = "<?php echo Session::get('error'); ?>";
        if (error != "") {
            show_error(error);
        }
    });

    $(document).ready(function () {
        var warning = "<?php echo Session::get('warning'); ?>";
        if (warning != "") {
            show_warning(warning);
        }

    });


</script>
