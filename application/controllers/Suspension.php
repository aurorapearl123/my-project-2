<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Suspension extends CI_Controller
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
        $this->data['controller_page'] = $this->controller_page = site_url('suspension'); // defines contoller link
        $this->table = 'suspensions'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'suspensionID'; // defines primary key
        $this->logfield = 'suspensionNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/suspensions'; // defines module path
        
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
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Suspensions']; // defines the current sub module
        
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
        // echo 'hello';
        $this->submenu();
        $data = $this->data;
        // // check roles
        if ($this->roles['create']) {

            $this->frameworkhelper->clear_session_item('employees');
            
        //     // load views
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
            
            $table_fields = array('series','dateFiled', 'subject', 'reason','remarks','totalDays');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['suspensionNo'] = $genNo = $this->_generateID($this->record->fields['series']);
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            $this->record->fields['startDate']  = ($this->input->post('startDate')!="") ? date('Y-m-d', strtotime(trim($this->input->post('startDate')))) : "0000-00-00";
            $this->record->fields['endDate']    = ($this->input->post('endDate')!="") ? date('Y-m-d', strtotime(trim($this->input->post('endDate')))) : "0000-00-00";
            
            if ($this->record->save()) {
                $id = $this->db->insert_id();
                $this->record->fields = array();
                $this->record->where['suspensionNo']  = $genNo;
                $this->record->where['series']   = trim($this->input->post('series'));
                $this->record->retrieve();
                
                $this->_incrementSeries();
                
                if (!empty($_SESSION['employees'])) {
                    $batch = array();
                    foreach($_SESSION['employees'] as $item){
                        $info = array();
                        $info['suspensionID']   = $id;
                        $info['empID']          = $item['empID'];
                        $info['employmentID']   = $item['employmentID'];
                        $batch[] = $info;
                    }
                    $this->db->insert_batch('suspension_details', $batch);
                }
                
                $this->frameworkhelper->clear_session_item('employees');
                
                $this->session->set_userdata('current_series', $this->record->field->series);
                
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
            $data['required_fields'] = array('dateFiled'=>'Date Filed','startDate'=>'Start Date','endDate'=>'End Date',
                'subject'=>'Subject','reason'=>'Reason');
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
            
            $this->frameworkhelper->clear_session_item('employees');
            
            $this->db->select('suspension_details.*');
            $this->db->select('employees.empNo');
            $this->db->select('employees.lname');
            $this->db->select('employees.fname');
            $this->db->select('employees.mname');
            $this->db->select('employees.suffix');
            $this->db->select('employees.imageExtension');
            $this->db->select('employments.employmentNo');
            $this->db->select('companies.companyName');
            $this->db->select('companies.companyAbbr');
            $this->db->select('branches.branchName');
            $this->db->select('branches.branchAbbr');
            $this->db->select('employee_types.employeeType');
            $this->db->select('job_positions.positionCode');
            $this->db->select('job_titles.jobTitle');
            $this->db->from('suspension_details');
            $this->db->join('employees','suspension_details.empID=employees.empID','left');
            $this->db->join('employments','suspension_details.employmentID=employments.employmentID','left');
            $this->db->join('companies','employments.companyID=companies.companyID','left');
            $this->db->join('branches','employments.branchID=branches.branchID','left');
            $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
            $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
            $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
            $this->db->where('suspensionID', $id);
            $this->db->order_by('employees.lname','asc');
            $this->db->order_by('employees.fname','asc');
            $items = $this->db->get();
            
            if ($items->num_rows()) {
                foreach($items->result() as $item) {
                    $d = array();
                    $d['suspensionDetailID']= $item->orderDateID;
                    $d['suspensionID']      = $item->suspensionID;
                    $d['empID']             = $item->empID;
                    $d['employeename']      = $item->empNo.' - '.$item->lname.', '.$item->fname.' '.$item->mname.' - '.$item->suffix;
                    $d['employmentID']      = $item->employmentID;
                    $d['employment']        = $item->employmentNo.' - '.$item->companyAbbr.' / '.$item->branchAbbr.' / '.$item->employeeType.' / '.$item->jobTitle;
                    
                    $_SESSION['employees'][] = $d;
                }
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
        
        $table_fields = array('series','dateFiled', 'subject', 'reason','remarks','totalDays');
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            $this->record->fields['startDate']  = ($this->input->post('startDate')!="") ? date('Y-m-d', strtotime(trim($this->input->post('startDate')))) : "0000-00-00";
            $this->record->fields['endDate']    = ($this->input->post('endDate')!="") ? date('Y-m-d', strtotime(trim($this->input->post('endDate')))) : "0000-00-00";
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = trim($this->input->post($this->pfield));
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                $this->db->where('suspensionID', $this->record->pk);
                $this->db->delete('suspension_details');
                
                if (!empty($_SESSION['employees'])) {
                    $batch = array();
                    foreach($_SESSION['employees'] as $item){
                        $info = array();
                        $info['suspensionID']   = $this->record->pk;
                        $info['empID']          = $item['empID'];
                        $info['employmentID']   = $item['employmentID'];
                        $batch[] = $info;
                    }
                    $this->db->insert_batch('suspension_details', $batch);
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
                        $this->db->where('suspensionID', $id);
                        $this->db->delete('suspension_details');
                        
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
            
            
            $this->db->select('suspension_details.*');
            $this->db->select('employees.empNo');
            $this->db->select('employees.lname');
            $this->db->select('employees.fname');
            $this->db->select('employees.mname');
            $this->db->select('employees.suffix');
            $this->db->select('employees.imageExtension');
            $this->db->select('employments.employmentNo');
            $this->db->select('companies.companyName');
            $this->db->select('companies.companyAbbr');
            $this->db->select('branches.branchName');
            $this->db->select('branches.branchAbbr');
            $this->db->select('employee_types.employeeType');
            $this->db->select('job_positions.positionCode');
            $this->db->select('job_titles.jobTitle');
            $this->db->from('suspension_details');
            $this->db->join('employees','suspension_details.empID=employees.empID','left');
            $this->db->join('employments','suspension_details.employmentID=employments.employmentID','left');
            $this->db->join('companies','employments.companyID=companies.companyID','left');
            $this->db->join('branches','employments.branchID=branches.branchID','left');
            $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
            $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
            $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
            $this->db->where('suspensionID', $id);
            $this->db->order_by('employees.lname','asc');
            $this->db->order_by('employees.fname','asc');
            $data['suspension_details'] = $this->db->get(); 
            
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
            array('variable'=>'series', 'field'=>'series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'dateFiled', 'field'=>'dateFiled', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'suspensionNo', 'field'=>'suspensionNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'subject', 'field'=>'subject', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'reason', 'field'=>'reason', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>'totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>'status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','suspensionNo'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(4))
            $offset = $this->uri->segment(4);
            else
                $offset = 0;
                
                // source of filtering
                $filter_source = 0; // default/blank
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
                        $dateFiled  = trim($this->input->post('dateFiled'));
                        $sortby     = trim($this->input->post('sortby'));
                        $sortorder  = trim($this->input->post('sortorder'));
                        
                        break;
                    case 2:
                        foreach($condition_fields as $key) {
                            $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                        }
                        $dateFiled  = $this->session->userdata($controller.'_dateFiled');
                        $sortby     = $this->session->userdata($controller.'_sortby');
                        $sortorder  = $this->session->userdata($controller.'_sortorder');
                        break;
                    default:
                        foreach($condition_fields as $key) {
                            $$key['variable'] = $key['default_value'];
                        }
                        $dateFiled  = "";
                        $sortby     = "";
                        $sortorder  = "";
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
                
                $data['dateFiled'] = $dateFiled;
                
                // select
                $this->db->select($this->table.'*');
                
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
                
                // if($dateFiled) {
                //     $this->db->like('orders.dateFiled',date('Y-m-d',strtotime($dateFiled)));
                // }
                
                // $this->db->group_by($this->table.'.suspensionID');
                
                // get
                $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
                
                // set pagination
                $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
                $config['per_page'] = $limit;
                $config['uri_segment'] = 4;
                $this->pagination->initialize($config);
                
                // select
                // $this->db->select($this->table.'.empID');
                $this->db->select('suspensions.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                // $this->db->join('suspensions', $this->table.'.suspensionID=suspensions.suspensionID','left');
                
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
                
                // if($dateFiled) {
                //     $this->db->like('orders.dateFiled',date('Y-m-d',strtotime($dateFiled)));
                // }
                
                // $this->db->group_by($this->table.'.suspensionID');   
                
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
                
                // get
                $data['records'] = $this->db->get()->result();
                
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
            array('variable'=>'series', 'field'=>'series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'dateFiled', 'field'=>'dateFiled', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'suspensionNo', 'field'=>'suspensionNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'subject', 'field'=>'subject', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'reason', 'field'=>'reason', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>'totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>'status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','suspensionNo'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
            else
                $offset = 0;
                
                foreach($condition_fields as $key) {
                    $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
                }
                $dateFiled  = $this->session->userdata($controller.'_dateFiled');
                $sortby     = $this->session->userdata($controller.'_sortby');
                $sortorder  = $this->session->userdata($controller.'_sortorder');
                $limit      = $this->session->userdata($controller.'_limit');
                
                        
                // select
                // $this->db->select($this->table.'.empID');
                $this->db->select('suspensions.*');
                
                // from
                $this->db->from($this->table);
                
                // join     
                // $this->db->join('suspensions', $this->table.'.suspensionID=suspensions.suspensionID','left');
                
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

                if($dateFiled) {
                    $this->db->like($this->table.'.dateFiled',date('Y-m-d',strtotime($dateFiled)));
                }    

                $this->db->group_by($this->table.'.suspensionID');    
                
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
                
                // get
                $data['records'] = $this->db->get()->result();
                
                $data['title'] = "Suspension List";
                
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
        $this->table = "suspension_details";
        
        $condition_fields = array(
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'series', 'field'=>'orders.series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'suspensionNo', 'field'=>'orders.suspensionNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'subject', 'field'=>'orders.subject', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'reason', 'field'=>'orders.reason', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>'orders.totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>'orders.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','suspensionNo'=>'asc');
        
        $controller = $this->uri->segment ( 1 );
        
        foreach ( $condition_fields as $key ) {
            $$key ['variable'] = $this->session->userdata ( $controller . '_' . $key ['variable'] );
        }
        
        $limit = $this->session->userdata ( $controller . '_limit' );
        $offset = $this->session->userdata ( $controller . '_offset' );
        $sortby = $this->session->userdata ( $controller . '_sortby' );
        $sortorder = $this->session->userdata ( $controller . '_sortorder' );
        
        
        // select
        $this->db->select($this->table.'.empID');
        $this->db->select('suspensions.*');
        
        // from
        $this->db->from($this->table);
        
        // join
        $this->db->join('suspensions', $this->table.'.suspensionID=suspensions.suspensionID','left');
        
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
        $fields [] = "SERIES";
        $fields [] = "DATE FILED";
        $fields [] = "SUSPENSION NO.";
        $fields [] = "SUBJECT";
        $fields [] = "PERIOD";
        $fields [] = "REASON";
        $fields [] = "DAYS";
        $fields [] = "STATUS";
        
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
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->series . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . date('m/d/Y', strtotime($row->dateFiled)) . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->subject . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . "LATER" . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->reason . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->totalDays . "</Data></Cell>";

                
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    private function _generateID($series)
    {       
        $idSeries   = $this->config_model->getConfig('Suspension Order Series');
        $idLength   = $this->config_model->getConfig('Order Series Length');
    
        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return "SO".substr($series, 2, 2).$id;
    }
    
    private function _incrementSeries()
    {   
        $query = "UPDATE `config` SET `value` = `value`+ 1 WHERE `name` = 'Suspension Order Series'"; $this->db->query($query);     
    }
    
    
    // AJAX HANDLER FUNCTIONS
    // public function check_duplicate()
    // {
    //     // set table
    //     $this->record->table = $this->table;
    //     // set where        
    //     $this->record->where['suspensionNo'] = trim($this->input->post('suspensionNo'));
    //     $this->record->where['series'] = trim($this->input->post('series'));
    //     // execute retrieve
    //     $this->record->retrieve();
        
    //     if (!empty($this->record->field))
    //         echo "1"; // duplicate
    //     else 
    //         echo "0";
    // }
    

    
    public function display_session()
    {
        echo var_dump($_SESSION);
    }

    // public function display_session_items($display_area='')
    // {
    //     $sessionSet = $this->input->post('sessionSet');
    //     $records = isset($_SESSION[$sessionSet])? $_SESSION[$sessionSet]:array();
        

    //     echo $this->_session_tbody_view($sessionSet);
    // }

    // function _session_tbody_view($sessionSet='')
    // {
    //     $view = '';
    //         if (!empty($_SESSION[$sessionSet])) {
    //             foreach($_SESSION[$sessionSet] as $order) {
    //             $view .= '<tr>';   
    //             $view .= '<td>'. $order['employeename'] . '</td>';
    //             $view .= '<td>'. $order['employment'] . '</td>';
    //             $view .= '</tr>';
    //             }
    //         }
        
    //     return $view;

    // }



    public function display_session_items($display_area='')
    {
        $sessionSet = $this->input->post('sessionSet');
        $records = isset($_SESSION[$sessionSet])? $_SESSION[$sessionSet]:array();
        
        // display session data
        $headers = array('Employee'=>'left','Employment'=>'left');
                $headers = array(
            array('column_header'=>'','column_field'=>'','width'=>'w-5','align'=>'center'),
            array('column_header'=>'Employee','column_field'=>'','width'=>'','align'=>''),
            array('column_header'=>'Employment','column_field'=>'','width'=>'','align'=>''),
        );
        $display = array(
            array('align'=>'left','field'=>'employeename'),
            array('align'=>'left','field'=>'employment'),
        );
        echo $this->_tm_session_tabular_view($records,$headers,$display,$sessionSet,'950',$display_area);
    }



    private function _tm_session_tabular_view($records, $headers, $display, $sessionID, $width="100%",$display_area='',$default_rows=9,$callback="")
    {
        $colspan = count($headers)+1;
        $view = '<table class="table hover">'."\n";

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

 
        if (!empty($records)) {
            foreach($records as $id=>$item) {
            $view .= '<tr colspan="'.$colspan.'">'."\n";
            $view .= '<td>
                    <i style="font-size: 24px; color: #14699e!important;"class="la la-trash-o" alt="Delete" title="Delete Row" style="cursor: pointer;" onclick="tm_delete_session_item(\''.$sessionID.'\',\''.$id.'\',\''.$display_area.'\',\''.$callback.'\');"></i>
                    </td>'."\n";
                    
            if(!empty($display)) {
                foreach($display as $td) {
                    $text = $td['field'];
                    
                    $view .= '<td align="'.$td['align'].'" nowrap>'.$item[$text].'</td>'."\n";  
                }
            }
            $view .= '</tr>';
            }
        }           
        
        
        $view .= '</tbody>'."\n";
        $view .= '</table>'."\n";
        
        return $view;
    }

    public function confirm_record($id=0, $status=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        // **************************************************
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();

        // check roles
        // if ($this->roles['approve'] && $this->record->field->status==1) {
        if ($this->roles['approve']) {
            $this->record->fields = array();

            $this->record->fields['status'] = $status;

            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;
            
            switch ($status) {
                case 2: $operation = "Approve"; break;
                case 0: $operation = "Cancel"; break;
                case -1: $operation = "Disapprove"; break;
            }

            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $this->record->fields);

            if ($this->record->update()) {                              
                if ($status==2) {
                    $this->db->set('status', 2);
                } else {
                    $this->db->set('status', 0);
                }
                $this->db->where('suspensionID', $this->record->field->suspensionID);
                $this->db->update('suspension_details');
                
                
                // record logs
                if ($wasChange) {
                    $logfield = $this->logfield;
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $logs);
                }

                // successful
                $data["class"] = "success";
                $data["msg"] = $this->data['current_module']['module_label'] . " successfully approved.";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            } else {
                // error
                $logfield = $this->pfield;
                $data["display"] = "block";
                $data["class"] = "errorbox";
                $data["msg"] = "Error in changing the ".$this->data['current_module']['module_label']." status!";
                $data["urlredirect"] = $this->controller_page . "/view/" . $this->encrypter->encode($id);
                $this->load->view("header", $data);
                $this->load->view("message");
                $this->load->view("footer");
            }
        } else {
            // error
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header".$page_type,$data);
            $this->load->view("message",$data);
            $this->load->view("footer".$page_type);
        }
    }
}


        // html += '<tr class="suspended_employees">'
        //     + '<td>'
        //     + employmentText
        //     + '</td>'
        //     + '<td>'
        //     + empText
        //     + '</td>'
        //     + '</tr>';
        // $('#suspendedEmpTable').append(html)
