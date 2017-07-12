<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo Config::get('constants.Project Name'); ?></title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <?php
        echo View::make('includes/css_header');
        ?>
        <link href="<?php echo url('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css'); ?>" rel="stylesheet" type="text/css" />

    </head>


    <body lang="<?php echo App::getLocale(); ?>" class="hold-transition skin-blue layout-top-nav">

        