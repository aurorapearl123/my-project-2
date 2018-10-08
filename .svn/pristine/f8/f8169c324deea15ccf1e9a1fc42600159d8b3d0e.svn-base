<?php
require APPPATH.'libraries/REST_Controller.php';

class ApiCustomer extends REST_Controller
{
    protected $table = "customers";
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function customer_get()
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
            //get customer by user id
            $this->db->where('status',1);
            $customers = $this->db->get($this->table)->result();
            $this->response([
                'status' => true,
                'user_id' => $user_id,
                'data' => $customers
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
}