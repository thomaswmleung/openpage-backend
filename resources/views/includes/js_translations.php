<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

var translationsArray = new Array();
var defaultLang = <?php echo "'" . App::getLocale() . "'" ?>;


<?php
if (isset($js_errors) AND sizeof($js_errors) > 0) {
    foreach ($js_errors as $key => $value) {
        ?>


        var error_key = <?php echo "'" . $key . "'"; ?>;

        translationsArray[error_key] = new Array();
        translationsArray[error_key]['en'] = <?php echo "'" . $value['en'] . "'" ?>;
        translationsArray[error_key]['es'] = <?php echo "'" . $value['es'] . "'" ?>;



        <?php
    }
}