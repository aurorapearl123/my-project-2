<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Physical_count extends CI_Controller {
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
    var $branchID;

    public function __construct() {
        parent::__construct ();
        $this->load->model ( 'generic_model', 'records' );
        $this->module = 'Inventory';
        $this->data['controller_page'] = $this->controller_page = site_url('physical_count'); // defines contoller link
        $this->table = 'pc_headers'; // defines the default table
        $this->pfield = $this->data ['pfield'] = 'pcID'; // defines primary key
        $this->logfield = 'rrID';
        $this->module_path = 'modules/' . strtolower ( str_replace(" ","_",$this->module) ) . '/physical_count'; // defines module path
        $this->branchID = $this->session->userdata('current_user')->branchID;

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
        $this->data ['current_module'] = $this->modules [$this->module]['sub']['Physical count']; // defines the current sub module

        // check roles
        $this->check_roles ();
        $this->data ['roles'] = $this->roles;
    }

    private function check_roles() {
        // check roles
        $this->roles ['create'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Add ' . $this->data['current_module']['module_label'] );
        $this->roles ['view'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'View ' . $this->module );
        $this->roles ['edit'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Edit Existing ' . $this->module );
        $this->roles ['delete'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Delete Existing ' . $this->module );
        $this->roles ['approve'] = $this->userrole_model->has_access ( $this->session->userdata ( 'current_user' )->userID, 'Approve ' . $this->module );

    }

    private function _in_used($id = 0) {
        $tables = array ('branches' => 'branchID');

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
            //get items
            $this->db->select(' *');
            $this->db->where('status', 1);
            $this->db->from('items');
            //intem inventory
            $this->db->select('item_inventory.qty as expected_qty');
             // join
            $this->db->join ( 'item_inventory', 'items' . '.itemID=item_inventory.itemID', 'left' );
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


        $table_fields = array ('date', 'branchID','conductedBy','remarks','createdBy');

        // check role
        if ($this->roles ['create']) {
            $this->records->table = $this->table;
            $this->records->fields = array ();

            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }

            //check branchID of current user
            $userCurrentBranch      =   $this->session->userdata('current_user')->branchID;

            $this->db->where('branchID', $userCurrentBranch);       
            $pcSeries= $this->db->get('seriesno')->row();
            
            $pcSeriesNo = $pcSeries->pcNo + 1;
            
            require_once(APPPATH.'controllers/Generic_ajax.php');
            $seriesNo = Generic_ajax::updateSeriesNo('pcNo',$pcSeriesNo,$userCurrentBranch);

            $this->records->fields['pcNo']           =   str_pad($pcSeriesNo,4,'0',STR_PAD_LEFT);
            $this->records->fields['createdBy']      =   $this->session->userdata('current_user')->userID;
            $this->records->fields['date']           =   date('Y-m-d');
            $this->records->fields['dateCreated']    =   date('Y-m-d H:i:s');

            if ($this->records->save ()) {
                $this->records->fields = array ();
                $id = $this->records->where ['pcID'] = $this->db->insert_id ();
                $this->records->retrieve ();
                // record logs
                $logs = "Record - " . trim ( $this->input->post ( $this->logfield ) );

                //insert details
                //system quantity
                $system_quantity = $this->input->post('expected_qyts');
                //get physical count
                $physical_counts = $this->input->post('physical_counts');
                //get item ids
                $item_ids = $this->input->post('item_ids');
                //get variances
                $variances = $this->input->post('variances');

                $this->insert_pc_details($id, $item_ids, $physical_counts, $variances, $system_quantity);
               // echo json_encode($test);
                //die();

                $this->log_model->table_logs ( $data ['current_module'] ['module_label'], $this->table, $this->pfield, $id, 'Insert', $logs );


                $this->load->model('elastic_model');
                $data = [
                    'index' => 'pc_headers',
                    'type' => 'pc_header',
                    'id' => $id,
                    'body' => [
                        'id' => $id,
                        'date' => $this->input->post('date'),
                        'branch' => $this->elastic_model->getBranchName($this->branchID),
                        'branch_id' => $this->input->post('branchID'),
                        'conducted_by' => $this->input->post('conductedBy'),
                        'date' => $this->input->post('date'),
                        'remarks' => $this->input->post('remarks'),
                        'status' => "pending"
                    ]
                ];

                $this->elastic_model->saveToElasticSearch($data);



                $logfield = $this->pfield;
                // success msg
                $data ["class"] = "success";
                $data ["msg"] = $this->data ['current_module'] ['module_label'] . " successfully saved.";
                $data ["urlredirect"] = $this->controller_page . "/view/" . $id;
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
            $this->db->join ( 'users as cancelledUser', $this->table . '.approvedBy=users.userID', 'left' );

            $this->db->where('pcID', $id);

            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

            $result_data = [];
             $pc_items = $this->getItems();
             foreach ($pc_items as $key => $value) {
                $result_data[] = [
                    'itemID' => $value['itemID'],
                    'brand' => $value['brand'],
                    'item' => $value['item'],
                    'description' => $value['description'],
                    'umsr' => $value['umsr'],
                    'status' => $value['status'],
                    'expected_qty' => $value['expected_qty'],
                    'pc_details' => $this->getPcdetailByItemID($value['itemID'], $id),
                ];
             }

            $data['details_pc'] = $result_data;
        
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

        $table_fields = array ('date', 'branchID','remarks','createdBy', 'conductedBy');


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
                //insert details
                //system quantity
                $system_quantity = $this->input->post('expected_qyts');
                //get physical count
                $physical_counts = $this->input->post('physical_counts');
                //get item ids
                $item_ids = $this->input->post('item_ids');
                //get variances
                $variances = $this->input->post('variances');

                //remove details first
                
                $this->delete_details('pc_details', 'pcID', $this->records->pk);
                //insert details
                $this->insert_pc_details($this->records->pk, $item_ids, $physical_counts, $variances, $system_quantity);

                $this->load->model('elastic_model');
                $data = [
                    'index' => 'pc_headers',
                    'type' => 'pc_header',
                    'id' => $this->records->pk,
                    'body' => [
                        'doc' => [
                            'id' => $this->records->pk,
                            'date' => $this->input->post('date'),
                            'branch' => $this->elastic_model->getBranchName($this->branchID),
                            'branch_id' => $this->input->post('branchID'),
                            'conducted_by' => $this->input->post('conductedBy'),
                            'date' => $this->input->post('date'),
                            'remarks' => $this->input->post('remarks'),
                        ]
                    ]
                ];

                $this->elastic_model->update($data);

                //echo json_encode($test);
                //die();

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

                        $this->elastic_model->delete('pc_headers', 'pc_header', $id);

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
            $this->db->join ( 'users as cancelledUser', $this->table . '.cancelledBy=users.userID', 'left' );

            $this->db->where('pcID', $id);


            // ----------------------------------------------------------------------------------
            $data ['rec'] = $this->db->get ()->row ();

            //echo json_encode($data['rec']);
            //die

            //get details
            $details = $this->getDetails('pc_details', 'pcID', $id);
            $data['items'] = $details;

            $row_series = self::get_instance()->db->select("*")->where('branchID', $this->session->userdata('current_user')->branchID)->limit(1)->order_by('seriesID',"DESC")->get("seriesno")->row();
            //'0000'.$row_series->pcNo,
            //$date = ['date' => $row_series->pcNo];
            $data['series_no'] = '0000'.$row_series->pcNo;



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
        //migrate data to elasticsearch
        //$this->migratePhysicalElastic();
        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter                
        $condition_fields = array (
            array ('variable' => 'pcNo', 'field' => $this->table . '.pcNo', 'default_value' => '', 'operator' => 'like_both' ), 
            // array ('variable' => 'date', 'field' => $this->table . '.date', 'default_value' => '', 'operator' => 'like_both' ),             
            array ('variable' => 'branchName', 'field' =>  'branches.branchName', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'conductedBy', 'field' =>  $this->table .'.conductedBy', 'default_value' => '', 'operator' => 'like_both' ),             
            array ('variable' => 'remarks', 'field' => $this->table . '.remarks', 'default_value' => '', 'operator' => 'like_both' ), 
            
            array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '', 'operator' => 'where' ) 
        );
        
        // // sorting fields
        $sorting_fields = array ('pcNo' => 'desc' );
        
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
                $date     = trim($this->input->post('date'));
                $sortby = trim ( $this->input->post ( 'sortby' ) );
                $sortorder = trim ( $this->input->post ( 'sortorder' ) );
                
                break;
            case 2 :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
                }
                $date  = $this->session->userdata($controller.'date');
                $sortby = $this->session->userdata ( $controller . '_sortby' );
                $sortorder = $this->session->userdata ( $controller . '_sortorder' );
                break;
            default :
                foreach ( $condition_fields as $key ) {
                    $$key ['variable'] = $key ['default_value'];
                }
                $date  = "";
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
        $this->session->set_userdata($controller.'date', $date);
        $this->session->set_userdata ( $controller . '_sortby', $sortby );
        $this->session->set_userdata ( $controller . '_sortorder', $sortorder );
        $this->session->set_userdata ( $controller . '_limit', $limit );
        
        // assign data variables for views
        foreach ( $condition_fields as $key ) {
            $data [$key ['variable']] = $$key ['variable'];
        }
        
        $data['date'] = $date;
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
        
        if($date) {
            $this->db->like($this->table.'.date',date('Y-m-d',strtotime($date)));
        }
        // get
        $data ['ttl_rows'] = $config ['total_rows'] = $this->db->count_all_results ();
                
        $config ['base_url'] = $this->controller_page . '/show/';
        $config ['per_page'] = $limit;
        $this->pagination->initialize ( $config );
        
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
        
        if($date) {
            $this->db->like($this->table.'.date',date('Y-m-d',strtotime($date)));
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
        $this->db->where ( 'branchID', trim ( $this->input->post ( 'branchID' ) ) );
        $this->db->where ( 'conductedBy', trim ( $this->input->post ( 'conductedBy' ) ) );
        $this->db->where ( 'remarks', trim ( $this->input->post ( 'remarks' ) ) );

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

    public function delete_details($table, $compare, $id)
    {
        $this->db->where($compare, $id);
        $this->db->delete($table);
    }

    public function getDetails($table, $compare, $value)
    {


        //join item
        $this->db->select('items.*');
        $this->db->select($table.'.*');
        $this->db->from($table);
        $this->db->join ( 'items', 'pc_details.itemID=items.itemID', 'right' );

         //$this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->where($compare, $value);
        $details = $this->db->get()->result();
        return $details;
    }   
    
    public function insert_pc_details($rrID, $items_ids = array(), $physical_counts = array(), $variances= array(), $system_quantity = array())
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


        $pc_details = $this->combine_keys_with_arrays($keys, array(
            'pcID' => $rrIDs,
            'itemID'  => $items_ids,
            'systemQty' => $system_quantity,
            'actualQty'    => $physical_counts,
            'variance' => $variances
        ));


        //echo json_encode($rr_details);
        //return $pc_details;

       return $this->db->insert_batch('pc_details', $pc_details);
    }

    public function getItems()
    {
          //get items
            $this->db->select(' *');
            $this->db->select('item_inventory.qty as expected_qty');
            $this->db->where('status', 1);
            //$this->db->where('items.itemID', $itemID);
            $this->db->from('items');
            //intem inventory
            $this->db->select('item_inventory.qty as expected_qty');
             // join
            $this->db->join ( 'item_inventory', 'items' . '.itemID=item_inventory.itemID', 'left' );
            $this->db->where('item_inventory.branchID',$this->session->userdata('current_user')->branchID);

            $items = $this->db->get()->result_array();

            return $items;
    }

    public function getPcdetailByItemID($itemID, $id)
    {
        //join item
        $this->db->select('items.*');
        $this->db->select('pc_details'.'.*');
        $this->db->from('pc_details');
        $this->db->join ( 'items', 'pc_details.itemID=items.itemID', 'right' );

         $this->db->where('pc_details.itemID', $itemID);
         $this->db->where('pc_details.pcID', $id);
        $details = $this->db->get()->result();
        return $details;
    }

    public function updateStockCard()
    {
        $reffNo = trim($this->input->post('reffNo'));
        $data = [
            'data' => ['status' => 200, 'reffno' => $reffNo]
        ];
        echo json_encode($data);

    }

    public function updateStockCardAndItemInventory()
    {

        //reffNo: reffNo, item_id: item_id, variance: variance, debit: debit, credit: credit, begBal: begBal, endBal: endBal, pcID: pcID
        $reffNo = trim($this->input->post('reffNo'));
        $itemID = trim($this->input->post('item_id'));
        $variance = trim($this->input->post('variance'));
        $debit = trim($this->input->post('debit'));
        $credit = trim($this->input->post('credit'));
        $begbal = trim($this->input->post('begBal'));
        $endbal = trim($this->input->post('endBal'));
        $pcID = trim($this->input->post('pcID'));
        $sign = trim($this->input->post('sign'));



        //add stockcard
        //$endbal = 0;
        //$begbal = 0;
        $added = $this->addStockCard($pcID, $itemID, $debit, $credit , $endbal, $begbal, $variance, $sign);
        //update item inventory
        $result_item_inventory = $this->updateItemInventoryQty($itemID, $variance, $sign);
        //update physical count update approved by
        $update_pc = $this->updateApproveBy($pcID);

        $response = [
            'data' => [
                'status' => 200,
                'added' => $added,
                'result_item_added' => $result_item_inventory,
                'pc_updated' => $update_pc,
                //'seriesno' => $this->addSeriesNo(45)
            ]
        ];
        echo json_encode($response);
    }

    public function addStockCard($pcID, $itemID, $debit, $credit , $endbal, $begbal, $variance, $sign)
    {
        //auth user branch

        $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
        $branch = $this->db->get('branches');
        $current_branch = $branch->row();

        //$last_insert_id = $this->db->insert_id();
        $row = $this->db->select("*")->limit(1)->order_by('id',"DESC")->get("stockcard")->row();
        //echo $row->id; //it will provide latest or last record id.
        $endbal = 0;
        //current end balance
        if($sign == "positive") {
            $endbal = $row->endBal + $variance;
        }
        elseif($sign == "negative") {
            $endbal = $row->endBal - $variance;
        }

        $row_series = self::get_instance()->db->select("*")->where('branchID', $this->session->userdata('current_user')->branchID)->limit(1)->order_by('seriesID',"DESC")->get("seriesno")->row();

        $data  = [
            'branchID' => $current_branch->branchID,
            'itemID' => $itemID,
            'date' => date('Y-m-d'),
            'refNo' => '0000'.$row_series->pcNo,
            'debit' => $debit,
            'credit' => $credit,
            'endbal' => $endbal,
            'begbal' => $row->endBal,
        ];

        //return $data;
       return $this->db->insert('stockcard', $data);
    }

    public function updateItemInventoryQty($itemId, $qty)
    {
        $this->db->set('qty', $qty);
        $this->db->where('itemID', $itemId);
        $this->db->where('branchID', $this->session->userdata('current_user')->branchID);
        $data = $this->db->update('item_inventory');
        return $data;
    }

    public function updateApproveBy($pcID)
    {

        $data = [
            'approvedBy' => $this->session->userdata('current_user')->userID,
            'dateApproved' => date('Y-m-d h:i:s'),
            'status' => 2
        ];
        $this->db->where('pcID', $pcID);
        $add =  $this->db->update('pc_headers', $data);

        $this->updateStatusElasticSearch("confirmed", $pcID);

        return $add;

    }

    
    public function updateApprovedBy()
    {
        $pcID = trim($this->input->post('pcID'));   
        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update approved by        
        $approvedBy = Generic_ajax::updateApprovedBy($this->table, "pcID", $pcID, $this->session->userdata('current_user')->userID);

        $this->updateStatusElasticSearch("approved", $pcID);

        $response = [
            'data' => [
                'status' => 200,                
                'approvedBy' => $approvedBy
            ]
        ];
        echo json_encode($response);
    }

    public function updateCancelledBy()
 
    {
        $pcID = trim($this->input->post('pcID'));
       
        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update approved by
        //updateApproveBy($table, $compareId, $id, $approvedBy)
        $approvedBy = Generic_ajax::updateCancelledBy($this->table, "pcID",  $pcID, $this->session->userdata('current_user')->userID);

        $this->updateStatusElasticSearch("cancelled", $pcID);

        $response = [
            'data' => [
                'status' => 200,                
                'approvedBy' => $approvedBy
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

    public function elastic_search()
    {
        $this->load->model('elastic_model');
        $q = $this->input->post('search');
        $data = [
            'index' => 'pc_headers',
            'type' => 'pc_header',
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $q,
                        'fields' => [
                            'date', 'branch', 'conducted_by', 'status', 'remarks'
                        ]
                    ]
                ]
            ]
        ];
        $result = $this->elastic_model->uniqueSearch($data);
        echo json_encode($result);
    }


    public function migratePhysicalElastic()
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $physical_counts = $this->db->get()->result();

        $this->load->model('elastic_model');
        foreach($physical_counts as $count) {
            $status  = "";
            switch ($count->status) {
                case 1:
                    $status = "pending";
                    break;
                case 2:
                    $status = "confirmed";
                    break;
                default:
                    $status = "cancelled";
            }
            $data = [
                'index' => 'pc_headers',
                'type' => 'pc_header',
                'id' => $count->pcID,
                'body' => [
                    'id' => $count->pcID,
                    'date' => date('F d, Y', strtotime($count->date)),
                    'branch' => $this->elastic_model->getBranchName($count->branchID),
                    'branch_id' => $count->branchID,
                    'conducted_by' => $count->conductedBy,
                    'remarks' => $count->remarks,
                    'status' => $status
                ]
            ];

            $this->elastic_model->saveToElasticSearch($data);
        }

    }

    function updateStatusElasticSearch($status, $id)
    {

        $this->load->model('elastic_model');

        $data = [
            'index' => 'pc_headers',
            'type' => 'pc_header',
            'id' => $id,
            'body' => [
                'doc' => [
                    'id' => $id,
                    'status' => $status,
                ]
            ],
        ];

        $this->elastic_model->update($data);

    }


}
