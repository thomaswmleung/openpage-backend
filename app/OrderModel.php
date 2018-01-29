<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DateTime;

class OrderModel extends Eloquent {

    protected $collection = 'order';
    protected $fillable = array('user_id', 'metadata', 'products', 'status', 'payment_details', 'created_by');

    public function add_order($insert_data, $order_id) {

        $result = OrderModel::updateOrCreate(
                        ['_id' => $order_id], $insert_data
        );
        return $result;
    }

    public function order_list($data_array = NULL, $search_key = NULL, $skip = NULL, $limit = NULL) {
        if ($data_array == NULL) {
            $order_data = OrderModel::
//                    where('order_text', 'like', '%'.$search_key.'%')
                    skip($skip)->take($limit)->get();
        } else {
            $order_data = OrderModel::where($data_array)->get();
        }
        return $order_data;
    }

    public static function get_order_details($order_id) {

        $order_details = OrderModel::where('_id', $order_id)->first();
        if ($order_details != NULL) {
            $order_details = $order_details->toArray();
        }
        return $order_details;
    }

    public function order_details($query_details = NULL) {
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
        $from_date = $query_details['from_date'];
        $to_date = $query_details['to_date'];
        $sort_by = $query_details['sort_by'];
        $order_by = $query_details['order_by'];

        if ($search_key != "" || $from_date != "") {
            $order_data = OrderModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $end_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $end_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('status', 'like', "%$search_key%");
                        }
                    })
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy($sort_by, $order_by)
                    ->get();
        } else {
            $order_data = OrderModel::skip($skip)->take($limit)->get();
        }
        return $order_data;
    }

    public function total_count($query_details) {
        $search_key = "";
        if (isset($query_details['search_key'])) {
            $search_key = $query_details['search_key'];
        }
        $from_date = $query_details['from_date'];
        if ($search_key != "" || $from_date != "") {
            $total_count = OrderModel::
                    Where(function($filterByQuery)use ($query_details) {
                        if ($query_details['from_date'] != "") {
                            $start_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['from_date']));
                            $end_date = new \MongoDB\BSON\UTCDateTime(new DateTime($query_details['to_date']));
                            $filterByQuery->whereBetween('created_at', array($start_date, $end_date));
                        }
                    })
                    ->Where(function($searchQuery)use ($search_key) {
                        if ($search_key != "") {
                            $searchQuery->where('status', 'like', "%$search_key%");
                        }
                    })
                    ->count();
        } else {
            $total_count = OrderModel::count();
        }
        return $total_count;
    }

    public function find_order_details($order_id) {
        $order_data = OrderModel::find($order_id);
        return $order_data;
    }

}
