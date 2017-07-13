<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu">
            <!-- Optionally, you can add icons to the links -->
            <li class=""><a href="<?php echo url('home'); ?>"><i class="fa fa-link"></i> <span><?php echo trans('generic_lables.dashboard') ?></span></a></li>

            <!--contacts-->
            <?php if (!isset($access_modules['contacts']) || isset($access_modules) && $access_modules['contacts'] == "1") { ?>
                <?php if (isset($role) && $role != Config::get('constants.recruiter') && $role != Config::get('constants.survey_user')) { ?>
                    <li class="<?php
                    if (isset($main_menu) && $main_menu == 'UsersDetail') {
                        echo "active";
                    }
                    ?>">
                        <a href="#"><i class="fa fa-bars"></i><span><?php echo trans('generic_lables.users') ?> </span><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li <?php
                            if (isset($sub_menu) && $sub_menu == 'RecruitersList') {
                                echo "class='active'";
                            }
                            ?>><a href="<?php echo url('user'); ?>"><i class="fa fa-circle-o"></i><?php echo trans('generic_lables.recruiters') ?> </a> </li>

                        </ul>
                    </li>
                <?php } ?>
                <?php
            }
            ?>
            <?php if (!isset($access_modules['contacts']) || isset($access_modules) && $access_modules['contacts'] == "1") { ?>
                <?php // if (isset($role) && $role != Config::get('constants.recruiter')) { ?>
                    <li class="<?php
                    if (isset($main_menu) && $main_menu == 'ManageJob') {
                        echo "active";
                    }
                    ?>">
                        <a href="#"><i class="fa fa-bars"></i><span><?php echo trans('generic_lables.manage_job') ?></span><i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu">
                            <li <?php
                            if (isset($sub_menu) && $sub_menu == 'CreateJob') {
                                echo "class='active'";
                            }
                            ?>><a href="<?php echo url('job/create-job'); ?>"><i class="fa fa-circle-o"></i><?php echo trans('generic_lables.create_job') ?></a> </li>
                            <li <?php
                            if (isset($sub_menu) && $sub_menu == 'JobList') {
                                echo "class='active'";
                            }
                            ?>><a href="<?php echo url('job'); ?>"><i class="fa fa-circle-o"></i><?php echo trans('generic_lables.jobs') ?> </a> </li>

                        </ul>
                    </li>
                <?php // } ?>
                <?php
            }
            ?>

        </ul><!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>