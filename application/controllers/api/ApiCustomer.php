<?php
require APPPATH.'libraries/REST_Controller.php';

class ApiCustomer extends REST_Controller
{
    protected $table = "customers";
    var $data;
    var $pfield;
    var $logfield;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model ( 'generic_model', 'records' );
        $this->pfield = $this->data['pfield'] = 'custID'; // defines primary key
        $this->logfield = 'custID';
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
            $this->db->select($this->table.'.*');
            $this->db->select('provinces.province as province_name');
            $this->db->select('barangays.barangay as barangay_name');
            $this->db->select('cities.city as city_name');
            $this->db->where($this->table.'.status',1);
            $this->db->from($this->table);
            $this->db->join('provinces', $this->table.'.provinceID=provinces.provinceID', 'left');
            $this->db->join('barangays', $this->table.'.barangayID=barangays.barangayID', 'left');
            $this->db->join('cities', $this->table.'.cityID=cities.cityID', 'left');
            $customers = $this->db->get()->result();
            //$data = $this->formatCustomer($customers);
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

    public function customer_post()
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
            $_POST = $this->security->xss_clean($_POST);

            //validation
            $this->form_validation->set_rules('fname', 'fname', 'trim|required');
            $this->form_validation->set_rules('lname', 'lname', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('mname', 'mname', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('contact', 'contact', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('bday', 'bday', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('provinceID', 'provinceID', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('cityID', 'cityID', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('barangayID', 'barangayID', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('address', 'address', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('title', 'title', 'trim|max_length[100]');
            $this->form_validation->set_rules('isRegular', 'isRegular', 'trim|max_length[100]');
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

                $first_name = $this->input->post('fname', TRUE);
                $last_name = $this->input->post('lname', TRUE);
                $middle_name = $this->input->post('mname', TRUE);
                $check_duplicate = $this->check_duplicate($first_name, $middle_name, $last_name);
                if(!$check_duplicate) {

                    $table_fields = array ('title', 'fname', 'mname', 'lname', 'suffix', 'provinceID', 'cityID', 'barangayID', 'telephone', 'isRegular', 'bday', 'address', 'contact', 'isRegular');

                    $this->records->table = $this->table;
                    $this->records->fields = array ();

                    foreach ( $table_fields as $fld ) {
                        $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                    }

                    if ($this->records->save ()) {
                        $this->records->fields = array();
                        $id = $this->records->where ['custID'] = $this->db->insert_id();
                        $this->records->retrieve();

                        $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
                        $this->log_model->table_logs ('api-customer', $this->table, $this->pfield, $id, 'Insert', $logs, $user_id );

                        $return_data = [
                            'status' => TRUE,
                            'data' => [
                                'id' => $id
                            ],
                            'message' => 'Customer Successfully Added.'
                        ];
                        $this->response($return_data);
                    }
                    else {
                        $response = [
                            'status' => FALSE,
                            'message' => 'Error Inserting Customer.',
                        ];
                        $this->response($response, 400);
                    }



                }
                else {
                    $response = [
                        'status' => FALSE,
                        'message' => 'Customer already exists.',
                    ];
                    $this->response($response, 400);
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

    public function province_get(){
        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            //get province
            $this->db->where('status',1);
            $provinces = $this->db->get('provinces')->result();
            $this->response([
                'status' => true,
                'user_id' => $user_id,
                'data' => $provinces
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

    public function cities_get(){
        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;
            $city_id = $this->uri->segment(3);
            //get province
            $this->db->where('status',1);
            $this->db->where('provinceID', $city_id);
            $cities = $this->db->get('cities')->result();
            $this->response([
                'status' => true,
                'user_id' => $user_id,
                'city_id' => $city_id,
                'data' => $cities
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

    public function barangays_get(){
        header("Access-Control-Allow-Origin: *");
        //Load Authorization Token Library
        $this->load->library('Authorization_Token');
        //user token validation
        $is_valid_token = $this->authorization_token->validateToken();
        //get the id
        //$is_valid_token['data']->id;
        if(!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $is_valid_token['data']->id;

            $param = $this->uri->segment(3);
            $array_param = explode(':', $param);

            if(!empty($array_param)) {
                //get from and to date
                $province_id = $array_param[0];
                $city_id = $array_param[1];
                $barangays = $this->getBarangays($province_id, $city_id);
                $this->response([
                    'status' => true,
                    'data' => $barangays,
                ]);
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

    public function getBarangays($provinceID, $cityID)
    {
        $this->db->where('status',1);
        $this->db->where('provinceID', $provinceID);
        $this->db->where('cityID', $cityID);
        return $this->db->get('barangays')->result();
    }

    public function check_duplicate($first_name, $middle_name, $last_name) {
        $this->db->where ( 'fname', trim ($first_name ), 'mname', trim ($middle_name) ,  'lname', trim ($last_name) );

        if ($this->db->count_all_results ( $this->table ))
            return true; // duplicate
        else
            return false;
    }

    public function customer_delete()
    {
        $id = $this->delete('id');
        // set fields
        $this->records->fields = array ();
        // set table
        $this->records->table = $this->table;

        $this->records->where [$this->pfield] = $id;
        // execute retrieve
        $this->records->retrieve ();
        if (! empty ( $this->records->field )) {
            $this->records->pfield = $this->pfield;
            $this->records->pk = $id;
            $data = $this->records->delete ();
            $this->response([
                'data' => $data
            ]);
        }
        else {
            $response = [
                'status' => FALSE,
                'message' => 'Customer not found.',
            ];
            $this->response($response, 400);
        }
    }

}