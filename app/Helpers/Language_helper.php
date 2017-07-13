<?php

namespace App\Helpers;

use Carbon\Carbon;
use Emarref\Jwt\Encryption;
use Illuminate\Support\Facades\Config;
use App;

class Language_helper {

    public static function get_js_messages($label_page) {
        $localesArray = Config::get('app.locales');

        $errorMsgArray = array();
//        $errorMsgArray = ;

        $defaultLocale = App::getLocale();

        foreach ($localesArray as $tempLocaleCode => $tempLocale) {


            App::setLocale($tempLocaleCode);

            $langArray = trans($label_page . '.js_msgs');
            foreach ($langArray as $msgKey => $errorMsg) {
                $errorMsgArray[$msgKey][$tempLocaleCode] = $errorMsg;
            }
        }
        App::setLocale($defaultLocale);
        return $errorMsgArray;
    }

}
