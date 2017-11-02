<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\MainModel;
use DateTime;

class PageModel extends Eloquent {

    protected $collection = 'page';
    protected $fillable = array('overlay', 'main_id', 'background', 'remark', 'is_imported', 'preview_images',
        'title', 'sub_title', 'subject', 'domain', 'subdomain', 'preview_url', 'created_by');

    public function add_or_update_page($insert_data, $page_id = "") {
        if (!isset($insert_data['is_imported'])
                OR $insert_data['is_imported'] != TRUE) {
            $insert_data['is_imported'] = FALSE;
        }
        if ($page_id != null && $page_id != "") {
            PageModel::find($page_id)->update($insert_data);
            $result = PageModel::find($page_id);
        } else {
            $result = PageModel::create($insert_data);
        }



        return $result;
    }

    public function fetch_main_id($page_id) {
        $result = PageModel::find($page_id)->first();
        return $result->main_id;
    }

    public function get_page($page_id_array) {
        $page_data = PageModel::where($page_id_array)->first();
        return $page_data;
    }

    public function page_list($query_details = NULL) {
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

        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "" || $subject != "" || $domain != "" || $subdomain != "") {
            $page_data = PageModel::
                    Where(function($filterByQuery)use ($query_details) {
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
                            ->orWhere('subdomain', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $page_data = PageModel::skip($skip)->take($limit)->orderBy($sort_by, $order_by)->get();
        }
        return $page_data;
    }

    public static function get_page_details($page_id) {
        $page_details = array();
        $page_model_data = PageModel::find($page_id);

        if ($page_model_data != NULL) {
            $page_details['overlay'] = $page_model_data->overlay;
            $page_details['background'] = $page_model_data['background'];

            $main_id = $page_model_data->main_id;
            $main_details = MainModel::get_main_details($main_id);
            $page_model_data->main_details = $main_details;
            return $page_model_data;
        } else {
            return FALSE;
        }
    }

    public function page_search($page_data) {
        $search_key = $page_data['search_key'];
        $skip = $page_data['skip'];
        $limit = $page_data['limit'];
        $page_query = PageModel::where('remark', 'like', '%' . $search_key . '%');
        $page_query->skip($skip);
        $page_query->take($limit);
        return $page_query->get();
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

        if ($search_key != "" || $from_date != "" || $sub_title != "" || $title != "" || $created_by != "" || $subject != "" || $domain != "" || $subdomain != "") {
            $total_count = PageModel::
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
                         if ($query_details['subject'] != "") {
                            $filterByQuery->where('subject', 'like', $query_details['subject']);
                        }
                        if ($query_details['domain'] != "") {
                            $filterByQuery->where('domain', 'like', $query_details['domain']);
                        }
                        if ($query_details['subdomain'] != "") {
                            $filterByQuery->where('subdomain', 'like', $query_details['subdomain']);
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
                            ->orWhere('subdomain', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = PageModel::count();
        }
        return $total_count;
    }

    public function find_page_details($page_id) {
        $page_data = PageModel::find($page_id);
        return $page_data;
    }

}
