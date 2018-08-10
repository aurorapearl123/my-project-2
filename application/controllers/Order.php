<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Order extends CI_Controller {
    //Default Variables
    var $menu;
    var $roles;
    var $data;
    var $table;
    var $pfield;
    var $logfield;
    var $module;
    var $modules;
    var $module_path;
    var $controller_page;

    public function __construct() {
        parent::__construct ();
        $this->load->model ( 'generic_model', 'records' );
        $this->module = 'Order';
        $this->data['controller_page'] = $this->controller_page = site_url('order'); // defines contoller link
        $this->table = 'order_headers'; // defines the default table
        $this->pfield = $this->data ['pfield'] = 'orderID'; // defines primary key
        $this->logfield = 'orderID';
        $this->module_path = 'modules/' . strtolower ( str_replace ( " ", "_", $this->module ) ) . '/order'; // defines module path


        // check for maintenance period
        if ($this->config_model->getConfig ( 'Maintenance Mode' ) == '1') {
            header ( 'location: ' . site_url ( 'maintenance_mode' ) );
        }

        // check user session
        if (! $this->session->userdata ( 'current_user' )->sessionID) {
            header ( 'location: ' . site_url ( 'login' ) );
        }
    }

    private function submenu() {
        //submenu setup
        require_once ('modules.php');

        foreach($modules as $mod) {
            //modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/'.str_replace(" ","_",strtolower($mod)).'/metadata');
        }


        $this->data ['modules'] = $this->modules;
        $this->data ['current_main_module'] = $this->modules [$this->module]['main']; // defines the current main module
        $this->data ['current_module'] = $this->modules [$this->module]['sub']['Order']; // defines the current sub module
        // check roles
        $this->check_roles ();
        $this->data ['roles'] = $this->roles;
    }

    private function check_roles() {
        // check roles
        $this->roles ['create'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Add ' . $this->module );
        $this->roles ['view'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'View ' . $this->module );
        $this->roles ['edit'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Edit Existing ' . $this->module );
        $this->roles ['delete'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Delete Existing ' . $this->module );
        $this->roles ['approve'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_userID' ), 'Approve ' . $this->module );
    }

    private function _in_used($id = 0) {
        $tables = array ('branches' => 'companyID', 'customers' => 'custID', 'service_types' => 'serviceID' );

        if (! empty ( $tables )) {
            foreach ( $tables as $table => $fld ) {
                $this->db->where ( $fld, $id );
                if ($this->db->count_all_results ( $table )) {
                    return true;
                }
            }
        }
        return false;
    }

    public function index() {
        $this->show ();
    }

    public function create() {
        $this->submenu ();
        $data = $this->data;

        // check roles
        if ($this->roles ['create']) {

            //echo $this->session->userdata('current_user' )->branchID;
            //echo $this->session->userdata('current_user' )->userID;

            //get branch
            $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
            $branch = $this->db->get('branches');
            $current_branch = $branch->row();
            $data['branchName'] = $current_branch->branchName;
            $data['branchID'] = $current_branch->branchID;
            //get service_types
            $this->db->where('status',1);
            $service_types = $this->db->get('service_types')->result();
            $data['service_types'] = $service_types;
            $data['service_types_json'] = json_encode($service_types);
            //var_dump($service_types);
            //die();
            //get customers
            $this->db->select('fname');
            $this->db->select('mname');
            $this->db->select('lname');
            $this->db->select('custID');
            $customers = $this->db->get('customers')->result();
            $data['customers'] = $customers;
            //get cloths categories
            $this->db->select('	clothesCatID');
            $this->db->select('category');
            $this->db->where('status', 1);
            $clothes_categories = $this->db->get('clothes_categories')->result();
            $data['clothes_categories'] = $clothes_categories;
            // load views
            $this->load->view ( 'header', $data );
            $this->load->view ( $this->module_path . '/create' );
            $this->load->view ( 'footer' );

        } else {
            // no access this page
            $data ['class'] = "danger";
            $data ['msg'] = "Sorry, you don't have access to this page!";
            $data ['urlredirect'] = "";
            $this->load->view ( 'header', $data );
            $this->load->view ( 'message' );
            $this->load->view ( 'footer' );
        }
    }

    public function save() {
        //load submenu
        $this->submenu ();
        $data = $this->data;

        $table_fields = array ('date', 'branchID', 'serviceID', 'ttlAmount', 'rate', 'deliveryFee', 'amount' , 'ttlAmount', 'createdBy', 'qty', 'custID');

        // check role
        if ($this->roles ['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                if($fld == 'isDiscounted') {
                    if(empty($this->input->post('isDiscounted'))) {
                       // $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                        $this->records->fields [$fld] = 'N';
                    }
                    else {
                        $this->records->fields [$fld] = 'Y';
                    }
                }
                else {
                    $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                }
                //add created by
                if($fld == 'createdBy') {
                    $this->records->fields [$fld] = $this->session->userdata('current_user')->userID;
                }

            }

            if ($this->records->save ()) {
                $this->records->fields = array ();
                $id = $this->records->where ['orderID'] = $this->db->insert_id ();
                $this->records->retrieve ();
                // record logs
                $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );

                //insert details

                $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $id, 'Insert', $logs );

                // insert order detail
                $clothes_qtys = $this->input->post ( 'clothes_qtys' );
                $clothes_ids = $this->input->post('clothes_ids');
                $this->insert_order_details($id, $clothes_qtys, $clothes_ids);
                $logfield = $this->pfield;
                // success msg
                $data ["class"] = "success";
                $data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully saved.";
                $data ["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode ( $id );
                $this->load->view ( "header", $data );
                $this->load->view ( "message" );
                $this->load->view ( "footer" );

            } else {
                // error
                $data ["class"] = "danger";
                $data ["msg"] = "Error in saving the " . $this->data ['current_module'] ['module_label'] . "!";
                $data ["urlredirect"] = "";
                $this->load->view ( "header", $data );
                $this->load->view ( "message" );
                $this->load->view ( "footer" );
            }
        } else {
            // error
            $data ["class"] = "danger";
            $data ["msg"] = "Sorry, you don't have access to this page!";
            $data ["urlredirect"] = "";
            $this->load->view ( "header", $data );
            $this->load->view ( "message" );
            $this->load->view ( "footer" );
        }
    }

    public function edit($id) {
        $this->submenu ();
        $data = $this->data;
        $id = $this->encrypter->decode ( $id );

        if ($this->roles ['edit']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->records->table = $this->table;
            // set fields for the current table
            $this->records->setFields ();
            // set where
            $this->records->where [$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve ();
            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->records->field;


            //get branch
            $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
            $branch = $this->db->get('branches');
            $current_branch = $branch->row();
            $data['branchName'] = $current_branch->branchName;
            $data['branchID'] = $current_branch->branchID;
            //get service_types
            $this->db->where('status',1);
            $service_types = $this->db->get('service_types')->result();
            $data['service_types'] = $service_types;
            $data['service_types_json'] = json_encode($service_types);
            //var_dump($service_types);
            //die();
            //get customers
            $this->db->select('fname');
            $this->db->select('mname');
            $this->db->select('lname');
            $this->db->select('custID');
            $customers = $this->db->get('customers')->result();
            $data['customers'] = $customers;
            //get cloths categories
            $this->db->select('	clothesCatID');
            $this->db->select('category');
            $this->db->where('status', 1);
            $clothes_categories_actual = $this->db->get('clothes_categories')->result();
            $data['clothes_categories_actual'] = $clothes_categories_actual;

            //order details
            $this->db->select('order_details.qty');
            $this->db->select('order_details.clothesCatID');
            $this->db->select('clothes_categories.category');
            $this->db->from('order_details');
            $this->db->join('clothes_categories', 'order_details.clothesCatID=clothes_categories.	clothesCatID', 'right');
            $this->db->where('orderID', $id);
            $clothes_categories = $this->db->get()->result();

            $result = array_merge_recursive($clothes_categories, $clothes_categories_actual);


            $unique_array = [];
            foreach ($result as $row => $value) {
                $unique_array[] = [
                    'category' => $value->category,
                    'clothesCatID' => $value->clothesCatID,
                    'qty' => isset($value->qty) ? $value->qty : "",
                ];
            }

            $remove_duplicates = $this->super_unique($unique_array,'category');
            $data['clothes_categories'] =  $remove_duplicates;

            // load views
            $this->load->view ( 'header', $data );
            $this->load->view ( $this->module_path . '/edit' );
            $this->load->view ( 'footer' );
        } else {
            // no access this page
            $data ['class'] = "danger";
            $data ['msg'] = "Sorry, you don't have access to this page!";
            $data ['urlredirect'] = "";
            $this->load->view ( 'header', $data );
            $this->load->view ( 'message' );
            $this->load->view ( 'footer' );
        }
    }

    function super_unique($array,$key)
    {
        $temp_array = [];
        foreach ($array as &$v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] =& $v;
        }
        $array = array_values($temp_array);
        return $array;

    }


    function array_remove_keys($array, $keys = array()) {

        // If array is empty or not an array at all, don't bother
        // doing anything else.
        if(empty($array) || (! is_array($array))) {
            return $array;
        }

        // If $keys is a comma-separated list, convert to an array.
        if(is_string($keys)) {
            $keys = explode(',', $keys);
        }

        // At this point if $keys is not an array, we can't do anything with it.
        if(! is_array($keys)) {
            return $array;
        }

        // array_diff_key() expected an associative array.
        $assocKeys = array();
        foreach($keys as $key) {
            $assocKeys[$key] = true;
        }

        return array_diff_key($array, $assocKeys);
    }


    function unique_key($array,$keyname){

        $new_array = array();
        foreach($array as $key=>$value){

            if(!isset($new_array[$value[$keyname]])){
                $new_array[$value[$keyname]] = $value;
            }

        }
        $new_array = array_values($new_array);
        return $new_array;
    }


    public function update() {
        // load submenu
        $this->submenu ();
        $data = $this->data;
        $table_fields = array ('date', 'branchID', 'custID', 'serviceID', 'isDiscounted', 'rate', 'deliveryFee', 'amount' , 'ttlAmount', 'createdBy', 'qty');


        // check roles
        if ($this->roles ['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                if($fld == 'isDiscounted') {
                    if(empty($this->input->post('isDiscounted'))) {
                        // $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                        $this->records->fields [$fld] = 'N';
                    }
                    else {
                        $this->records->fields [$fld] = 'Y';
                    }
                }
                else {
                    $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                }
                //add created by
                if($fld == 'createdBy') {
                    $this->records->fields [$fld] = $this->session->userdata('current_user')->userID;
                }

            }

            $this->records->pfield = $this->pfield;
            $this->records->pk = $this->encrypter->decode ( $this->input->post ( $this->pfield ) );

            // field logs here
            $wasChange = $this->log_model->field_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->encrypter->decode ( $this->input->post ( $this->pfield ) ), 'Update', $this->records->fields );

            if ($this->records->update ()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
                    $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->records->pk, 'Update', $logs );
                }
                //remove order details
                $this->delete_order_details($this->records->pk);
                //add order details
                $clothes_qtys = $this->input->post ( 'clothes_qtys' );
                $clothes_ids = $this->input->post('clothes_ids');
                $this->insert_order_details($this->records->pk, $clothes_qtys, $clothes_ids);

                // successful
                $data ["class"] = "success";
                $data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully updated.";
                $data ["urlredirect"] = $this->controller_page . "/view/" . trim ( $this->input->post ( $this->pfield ) );
                $this->load->view ( "header", $data );
                $this->load->view ( "message" );
                $this->load->view ( "footer" );
            } else {
                // error
                $data ["class"] = "danger";
                $data ["msg"] = "Error in updating the " . $this->data ['current_module'] ['module_label'] . "!";
                $data ["urlredirect"] = $this->controller_page . "/view/" . $this->records->pk;
                $this->load->view ( "header", $data );
                $this->load->view ( "message" );
                $this->load->view ( "footer" );
            }
        } else {
            // error
            $data ["class"] = "danger";
            $data ["msg"] = "Sorry, you don't have access to this page!";
            $data ["urlredirect"] = "";
            $this->load->view ( "header", $data );
            $this->load->view ( "message" );
            $this->load->view ( "footer" );
        }
    }

    public function delete($id = 0) {
        // load submenu
        $this->submenu ();
        $data = $this->data;
        $id = $this->encrypter->decode ( $id );

        // check roles
        if ($this->roles ['delete']) {
            // set fields
            $this->records->fields = array ();
            // set table
            $this->records->table = $this->table;
            // set where
            $this->records->where [$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve ();

            if (! empty ( $this->records->field )) {
                $this->records->pfield = $this->pfield;
                $this->records->pk = $id;

                // record logs
                $rec_value = $this->records->field->name;

                // check if in used
                if (! $this->_in_used ( $id )) {
                    if ($this->records->delete ()) {
                        // record logs
                        $logfield = $this->logfield;

                        $logs = "Record - " . $this->records->field->$logfield;
                        $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $this->records->pk, 'Delete', $logs );

                        // successful
                        $data ["class"] = "success";
                        $data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully deleted.";
                        $data ["urlredirect"] = $this->controller_page . "/";
                        $this->load->view ( "header", $data );
                        $this->load->view ( "message" );
                        $this->load->view ( "footer" );
                    } else {
                        // error
                        $data ["class"] = "danger";
                        $data ["msg"] = "Error in deleting the " . $this->data ['current_module'] ['module_label'] . "!";
                        $data ["urlredirect"] = "";
                        $this->load->view ( "header", $data );
                        $this->load->view ( "message" );
                        $this->load->view ( "footer" );
                    }
                } else {
                    // error
                    $data ["class"] = "danger";
                    $data ["msg"] = "Data integrity constraints.";
                    $data ["urlredirect"] = "";
                    $this->load->view ( "header", $data );
                    $this->load->view ( "message" );
                    $this->load->view ( "footer" );
                }

            } else {
                // error
                $data ["class"] = "danger";
                $data ["msg"] = $this->module . " record not found!";
                $data ["urlredirect"] = "";
                $this->load->view ( "header", $data );
                $this->load->view ( "message" );
                $this->load->view ( "footer" );
            }
        } else {
            echo "no roles";
            // error
            $data ["class"] = "danger";
            $data ["msg"] = "Sorry, you don't have access to this page!";
            $data ["urlredirect"] = "";
            $this->load->view ( "header", $data );
            $this->load->view ( "message" );
            $this->load->view ( "footer" );
        }
    }

    public function view($id) {
        $id = $this->encrypter->decode ( $id );

        // load submenu
        $this->submenu ();
        $data = $this->data;
        // $this->roles['view'] = 1;
        if ($this->roles ['view']) {

            $this->db->select ( 'branches.branchName' );
            $this->db->select ( 'branches.branchCode' );
            //customer details
            $this->db->select ( 'customers.fname' );
            $this->db->select ( 'customers.mname' );
            $this->db->select ( 'customers.lname' );
            //service type
            $this->db->select('service_types.serviceID');
            $this->db->select('service_types.regRate');
            $this->db->select('service_types.discountedRate');
            $this->db->select('service_types.serviceType');
            //users details
            $this->db->select('users.firstName');
            $this->db->select('users.middleName');
            $this->db->select('users.lastName');

            // select
            $this->db->select ( $this->table . '.*' );

            // from
            $this->db->from ( $this->table );

            // join
            $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
            $this->db->join ( 'customers', $this->table . '.custID=customers.custID', 'left' );
            $this->db->join ( 'service_types', $this->table . '.serviceID=service_types.serviceID', 'left' );
            $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );
            $this->db->where('orderID', $id);



            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

            //order details
            $this->db->select('order_details.qty');
            $this->db->select('clothes_categories.category');
            $this->db->from('order_details');
            $this->db->join('clothes_categories', 'order_details.clothesCatID=clothes_categories.	clothesCatID', 'left');
            $this->db->where('orderID', $id);
            $clothes_categories = $this->db->get()->result();

            //echo json_encode($order_details);

            $data['clothes_categories'] =  $clothes_categories;



            $data ['in_used'] = $this->_in_used ( $id );

            // record logs
            if ($this->config_model->getConfig ( 'Log all record views' ) == '1') {
                $logs = "Record - " . $this->records->field->name;
                $this->log_model->table_logs ( $this->module, $this->table, $this->pfield, $this->records->field->$data ['pfield'], 'View', $logs );
            }

            //load views
            $this->load->view ( 'header', $data );
            $this->load->view ( $this->module_path . '/view' );
            $this->load->view ( 'footer' );
        } else {
            // no access this page
            $data ['class'] = "danger";
            $data ['msg'] = "Sorry, you don't have access to this page!";
            $data ['urlredirect'] = "";
            $this->load->view ( 'header', $data );
            $this->load->view ( 'message' );
            $this->load->view ( 'footer' );
        }
    }

    public function show() {
        //************** general settings *******************
        // load submenu
        $this->submenu ();
        $data = $this->data;

        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array (
            array ('variable' => 'lname', 'field' =>'customers'. '.lname', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'branchID', 'field' => $this->table . '.branchID', 'default_value' => '', 'operator' => 'where' ),
            //array ('variable' => '', 'field' => 'customers' . '.lname', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'serviceID', 'field' => $this->table . '.serviceID', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'isDiscounted', 'field' => 'employees.isDiscounted', 'default_value' => '', 'operator' => 'where' ),
            array ('variable' => 'qyt', 'field' => $this->table . '.qty', 'default_value' => '', 'operator' => 'where' )
        );

        // sorting fields
        $sorting_fields = array ('orderID' => 'asc' );

        $controller = $this->uri->segment ( 1 );

        if ($this->uri->segment ( 3 ))
            $offset = $this->uri->segment ( 3 );
        else
            $offset = 0;

        // source of filtering
        $filter_source = 0; // default/blank
        if ($this->input->post ( 'filterflag' ) || $this->input->post ( 'sortby' )) {
            $filter_source = 1;
        } else {
            foreach ( $condition_fields as $key ) {
                if ($this->input->post ( $key ['variable'] )) {
                    $filter_source = 1; // form filters
                    break;
                }
            }
        }

        if (! $filter_source) {
            foreach ( $condition_fields as $key ) {
                if ($this->session->userdata ( $controller . '_' . $key ['variable'] ) || $this->session->userdata ( $controller . '_sortby' ) || $this->session->userdata ( $controller . '_sortorder' )) {
                    $filter_source = 2; // session
                    break;
                }
            }
        }

        switch ($filter_source) {
            case 1 :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = trim ( $this->input->post ( $key ['variable'] ) );
                }

                $sortby = trim ( $this->input->post ( 'sortby' ) );
                $sortorder = trim ( $this->input->post ( 'sortorder' ) );

                break;
            case 2 :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
                }

                $sortby = $this->session->userdata ( $controller . '_sortby' );
                $sortorder = $this->session->userdata ( $controller . '_sortorder' );
                break;
            default :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $key ['default_value'];
                }
                $sortby = "";
                $sortorder = "";
        }

        if ($this->input->post ( 'limit' )) {
            if ($this->input->post ( 'limit' ) == "All")
                $limit = "";
            else
                $limit = $this->input->post ( 'limit' );
        } else if ($this->session->userdata ( $controller . '_limit' )) {
            $limit = $this->session->userdata ( $controller . '_limit' );
        } else {
            $limit = 25; // default limit
        }

        // set session variables
        foreach ( $condition_fields as $key ) {
            $this->session->set_userdata ( $controller . '_' . $key ['variable'], $$key ['variable'] );
        }
        $this->session->set_userdata ( $controller . '_sortby', $sortby );
        $this->session->set_userdata ( $controller . '_sortorder', $sortorder );
        $this->session->set_userdata ( $controller . '_limit', $limit );

        // assign data variables for views
        foreach ( $condition_fields as $key ) {
            $data [$key ['variable']] = $$key ['variable'];
        }

        // select
        $this->db->select ( $this->table . '.*' );
        $this->db->select ( 'branches.branchName' );
        $this->db->select ( 'branches.branchCode' );
        //customer details
        $this->db->select ( 'customers.fname' );
        $this->db->select ( 'customers.mname' );
        $this->db->select ( 'customers.lname' );
        //service type
        $this->db->select('service_types.serviceID');
        $this->db->select('service_types.regRate');
        $this->db->select('service_types.discountedRate');
        $this->db->select('service_types.serviceType');

        // from
        $this->db->from ( $this->table );

        // join
        $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->join ( 'customers', $this->table . '.custID=customers.custID', 'left' );
        $this->db->join ( 'service_types', $this->table . '.serviceID=service_types.serviceID', 'left' );

        // where
        // set conditions here
        foreach ( $condition_fields as $key ) {
            $operators = explode ( '_', $key ['operator'] );
            $operator = $operators [0];
            // check if the operator is like
            if (count ( $operators ) > 1) {
                // like operator
                if (trim ( $$key ['variable'] ) != '' && $key ['field'])
                    $this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
            } else {
                if (trim ( $$key ['variable'] ) != '' && $key ['field'])
                    $this->db->$operator ( $key ['field'], $$key ['variable'] );
            }
        }

        // get
        $data ['ttl_rows'] = $config ['total_rows'] = $this->db->count_all_results ();

        // set pagination
        $config ['full_tag_open'] = "<ul class='pagination'>";
        $config ['full_tag_close'] = "</ul>";
        $config ['num_tag_open'] = "<li class='page-order'>";
        $config ['num_tag_close'] = "</li>";
        $config ['cur_tag_open'] = "<li class='page-order active'>";
        $config ['cur_tag_close'] = "</li>";
        $config ['next_tag_open'] = "<li class='page-order'>";
        $config ['next_tagl_close'] = "</li>";
        $config ['prev_tag_open'] = "<li class='page-order'>";
        $config ['prev_tagl_close'] = "</li>";
        $config ['first_tag_open'] = "<li class='page-order'>";
        $config ['first_tagl_close'] = "</li>";
        $config ['last_tag_open'] = "<li class='page-order'>";
        $config ['last_tagl_close'] = "</li>";

        $config ['base_url'] = $this->controller_page . '/show/';
        $config ['per_page'] = $limit;
        $this->pagination->initialize ( $config );


        // select
        $this->db->select ( $this->table . '.*' );
        $this->db->select ( 'branches.branchName' );
        $this->db->select ( 'branches.branchCode' );
        //customer details
        $this->db->select ( 'customers.fname' );
        $this->db->select ( 'customers.mname' );
        $this->db->select ( 'customers.lname' );
        //service type
        $this->db->select('service_types.serviceID');
        $this->db->select('service_types.regRate');
        $this->db->select('service_types.serviceType');
        $this->db->select('service_types.discountedRate');

        // from
        $this->db->from ( $this->table );

        // join
        $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->join ( 'customers', $this->table . '.custID=customers.custID', 'left' );
        $this->db->join ( 'service_types', $this->table . '.serviceID=service_types.serviceID', 'left' );

        // where
        // set conditions here
        foreach ( $condition_fields as $key ) {
            $operators = explode ( '_', $key ['operator'] );
            $operator = $operators [0];
            // check if the operator is like
            if (count ( $operators ) > 1) {
                // like operator
                if (trim ( $$key ['variable'] ) != '' && $key ['field'])
                    $this->db->$operator ( $key ['field'], $$key ['variable'], $operators [1] );
            } else {
                if (trim ( $$key ['variable'] ) != '' && $key ['field'])
                    $this->db->$operator ( $key ['field'], $$key ['variable'] );
            }
        }

        if ($sortby && $sortorder) {
            $this->db->order_by ( $sortby, $sortorder );

            if (! empty ( $sorting_fields )) {
                foreach ( $sorting_fields as $fld => $s_order ) {
                    if ($fld != $sortby) {
                        $this->db->order_by ( $fld, $s_order );
                    }
                }
            }
        } else {
            $ctr = 1;
            if (! empty ( $sorting_fields )) {
                foreach ( $sorting_fields as $fld => $s_order ) {
                    if ($ctr == 1) {
                        $sortby = $fld;
                        $sortorder = $s_order;
                    }
                    $this->db->order_by ( $fld, $s_order );

                    $ctr ++;
                }
            }
        }

        if ($limit) {
            if ($offset) {
                $this->db->limit ( $limit, $offset );
            } else {
                $this->db->limit ( $limit );
            }
        }

        // assigning variables
        $data ['sortby'] = $sortby;
        $data ['sortorder'] = $sortorder;
        $data ['limit'] = $limit;
        $data ['offset'] = $offset;

        // get
        $data ['records'] = $this->db->get ()->result ();
        // load views
        $this->load->view ( 'header', $data );
        $this->load->view ( $this->module_path . '/list' );
        $this->load->view ( 'footer' );


    }


    //Conditions and fields changes
    public function check_duplicate() {
        $this->db->where ( 'branchID', trim ( $this->input->post ( 'custID' ) ) );

        if ($this->db->count_all_results ( $this->table ))
            echo "1"; // duplicate
        else
            echo "0";
    }

    public function check_service_discount()
    {
        $isDiscounted = trim($this->input->post('is_discounted'));
        $this->db->select('discountedRate');
        $this->db->select('regRate');
        $this->db->where('serviceID', trim($this->input->post('service_id')));
        $query = $this->db->get('service_types');
        if ($query->num_rows() > 0) {
            if($isDiscounted) {
                echo json_encode((int)$query->row()->discountedRate);
            }
            else {
                echo json_encode((int)$query->row()->regRate);
            }
        } else {
            echo 0;
        }
    }

    public function insert_order_details($orderID, $clothes_qtys = array(), $clothes_ids = array())
    {
        //return $clothes_qtys;
       // $result = array_combine($clothes_qtys, $clothes_ids);
        $result = array_combine($clothes_ids, $clothes_qtys);

        $data = [];
        foreach ($result as $key => $value) {
            if(!empty($key)) {
                $data[] = [
                    'orderID' => $orderID,
                    'clothesCatID' => $key,
                    'qty' => $value
                ];
            }

        }
        //return $data;
       $this->db->insert_batch('order_details', $data);
    }

    public function delete_order_details($orderID)
    {
        $this->db->where('orderID', $orderID);
        $this->db->delete('order_details');
    }

}
