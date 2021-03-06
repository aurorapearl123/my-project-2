<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leave_type extends CI_Controller
{
    
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
        $this->load->model('generic_model', 'record');
        $this->module = 'Leave and Travel';
        $this->data['controller_page'] = $this->controller_page = site_url('leave_type'); // defines contoller link
        $this->table = 'leave_types'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'leaveTypeID'; // defines primary key
        $this->logfield = 'code';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/leave_type'; // defines module path
        
        // check for maintenance period
        if ($this->config_model->getConfig('Maintenance Mode') == '1') {
            header('location: ' . site_url('maintenance_mode'));
        }
        
        // check user session
        if (! $this->session->userdata('current_user')->sessionID) {
            header('location: ' . site_url('login'));
        }
    }
    
    private function submenu()
    {
        // submenu setup
        require_once ('modules.php');
        
        foreach ($modules as $mod) {
            // modules/<module>/
            // - <menu>
            // - <metadata>
            $this->load->view('modules/' . str_replace(" ", "_", strtolower($mod)) . '/metadata');
        }
        
        $this->data['modules'] = $this->modules;
        $this->data['current_main_module'] = $this->modules[$this->module]['main']; // defines the current main module
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Leave Type']; // defines the current sub module
        
        // check roles
        $this->check_roles();
        $this->data['roles'] = $this->roles;
    }
    
    private function check_roles()
    {
        // check roles
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Add '.$this->data['current_module']['module_label']);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View '.$this->data['current_module']['module_label']);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Edit Existing '.$this->data['current_module']['module_label']);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->data['current_module']['module_label']);
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Approve '.$this->data['current_module']['module_label']);
    }
    
    private function _in_used($id = 0)
    {
        $tables = array();
        
        if (! empty($tables)) {
            foreach ($tables as $table => $fld) {
                $this->db->where($fld, $id);
                if ($this->db->count_all_results($table)) {
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
            $this->load->view($this->module_path . '/create');
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
    
    public function save()
    {
        //If isCashable kay di makita sa list, pati sa hris mao sad, let's debug later
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $table_fields = array('code', 'leaveType', 'creditEarn', 'frequency', 'isWithoutpayCover', 'isCashable', 'remarks');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['code']  	= trim($this->input->post('code'));
                $id = $this->db->insert_id();
                $this->record->retrieve();
                
                $this->db->where('status', 1);
                $this->db->order_by('rank', 'asc');
                $this->db->order_by('employeeType', 'asc');
                $query = $this->db->get('employee_types');
                
                // var_dump($query);
                if ($query->num_rows()) {
                    $type_earning  = array();
                    foreach ($query->result() as $row) {
                        $info = array();
                        $info['leaveTypeID'] 	= $this->record->field->leaveTypeID;
                        $info['employeeTypeID'] = $row->employeeTypeID;
                        $info['earning'] 		= $this->input->post('earning_'.$row->employeeTypeID);
                        $type_earning[]         = $info;
                    }
                    $this->db->insert_batch('leave_type_earnings', $type_earning);
                    // echo $this->db->last_query();
                }
                
                // update all employees
                $this->db->select('employees.empID');
                $query = $this->db->get('employees');
                
                if ($query->num_rows()) {
                    $credit  = array();
                    $ledger  = array();
                    foreach ($query->result() as $row) {
                        $this->db->select('employments.employeeTypeID');
                        $this->db->from('employments');
                        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
                        $this->db->where('employments.empID', $row->empID);
                        $this->db->order_by('employee_types.rank','asc');
                        $this->db->limit(1);
                        $result = $this->db->get()->row();
                        
                        $info = array();
                        $info['empID'] 		= $row->empID;
                        $info['leaveTypeID']= $this->record->field->leaveTypeID;
                        $info['credit']     = $this->input->post('earning_'.$result->employeeTypeID);
                        $info['lastUpdate']	= date('Y-m-d H:i:s');
                        $credit[]           = $info;
                        
                        $info = array();
                        $info['empID'] 		= $row->empID;
                        $info['leaveTypeID']= $this->record->field->leaveTypeID;
                        $info['date']		= date('Y-m-d H:i:s');
                        $info['debit']      = $this->input->post('earning_'.$result->employeeTypeID);
                        $info['credit'] 	= 0;
                        $info['remarks'] 	= "Initial Credits";
                        $info['updatedBy'] 	= $this->session->userdata('current_userID');
                        $ledger[]           = $info;
                    }
                    $this->db->insert_batch('leave_credits', $credit);
                    $this->db->insert_batch('leave_credit_ledger', $ledger);
                }
                
                // record logs
                $pfield = $this->pfield;
                
                $logs = "Record - " . trim($this->input->post($this->logfield));
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$pfield, 'Insert', $logs);
                
                $logfield = $this->pfield;
                
                // success msg
                $data["class"] = "success";
                $data["msg"] = $data['current_module']['module_label'] . " successfully saved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in saving the " . $this->data['current_module']['module_label'] . "!";
                ;
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
            // extend fields - join tables
            // set joins
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/edit');
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
    
    public function update()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($this->input->post($this->pfield));

        $table_fields = array('code', 'leaveType', 'creditEarn', 'frequency', 'isWithoutpayCover', 'isCashable', 'remarks');
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->pfield = $this->pfield;
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                $this->db->where('leaveTypeID', $this->record->pk);
                $this->db->delete('leave_type_earnings');
                
                $this->db->where('status', 1);
                $this->db->order_by('rank', 'asc');
                $this->db->order_by('employeeType', 'asc');
                $result = $this->db->get('employee_types');
                
                if ($result->num_rows()) {
                    $earning  = array();
                    foreach ($result->result() as $row) {
                        $info = array();
                        $info['leaveTypeID'] 	= $this->record->pk;
                        $info['employeeTypeID'] = $row->employeeTypeID;
                        $info['earning'] 		= $this->input->post('earning_'.$row->employeeTypeID);
                        $earning[] = $info;
                    }
                    $this->db->insert_batch('leave_type_earnings', $earning);
                }
                
                // record logs
                if ($wasChange) {
                    $logs = "Record - " . trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }
                
                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully updated.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $data["class"] = "danger";
                $data["msg"] = "Error in updating the " . $this->data['current_module']['module_label'] . "!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->record->pk;
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
    
    public function delete($id = 0)
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
            $this->record->table  = $this->table;
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
                        
                        // record logs
                        $logs = "Record - " . $this->record->field->$logfield;
                        $this->log_model->table_logs($this->data['current_module']['module_label'], $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);
                        
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
        $id = $this->encrypter->decode($id);

        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // $this->roles['view'] = 1;
        if ($this->roles['view']) {
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            // set joins
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
            
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;	

            $data['in_used'] = $this->_in_used($id);
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            // load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path . '/view');
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
    
    public function show()
    {
        // ************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveType', 'field'=>$this->table.'.leaveType', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'creditEarn', 'field'=>$this->table.'.creditEarn', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'frequency', 'field'=>$this->table.'.frequency', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isCashable', 'field'=>$this->table.'.isCashable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isWithoutpayCover', 'field'=>$this->table.'.isWithoutpayCover', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'remarks', 'field'=>$this->table.'.remarks', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
            array('variable'=>'rank', 'field'=>$this->table.'.rank', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('rank'=>'asc','leaveType'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(4))
            $offset = $this->uri->segment(4);
            else
                $offset = 0;
                
                // source of filtering
                $filter_source = 0;	// default/blank
                if ($this->input->post('filterflag') || $this->input->post('sortby')) {
                    $filter_source = 1;
                } else {
                    foreach($condition_fields as $key) {
                        if ($this->input->post($key['variable'])) {
                            $filter_source = 1; // form filters
                            break;
                        }
                    }
                }
                
                if (!$filter_source) {
                    foreach($condition_fields as $key) {
                        if ($this->session->userdata($controller.'_'.$key['variable'])) {
                            $filter_source = 2; // session
                            break;
                        }
                    }
                }
                switch($filter_source)
                {
                    case 1:
                        foreach($condition_fields as $key) {
                            $$key['variable'] = trim($this->input->post($key['variable']));
                        }
                        
                        $sortby 	= trim($this->input->post('sortby'));
                        $sortorder 	= trim($this->input->post('sortorder'));
                        
                        break;
                    case 2:
                        foreach($condition_fields as $key) {
                            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                        }
                        
                        $sortby 	= $this->session->userdata($controller.'_sortby');
                        $sortorder 	= $this->session->userdata($controller.'_sortorder');
                        break;
                    default:
                        foreach($condition_fields as $key) {
                            $$key['variable'] = $key['default_value'];
                        }
                        $sortby 	= "";
                        $sortorder 	= "";
                }
                
                if ($this->input->post('limit')) {
                    if ($this->input->post('limit')=="All")
                        $limit = "";
                        else
                            $limit = $this->input->post('limit');
                } else if ($this->session->userdata($controller.'_limit')) {
                    $limit = $this->session->userdata($controller.'_limit');
                } else {
                    $limit = ($pageType) ? 10 : 25; // default limit
                }
                
                // set session variables
                foreach($condition_fields as $key) {
                    $this->session->set_userdata($controller.'_'.$key['variable'], $$key['variable']);
                }
                $this->session->set_userdata($controller.'_sortby', $sortby);
                $this->session->set_userdata($controller.'_sortorder', $sortorder);
                $this->session->set_userdata($controller.'_limit', $limit);
                
                // assign data variables for views
                foreach($condition_fields as $key) {
                    $data[$key['variable']] = $$key['variable'];
                }
                
                // select
                $this->db->select($this->table.'.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                
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
                
                // get
                $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
                
                // set pagination
                $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
                $config['per_page'] = $limit;
                $config['uri_segment'] = 4;
                $this->pagination->initialize($config);
                
                
                // select
                $this->db->select($this->table.'.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                
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
                                $sortby 	= $fld;
                                $sortorder 	= $s_order;
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
                $data['sortby'] 	= $sortby;
                $data['sortorder'] 	= $sortorder;
                $data['limit'] 		= $limit;
                
                // get
                $data['records'] = $this->db->get();
                
                // load views
                $this->load->view('header', $data);
                $this->load->view($this->module_path . '/list');
                $this->load->view('footer');
    }
    
    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // sorting
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveType', 'field'=>$this->table.'.leaveType', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'creditEarn', 'field'=>$this->table.'.creditEarn', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'frequency', 'field'=>$this->table.'.frequency', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isCashable', 'field'=>$this->table.'.isCashable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isWithoutpayCover', 'field'=>$this->table.'.isWithoutpayCover', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'remarks', 'field'=>$this->table.'.remarks', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('leaveType'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
            else
                $offset = 0;
                
                foreach($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                }
                
                $sortby 	= $this->session->userdata($controller.'_sortby');
                $sortorder 	= $this->session->userdata($controller.'_sortorder');
                $limit 		= $this->session->userdata($controller.'_limit');
                
                
                // select
                
                // from
                $this->db->from($this->table);
                
                // join
                
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
                                $sortby 	= $fld;
                                $sortorder 	= $s_order;
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
                
                // get
                $data['records'] = $this->db->get()->result();
        
        $data['title'] = "Group List";
        
        // load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path . '/printlist');
        $this->load->view('footer_print');
    }
    
    function exportlist() {
        // load submenu
        $this->submenu ();
        $data = $this->data;
        //sorting
        
        
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'code', 'field'=>$this->table.'.code', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveType', 'field'=>$this->table.'.leaveType', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'creditEarn', 'field'=>$this->table.'.creditEarn', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'frequency', 'field'=>$this->table.'.frequency', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isCashable', 'field'=>$this->table.'.isCashable', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'isWithoutpayCover', 'field'=>$this->table.'.isWithoutpayCover', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'remarks', 'field'=>$this->table.'.remarks', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'1', 'operator'=>'where'),
            array('variable'=>'rank', 'field'=>$this->table.'.rank', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('rank'=>'asc','leaveType'=>'asc');
        
        $controller = $this->uri->segment ( 1 );
        
        foreach ( $condition_fields as $key ) {
            $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
        }
        
        $limit = $this->session->userdata ( $controller . '_limit' );
        $offset = $this->session->userdata ( $controller . '_offset' );
        $sortby = $this->session->userdata ( $controller . '_sortby' );
        $sortorder = $this->session->userdata ( $controller . '_sortorder' );
        
        
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
        $records = $this->db->get ()->result ();
        
        $title = "Leave Types List";
        $companyName = $this->config_model->getConfig ( 'Company' );
        $address = $this->config_model->getConfig ( 'Address' );
        
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
            
    		<Worksheet ss:Name='" . $title . "'>
    		    
    		<Table>
    		<Column ss:Index='1' ss:AutoFitWidth=\"1\" ss:Width='25.00'/>
    		<Column ss:Index='2' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='3' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='100.00'/>
    		<Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
    		<Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
            <Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='9' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
        
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>" . $address . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>" . strtoupper ( $title ) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields [] = "  ";
        $fields [] = "CODE";
        $fields [] = "LEAVE TYPE";
        $fields [] = "FREQUENCY";
        $fields [] = "CREDIT LEARNING";
        $fields [] = "CASHABLE";
        $fields [] = "COVER W/OUT PAY";
        $fields [] = "STATUS";
        $fields [] = "RANK";
        
        $data .= "<Row ss:StyleID='s24'>";
        //Field Name Data
        foreach ( $fields as $fld ) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
        
        if (count ( $records )) {
            $ctr = 1;
            foreach ( $records as $row ) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->code . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->leaveType . "</Data></Cell>";
                if ($row->frequency == 0) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>None</Data></Cell>";
                } else if ($row->frequency == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Daily</Data></Cell>";
                } else if ($row->frequency == 2) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Weekly</Data></Cell>";
                } else if ($row->frequency == 3) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Monthly</Data></Cell>";
                } else if ($row->frequency == 4) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Quarterly</Data></Cell>";
                } else if ($row->frequency == 5) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Semi-Annual</Data></Cell>";
                } else if ($row->frequency == 6) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Annual</Data></Cell>";
                }
                
                if ($row->creditEarn == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Incremental</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Reset</Data></Cell>";
                }
                
                if ($row->isCashable == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Yes</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>No</Data></Cell>";
                }
                
                if ($row->isWithoutpayCover == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Yes</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>No</Data></Cell>";
                }
                
                if ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Active</Data></Cell>";
                } else {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Inactive</Data></Cell>";
                }
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->rank . "</Data></Cell>";

                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        //Final XML Blurb
        $filename = "leave_types_list";
        
        header ( "Content-type: application/octet-stream" );
        header ( "Content-Disposition: attachment; filename=$filename.xls;" );
        header ( "Content-Type: application/ms-excel" );
        header ( "Pragma: no-cache" );
        header ( "Expires: 0" );
        
        echo $data;
        
    }
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // AJAX HANDLER FUNCTIONS
    public function check_duplicate()
    {
        // set table
        $this->record->table = $this->table;
        // set where
        $this->record->where['code'] = trim($this->input->post('code'));
        // execute retrieve
        $this->record->retrieve();
        
        if (!empty($this->record->field))
            echo "1"; // duplicate
            else
                echo "0";
    }
    
    public function update_rank()
    {
        $id = trim($this->input->post('id'));
        
        $this->record->table  = $this->table;
        $this->record->fields = array();
        
        $this->record->fields['rank'] = trim($this->input->post('rank'));
        $this->record->pfield 	= $this->pfield;
        $this->record->pk 		= $id;
        
        // field logs here
        $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Rank', $this->record->fields);
        
        if ($this->record->update()) {
            $this->record->fields = array();
            $this->record->table  = $this->table;
            $this->record->where[$this->pfield] = $id;
            $this->record->retrieve();
            // record logs
            if ($wasChange) {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Rank', $logs);
                echo "1";
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }
    
    public function getLeaveTypes()
    {
        $this->db->order_by('code','asc');
        $records = $this->db->get('leave_types');
        echo $this->frameworkhelper->get_json_data($records, 'leaveTypeID', 'leaveType');
    }
    
    public function display_session()
    {
        echo var_dump($_SESSION);
    }
}
