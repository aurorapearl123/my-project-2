<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leave_credit extends CI_Controller
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
        $this->data['controller_page'] = $this->controller_page = site_url('leave_credit'); // defines contoller link
        $this->table = 'leave_credits'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'creditID'; // defines primary key
        $this->logfield = 'empID';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/leave_credit'; // defines module path
        
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
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Leave Credit']; // defines the current sub module
        
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
            $data['empID']  	= $this->session->userdata('current_user')->empID;
            $data['empNo']  	= $this->session->userdata('current_user')->empNo;
            $data['leaveTypeID']= $this->session->userdata('current_user')->leaveTypeID;
            
            if ($data['empID']) {
                $this->db->where('empID', $data['empID']);
                $query = $this->db->get('employees', 1)->row();
                
                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }
            
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
            
            $table_fields = array('empID', 'leaveTypeID', 'credit');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            if ($this->record->fields['credit'] > 0) {
                $this->record->fields['lastUpdate'] = date('Y-m-d H:i:s');
            }
            
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['empID']		= trim($this->input->post('empID'));
                $this->record->where['leaveTypeID']	= trim($this->input->post('leaveTypeID'));
                $this->record->retrieve();
                
                if ($this->record->field->credit > 0) {
                    // post to ledger
                    $this->db->set('empID', $this->record->field->empID);
                    $this->db->set('leaveTypeID', $this->record->field->leaveTypeID);
                    $this->db->set('date', date('Y-m-d H:i:s'));
                    $this->db->set('debit', $this->record->field->credit);
                    $this->db->set('credit', 0);
                    $this->db->set('remarks','Initial Credits');
                    $this->db->insert('leave_credit_ledger');
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
            $data['required_fields'] = array('empID'=>'Employee', 'leaveTypeID'=>'Special Skill');
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
            
            if ($this->record->field->empID) {
                $this->db->where('empID', $this->record->field->empID);
                $query = $this->db->get('employees', 1)->row();
                
                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }
            
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
        
        $table_fields = array('empID', 'leaveTypeID', 'credit');
        
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
            $this->record->fields[]	= 'employees.empNo';
            $this->record->fields[]	= 'employees.lname';
            $this->record->fields[]	= 'employees.fname';
            $this->record->fields[]	= 'employees.mname';
            $this->record->fields[]	= 'employees.suffix';
            // set joins
            $this->record->joins[]	= array('employees',$this->table.'.empID=employees.empID','left');
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
//         $this->table = 'employments';
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'companyID', 'field'=>'employments.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
        );
        
        // sorting fields
        $sorting_fields = array('employees.lname'=>'desc');
        
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
                
                if ($pageType==3) {
                    foreach($condition_fields as $key) {
                        if ($key['variable']=='empID') {
                            $$key['variable'] = $this->encrypter->decode(trim($this->input->post('id')));
                        }
                    }
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
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.companyID');
                $this->db->select('employments.branchID');
                $this->db->select('employments.deptID');
                $this->db->select('employments.divisionID');
                $this->db->select('companies.companyAbbr');
                $this->db->select('branches.branchAbbr');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments', 'employees.empID=employments.empID', 'left');
                $this->db->join('companies', 'employments.companyID=companies.companyID', 'left');
                $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
                
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
                
                $this->db->where('employees.status', 1);
                $this->db->group_by($this->table.'.empID');
                
                // get
                $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
                
                // set pagination
                $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
                $config['per_page'] = $limit;
                $config['uri_segment'] = 4;
                $this->pagination->initialize($config);
                
                $this->db->select($this->table.'.*');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.companyID');
                $this->db->select('employments.branchID');
                $this->db->select('employments.deptID');
                $this->db->select('employments.divisionID');
                $this->db->select('companies.companyAbbr');
                $this->db->select('branches.branchAbbr');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments', 'employees.empID=employments.empID', 'left');
                $this->db->join('companies', 'employments.companyID=companies.companyID', 'left');
                $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
                
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
                
                $this->db->where('employees.status', 1);
                $this->db->group_by($this->table.'.empID');
                
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

        
        $condition_fields = array(
            array('variable'=>'companyID', 'field'=>'employments.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
        );
        
        // sorting fields
        $sorting_fields = array('employees.lname'=>'desc');
        
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
                $this->db->select($this->table.'.*');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.companyID');
                $this->db->select('employments.branchID');
                $this->db->select('employments.deptID');
                $this->db->select('employments.divisionID');
                $this->db->select('companies.companyAbbr');
                $this->db->select('branches.branchAbbr');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments', 'employees.empID=employments.empID', 'left');
                $this->db->join('companies', 'employments.companyID=companies.companyID', 'left');
                $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
                
                $this->db->where('employees.status', 1);
                $this->db->group_by($this->table.'.empID');
                
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
                
                $data['title'] = "Leave Credit List";
                
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
            array('variable'=>'companyID', 'field'=>'employments.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
        );
        
        // sorting fields
        $sorting_fields = array('employees.lname'=>'desc');
        
        $controller = $this->uri->segment ( 1 );
        
        foreach ( $condition_fields as $key ) {
            $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
        }
        
        $limit = $this->session->userdata ( $controller . '_limit' );
        $offset = $this->session->userdata ( $controller . '_offset' );
        $sortby = $this->session->userdata ( $controller . '_sortby' );
        $sortorder = $this->session->userdata ( $controller . '_sortorder' );
        
        
        // select
        $this->db->select($this->table.'.*');
        $this->db->select('employees.empNo');
        $this->db->select('employees.lname');
        $this->db->select('employees.fname');
        $this->db->select('employees.mname');
        $this->db->select('employees.suffix');
        $this->db->select('employments.companyID');
        $this->db->select('employments.branchID');
        $this->db->select('employments.deptID');
        $this->db->select('employments.divisionID');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
        $this->db->join('employments', 'employees.empID=employments.empID', 'left');
        $this->db->join('companies', 'employments.companyID=companies.companyID', 'left');
        $this->db->join('branches', 'employments.branchID=branches.branchID', 'left');
        
        $this->db->where('employees.status', 1);
        $this->db->group_by($this->table.'.empID');
        
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
        
        $title = "Leave Credit List";
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
            <Column ss:Index='10' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		<Column ss:Index='11' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
    		    ";
        
        // header
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s20'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
        $data .= "</Row>";
        $data .= "<Row ss:StyleID='s24A'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>" . $address . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'>" . strtoupper ( $title ) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='10'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields [] = "  ";
        $fields [] = "COMPANY";
        $fields [] = "BRANCH";
        $fields [] = "ID NUMBER";
        $fields [] = "EMPLOYEE";
        
        $types = $this->db->get('leave_types');
        
        if ($types->num_rows()) {
            foreach ($types->result() as $type) {
                $fields [] = $type->code;
            }
        }

        
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->companyAbbr . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->branchAbbr . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->empNo . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->lname. ", ".$row->fname." ".$row->mname." ".$row->suffix. "</Data></Cell>";
                if ($types->num_rows()) {
                    foreach ($types->result() as $type) {
                        $this->db->where('empID', $row->empID);
                        $this->db->where('leaveTypeID', $type->leaveTypeID);
                        $credit = $this->db->get('leave_credits', 1)->row();
                        $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $credit->credit . "</Data></Cell>";
                    }
                }
                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        //Final XML Blurb
        $filename = "leave_credit_list";
        
        header ( "Content-type: application/octet-stream" );
        header ( "Content-Disposition: attachment; filename=$filename.xls;" );
        header ( "Content-Type: application/ms-excel" );
        header ( "Pragma: no-cache" );
        header ( "Expires: 0" );
        
        echo $data;
        
    }
    
    
    public function ledger($id=0, $leaveTypeID=0, $year=0, $pageType=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // **************************************************
        $id = $this->encrypter->decode($id);
        $leaveTypeID = $this->encrypter->decode($leaveTypeID);
        
        
        // check roles
        if ($this->roles['view']) {
            $this->db->select('leave_credit_ledger.*');
            $this->db->select('users.userName');
            $this->db->from('leave_credit_ledger');
            $this->db->join('users','leave_credit_ledger.updatedBy=users.userID','left');
            $this->db->where('leave_credit_ledger.empID', $id);
            $this->db->where('leave_credit_ledger.leaveTypeID', $leaveTypeID);
            $this->db->where('YEAR(`leave_credit_ledger`.`date`)', $year);
            $this->db->order_by('leave_credit_ledger.date','asc');
            $data['records'] = $this->db->get();
            
            $this->db->where('empID', $id);
            $data['employee'] = $this->db->get('employees', 1)->row();
            
            $this->db->where('leaveTypeID', $leaveTypeID);
            $data['leave_type'] = $this->db->get('leave_types', 1)->row();
            
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            // load views
            $this->load->view('header_popup', $data);
            $this->load->view($this->module_path . '/ledger');
            $this->load->view('footer_popup');

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
    

    public function ledger_decoded($id=0, $leaveTypeID=0, $year=0, $pageType=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // **************************************************
        
        
        // check roles
        if ($this->roles['view']) {
            $this->db->select('leave_credit_ledger.*');
            $this->db->select('users.userName');
            $this->db->from('leave_credit_ledger');
            $this->db->join('users','leave_credit_ledger.updatedBy=users.userID','left');
            $this->db->where('leave_credit_ledger.empID', $id);
            $this->db->where('leave_credit_ledger.leaveTypeID', $leaveTypeID);
            $this->db->where('YEAR(`leave_credit_ledger`.`date`)', $year);
            $this->db->order_by('leave_credit_ledger.date','asc');
            $data['records'] = $this->db->get();
            
            $this->db->where('empID', $id);
            $data['employee'] = $this->db->get('employees', 1)->row();
            
            $this->db->where('leaveTypeID', $leaveTypeID);
            $data['leave_type'] = $this->db->get('leave_types', 1)->row();
            
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            // load views
            $this->load->view('header_popup', $data);
            $this->load->view($this->module_path . '/ledger');
            $this->load->view('footer_popup');

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
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // AJAX HANDLER FUNCTIONS
    public function check_duplicate()
    {
        // set table
        $this->record->table = $this->table;
        // set where
        $this->record->where['empID'] 		= trim($this->input->post('empID'));
        $this->record->where['leaveTypeID']		= trim($this->input->post('leaveTypeID'));
        // execute retrieve
        $this->record->retrieve();
        
        if (!empty($this->record->field))
            echo "1"; // duplicate
            else
                echo "0";
    }
    
    public function getLeaveCredits()
    {
        $empID = trim($this->input->post('empID'));
        
        $this->db->select('leave_credits.*');
        $this->db->select('leave_types.code');
        $this->db->select('leave_types.leaveType');
        $this->db->from('leave_credits');
        $this->db->join('leave_types','leave_credits.leaveTypeID=leave_types.leaveTypeID','left');
        $this->db->where('leave_credits.empID', $empID);
        $this->db->where('leave_credits.credit >', 0);
        $this->db->order_by('leave_credits.credit','desc');
        $this->db->order_by('leave_types.rank','asc');
        $this->db->order_by('leave_types.leaveType','asc');
        $records = $this->db->get();
        echo $this->frameworkhelper->get_json_data($records, 'creditID', array('credit'=>' - ','leaveType'=>''));
    }
    
    public function update_credit()
    {
        $id 		= trim($this->input->post('id'));
        $new_credit = trim($this->input->post('credit'));
        
        $this->db->where('creditID', $id);
        $query = $this->db->get('leave_credits', 1)->row();
        $old_credit = $query->credit;
        
        $this->record->table  = $this->table;
        $this->record->fields = array();
        
        $this->record->fields['credit'] = $new_credit;
        $this->record->pfield 	= $this->pfield;
        $this->record->pk 		= $id;
        
        // field logs here
        $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Credit', $this->record->fields);
        
        if ($this->record->update()) {
            $this->record->fields = array();
            $this->record->table  = $this->table;
            $this->record->where[$this->pfield] = $id;
            $this->record->retrieve();
            
            $debit  = 0;
            $credit = 0;
            
            if ($new_credit > $old_credit) {
                $debit  = number_format($new_credit - $old_credit, 3);
            } else {
                $credit = number_format($old_credit - $new_credit, 3);
            }
//             echo $this->record->field->empID;
            
            // post to ledger
            $this->db->set('empID', $this->record->field->empID);
            $this->db->set('leaveTypeID', $this->record->field->leaveTypeID);
            $this->db->set('date', date('Y-m-d H:i:s'));
            $this->db->set('debit', $debit);
            $this->db->set('credit', $credit);
            $this->db->set('remarks', 'Adjustment');
            $this->db->set('updatedBy', $this->session->userdata('current_user')->userID);
            $this->db->insert('leave_credit_ledger');
            
            // record logs
            if ($wasChange) {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update Credit', $logs);
                echo "1";
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }
    
    public function display_session()
    {
        echo var_dump($_SESSION);
    }










    public function emp_leave_credit($id)
    {

        $this->db->select('leave_types.*');
        $this->db->select('leave_credits.creditID');
        $this->db->select('leave_credits.empID');
        $this->db->select('leave_credits.credit');

        $this->db->from('leave_types');

        $this->db->join('leave_credits', 'leave_types.leaveTypeID=leave_credits.leaveTypeID', 'left');

        //where
        $this->db->where('empID', $id);

        $results = $this->db->get()->result();
        // var_dump($results);
        $this->_display_session_items($results);

    }



      // public 'leaveTypeID' => string '1' (length=1)
      // public 'code' => string 'VL' (length=2)
      // public 'leaveType' => string 'Vacation Leave' (length=14)
      // public 'creditEarn' => string '2' (length=1)
      // public 'frequency' => string '0' (length=1)
      // public 'isWithoutpayCover' => string '0' (length=1)
      // public 'isCashable' => string '0' (length=1)
      // public 'remarks' => string '' (length=0)
      // public 'rank' => string '3' (length=1)
      // public 'status' => string '1' (length=1)
      // public 'creditID' => string '64' (length=2)
      // public 'empID' => string '64' (length=2)
      // public 'credit' => string '21.000' (length=6)

    private function _display_session_items($fields)
    {

        // display session data
        $headers = array();
        $display = array();
        foreach ($fields as $fld) {
            $headers[] = array('column_header'=>$fld->code,'column_field'=>'','width'=>'w-5','align'=>'center');
            $headers[] = array('column_header'=>'','column_field'=>'','width'=>'w-5','align'=>'center');

            $field = '<input class="form-control" type="text" name="credit_'.$fld->creditID.'" id="credit_'.$fld->creditID.'" value="'.$fld->credit.'" style="width:60px; height: 32px;" onblur="updateCredit('.$fld->creditID.')" onkeypress="return event.charCode >= 48 && event.charCode <= 57"/>';
            
            // $field = $fld->credit;
            $display[] = array('align'=>'left','field'=>$field);

            $icon = '<img src="'.base_url('assets/img/main/ok_.png').'" id="updated_'.$fld->creditID.'" style="display: none; font-size: 18px; color: green;" class="la la-check">&nbsp;';
            $btn = '<img src="'.base_url('assets/img/main/ledger.png').'" id="btn_'.$fld->creditID.'" style="font-size: 24px; color: green;" class="view-credit la la-clipboard" data-id="'.$fld->leaveTypeID.'"/>'; 
            
            // $display[] = array('align'=>'left','field'=>$field);
            $display[] = array('align'=>'left','field'=>$icon.$btn);
        }
            
        echo $this->_tm_session_tabular_view($headers,$display,$sessionSet,'950',$display_area);
    }



    private function _tm_session_tabular_view($headers, $display, $sessionID, $width="100%",$display_area='',$default_rows=9,$callback="")
    {
        $colspan = count($headers);
        $view = '<table id="family-members" class="table hover">'."\n";

        //thead
        $view .= '<thead class="thead-light">'."\n";
        if (!empty($headers)) {
            foreach($headers as $col) {
                if ($col['column_field'] == $sortby) {
                    if ($sortorder=="DESC") {
                        $view .= "\n".'<th class="'.$col['width'].'" align="'.$col['align'].'" nowrap>';
                    } else {
                        $view .= "\n".'<th class="'.$col['width'].'" align="'.$col['align'].'" nowrap>';
                    }
                } else {
                    $view .= "\n".'<th class="'.$col['width'].'" align="'.$col['align'].'" nowrap>';
                }
                
                $view .= $col['column_header'];
                $view .= '</th>';
            }
        }
        $view .= '</thead>'."\n";


        //tbody
        $view .= '<tbody>'."\n";
        $view .= '<tr colspan="'.$colspan.'">'."\n";

        if(!empty($display)) {
            foreach($display as $td) {
                $text = $td['field'];

                $view .= '<td align="'.$td['align'].'" nowrap>'.$td['field'].'</td>'."\n"; 
            }
        }
        $view .= '</tr>';         
        
        
        $view .= '</tbody>'."\n";
        $view .= '</table>'."\n";
        
        return $view;
    }
}
