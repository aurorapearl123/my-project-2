<?php
require APPPATH.'libraries/REST_Controller.php';

class ApiOrder extends REST_Controller
{
    protected $table = "order_header";

    public function __construct()
    {
        parent::__construct();
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

            $orders = $this->getOrders($id);


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

    public function getOrders($cusID)
    {

        $this->db->select('order_headers.*');
        $this->db->select('service_types.*');
        $this->db->select('branches.branchName');
        $this->db->from('order_headers');
        $this->db->join('service_types', 'order_headers.serviceID=service_types.serviceID', 'left');
        $this->db->join('branches', 'order_headers.branchID=branches.branchID', 'left');
        $this->db->where('order_headers.status',1);
        $this->db->where('order_headers.custID', $cusID);
        $orders = $this->db->get()->result();
        $data = [];
       foreach($orders as $order) {
           $data[] = [
               'order_id' => $order->orderID,
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

    public function getDetails($table, $orderID)
    {
        //join item
        $this->db->select('order_details.*');
        $this->db->select('clothes_categories.*');
        $this->db->from('order_details');
        $this->db->join ( 'clothes_categories', $table . '.clothesCatID=clothes_categories.clothesCatID', 'left' );
        $this->db->where('order_details.orderID', $orderID);
        $details = $this->db->get()->result();
        return $details;
    }
}