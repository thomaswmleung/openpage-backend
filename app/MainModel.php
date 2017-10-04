<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\SectionModel;

class MainModel extends Eloquent {

    protected $collection = 'main';
    protected $fillable = array('header_text', 'footer_text', 'section');

    public function add_main($insert_data, $main_id) {
        //$result = MainModel::create($insert_data);
        $result = MainModel::updateOrCreate(
                        ['_id' => $main_id], $insert_data
        );
        return $result;
    }

    public static function get_main_details($main_id) {
        $main_details = MainModel::find($main_id);

        if ($main_details != NULL) {
            $sectionArray = $main_details->section;
            $section_details = array();
            foreach ($sectionArray as $section_id) {
                $details = SectionModel::get_section_details($section_id);
                array_push($section_details, $details);
            }
            $main_details->section = $section_details;
        }
        return $main_details;
    }

}
