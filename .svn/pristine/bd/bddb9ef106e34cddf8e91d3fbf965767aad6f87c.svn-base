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

        $table_fields = array ('title', 'fname', 'lname', 'mname', 'suffix', 'provinceID', 'cityID', 'barangayID', 'telephone', 'mobile', 'bday' , 'isRegular');


        // check role
        if ($this->roles ['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
            }

            if ($this->records->save ()) {
                $this->records->fields = array ();
                $id = $this->records->where ['custID'] = $this->db->insert_id ();
                $this->records->retrieve ();
                // record logs
                $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );
                $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $id, 'Insert', $logs );

                //echo $this->db->last_query();

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
        $table_fields = array ('title', 'fname', 'lname', 'mname', 'suffix', 'provinceID', 'cityID', 'barangayID', 'telephone', 'mobile', 'bday' , 'isRegular');


        // check roles
        if ($this->roles ['edit']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach ( $table_fields as $fld ) {
                $this->records->fields [$fld] = trim ( $this->input->post ( $fld ) );
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
            // for retrieve with joining tables -------------------------------------------------
            $this->db->select ( $this->table . ".*" );
            $this->db->select ( 'provinces.province' );
            $this->db->select ( 'cities.city' );
            $this->db->select ( 'barangays.barangay' );
            $this->db->from($this->table);
            $this->db->join ( 'provinces', $this->table . '.provinceID=provinces.provinceID', 'left' );
            $this->db->join ( 'cities', $this->table . '.cityID=cities.cityID', 'left' );
            $this->db->join ( 'barangays', $this->table . '.barangayID=barangays.barangayID', 'left' );
            $this->db->where ( $this->pfield, $id );
            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

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
        $condition_fields = array(
            array(
                'variable'=>'lname',
                'field'=>$this->table.'.lname',
                'default_value'=>'',
                'operator'=>'where'
            ),
            array(
                'variable'=>'fname',
                'field'=>$this->table.'.fname',
                'default_value'=>'',
                'operator'=>'where'
            ),
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

        // from
        $this->db->from ( $this->table );

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


        // from
        $this->db->from ( $this->table );

        // join
//        $this->db->join ( 'companies', $this->table . '.companyID=companies.companyID', 'left' );
//        $this->db->join ( 'employees', $this->table . '.branchHeadID=employees.empID', 'left' );

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
        $this->db->where ( 'fName', trim ( $this->input->post ( 'fName' ) ), 'lName', trim ($this->input->post('fName')) );

        if ($this->db->count_all_results ( $this->table ))
            echo "1"; // duplicate
        else
            echo "0";
    }

}
