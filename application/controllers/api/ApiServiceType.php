<?php
require APPPATH.'libraries/REST_Controller.php';

class ApiServiceType extends REST_Controller
{
    protected $table = "service_types";
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

    public function services_get()
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
            $this->db->select('*');
            $this->db->where('status', 1);
            $services = $this->db->get($this->table)->result();
            $this->response([
                'status' => true,
                'user_id' => $user_id,
                'data' => $services
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

    public function categories_get()
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
            $service_id = $this->uri->segment(3);

            require_once(APPPATH.'controllers/ApiHelpers.php');
            $categories = ApiHelpers::getCategories($service_id);

            $this->response([
                'status' => true,
                'user_id' => $user_id,
                'data' => $categories
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