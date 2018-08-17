<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Withdrawal_slip extends CI_Controller {
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
        $this->module = 'Inventory';
        $this->data['controller_page'] = $this->controller_page = site_url('withdrawal_slip'); // defines contoller link
        $this->table = 'ws_headers'; // defines the default table
        $this->pfield = $this->data ['pfield'] = 'wsID'; // defines primary key
        $this->logfield = 'wsID';
        $this->module_path = 'modules/' . strtolower ( str_replace(" ","_",$this->module) ) . '/withdrawal_slip'; // defines module path




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
        $this->data ['current_module'] = $this->modules [$this->module]['sub']['Withdrawal slip']; // defines the current sub module

        // check roles
        $this->check_roles ();
        $this->data ['roles'] = $this->roles;
    }

    private function check_roles() {
        // check roles
        $this->roles ['create'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Add ' . $this->module );
        $this->roles ['view'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'View ' . $this->module );
        $this->roles ['edit'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Edit Existing ' . $this->module );
        $this->roles ['delete'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Delete Existing ' . $this->module );
        $this->roles ['approve'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Approve ' . $this->module );

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

            //get branch
            $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
            $branch = $this->db->get('branches');
            $current_branch = $branch->row();
            $data['branchName'] = $current_branch->branchName;
            $data['branchID'] = $current_branch->branchID;

            //get branch
            $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
            $branch = $this->db->get('branches');
            $current_branch = $branch->row();
            $data['branchName'] = $current_branch->branchName;
            $data['branchID'] = $current_branch->branchID;

            //get items
            $this->db->select(' *');
            $this->db->from('items');
            //intem inventory
            //$this->db->select('item_inventory.qty as expected_qty');
             // join
            $this->db->join ( 'item_inventory', 'items' . '.itemID=item_inventory.itemID', 'left' );
            $this->db->where('items.status', 1);
            $this->db->where('item_inventory.branchID', $this->session->userdata('current_user')->branchID);

            $items = $this->db->get()->result();
            $data['items'] = $items;


        
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

        $table_fields = array ('date', 'branchID', 'remarks','createdBy');


        // check role
        if ($this->roles ['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                //add created by
                if($fld == 'createdBy') {
                    $this->records->fields [$fld] = $this->session->userdata('current_user')->userID;
                }
                else {
                    $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
                }


            }

                 // $items_ids = $this->input->post ( 'item_ids' );
                 //    $quantity = $this->input->post('quantity');
                 //    $test = $this->insert_details(3, $items_ids, $quantity);
                 //    echo json_encode($test);
                 //    die();

            if ($this->records->save ()) {
                $this->records->fields = array ();
                $id = $this->records->where ['wsID'] = $this->db->insert_id ();
                $this->records->retrieve ();
                // record logs
                $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );

                $items_ids = $this->input->post ( 'item_ids' );
                $quantity = $this->input->post('quantity');
                $this->insert_details($id, $items_ids, $quantity);


                $this->addSeriesNo($id, $this->session->userdata('current_user')->branchID);
                $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $id, 'Insert', $logs );

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


            $this->db->select ( $this->table . '.*' );
            $this->db->select ( 'branches.branchName' );
            $this->db->select ( 'branches.branchCode' );

            //users details
            $this->db->select ( 'users.firstName' );
            $this->db->select ( 'users.middleName' );
            $this->db->select ( 'users.lastName' );


            // from
            $this->db->from ( $this->table );

            // join
            $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
            //$this->db->join ( 'suppliers', $this->table . '.suppID=suppliers.supplierID', 'left' );
            $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );
            $this->db->where('wsID', $id);

            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

        

            //get headers details
            $details = $this->getDetails('ws_details', 'wsID', $id, $this->session->userdata('current_user')->branchID);
            $data['items'] = $details;


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
        $table_fields = array ('date', 'branchID', 'remarks','createdBy');


        // check roles
        if ($this->roles ['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                //add created by
                if($fld == 'createdBy') {
                    $this->records->fields [$fld] = $this->session->userdata('current_user')->userID;
                }
                else {
                    $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
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
                //remove rr details
                $this->delete_details('ws_details', 'wsID', $this->records->pk);
                //insert details

                $item_ids = $this->input->post ( 'item_ids' );
                $quantities = $this->input->post('quantities');
                $this-> insert_details($this->records->pk, $item_ids, $quantities);


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

            // select
            $this->db->select ( $this->table . '.*' );
            $this->db->select ( 'branches.branchName' );
            $this->db->select ( 'branches.branchCode' );

            //users details
            $this->db->select ( 'users.firstName' );
            $this->db->select ( 'users.middleName' );
            $this->db->select ( 'users.lastName' );
            
            //approved by
            $this->db->select('approvedUser.firstName as approvedFirstName');
            $this->db->select('approvedUser.middleName as approvedMiddleName');
            $this->db->select('approvedUser.lastName as approvedLastName');
            //cancelled by
            $this->db->select('cancelledUser.firstName as cancelledFirstName');
            $this->db->select('cancelledUser.middleName as cancelledMiddleName');
            $this->db->select('cancelledUser.lastName as cancelledLastName');




            // from
            $this->db->from ( $this->table );

            // join
            $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
            $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );
            //approved by
            $this->db->join ( 'users as approvedUser', $this->table . '.approvedBy=users.userID', 'left' );
            //called by
            $this->db->join ( 'users as cancelledUser', $this->table . '.	cancelledBy=users.userID', 'left' );

            $this->db->where('wsID', $id);


            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

            //get details
            $details = $this->getDetails('ws_details', 'wsID', $id, $this->session->userdata('current_user')->branchID);
            $data['items'] = $details;

           
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
            array ('variable' => 'date', 'field' =>'date'. '.lname', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'branchID', 'field' => $this->table . '.branchID', 'default_value' => '', 'operator' => 'where' ),
            //array ('variable' => '', 'field' => 'customers' . '.lname', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'remark', 'field' => $this->table . '.remark', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'conductedBy', 'field' => $this->table . '.conductedBy', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'createdBy', 'field' => $this->table . '.createdBy', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '', 'operator' => 'like_both' ),
        );

        // sorting fields
        $sorting_fields = array ('wsID' => 'asc' );

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

        
         $this->db->select ( 'users.firstName' );
         $this->db->select ( 'users.middleName' );
         $this->db->select ( 'users.lastName' );
      

        // from
        $this->db->from ( $this->table );

        // join
        $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );
       
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

        //users details
        $this->db->select ( 'users.firstName' );
        $this->db->select ( 'users.middleName' );
        $this->db->select ( 'users.lastName' );
      
    
        // from
        $this->db->from ( $this->table );

        // join
        $this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );


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
        $this->db->select('descountedRate');
        $this->db->select('regRate');
        $this->db->where('serviceID', trim($this->input->post('service_id')));
        $query = $this->db->get('service_types');
        if ($query->num_rows() > 0) {
            if($isDiscounted) {
                echo json_encode((int)$query->row()->descountedRate);
            }
            else {
                echo json_encode((int)$query->row()->regRate);
            }
        } else {
            echo 0;
        }
    }

    public function insert_details($rrID, $items_ids = array(), $quanties = array())
    {
        /*$result = array_merge($items_ids, $prices);*/
         $size = count($items_ids);
        $size = $size - 1;

        $keys = [];
        foreach (range(0, $size) as $number) {
            //echo $number;
            $keys[] = $number;
        }

        $rrIDs = [];
        foreach (range(0, $size) as $number) {
            //echo $number;
            $rrIDs[] = $rrID;
        }


        $ws_details = $this->combine_keys_with_arrays($keys, array(
            'wsID' => $rrIDs,
            'itemID'  => $items_ids,
            'qty' => $quanties,
        ));

       // return $ws_details;
        return $this->db->insert_batch('ws_details', $ws_details);
    }

    function combine_keys_with_arrays($keys, $arrays) {
        $results = array();

        foreach ($arrays as $subKey => $arr)
        {
            foreach ($keys as $index => $key)
            {
                $results[$key][$subKey] = $arr[$index];
            }
        }

        return $results;
    }

    public function delete_details($table, $compare, $rrID)
    {
        $this->db->where($compare, $rrID);
        $this->db->delete($table);
    }

    public function getDetails($table, $compare, $value, $branchID)
    {


        //join item
        $this->db->select('items.*');
        $this->db->select($table.'.*');
        $this->db->select('item_inventory.qty as inventory_qty');
        $this->db->from($table);
        $this->db->join ( 'items', $table.'.itemID=items.itemID', 'right' );
        $this->db->join ( 'item_inventory', 'items.itemID=item_inventory.itemID', 'left' );

         //$this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->where($compare, $value);
        $this->db->where('item_inventory.branchID', $branchID);
        $details = $this->db->get()->result();
        return $details;
    }

    public function addStockCard()
    {

        $itemID = trim($this->input->post('item_id'));
        $debit = trim($this->input->post('debit'));
        $credit = trim($this->input->post('credit'));
        //$begbal = trim($this->input->post('begBal'));
        //$endbal = trim($this->input->post('endBal'));
        $variance = trim($this->input->post('variance'));
        $wsID = trim($this->input->post('wsID'));

        $row_series = self::get_instance()->db->select("*")->limit(1)->order_by('seriesID',"DESC")->get("seriesno")->row();
        $reference_no = $this->router->fetch_class().'-'.$row_series->wsNo;


        $row = $this->db->select("*")->limit(1)->order_by('id',"DESC")->get("stockcard")->row();
        //$row->endBal;

        $begbal = $row->endBal;

        $endbal = $row->endBal - $variance;

        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update approved by
        //updateApproveBy($table, $compareId, $id, $approvedBy)
        $approvedBy = Generic_ajax::updateApproveBy($this->table, "wsID", $wsID, $this->session->userdata('current_user')->userID);

        $branchId = $this->session->userdata('current_user')->branchID;
        $added = Generic_ajax::addStockCard($reference_no, $itemID, $debit, $credit, $endbal, $begbal, $branchId);

        $response = [
            'data' => [
                'status' => 200,
                'added' => $added,
                'approvedBy' => $approvedBy
            ]
        ];

        echo json_encode($response);
    }

    public function updateCanceledBy()
    {
        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update cancel by
        $wsID = trim($this->input->post('wsID'));

        $cancelledBy = Generic_ajax::updateCancelBy($this->table, "wsID", $wsID, $this->session->userdata('current_user')->userID);
        $response = [
            'data' => [
                'status' => 200,
                'cancelledBy' => $cancelledBy
            ]
        ];

        echo json_encode($response);
    }

    public function addSeriesNo($id, $branchID)
    {
        require_once(APPPATH.'controllers/Generic_ajax.php');
        $series = '0000'.$id;
        $className = $this->router->fetch_class();

        $data = Generic_ajax::addSeriesNo($className, $series, $branchID);
        return $data;

    }


}
