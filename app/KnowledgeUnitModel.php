<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class KnowledgeUnitModel extends Eloquent {

    protected $collection = 'knowledge_unit';
    protected $fillable = array('code', 'title');

    
    public function create_knowledge_unit($insert_data) {
        $result = KnowledgeUnitModel::create($insert_data);
        return $result->_id;
    }   
    
    public function find_knowledge_unit_details($knowledge_unit_id) {
        $knowledge_unit_info = KnowledgeUnitModel::find($knowledge_unit_id);
        return $knowledge_unit_info;
    }
    
    public function create_or_update_knowledge_unit($insert_data, $id) {
        $result = KnowledgeUnitModel::updateOrCreate(['_id' => $id], $insert_data);
        return $result->_id;
    }
    
    public function knowledge_unit_details($query_details = NULL) {

        if ($query_details == NULL) {
            $skip = 0;
            $limit = config('constants.default_query_limit');
            $search_key = "";
        } else {
            if (isset($query_details['skip'])) {
                $skip = $query_details['skip'];
            } else {
                $skip = 0;
            }
            if (isset($query_details['limit'])) {
                $limit = $query_details['limit'];
            } else {
                $limit = config('constants.default_query_limit');
            }
            if (isset($query_details['search_key'])) {
                $search_key = $query_details['search_key'];
            } else {
                $search_key = "";
            }
        }

        if ($search_key != "") {
            $knowledge_unit_data = KnowledgeUnitModel::
                    where('title', 'like', "%$search_key%")
                    ->orWhere('code', 'like', "%$search_key%")
                    ->skip($skip)
                    ->take($limit)
                    ->get();
        } else {
            $knowledge_unit_data = KnowledgeUnitModel::skip($skip)->take($limit)->get();
        }
        return $knowledge_unit_data;
    }

    public function total_count($search_key) {
        if ($search_key != "") {
            $total_count = KnowledgeUnitModel::where('title', 'like', "%$search_key%")
                    ->orWhere('code', 'like', "%$search_key%")
                    ->count();
        } else {
            $total_count = KnowledgeUnitModel::count();
        }
        return $total_count;
    }

}
