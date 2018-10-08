<?php
require APPPATH.'libraries/REST_Controller.php';

class ApiOrder extends REST_Controller
{
    protected $table = "order_headers";

    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        $this->load->library('form_validation');
    }

    public function order_get()
    {

        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $id = $this->uri->segment(3);

            $branchID = $is_valid_token['data']->branchID;

            $orders = $this->getOrders($id, $branchID);


            $this->response([
                    'status' => true,
                    'data' => $orders,
                ]);
        }
        else {
            $this->response(
                [
                    'status' => FALSE,
                    'message' => $is_valid_token['message']
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        }
    }

    public function getOrders($cusID, $branchID)
    {

        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        $this->db->where('order_headers.status',4);

        $this->db->where('DATE(order_headers.dateReady) >= ', Date('Y-m-d'));
        $this->db->where('DATE(order_headers.dateReady) <= ', Date('Y-m-d'));
        $this->db->where('order_headers.branchID', $branchID);
        $orders = $this->db->get()->result();
        $data = [];
       foreach($orders as $order) {
           $data[] = [
               'order_id' => $order->orderID,
               'custID' => $order->custID,
               'suffix' => $order->suffix,
               'fname' => $order->fname,
               'mname' => $order->mname,
               'lname' => $order->lname,
               'date' => $order->date,
               'service_id' => $order->serviceID,
               'service_type' => $order->serviceType,
               'branch_id' => $order->branchID,
               'branch_name' => $order->branchName,
               'order_details' => $this->getDetails('order_details',$order->orderID),
           ];
       }

       return $data;
    }

    public function getDetails($table, $orderID)
    {
        //join item
        $this->db->select('order_details.*');
        $this->db->select('service_types.*');
        $this->db->from('order_details');
        $this->db->join ('service_types', $table . '.serviceID=service_types.serviceID', 'left' );
        $this->db->where('order_details.orderID', $orderID);
        $details = $this->db->get()->result();

        $data = [];
        foreach($details as $detail) {
            $data[] = [
                'id' => $detail->id,
                'unit' => $detail->unit,
                'orderID' => $detail->orderID,
                'serviceID' => $detail->serviceID,
                'qty' => $detail->qty,
                'rate' => $detail->rate,
                'amount' => $detail->amount,
                'serviceType' => $detail->serviceType,
                'regRate' => $detail->regRate,
                 'discountedRate' => $detail->discountedRate,
                'desc' => $detail->desc,
                'status' => $detail->status,
                'categories' => $this->order_detail_categories($detail->id)
            ];
        }
        return $data;
    }

    public function order_detail_categories($detailID)
    {
        $this->db->select('order_detail_categories.*');
        $this->db->select('clothes_categories.*');
        $this->db->from('order_detail_categories');
        $this->db->where('order_detail_categories.order_detail_id', $detailID);
        $this->db->join ('clothes_categories', 'order_detail_categories' . '.	clothes_catID=clothes_categories.	clothesCatID', 'left' );
        $details = $this->db->get()->result();
        return $details;
    }

    public function getOrderCategories($table, $orderID)
    {
        //join item
        $this->db->select($table.'.*');
        $this->db->select('clothes_categories.*');
        $this->db->from($table);
        $this->db->join ( 'clothes_categories', $table . '.clothesCatID=clothes_categories.clothesCatID', 'left' );
        $this->db->where($table.'.orderID', $orderID);
        $details = $this->db->get()->result();
        return $details;
//        $data = [];
//        foreach($details as $detail) {
//            $data[] = [
//                'orderID' => $detail->orderID,
//                'clothesCatID' => $detail->clothesCatID,
//                'qty' => $detail->qty,
//                'services_data' => $this->getCategoriesById($detail->clothesCatID)
//            ];
//        }
//        return $data;
    }

    public function getCategoriesById($id)
    {
        $this->db->select('clothes_categories.*');
        //$this->db->select('clothes_categories.*');
        $this->db->from('clothes_categories');
        //$this->db->join ( 'clothes_categories', $table . '.clothesCatID=clothes_categories.clothesCatID', 'left' );
        $this->db->where('clothes_categories.clothesCatID', $id);
        $details = $this->db->get()->result();

        return $details;
    }

    public function order_details_get(){
        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $id = $this->uri->segment(3);

            $orders = $this->getOrdersDetailsById($id);


            $this->response([
                'status' => true,
                'data' => $orders,
            ]);
        }
        else {
            $this->response(
                [
                    'status' => FALSE,
                    'message' => $is_valid_token['message']
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        }
    }

    public function getOrdersDetailsById($orderID)
    {
        $this->db->select('order_headers.status as order_status');
        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        //$this->db->where('order_headers.status',4);
        $this->db->where('order_headers.orderID', $orderID);
        $orders = $this->db->get()->result();
        $data = [];
        foreach($orders as $order) {
            $data[] = [
                'order_id' => $order->orderID,
                'custID' => $order->custID,
                'suffix' => $order->suffix,
                'fname' => $order->fname,
                'mname' => $order->mname,
                'lname' => $order->lname,
                'rate' => $order->rate,
                'deliveryFee' => $order->deliveryFee,
                'amount' => $order->amount,
                'ttlAmount' => $order->ttlAmount,
                'discounted' => $order->isDiscounted,
                'date' => $order->date,
                'service_id' => $order->serviceID,
                'service_type' => $order->serviceType,
                'branch_id' => $order->branchID,
                'branch_name' => $order->branchName,
                'custsign' => $order->custSign,
                'status' => $order->order_status,
                'order_details' => $this->getDetails('order_details',$order->orderID),
                //'order_categories' => $this->getOrderCategories('order_categories',$order->orderID)
            ];
        }

        return $data;
    }

    public function update_order_details_post()
    {
        header("Access-Control-Allow-Origin: *");
        //$_POST = $this->security->xss_clean($_POST);

        //validation
        $this->form_validation->set_rules('order_id', 'order_id', 'trim|required');
        $this->form_validation->set_rules('signature', 'signature', 'trim|required');
        $this->load->library('form_validation');
        if ($this->form_validation->run() == FALSE)
        {
            //validation message
            $message = array(
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors()
            );

            $this->response($message, REST_Controller::HTTP_NOT_FOUND);

        }else {

            $order_id = $this->input->post('order_id', TRUE);
            $signature = $this->input->post('signature');
            $added = $this->update_order_header($order_id, $signature);

            $message = array(
                'status' => $added,
                'message' => "Successfully Added",

            );

            $this->response($message, 200);
        }

    }

    public function update_order_header($id, $blob)
    {
        $data = [
            'custSign' => $blob,
            'dateReleased' => date('Y-m-d h:i:s'),
            'status' => 5,
        ];
        $this->db->where('orderID', $id);
        return $this->db->update($this->table, $data);
    }

    public function order_by_date_get()
    {
        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $branchID = $is_valid_token['data']->branchID;
            $param = $this->uri->segment(3);
            $array_param = explode(':', $param);

            if(!empty($array_param)) {
                //get from and to date
                $from = $array_param[0];
                $to = $array_param[1];
                if($from == "new") {
                    $status = $array_param[1];
                    $filterOrderByDate = $this->filterOrderByNew($branchID, $status);
                    $this->response([
                        'status' => true,
                        'data' => $filterOrderByDate,
                    ]);
                }
                else {

                    $status = $array_param[2];
                    $filterOrderByDate = $this->filterOrderByDate($from, $to, $branchID, $status);
                    $this->response([
                        'status' => true,
                        'data' => $filterOrderByDate,
                    ]);

                }

            }
        }
        else {
            $this->response(
                [
                    'status' => FALSE,
                    'message' => $is_valid_token['message']
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        }
    }

    public function filterOrderByNew($branchID, $status)
    {
        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        $this->db->where('order_headers.status',$status);
        $this->db->where('order_headers.branchID', $branchID);
        $orders = $this->db->get()->result();
        $data = [];
        foreach($orders as $order) {
            $data[] = [
                'order_id' => $order->orderID,
                'custID' => $order->custID,
                'suffix' => $order->suffix,
                'fname' => $order->fname,
                'mname' => $order->mname,
                'lname' => $order->lname,
                'date' => $order->date,
                'service_id' => $order->serviceID,
                'service_type' => $order->serviceType,
                'branch_id' => $order->branchID,
                'branch_name' => $order->branchName,
                'order_details' => $this->getDetails('order_details',$order->orderID)
            ];
        }

        return $data;
    }

    public function filterOrderByDate($from, $to, $branchID, $status)
    {
        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        $this->db->where('order_headers.status',$status);
        $this->db->where('DATE(order_headers.date) >= ', $from);
        $this->db->where('DATE(order_headers.date) <= ', $to);
        $this->db->where('order_headers.branchID', $branchID);
        $orders = $this->db->get()->result();
        $data = [];
        foreach($orders as $order) {
            $data[] = [
                'order_id' => $order->orderID,
                'custID' => $order->custID,
                'suffix' => $order->suffix,
                'fname' => $order->fname,
                'mname' => $order->mname,
                'lname' => $order->lname,
                'date' => $order->date,
                'service_id' => $order->serviceID,
                'service_type' => $order->serviceType,
                'branch_id' => $order->branchID,
                'branch_name' => $order->branchName,
                'order_details' => $this->getDetails('order_details',$order->orderID)
            ];
        }

        return $data;
    }

    public function order_history_get()
    {

        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $id = $this->uri->segment(3);

            $branchID = $is_valid_token['data']->branchID;


            $orders = $this->getOrdersHistory($id, $branchID);

            $this->response([
                'status' => true,
                'data' => $orders,
                'date' => date('Y-m-d')
            ]);
        }
        else {
            $this->response(
                [
                    'status' => FALSE,
                    'message' => $is_valid_token['message']
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        }
    }

    public function getOrdersHistory($cusID, $branchID)
    {

        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('customers.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->join('customers', 'order_headers.custID=customers.custID', 'left');
        $this->db->where('order_headers.status',5);
        $this->db->where('DATE(order_headers.dateReleased)', date('Y-m-d'));
        $this->db->where('order_headers.branchID', $branchID);
        $orders = $this->db->get()->result();
        $data = [];
        foreach($orders as $order) {
            $data[] = [
                'order_id' => $order->orderID,
                'custID' => $order->custID,
                'suffix' => $order->suffix,
                'fname' => $order->fname,
                'mname' => $order->mname,
                'lname' => $order->lname,
                'date' => $order->date,
                'service_id' => $order->serviceID,
                'service_type' => $order->serviceType,
                'branch_id' => $order->branchID,
                'branch_name' => $order->branchName,
                'order_details' => $this->getDetails('order_details',$order->orderID)
            ];
        }

        return $data;
    }

    public function create_order_post()
    {
        header("Access-Control-Allow-Origin: *");
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $branch_id = $is_valid_token['data']->branchID;
            $data = $this->input->post('data');
            $customer_id = $this->input->post('customer_id');
            $grand_total = $this->input->post('grand_total');
            $remarks = $this->input->post('remarks');
            $category_data = $this->input->post('category_data');

            //save order_header
            $order_headers = [
                'date' => date('Y-m-d'),
                'custID' => $customer_id,
                'branchID' => $branch_id,
                'remarks' => $remarks,
                'ttlAmount' => $grand_total
            ];

            $this->db->insert($this->table, $order_headers);
            $lastId = $this->db->insert_id();
            $order_details = [];
            foreach($data as $key => $value) {
                $this->insertDetails($category_data, $lastId, $value['quantity'], $value['amount'], $value['rate'], $value['unit'], $value['service_id']);
            }

            $this->response([
                'status' => true,
            ]);
        }
        else {
            $this->response(
                [
                    'status' => FALSE,
                    'message' => $is_valid_token['message']
                ],
                REST_Controller::HTTP_NOT_FOUND
            );
        }

    }

    public function insertDetails($category_data, $orderID, $qty, $amount, $rate, $unit, $serviceID)
    {
        $order_details = [
            'orderID' => $orderID,
            'qty' => $qty,
            'amount' => $amount,
            'rate' => $rate,
            'unit' => $unit,
            'serviceID' => $serviceID
        ];

        $this->db->insert('order_details', $order_details);
        $lastInserted = $this->db->insert_id();//
        foreach($category_data as $category_datum) {
            if($serviceID == $category_datum['service_id']) {
                $order_category_data = [
                    'order_detail_id' => $lastInserted,
                    'clothes_catID' => $category_datum['category_id'],
                    'qty' => $category_datum['category_quantity']
                ];

                $this->db->insert('order_detail_categories', $order_category_data);
            }

            }
    }

    public function getServices()
    {
        $this->db->select('*');
        $this->db->where('status', 1);
        $services = $this->db->get("service_types")->result();
        $data = [];
        foreach($services as $service)
        {
            $data[] =[
                'services_type' => $service->serviceID.''.$service->serviceType
            ];
        }

        return $data;
    }
}