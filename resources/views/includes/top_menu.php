<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container">
            <div class="navbar-header">
                <a href="<?php echo url('home') ?>" class="navbar-brand"><b><?php echo Config::get('constants.Project Name'); ?></b></a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">

                </ul>
               
            </div><!-- /.navbar-collapse -->
            <!-- Navbar Right Menu -->

            <?php
            $del = TRUE;
            $user_info = Session::get('user_info');
//            var_dump($user_info);
            if (!empty($user_info) && $user_info != null) {
                ?>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">

                        <!-- User Account Menu -->
                        <li class="dropdown user user-menu">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <?php
                                $url = Config::get('constants.rest_api_url') . '/admin_profile_pic/default.png';
                                ?>
                                <!-- The user image in the navbar-->
                                <span style="margin-right: 15px"> <?php echo $user_info['first_name'] . ' ' . $user_info['last_name']; ?></span>
                                <img src="<?php echo $url; ?>" class="user-image pull-right" alt="User Image">
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs"><?php
//                                    $admin_info = Session::get('admin_info');
                                    ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- The user image in the menu -->
                                <li class="user-header">

                                    <img src="<?php echo $url; ?>" class="img-circle" alt="User Image">
                                    <p>
                                        <?php echo $user_info['first_name'] . ' ' . $user_info['last_name'] ?>
                                    </p>
                                </li>

                                <!-- Menu Footer-->
                                <li class="user-footer">
                    
                                    <div class="pull-right">
                                        <a href="<?php echo url('login/logout'); ?>" class="btn btn-default btn-flat">Sign Out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>

            <?php } else { ?>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="no-bg login-btn-no-bg"><a href="<?php echo url('login') ?>" class="login-header-btn">
                                <i class="fa fa-sign-in"></i><font><font class=""> Sign In</font></font></a>
                        </li>
                    </ul>
                </div>
            <?php } ?>

        </div><!-- /.container-fluid -->
    </nav>
</header>