<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\PageModel;
use DateTime;
class PageGroupModel extends Eloquent {

    protected $collection = 'page_group';
    protected $fillable = array('page', 'title', 'sub_title', 'preview_url', 'teacher_copy_preview_url',
                                                'student_copy_preview_url','teacher_preview_image_array',
                                                'student_preview_image_array','preview_image_array', 'created_by', 'layout', 'syllabus');

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

        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "") {
            $page_group_data = PageGroupModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['sub_title'] != "") {
                            $filterByQuery->where('sub_title', 'like', $query_details['sub_title']);
                        }
                        if ($query_details['created_by'] != "") {
                            $filterByQuery->where('created_by', 'like', $query_details['created_by']);
                        }

                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $stop_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('preview_url', 'like', "%$search_key%")
                            ->orWhere('title', 'like', "%$search_key%")
                            ->orWhere('sub_title', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $page_group_data = PageGroupModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $page_group_data;
    }

    public function total_count($query_details) {

        $search_key = "";
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        $title = "";
        if (isset($query_details['title'])) {
            $title = $query_details['title'];
        }
        $sub_title = "";
        if (isset($query_details['sub_title'])) {
            $sub_title = $query_details['sub_title'];
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

        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "") {
            $total_count = PageGroupModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['title'] != "") {
                            $filterByQuery->where('title', 'like', $query_details['title']);
                        }
                        if ($query_details['sub_title'] != "") {
                            $filterByQuery->where('sub_title', 'like', $query_details['sub_title']);
                        }
                        if ($query_details['created_by'] != "") {
                            $filterByQuery->where('created_by', 'like', $query_details['created_by']);
                        }

                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $stop_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $stop_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('preview_url', 'like', "%$search_key%")
                            ->orWhere('title', 'like', "%$search_key%")
                            ->orWhere('sub_title', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = PageGroupModel::count();
        }
        return $total_count;
    }

    public function find_page_group_details($page_group_id) {
        $page_group_info = PageGroupModel::find($page_group_id);

        if ($page_group_info != null) {
            $pagesArray = $page_group_info->page;
//            \Illuminate\Support\Facades\Log::error(json_encode($pagesArray));
            $page_detail_array = array();
            foreach ($pagesArray as $page_id) {
                $page_detail = PageModel::get_page_details($page_id);
                if ($page_detail != NULL) {
                    array_push($page_detail_array, $page_detail);
                }
            }
            $page_group_info->page = $page_detail_array;
        }

        return $page_group_info;
    }

}
