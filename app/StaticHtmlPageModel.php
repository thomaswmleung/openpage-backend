<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class StaticHtmlPageModel extends Eloquent {

    protected $collection = 'static_html_page';
    protected $fillable = array('page_code', 'content', 'reference_1', 'reference_2', 'reference_3', 'reference_4', 'reference_5', 'created_by', 'updated_by');

    public function create_or_update_static_html_page($insert_data, $id) {
        $result = StaticHtmlPageModel::updateOrCreate(
                        ['_id' => $id], $insert_data
        );
        return $result->_id;
    }

    public function static_html_page_details($query_details = NULL) {

        $skip = 0;
        $limit = config('constants.default_query_limit');
        $search_key = "";

        if (isset($query_details['skip'])) {
            $skip = $query_details['skip'];
        }
        if (isset($query_details['limit'])) {
            $limit = $query_details['limit'];
        }
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        $page_code = "";
        if (isset($query_details['page_code'])) {
            $page_code = $query_details['page_code'];
        }
        $reference_1 = "";
        if (isset($query_details['reference_1'])) {
            $reference_1 = $query_details['reference_1'];
        }
        $reference_2 = "";
        if (isset($query_details['reference_2'])) {
            $reference_2 = $query_details['reference_2'];
        }
        $reference_3 = "";
        if (isset($query_details['reference_3'])) {
            $reference_3 = $query_details['reference_3'];
        }
        $reference_4 = "";
        if (isset($query_details['reference_4'])) {
            $reference_4 = $query_details['reference_4'];
        }
        $reference_5 = "";
        if (isset($query_details['reference_5'])) {
            $reference_5 = $query_details['reference_5'];
        }
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];

        if ($search_key != "" || $page_code !="" || $reference_1 !="" || $reference_2 !="" || $reference_3 !="" || $reference_4 !="" || $reference_5 !="") {
            $static_html_page_data = StaticHtmlPageModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['page_code'] != "") {
                            $filterByQuery->where('page_code', 'like', $query_details['page_code']);
                        }
                        if ($query_details['reference_1'] != "") {
                            $filterByQuery->where('reference_1', 'like', $query_details['reference_1']);
                        }
                        if ($query_details['reference_2'] != "") {
                            $filterByQuery->where('reference_2', 'like', $query_details['reference_2']);
                        }
                        if ($query_details['reference_3'] != "") {
                            $filterByQuery->where('reference_3', 'like', $query_details['reference_3']);
                        }
                        if ($query_details['reference_4'] != "") {
                            $filterByQuery->where('reference_4', 'like', $query_details['reference_4']);
                        }
                        if ($query_details['reference_5'] != "") {
                            $filterByQuery->where('reference_5', 'like', $query_details['reference_5']);
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('content', 'like', "%$search_key%")
                            ->orWhere('page_code', 'like', "%$search_key%")
                            ->orWhere('reference_1', 'like', "%$search_key%")
                            ->orWhere('reference_2', 'like', "%$search_key%")
                            ->orWhere('reference_3', 'like', "%$search_key%")
                            ->orWhere('reference_4', 'like', "%$search_key%")
                            ->orWhere('reference_5', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $static_html_page_data = StaticHtmlPageModel::skip($skip)->take($limit)->get();
        }
        return $static_html_page_data;
    }

    public function total_count($query_details) {
        $search_key = "";

        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        $page_code = "";
        if (isset($query_details['page_code'])) {
            $page_code = $query_details['page_code'];
        }
        $reference_1 = "";
        if (isset($query_details['reference_1'])) {
            $reference_1 = $query_details['reference_1'];
        }
        $reference_2 = "";
        if (isset($query_details['reference_2'])) {
            $reference_2 = $query_details['reference_2'];
        }
        $reference_3 = "";
        if (isset($query_details['reference_3'])) {
            $reference_3 = $query_details['reference_3'];
        }
        $reference_4 = "";
        if (isset($query_details['reference_4'])) {
            $reference_4 = $query_details['reference_4'];
        }
        $reference_5 = "";
        if (isset($query_details['reference_5'])) {
            $reference_5 = $query_details['reference_5'];
        }

        if ($search_key != "" || $page_code !="" || $reference_1 !="" || $reference_2 !="" || $reference_3 !="" || $reference_4 !="" || $reference_5 !="") {
            $total_count = StaticHtmlPageModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['page_code'] != "") {
                            $filterByQuery->where('page_code', 'like', $query_details['page_code']);
                        }
                        if ($query_details['reference_1'] != "") {
                            $filterByQuery->where('reference_1', 'like', $query_details['reference_1']);
                        }
                        if ($query_details['reference_2'] != "") {
                            $filterByQuery->where('reference_2', 'like', $query_details['reference_2']);
                        }
                        if ($query_details['reference_3'] != "") {
                            $filterByQuery->where('reference_3', 'like', $query_details['reference_3']);
                        }
                        if ($query_details['reference_4'] != "") {
                            $filterByQuery->where('reference_4', 'like', $query_details['reference_4']);
                        }
                        if ($query_details['reference_5'] != "") {
                            $filterByQuery->where('reference_5', 'like', $query_details['reference_5']);
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('content', 'like', "%$search_key%")
                            ->orWhere('page_code', 'like', "%$search_key%")
                            ->orWhere('reference_1', 'like', "%$search_key%")
                            ->orWhere('reference_2', 'like', "%$search_key%")
                            ->orWhere('reference_3', 'like', "%$search_key%")
                            ->orWhere('reference_4', 'like', "%$search_key%")
                            ->orWhere('reference_5', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = StaticHtmlPageModel::count();
        }
        return $total_count;
    }

    public function find_static_html_page_details($static_html_page_id) {
        $static_html_page_info = StaticHtmlPageModel::find($static_html_page_id);
        return $static_html_page_info;
    }

}
