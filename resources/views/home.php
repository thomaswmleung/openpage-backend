<!DOCTYPE html>

<?php
$sessionInfo = Session::get('candidate_info');


echo View::make('includes/header')->render();
?>
<style>
    #registration_modal {
        overflow-y:scroll;
    }
</style>
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
                <span style="text-align: center"> 
                    <h1>
                      Welcome to home
                    </h1>
                </span>
                <span style="text-align: center"> 

                </span>

                <div class="box-body">
                    <div class="row">

                      
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



</body>
</html>
