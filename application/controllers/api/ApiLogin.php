<?php


require APPPATH.'libraries/REST_Controller.php';

class ApiLogin extends REST_Controller
{
    protected $table = "users";
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    /**
     *  User Login
     * @method : POST
     * @url :
     *
     **/
    public function login_post()
    {
        header("Access-Control-Allow-Origin: *");
        $_POST = $this->security->xss_clean($_POST);

        //validation
        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[100]');
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

            $username = $this->input->post('username', TRUE);
            $password = md5($this->input->post('password', TRUE));
            $user = $this->login($username, $password);

            if(!$user) {
                $response = [
                    'status' => FALSE,
                    'message' => 'Invalid Username or Password'
                ];
                $this->response($response, 400);
            }
            elseif($user == 3) {
                $response = [
                    'status' => FALSE,
                    'message' => 'Only deliver and cashier can login.'
                ];
                $this->response($response, 400);
            }
            else {

                //Load Authorization Token Library
                $this->load->library('Authorization_Token');
                //Generate Token
                $token_data['id'] = $user['userID'];
                $token_data['userName'] = $user['userName'];
                $token_data['firstName'] = $user['firstName'];
                $token_data['branchID'] = $user['branchID'];
                $token_data['groupName'] = $user['groupName'];
                $token_data['time'] = time();


                $user_token = $this->authorization_token->generateToken($token_data);

                $response = [
                    'id' => $user['userID'],
                    'userName' => $user['userName'],
                    'firstName' => $user['firstName'],
                    'middleName' => $user['middleName'],
                    'lastName' => $user['lastName'],
                    'branchID' => $user['branchID'],
                    'groupName' => $user['groupName'],
                    'token' => $user_token
                ];

                $return_data = [
                    'status' => TRUE,
                    'data' => $response,
                    'message' => 'Login user Successfully.'
                ];
                $this->response($return_data);
            }
        }

    }

    public function login($username, $password)
    {
        $this->db->select('users.*');
        $this->db->select('usergroups.*');
        $this->db->where('userName', $username);
        $this->db->from($this->table);
        $this->db->join('usergroups', $this->table.'.groupID=usergroups.groupID', 'left');
        $q = $this->db->get();
        //get the user groups

        if($q->num_rows()) {
            $hashed_password = $q->row('userPswd');
            if(hash_equals($hashed_password, $password)) {
                if($q->row('groupName') === 'Delivery' || $q->row('groupName') === 'Cashier') {
                    $data['userID'] = $q->row('userID');
                    $data['userName'] = $q->row('userName');
                    $data['firstName'] = $q->row('firstName');
                    $data['middleName'] = $q->row('middleName');
                    $data['lastName'] = $q->row('lastName');
                    $data['branchID'] = $q->row('branchID');
                    $data['groupName'] = $q->row('groupName');
                    return $data;
                }else {
                    return 3;
                }

            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

}