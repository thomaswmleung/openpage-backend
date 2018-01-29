<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrderModel;
use App\Helpers\Token_helper;

class OrderController extends Controller {
    /**
     * @SWG\Get(path="/order",
     *   tags={"Order"},
     *   summary="Returns list of order",
     *   description="Returns order data",
     *   operationId="order_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="search_key",
     *     in="query",
     *     description="Search parameter or key word to search",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="from_date",
     *     in="query",
     *     description="Created at start date(YYYY-mm-dd) ",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="to_date",
     *     in="query",
     *     description="Created at end date(YYYY-mm-dd) ",
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="skip",
     *     in="query",
     *     description="this is offset or skip the records",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of records to be retrieved ",
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by value",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"created_at"},
     *             default="created_at"
     *         ),
     *         collectionFormat="multi"
     *   ),
     *   @SWG\Parameter(
     *         name="order_by",
     *         in="query",
     *         description="Order by Ascending or descending",
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"ASC", "DESC"},
     *             default="DESC"
     *         ),
     *         collectionFormat="multi"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Get(path="/order/{_id}",
     *   tags={"Order"},
     *   summary="Returns order data",
     *   description="Returns order data",
     *   operationId="order_list",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="path",
     *     description="ID of the order that needs to be displayed",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *  @SWG\Response(
     *     response=400,
     *     description="Invalid order id",
     *   ),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function order_list(Request $request) {
        $orderModel = new OrderModel();
        if (isset($request->_id) && $request->_id != "") {
            $order_id = $request->_id;
            $order_details = $orderModel->find_order_details($order_id);
            if ($order_details == NULL) {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_order_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_order_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            } else {
                $response_array = array("success" => TRUE, "data" => $order_details, "errors" => array());
                return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
            }
        } else {
            $search_key = "";
            if (isset($request->search_key)) {
                $search_key = $request->search_key;
            }
            $skip = 0;
            if (isset($request->skip)) {
                $skip = (int) $request->skip;
            }
            $limit = config('constants.default_query_limit');
            if (isset($request->limit)) {
                $limit = (int) $request->limit;
            }
            $from_date = "";
            if (isset($request->from_date)) {
                $from_date = date("Y-m-d H:i:s", strtotime($request->from_date . "00:00:00"));
            }
            $to_date = "";
            if (isset($request->to_date)) {
                $to_date = date("Y-m-d H:i:s", strtotime($request->to_date . " 23:59:59"));
            }

            if ($from_date == "" || $to_date == "") {
                $from_date = "";
                $to_date = "";
            }
            $sort_by = 'created_at';
            if (isset($request->sort_by)) {
                $sort_by = $request->sort_by;
            }
            $order_by = 'DESC';
            if (isset($request->order_by)) {
                $order_by = $request->order_by;
            }
            $query_details = array(
                'search_key' => $search_key,
                'limit' => $limit,
                'skip' => $skip,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'sort_by' => $sort_by,
                'order_by' => $order_by
            );

            $order_details = $orderModel->order_details($query_details);
            $total_count = $orderModel->total_count($query_details);
        }
        $response_array = array("success" => TRUE, "data" => $order_details, "total_count" => $total_count, "errors" => array());
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    /**
     * @SWG\Post(path="/order",
     *   tags={"Order"},
     *   summary="Create a order",
     *   description="",
     *   operationId="add_or_update_order",
     *   consumes={"application/json"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="order json input <br> Sample JSON to create order http://jsoneditoronline.org/?id=285c21877cf510b2b3855d796cd22a7e",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */

    /**
     * @SWG\Put(path="/order",
     *   tags={"Order"},
     *   summary="Update order details",
     *   description="",
     *   operationId="add_or_update_order",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="data",
     *     description="order json input <br> Sample JSON to update order http://jsoneditoronline.org/?id=358062bd4c079cf42e9e2d4bfb627b10",
     *     required=true,
     *     @SWG\Schema()
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation"
     *   ),
     *   @SWG\Response(response=400, description="Invalid data"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    public function add_or_update_order(Request $request) {
        $json_data = $request->getContent();
        $order = json_decode($json_data, true);
        if ($order == null) {
            return response(json_encode(array("error" => "Invalid Json")))->header('Content-Type', 'application/json');
        }

        $user_id = $order['user_id'];
        $metadata = $order['metadata'];
        $products = $order['products'];
        $status = $order['status'];
        $payment_details = $order['payment_details'];

        $insert_data = array(
            'user_id' => $user_id,
            'metadata' => $metadata,
            'products' => $products,
            'status' => $status,
            'payment_details' => $payment_details
        );
        if ($request->isMethod('post')) {
            $user_id = Token_helper::fetch_user_id_from_token($request->header('token'));
            $insert_data['created_by'] = $user_id;
        }

        $order_id = "";
        if ($request->isMethod('put')) {
            if (isset($order['order_id']) AND $order['order_id'] != "") {
                $order_id = $order['order_id'];
            } else {
                $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_order_id')['error_code'],
                        "ERR_MSG" => config('error_constants.invalid_order_id')['error_message']));

                $response_array = array("success" => FALSE, "errors" => $error_messages);
                return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
            }
        }


        $order_id = $this->create_or_update_order($insert_data, $order_id);


        $response_array['success'] = TRUE;

        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

    function create_or_update_order($insert_data, $order_id) {
        $orderModel = new OrderModel();
        $orderDetails = $orderModel->add_order($insert_data, $order_id);
        return $orderDetails->_id;
    }

    /**
     * @SWG\Delete(path="/order",
     *   tags={"Order"},
     *   summary="delete order data",
     *   description="Delete order from system",
     *   operationId="delete_order",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="_id",
     *     in="query",
     *     description="ID of the order that needs to be deleted",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=400, description="Invalid data supplied"),
     *   security={{
     *     "token":{}
     *   }}
     * )
     */
    function delete_order(Request $request) {
        $order_id = trim($request->_id);

        $orderModel = new OrderModel();
        $order_data = $orderModel->get_order_details($order_id);
        if ($order_data == null) {
            $error_messages = array(array("ERR_CODE" => config('error_constants.invalid_order_id')['error_code'],
                    "ERR_MSG" => config('error_constants.invalid_order_id')['error_message']));

            $response_array = array("success" => FALSE, "errors" => $error_messages);
            return response(json_encode($response_array), 400)->header('Content-Type', 'application/json');
        }

        OrderModel::destroy($order_id);
        $response_array = array("success" => TRUE);
        return response(json_encode($response_array), 200)->header('Content-Type', 'application/json');
    }

}
