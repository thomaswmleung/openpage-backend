<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\PageModel;
use DateTime;

class PageGroupModel extends Eloquent {

    protected $collection = 'page_group';
    protected $fillable = array('page', 'title', 'sub_title', 'subject', 'domain', 'subdomain', 'preview_url', 'teacher_copy_preview_url',
        'student_copy_preview_url', 'teacher_preview_image_array', 'parent_page_group_id', 'versions', 'affiliation', 'current_version_details',
        'student_preview_image_array', 'preview_image_array', 'created_by', 'layout', 'syllabus', 'level_of_difficulty', 'level_of_scaffolding',
        'codex', 'area', 'author', 'remark', 'particulars', 'learning_objective','syllabus_code','knowledge_unit',
        'copyright_content','copyright_artwork','copyright_photo','linkage',
        'row_reference','metadata');

    public function add_page_group($insert_data) {
        $result = PageGroupModel::create($insert_data);
        return $result;
    }

    public function create_page_group() {
        $result = PageGroupModel::create();
        return $result->_id;
    }

    public function update_page_group($update_data, $page_group_id) {
        $result = PageGroupModel::find($page_group_id)->update($update_data);
    }

    public function getRandomDocument() {
        return PageGroupModel::all()->first();
    }

    public function page_group_details($query_details = NULL) {
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
            if (isset($query_details['codex'])) {
                $codex = $query_details['codex'];
            } else {
                $codex = "";
            }
            if (isset($query_details['title'])) {
                $title = $query_details['title'];
            } else {
                $title = "";
            }
            if (isset($query_details['sub_title'])) {
                $sub_title = $query_details['sub_title'];
            } else {
                $sub_title = "";
            }
            $subject = "";
            if (isset($query_details['subject'])) {
                $subject = $query_details['subject'];
            }
            $domain = "";
            if (isset($query_details['domain'])) {
                $domain = $query_details['domain'];
            }
            $subdomain = "";
            if (isset($query_details['subdomain'])) {
                $subdomain = $query_details['subdomain'];
            }
            $level_of_difficulty = "";
            if (isset($query_details['level_of_difficulty'])) {
                $level_of_difficulty = $query_details['level_of_difficulty'];
            }
            $particulars = "";
            if (isset($query_details['particulars'])) {
                $particulars = $query_details['particulars'];
            }
            $learning_objective = "";
            if (isset($query_details['learning_objective'])) {
                $learning_objective = $query_details['learning_objective'];
            }
            $syllabus_code = "";
            if (isset($query_details['syllabus_code'])) {
                $learning_objective = $query_details['syllabus_code'];
            }
            if (isset($query_details['created_by'])) {
                $created_by = $query_details['created_by'];
            } else {
                $created_by = "";
            }
        }

        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];

        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "" || $subject != "" || $domain != "" || $subdomain != "" || $codex != "" || $learning_objective != "" || $particulars != "" || $level_of_difficulty != "" || $syllabus_code != "") {
            $page_group_data = PageGroupModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['codex'] != "") {
                            $filterByQuery->where('codex', 'like', $query_details['codex']);
                        }
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['sub_title'] != "") {
                            $filterByQuery->where('sub_title', 'like', $query_details['sub_title']);
                        }
                        if ($query_details['subject'] != "") {
                            $filterByQuery->where('subject', 'like', $query_details['subject']);
                        }
                        if ($query_details['domain'] != "") {
                            $filterByQuery->where('domain', 'like', $query_details['domain']);
                        }
                        if ($query_details['subdomain'] != "") {
                            $filterByQuery->where('subdomain', 'like', $query_details['subdomain']);
                        }
                        if ($query_details['created_by'] != "") {
                            $filterByQuery->where('created_by', 'like', $query_details['created_by']);
                        }
                        if ($query_details['level_of_difficulty'] != "") {
                            $filterByQuery->where('level_of_difficulty', '=', $query_details['level_of_difficulty']);
                        }
                        if ($query_details['particulars'] != "") {
                            $filterByQuery->where('particulars', 'like', $query_details['particulars']);
                        }
                        if ($query_details['learning_objective'] != "") {
                            $learningObjectiveArray = array($query_details['learning_objective']);
                            $filterByQuery->where('learning_objective', 'all', $learningObjectiveArray);
                        }
                        if ($query_details['syllabus_code'] != "") {
                            $filterByQuery->where('syllabus_code', 'like', $query_details['syllabus_code']);
                        }

                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $stop_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('title', 'like', "%$search_key%")
                            ->orWhere('sub_title', 'like', "%$search_key%")
                            ->orWhere('subject', 'like', "%$search_key%")
                            ->orWhere('domain', 'like', "%$search_key%")
                            ->orWhere('syllabus', 'like', "%$search_key%")
                            ->orWhere('syllabus.knowledge_unit', 'like', "%$search_key%")
                            ->orWhere('level_of_difficulty', 'like', "%$search_key%")
                            ->orWhere('codex', 'like', "%$search_key%")
                            ->orWhere('level_of_difficulty', 'like', "%$search_key%")
                            ->orWhere('particulars', 'like', "%$search_key%")
                            ->orWhere('learning_objective', 'like', "%$search_key%")
                            ->orWhere('syllabus_code', 'like', "%$search_key%")
                            ->orWhere('area', 'like', "%$search_key%")
                            ->orWhere('author', 'like', "%$search_key%")
                            ->orWhere('remark', 'like', "%$search_key%")
                            ->orWhere('subdomain', 'like', "%$search_key%");
                        }
                    })
                    ->Where('parent_page_group_id', '=', NULL)
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $page_group_data = PageGroupModel::skip($skip)->Where('parent_page_group_id', '=', NULL)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $page_group_data;
    }

    public function total_count($query_details) {

        $search_key = "";
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        if (isset($query_details['codex'])) {
            $codex = $query_details['codex'];
        } else {
            $codex = "";
        }
        $title = "";
        if (isset($query_details['title'])) {
            $title = $query_details['title'];
        }
        $sub_title = "";
        if (isset($query_details['sub_title'])) {
            $sub_title = $query_details['sub_title'];
        }
        $subject = "";
        if (isset($query_details['subject'])) {
            $subject = $query_details['subject'];
        }
        $domain = "";
        if (isset($query_details['domain'])) {
            $domain = $query_details['domain'];
        }
        $subdomain = "";
        if (isset($query_details['subdomain'])) {
            $subdomain = $query_details['subdomain'];
        }
        $from_date = "";
        if (isset($query_details['from_date'])) {
            $from_date = $query_details['from_date'];
        }
        $to_date = "";
        if (isset($query_details['to_date'])) {
            $to_date = $query_details['to_date'];
        }
        $created_by = "";
        if (isset($query_details['created_by'])) {
            $created_by = $query_details['created_by'];
        }
        $level_of_difficulty = "";
        if (isset($query_details['level_of_difficulty'])) {
            $level_of_difficulty = $query_details['level_of_difficulty'];
        }
        $particulars = "";
        if (isset($query_details['particulars'])) {
            $particulars = $query_details['particulars'];
        }
        $learning_objective = "";
        if (isset($query_details['learning_objective'])) {
            $learning_objective = $query_details['learning_objective'];
        }
        $syllabus_code = "";
        if (isset($query_details['syllabus_code'])) {
            $learning_objective = $query_details['syllabus_code'];
        }
        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "" || $subject != "" || $domain != "" || $subdomain != "" || $codex != "" || $learning_objective != "" || $particulars != "" || $level_of_difficulty != "" || $syllabus_code != "") {
            $total_count = PageGroupModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['codex'] != "") {
                            $filterByQuery->where('codex', 'like', $query_details['codex']);
                        }
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['sub_title'] != "") {
                            $filterByQuery->where('sub_title', 'like', $query_details['sub_title']);
                        }
                        if ($query_details['created_by'] != "") {
                            $filterByQuery->where('created_by', 'like', $query_details['created_by']);
                        }
                        if ($query_details['subject'] != "") {
                            $filterByQuery->where('subject', 'like', $query_details['subject']);
                        }
                        if ($query_details['domain'] != "") {
                            $filterByQuery->where('domain', 'like', $query_details['domain']);
                        }
                        if ($query_details['subdomain'] != "") {
                            $filterByQuery->where('subdomain', 'like', $query_details['subdomain']);
                        }
                        if ($query_details['level_of_difficulty'] != "") {
                            $filterByQuery->where('level_of_difficulty', '=', $query_details['level_of_difficulty']);
                        }
                        if ($query_details['particulars'] != "") {
                            $filterByQuery->where('particulars', 'like', $query_details['particulars']);
                        }
                        if ($query_details['learning_objective'] != "") {
                            $learningObjectiveArray = array($query_details['learning_objective']);
                            $filterByQuery->where('learning_objective', 'all', $learningObjectiveArray);
                        }
                        if ($query_details['syllabus_code'] != "") {
                            $filterByQuery->where('syllabus_code', 'like', $query_details['syllabus_code']);
                        }
                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $stop_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('title', 'like', "%$search_key%")
                            ->orWhere('sub_title', 'like', "%$search_key%")
                            ->orWhere('subject', 'like', "%$search_key%")
                            ->orWhere('domain', 'like', "%$search_key%")
                            ->orWhere('syllabus', 'like', "%$search_key%")
                            ->orWhere('syllabus.knowledge_unit', 'like', "%$search_key%")
                            ->orWhere('codex', 'like', "%$search_key%")
                            ->orWhere('level_of_difficulty', 'like', "%$search_key%")
                            ->orWhere('particulars', 'like', "%$search_key%")
                            ->orWhere('learning_objective', 'like', "%$search_key%")
                            ->orWhere('syllabus_code', 'like', "%$search_key%")
                            ->orWhere('area', 'like', "%$search_key%")
                            ->orWhere('author', 'like', "%$search_key%")
                            ->orWhere('remark', 'like', "%$search_key%")
                            ->orWhere('subdomain', 'like', "%$search_key%");
                        }
                    })
                    ->Where('parent_page_group_id', '=', NULL)
                    ->count();
        } else {
            $total_count = PageGroupModel::Where('parent_page_group_id', '=', NULL)->count();
        }
        return $total_count;
    }

    public function find_page_group_details($page_group_id) {
        $page_group_info = PageGroupModel::find($page_group_id);

        if ($page_group_info != null) {
            $pagesArray = $page_group_info->page;
//            \Illuminate\Support\Facades\Log::error(json_encode($pagesArray));
            $page_detail_array = array();
            if (is_array($pagesArray)) {
                foreach ($pagesArray as $page_id) {
                    $page_detail = PageModel::get_page_details($page_id);
                    if ($page_detail != NULL) {
                        array_push($page_detail_array, $page_detail);
                    }
                }
            }
            $page_group_info->page = $page_detail_array;
        }

        return $page_group_info;
    }

    public function version_update($page_group_id, $version_data) {
        $result = PageGroupModel::where('_id', $page_group_id)
                ->push('versions', $version_data);
        return $result;
    }

    public function affiliation_update($page_group_id, $affiliation_data) {
        $result = PageGroupModel::where('_id', $page_group_id)
                ->push('affiliation', $affiliation_data);
        return $result;
    }
    
    public function search_page_group($search_data) {
        $result = PageGroupModel::where($search_data)->first();
        return $result;
    }
    
    public function update_version_data($page_group_id,$versionArray,$arrayIndex) {
        $field_name = 'versions.'.$arrayIndex;
        $result = PageGroupModel::where('_id', $page_group_id)->update(array($field_name => $versionArray));
    }
    
    public function remove_version_data($page_group_id, $version_id) {
        $result = PageGroupModel::where('_id', $page_group_id)
                ->pull("versions",['version_id'=>$version_id]);
        return $result;
    }

}
