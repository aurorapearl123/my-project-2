<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_inventory extends CI_Controller
{
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

    public function __construct()
    {
        parent::__construct();
        $this->load->model('generic_model','record');
        $this->module       = 'Inventory';
        $this->data['controller_page']  = $this->controller_page = site_url('expense_inventory');// defines contoller link
        $this->table        = 'exp_headers';                                                  // defines the default table
        $this->pfield = $this->data['pfield'] = 'expID';                                                 // defines primary key
        // $this->logfield     = 'deptCode';
        $this->module_path  = 'modules/'.strtolower(str_replace(" ","_",$this->module)).'/expense_inventory';// defines module path / folder name
        
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        
        // check user session
        if (!$this->session->userdata('current_user')->sessionID) {
            header('location: '.site_url('login'));
        }
    }

    private function submenu()
    {
        //submenu setup
        require_once('modules.php');

        foreach($modules as $mod) {
            //modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/'.str_replace(" ","_",strtolower($mod)).'/metadata');
        }

        $this->data['modules']               = $this->modules;
        $this->data['current_main_module']   = $this->modules[$this->module]['main'];              // defines the current main module
        $this->data['current_module']        = $this->modules[$this->module]['sub']['Expense Inventory'];      // defines the current sub module
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }
    
    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->data['current_module']['module_label']);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->data['current_module']['module_label']);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->data['current_module']['module_label']);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->data['current_module']['module_label']);
    }
    
    private function _in_used($id=0)
    {
        $tables = array();
    
        if(!empty($tables)) {
            foreach($tables as $table=>$fld) {
                $this->db->where($fld, $id);
                if($this->db->count_all_results($table)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function index()
    {
        $this->show();
    }
    
    public function create()
    {
        $this->submenu();
        $data = $this->data;
        
        // check roles
        if ($this->roles['create']) {

            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/create');
            $this->load->view('footer');

        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }
    
    public function save()
    {
        //load submenu
        $this->submenu();
        $data = $this->data;
        $table_fields = array('branchID','ttlAmount','remarks', 'createdBy');

        // check role
        if ($this->roles['create']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
             
            }
                
            //check branchID of current user
            $userCurrentBranch      =   $this->session->userdata('current_user')->branchID;

            $this->db->where('branchID', $userCurrentBranch);       
            $expSeries= $this->db->get('seriesno')->row();

            $expSeriesNo = $expSeries->expNo + 1;
                       
            require_once(APPPATH.'controllers/Generic_ajax.php');            
            $seriesNo = Generic_ajax::updateSeriesNo('expNo',$expSeriesNo,$userCurrentBranch);

            $this->record->fields['expNo']          =   str_pad($expSeriesNo,4,'0',STR_PAD_LEFT);
            $this->record->fields['date']           =   date('Y-m-d');
            $this->record->fields['dateCreated']    =   date('Y-m-d H:i:s');
            $this->record->fields['createdBy']      =   $this->session->userdata('current_user')->userID;
                
            if ($this->record->save()) {
                $this->record->fields = array();
                $expID = $this->record->where['expID'] = $this->db->insert_id();
                $this->record->retrieve();

                $particularIDs = $this->input->post('particularIDs');
                $quantities = $this->input->post('quantities');
                $amounts = $this->input->post('amounts');
                //add details
                $this->insert_expense_details($expID, $particularIDs, $quantities, $amounts);

                // record logs
                $logs = "Record - ".trim($this->input->post($this->logfield));
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $expID, 'Insert', $logs);
                
                // $logfield = $this->pfield;
                // success msg
                $data["class"]   = "success";
                $data["msg"]     = $this->data['current_module']['module_label']." successfully saved.";
                $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode($expID);
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
               
            } else {
                // error
                $data["class"]   = "danger";
                $data["msg"]     = "Error in saving the ".$this->data['current_module']['module_label']."!";
                $data["urlredirect"] = "";
                $this->load->view("header",$data);
                $this->load->view("message");
            }
                $this->load->view("footer");
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function insert_expense_details($expID, $particularIDs = array(), $quantities = array(), $amounts = array())
    {
        /*$result = array_merge($items_ids, $prices);*/
        $size = count($particularIDs);
        $size = $size - 1;

        $keys = [];
        foreach (range(0, $size) as $number) {
            //echo $number;
            $keys[] = $number;
        }

        $expIDs = [];
        foreach (range(0, $size) as $number) {
            //echo $number;
            $expIDs[] = $expID;
        }


        $exp_details = $this->combine_keys_with_arrays($keys, array(
            'expID' => $expIDs,
            'particularID'  => $particularIDs,
            'qty' => $quantities,
            'amount'    => $amounts));


        //echo json_encode($exp_details);
        //return $exp_details;

        //$this->db->insert_batch('exp_details', $exp_details);
        return $this->db->insert_batch('exp_details', $exp_details);
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
    
    public function edit($id)
    {
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);

        if ($this->roles['edit']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;

            $data['details'] = $this->getDetails('exp_details', 'expID', $id);

            $this->db->select('expense_particulars.*');
            $this->db->from('expense_particulars');
            $this->db->where('status',1);
            $recs = $this->db->get()->result();

            $data['particulars'] = $recs;


            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/edit');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }
    
    public function update()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $table_fields = array('remarks','ttlAmount','branchID');



        //create array

        // check roles
        if ($this->roles['edit']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $this->encrypter->decode($this->input->post($this->pfield));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->encrypter->decode($this->input->post($this->pfield)), 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }

                //delete headers details
                //remove rr details
                $this->delete_details('exp_details', 'expID', $this->record->pk);
                //get headers details
                $particularIDs = $this->input->post('item_ids');
                $quantities = $this->input->post('prices');
                $amounts = $this->input->post('amounts');

                //add details
                $this->insert_expense_details($this->record->pk, $particularIDs, $quantities, $amounts);

                    
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label']." successfully updated.";
                $data["urlredirect"] = $this->controller_page."/view/".trim($this->input->post($this->pfield));
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the ".$this->data['current_module']['module_label']."!";
                $data["urlredirect"] = $this->controller_page."/view/".$this->record->pk;
                $this->load->view("header",$data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function delete_details($table, $compare, $rrID)
    {
        $this->db->where($compare, $rrID);
        $this->db->delete($table);
    }

    public function delete($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table = $this->table;
            // set where
            $this->record->where[$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            
            if (! empty($this->record->field)) {
                $this->record->pfield = $this->pfield;
                $this->record->pk = $id;
                
                // check if in used
                if (! $this->_in_used($id)) {
                    if ($this->record->delete()) {
                        $logfield = $this->logfield;

                        //change start
                        //change end
                        
                        // record logs
                        // $logs = "Record - " . $this->record->field->$logfield;
                        // $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        
                        // successful
                        $data["class"] = "success";
                        $data["msg"] = $this->data['current_module']['module_label'] . " successfully deleted.";
                        $data["urlredirect"] = $this->controller_page . "/";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    } else {
                        // error
                        $data["class"] = "danger";
                        $data["msg"] = "Error in deleting the " . $this->data['current_module']['module_label'] . "!";
                        $data["urlredirect"] = "";
                        $this->load->view("header", $data);
                        $this->load->view("message");
                        $this->load->view("footer");
                    }
                } else {
                    // error
                    $data["class"] = "danger";
                    $data["msg"] = "Data integrity constraints.";
                    $data["urlredirect"] = "";
                    $this->load->view("header", $data);
                    $this->load->view("message");
                    $this->load->view("footer");
                }
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = $this->data['current_module']['module_label'] . " record not found!";
                $data["urlredirect"] = "";
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["class"] = "danger";
            $data["msg"] = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header", $data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }
    
    public function view($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        
        if ($this->roles['view']) {

            // select
            $this->db->select ( $this->table . '.*' );
            //approved by
            $this->db->select('approvedUser.firstName as approvedFirstName');
            $this->db->select('approvedUser.middleName as approvedMiddleName');
            $this->db->select('approvedUser.lastName as approvedLastName');
            //cancelled by
            $this->db->select('cancelledUser.firstName as cancelledFirstName');
            $this->db->select('cancelledUser.middleName as cancelledMiddleName');
            $this->db->select('cancelledUser.lastName as cancelledLastName');
            //users details
            $this->db->select ( 'users.firstName' );
            $this->db->select ( 'users.middleName' );
            $this->db->select ( 'users.lastName' );
            // from
            $this->db->from ( $this->table );
            $this->db->join ( 'users', $this->table . '.createdBy=users.userID', 'left' );
            //approved by
            $this->db->join ( 'users as approvedUser', $this->table . '.approvedBy=users.userID', 'left' );
            //called by
            $this->db->join ( 'users as cancelledUser', $this->table . '.cancelledBy=users.userID', 'left' );
            $this->db->where($this->table.'.expID', $id);
            $data['rec'] = $this->db->get()->row();


            $data['details'] = $this->getDetails('exp_details', 'expID', $id);

            
            // // // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/view');
            $this->load->view('footer');
        } else {
            // no access this page
            $data['class'] = "danger";
            $data['msg'] = "Sorry, you don't have access to this page!";
            $data['urlredirect'] = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }

    public function getDetails($table, $compare, $value)
    {

        $this->db->select('expense_particulars.*');
        $this->db->select($table.'.*');
        $this->db->from($table);
        $this->db->join ( 'expense_particulars', 'exp_details.particularID=expense_particulars.particularID', 'right' );

        //$this->db->join ( 'branches', $this->table . '.branchID=branches.branchID', 'left' );
        $this->db->where($compare, $value);
        $details = $this->db->get()->result();
        return $details;
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
            array ('variable' => 'expNo', 'field' => $this->table . '.expNo', 'default_value' => '', 'operator' => 'like_both' ), 
            // array ('variable' => 'date', 'field' => $this->table . '.date', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'branchName', 'field' => 'branches.branchName', 'default_value' => '', 'operator' => 'like_both' ), 
            array ('variable' => 'ttlAmount', 'field' => $this->table . '.ttlAmount', 'default_value' => '', 'operator' => 'where' ), 
            array ('variable' => 'remarks', 'field' => $this->table . '.remarks', 'default_value' => '', 'operator' => 'like_both' ),            
            array ('variable' => 'status', 'field' => $this->table . '.status', 'default_value' => '', 'operator' => 'where' ) );
        
        // // sorting fields
        $sorting_fields = array ('expNo' => 'desc' );
        
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
        $this->db->select($this->table.'.*');
        $this->db->select('branches.branchName');
        
        // from
        $this->db->from($this->table);
        
        // join     

        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        
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
        $this->db->select($this->table.'.*');
        $this->db->select('branches.branchName');
        
        // from
        $this->db->from($this->table);
        
        // join     

        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        
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
    
    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'expNo', 'field'=>$this->table.'.expNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'date', 'field'=>$this->table.'.date', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'ttlAmount', 'field'=>$this->table.'.ttlAmount', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'remarks', 'field'=>$this->table.'.remarks', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('branches.branchName');
        
        // from
        $this->db->from($this->table);
        
        // join     

        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
        
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
        
        $data['title'] = "Expense Inventory List";

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
            array('variable'=>'expNo', 'field'=>$this->table.'.expNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'date', 'field'=>$this->table.'.date', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'ttlAmount', 'field'=>$this->table.'.ttlAmount', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'remarks', 'field'=>$this->table.'.remarks', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        $controller = $this->uri->segment(1);
        
        foreach($condition_fields as $key) {
            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
        }
        
        $limit      = $this->session->userdata($controller.'_limit');
        $offset     = $this->session->userdata($controller.'_offset');
        $sortby     = $this->session->userdata($controller.'_sortby');
        $sortorder  = $this->session->userdata($controller.'_sortorder');
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('branches.branchName');
        
        // from
        $this->db->from($this->table);
        
        // join     

        $this->db->join('branches',$this->table.'.branchID=branches.branchID','left');
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
        
    
        $title          = "Expense Inventory List";
        $companyName    = $this->config_model->getConfig('Company');
        $address        = $this->config_model->getConfig('Address');
         
        //XML Blurb
        $data = "<?xml version='1.0'?>
  
            <?mso-application progid='Excel.Sheet'?>
  
            <Workbook xmlns='urn:schemas-microsoft-com:office:spreadsheet' xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns:ss='urn:schemas-microsoft-com:office:spreadsheet' xmlns:html='http://www.w3.org/TR/REC-html40'>
            <Styles>
            <Style ss:ID='s20'>
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
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'>".$companyName."</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'>".$address."</Data></Cell>";
        $data .= "</Row>";
    
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'>".strtoupper($title)."</Data></Cell>";
        $data .= "</Row>";
         
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='5'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
    
        $fields[]="  ";                 
        $fields[]="REFERENCE NO";            
        $fields[]="DATE";            
        $fields[]="TOTAL AMOUNT";
        $fields[]="REMARKS";
        $fields[]="STATUS";
    
        $data .= "<Row ss:StyleID='s24'>";
        //Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
    
        if (count($records)) {
            
            foreach ($records as $row) {
                $data .= "<Row>";
                
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->expNo."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->date."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->ttlAmount."</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>".$row->remarks."</Data></Cell>";
                if  ($row->status == 0) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Cancelled</Data></Cell>";
                } else if ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Pending</Data></Cell>";                
                } else if ($row->status == 2) {
                    $data .= "<Cell ss:StyleID='s24B'><Data ss:Type='String'>Confirmed</Data></Cell>";
                }
                $data .= "</Row>";
    
                
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
         
    
        //Final XML Blurb
        $filename = "expense_inventory";
    
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
         
        echo $data;
    
    }
    
    //Conditions and fields changes
    public function check_duplicate()
    {
        $this->db->where('expID', trim($this->input->post('expID')));      
        if ($this->db->count_all_results($this->table))
            echo "1"; // duplicate
        else 
            echo "0";
    }


    
    public function updateApprovedBy()
    {
        $expID = trim($this->input->post('expID'));   
        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update approved by        
        $approvedBy = Generic_ajax::updateApprovedBy($this->table, "expID", $expID, $this->session->userdata('current_user')->userID);
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
        $expID = trim($this->input->post('expID'));
       
        require_once(APPPATH.'controllers/Generic_ajax.php');
        //update approved by
        //updateApproveBy($table, $compareId, $id, $approvedBy)
        $approvedBy = Generic_ajax::updateCancelledBy($this->table, "expID", $expID, $this->session->userdata('current_user')->userID);

        $response = [
            'data' => [
                'status' => 200,                
                'approvedBy' => $approvedBy
            ]
        ];
        echo json_encode($response);
    }

}