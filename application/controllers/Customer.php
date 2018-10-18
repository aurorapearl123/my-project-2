<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Customer extends CI_Controller {
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
        $this->module = 'Customer';
        $this->data['controller_page'] = $this->controller_page = site_url('customer'); // defines contoller link
        $this->table = 'customers'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'custID'; // defines primary key
        $this->logfield = 'custID';
        $this->module_path = 'modules/' . strtolower ( str_replace ( " ", "_", $this->module ) ) . '/customer'; // defines module path


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
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Customer']; // defines the current sub module
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
        $tables = array ('order_headers' => 'custID', 'complaints' => 'custID' );

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

        $table_fields = array ('title', 'fname', 'mname', 'lname', 'suffix', 'provinceID', 'cityID', 'barangayID','address',  'contact', 'isRegular','agreeTerms');


        // check role
        if ($this->roles ['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
            }
            $this->records->fields['bday'] = date('Y-m-d',strtotime($this->input->post('bday')));
            // die();


            if ($this->records->save ()) {
                $this->records->fields = array ();
                $id = $this->records->where ['custID'] = $this->db->insert_id ();
                $this->records->retrieve ();
                // record logs
                $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
                $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $id, 'Insert', $logs );

                //echo $this->db->last_query();
                //insert to elastic search
                $this->load->model('elastic_model');
                $full_name = $this->input->post('fname') .' '.$this->input->post('mname').' '.$this->input->post('lname');
                $data = [
                    'index' => 'customers',
                    'type' => 'customer',
                    'id' => $id,
                    'body' => [
                        'id' => $id,
                        'full_name' => $full_name,
                        'title' => $this->input->post('title'),
                        'suffix' => $this->input->post('suffix'),
                        'contact' => $this->input->post('contact'),
                        'bday' => date('Y-m-d',strtotime($this->input->post('bday'))),
                        'isRegular' => $this->input->post('isRegular'),
                        'status' => 1
                    ]
                ];

                $this->elastic_model->saveToElasticSearch($data);

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

            $this->records->fields[] = 'provinces.province';       
            $this->records->fields[] = 'cities.city';       
            $this->records->fields[] = 'barangays.barangay';       
            // set joins
            $this->records->joins[]  = array('provinces',$this->table.'.provinceID=provinces.provinceID','left');
            $this->records->joins[]  = array('cities',$this->table.'.cityID=cities.cityID','left');
            $this->records->joins[]  = array('barangays',$this->table.'.barangayID=barangays.barangayID','left');            
            // set where
            $this->records->where [$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve ();
            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->records->field;

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

    public function update() {
        // load submenu
        $this->submenu ();
        $data = $this->data;
        $table_fields = array ('title', 'fname', 'lname', 'mname', 'suffix', 'provinceID', 'cityID', 'barangayID', 'address', 'contact', 'isRegular','agreeTerms');


        // check roles
        if ($this->roles ['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
            }

            $this->records->fields['bday'] = date('Y-m-d',strtotime($this->input->post('bday'))); // dateFormat syntax

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


                $this->load->model('elastic_model');

                $full_name = $this->input->post('fname') .' '.$this->input->post('mname').' '.$this->input->post('lname');
                $data = [
                    'index' => 'customers',
                    'type' => 'customer',
                    'id' => $this->records->pk,
                    'body' => [
                        'doc' => [
                            'id' => $this->records->pk,
                            'full_name' => $full_name,
                            'title' => $this->input->post('title'),
                            'suffix' => $this->input->post('suffix'),
                            'contact' => $this->input->post('contact'),
                            'bday' => date('Y-m-d',strtotime($this->input->post('bday'))),
                            'isRegular' => $this->input->post('isRegular'),
                            'status' => 1
                        ]
                    ]
                ];

                $this->elastic_model->update($data);

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

                        $this->load->model('elastic_model');

                        $this->elastic_model->delete('customers', 'customer', $id);

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
        //$id = $this->encrypter->decode ( $id );

        // load submenu
        $this->submenu ();
        $data = $this->data;
        // $this->roles['view'] = 1;
        if ($this->roles ['view']) {
            // for retrieve with joining tables -------------------------------------------------
            $this->records->table = $this->table;
            // set fields for the current table
            $this->records->setFields ();

            $this->records->fields[] = 'provinces.province';       
            $this->records->fields[] = 'cities.city';       
            $this->records->fields[] = 'barangays.barangay';       
            // set joins
            $this->records->joins[]  = array('provinces',$this->table.'.provinceID=provinces.provinceID','left');
            $this->records->joins[]  = array('cities',$this->table.'.cityID=cities.cityID','left');
            $this->records->joins[]  = array('barangays',$this->table.'.barangayID=barangays.barangayID','left');            
            // set where
            $this->records->where [$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve ();
            // echo $this->db->last_query();
            // die();
            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->records->field;

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

       //$this->migrateCustomerElastic();


        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array (
            array ('variable' => 'title', 'field' => $this->table . '.title', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'fname', 'field' => $this->table . '.fname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'mname', 'field' => $this->table . '.mname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'lname', 'field' => $this->table . '.lname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'suffix', 'field' => $this->table . '.suffix', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'contact', 'field' => $this->table . '.contact', 'default_value' => '', 'operator' => 'where' ),
            // array ('variable' => 'bday', 'field' => $this->table . '.bday', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'isRegular', 'field' => $this->table . '.isRegular', 'default_value' => '', 'operator' => 'where' ),
            array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '', 'operator' => 'where' ) 
        );
        
        // sorting fields
        $sorting_fields = array ('lname' => 'asc' );
    
        
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
                $bday     = trim($this->input->post('bday'));
                $sortby = trim ( $this->input->post ( 'sortby' ) );
                $sortorder = trim ( $this->input->post ( 'sortorder' ) );
                
                break;
            case 2 :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
                }
                $bday     = trim($this->input->post('bday'));
                $sortby = $this->session->userdata ( $controller . '_sortby' );
                $sortorder = $this->session->userdata ( $controller . '_sortorder' );
                break;
            default :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $key ['default_value'];
                }
                $bday     = "";
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
        $this->session->set_userdata($controller.'bday', $bday);
        $this->session->set_userdata ( $controller . '_sortby', $sortby );
        $this->session->set_userdata ( $controller . '_sortorder', $sortorder );
        $this->session->set_userdata ( $controller . '_limit', $limit );
        
        // assign data variables for views
        foreach ( $condition_fields as $key ) {
            $data [$key ['variable']] = $$key ['variable'];
        }
        $data['bday'] = $bday;
        // select
        $this->db->select ( $this->table . '.*' );
        
        // from
        $this->db->from ( $this->table );
        
        // join        
        
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
        
        if($bday) {
            $this->db->like($this->table.'.bday',date('Y-m-d',strtotime($bday)));
        }
        // get
        $data ['ttl_rows'] = $config ['total_rows'] = $this->db->count_all_results ();
        
       
        $config ['base_url'] = $this->controller_page . '/show/';
        $config ['per_page'] = $limit;
        $this->pagination->initialize ( $config );
        
        // select
        $this->db->select ( $this->table . '.*' );
        
        // from
        $this->db->from ( $this->table );
        
        // join
               
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
        
        if($bday) {
            $this->db->like($this->table.'.bday',date('Y-m-d',strtotime($bday)));
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

    public function printlist() 
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array (
            array ('variable' => 'title', 'field' => $this->table . '.title', 'default_value' => '', 'operator' => 'where' ), 
            array ('variable' => 'fname', 'field' => $this->table . '.fname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'mname', 'field' => $this->table . '.mname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'lname', 'field' => $this->table . '.lname', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'suffix', 'field' => $this->table . '.suffix', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'contact', 'field' => $this->table . '.contact', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'bday', 'field' => $this->table . '.bday', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'isRegular', 'field' => $this->table . '.isRegular', 'default_value' => '', 'operator' => 'like_both' ),
            array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '', 'operator' => 'where' ) 
        );
        
        // sorting fields
        $sorting_fields = array('lname'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select ( $this->table . '.*' );
        $this->db->select ( 'provinces.province' );
        $this->db->select ( 'cities.city' );
        $this->db->select ( 'barangays.barangay' );
        // from
        $this->db->from ( $this->table );
        // join
        $this->db->join ( 'provinces', $this->table . '.provinceID=provinces.provinceID', 'left' );
        $this->db->join ( 'cities', $this->table . '.cityID=cities.cityID', 'left' );
        $this->db->join ( 'barangays', $this->table . '.barangayID=barangays.barangayID', 'left' );
        
        // where
        
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators)>1) {
                // like operator
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
        
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($ctr == 1) {
                        $sortby     = $fld;
                        $sortorder  = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
        
                    $ctr++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit,$offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        
        // get
        $data['records'] = $this->db->get()->result();
        $data['title'] = "Customer List";

        //load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path.'/printlist');
        $this->load->view('footer_print');
    }
        
    function exportlist()
    {
         // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'title', 'field'=>$this->table.'.title', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'fname', 'field'=>$this->table.'.fname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'mname', 'field'=>$this->table.'.mname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>$this->table.'.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'suffix', 'field'=>$this->table.'.suffix', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'contact', 'field'=>$this->table.'.contact', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'bday', 'field'=>$this->table.'.bday', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'isRegular', 'field'=>$this->table.'.isRegular', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('lname'=>'desc');
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select ( $this->table . '.*' );
        $this->db->select ( 'provinces.province' );
        $this->db->select ( 'cities.city' );
        $this->db->select ( 'barangays.barangay' );
        // from
        $this->db->from ( $this->table );
        // join
        $this->db->join ( 'provinces', $this->table . '.provinceID=provinces.provinceID', 'left' );
        $this->db->join ( 'cities', $this->table . '.cityID=cities.cityID', 'left' );
        $this->db->join ( 'barangays', $this->table . '.barangayID=barangays.barangayID', 'left' );
        
        // where
        // set conditions here
        foreach($condition_fields as $key) {
            $operators = explode('_',$key['operator']);
            $operator = $operators[0];
            // check if the operator is like
            if (count($operators)>1) {
                // like operator
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable'], $operators[1]);
            } else {
                if (trim($$key['variable'])!='' && $key['field'])
                    $this->db->$operator($key['field'], $$key['variable']);
            }
        }
        
        if ($sortby && $sortorder) {
            $this->db->order_by($sortby, $sortorder);
        
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($fld != $sortby) {
                        $this->db->order_by($fld, $s_order);
                    }
                }
            }
        } else {
            $ctr = 1;
            if (!empty($sorting_fields)) {
                foreach($sorting_fields as $fld=>$s_order) {
                    if ($ctr == 1) {
                        $sortby     = $fld;
                        $sortorder  = $s_order;
                    }
                    $this->db->order_by($fld, $s_order);
        
                    $ctr++;
                }
            }
        }
        
        if ($limit) {
            if ($offset) {
                $this->db->limit($limit,$offset);
            } else {
                $this->db->limit($limit);
            }
        }
        
        // assigning variables
        $data['sortby']     = $sortby;
        $data['sortorder']  = $sortorder;
        $data['limit']      = $limit;
        $data['offset']     = $offset;
        
        // get
        $records = $this->db->get()->result();
        
    
        $title          = "Customer List";
        $companyName    = $this->config_model->getConfig('Company');
        $address        = $this->config_model->getConfig('Address');
         
        //XML Blurb
        $data = "<?xml version='1.0'?>
  
            <?mso-application progid='Excel.Sheet'?>
  
            <Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'>
            <Styles>
            <Style ss:ID='s20'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
              <Font ss:Bold='1' ss:Size='14'/>
            </Style>
    
            <Style ss:ID='s23'>
              <Font ss:Bold='1'/>
            <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
  
            <Style ss:ID='s24'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
                <Font ss:Bold='1'/>
            </Style>
        
            <Style ss:ID='s24A'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
            </Style>
        
            <Style ss:ID='s24B'>
                <Alignment ss:Horizontal='Center' ss:Vertical='Bottom'/>
               <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
        
            <Style ss:ID='s25'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
                <Font ss:Bold='1'/>
            </Style>
            <Style ss:ID='s26'>
                <Alignment ss:Horizontal='Right' ss:Vertical='Bottom'/>
                <NumberFormat ss:Format='#,##0.00_ ;[Red]\-#,##0.00\ '/>
               <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            <Style ss:ID='s27'>
                  <Borders>
                    <Border ss:Position='Left' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Top' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Right' ss:LineStyle='Continuous' ss:Weight='1'/>
                    <Border ss:Position='Bottom' ss:LineStyle='Continuous' ss:Weight='1'/>
                   </Borders>
            </Style>
            </Styles>
    
            <Worksheet ss:Name='".$title."'>
  
            <Table>
            <Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
            <Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
            <Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
            <Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
            <Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
                ";
    
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='13'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="  ";
        $fields[]="TITLE";            
        $fields[]="FIRST NAME";            
        $fields[]="MIDDLE NAME";            
        $fields[]="LAST NAME";            
        $fields[]="SUFFIX";   
        $fields[]="CONTACT NO";            
        $fields[]="PROVINCE";            
        $fields[]="CITY";            
        $fields[]="BARANGAY";            
        $fields[]="ADDRESS";            
        $fields[]="BDAY";            
        $fields[]="IS_REGULAR";            
        $fields[]="STATUS";
    
        $data .= "<Row ss:StyleID='s24'>";
        //Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
    
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$ctr.".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->title."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->fname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->mname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->lname."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->suffix."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->contact."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->province."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->city."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->barangay."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->bday."</Data></Cell>";                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->isRegular."</Data></Cell>";                
                if  ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Active</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Inactive</Data></Cell>";
                }
                $data .= "</Row>";
    
                $ctr++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
         
    
        //Final XML Blurb
        $filename = "customer";
    
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
         
        echo $data;
    
    }

    //Conditions and fields changes
    //Conditions and fields changes
    public function check_duplicate()
    {
          
        $this->db->where('fname', trim($this->input->post('fname')));      
        $this->db->where('mname', trim($this->input->post('mname')));      
        $this->db->where('lname', trim($this->input->post('lname')));      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }
    public function elastic_search()
    {
        $this->load->model('elastic_model');
        $q = $this->input->post('search');
        $data = [
            'index' => 'customers',
            'type' => 'customer',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $q,
                        'fields' => [
                            'full_name', 'contact', 'suffix', 'title'
                        ]
                    ]
                ]
            ]
        ];


        $result = $this->elastic_model->search($data);
        echo json_encode($result);
    }

    public function migrateCustomerElastic()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $customers = $this->db->get()->result();

        $this->load->model('elastic_model');
        foreach ($customers as $customer) {
            $full_name = $customer->fname.' '.$customer->mname.' '.$customer->lname;
            $data = [
                'index' => 'customers',
                'type' => 'customer',
                'id' => $customer->custID,
                'body' => [
                    'id' => $customer->custID,
                    'full_name' => $full_name,
                    'title' => $customer->title,
                    'suffix' => $customer->suffix,
                    'contact' => $customer->contact,
                    'bday' => $customer->bday,
                    'isRegular' => $customer->isRegular,
                    'status' => $customer->status,
                    'profile' => $customer->profile

                ]
            ];
            $this->elastic_model->saveToElasticSearch($data);
        }

    }

}
