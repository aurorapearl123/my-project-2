<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leave extends CI_Controller
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
        $this->data['controller_page'] = $this->controller_page = site_url('leave'); // defines contoller link
        $this->table = 'leaves'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'leaveID'; // defines primary key
        $this->logfield = 'leaveNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/leave'; // defines module path
        
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
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Leave']; // defines the current sub module
        
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
        $this->roles['approve']  = $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'Delete Existing '.$this->data['current_module']['module_label']);
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
            $this->frameworkhelper->clear_session_item('leave_dates');
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
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // check role
        if ($this->roles['create']) {
            $this->record->table = $this->table;
            $this->record->fields = array();
            $table_fields = array('series', 'leaveNo','leaveTypeID', 'empID', 'employmentID', 'creditID', 'reason','location','commutation', 'remarks');
            
            foreach ($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            
            $this->db->select_sum('debit');
            $this->db->select_sum('credit');
            $this->db->where('empID', $this->record->fields['empID']);
            $this->db->where('leaveTypeID', $this->record->fields['leaveTypeID']);
            $this->db->where('date >=', date('Y-01-01 00:00:00', strtotime($this->record->fields['dateFiled'])));
            $this->db->where('date <=', date('Y-12-31 11:59:59', strtotime($this->record->fields['dateFiled'])));
            $credit = $this->db->get('leave_credit_ledger')->row();
            
            $this->record->fields['current_debit']  = $credit->debit;
            $this->record->fields['current_credit'] = $credit->credit;
            
            if ($this->record->save()) {
                $id = $this->db->insert_id();
                $this->record->fields = array();
                $this->record->where['leaveNo']   = trim($this->input->post('leaveNo'));
                $this->record->where['series']   = trim($this->input->post('series'));
                $this->record->retrieve();
                
                $this->_incrementSeries();
                
                // save dates
                $total = 0;
                if (!empty($_SESSION['leave_dates'])) {
                    $batch = array();
                    foreach($_SESSION['leave_dates'] as $item){
                        $info = array();
                        $info['leaveID']     = $this->record->field->leaveID;
                        $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                        $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                        $info['workCover']   = $item['workCover'];
                        $info['days']        = $item['days'];
                        $info['hours']       = $item['hours'];
                        $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                        $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                        $batch[] = $info;
                        
                        $total += ($item['days'] + ($item['hours'] / 8));
                    }
                    $this->db->insert_batch('leave_dates', $batch);
                }
                
                $this->db->set('totalDays', $total);
                $this->db->where('leaveID', $this->record->field->leaveID);
                $this->db->update('leaves');
                
                $this->frameworkhelper->clear_session_item('leave_dates');
                
                $this->session->set_userdata('current_series', $this->record->field->series);
                $this->session->set_userdata('current_leaveTypeID', $this->record->field->leaveTypeID);
                $this->session->set_userdata('current_empID', $this->record->field->empID);
                
                // record logs
                $logs = "Record - ".$genNo;
                $pfield = $this->pfield;
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
        
        // for retrieve with joining tables -------------------------------------------------
        // set table
        $this->record->table = $this->table;
        // set fields for the current table
        $this->record->setFields();
        // extend fields - join tables
        $this->record->fields[] = 'leave_credits.credit';
        // set joins
        $this->record->joins[]  = array('leave_credits',$this->table.'.creditID=leave_credits.creditID','left');
        // set where
        $this->record->where[$this->table.'.'.$this->pfield] = $id;
        // execute retrieve
        $this->record->retrieve();
        // ----------------------------------------------------------------------------------
        
        if ($this->roles['edit']) {
            $data['rec'] = $this->record->field;
            
            $this->frameworkhelper->clear_session_item('leave_dates');
            
            if ($this->record->field->empID) {
                $this->db->where('empID', $this->record->field->empID);
                $query = $this->db->get('employees', 1)->row();
                
                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }
            
            // get details
            $this->db->where('leaveID', $id);
            $this->db->order_by('startDate','asc');
            $items = $this->db->get('leave_dates');
            
            if ($items->num_rows()) {
                foreach($items->result() as $item) {
                    $d = array();
                    $d['leaveDateID']   = $item->leaveDateID;
                    $d['leaveID']       = $item->leaveID;
                    $d['startDate']     = date('m/d/Y', strtotime($item->startDate));
                    $d['endDate']       = date('m/d/Y', strtotime($item->endDate));
                    $d['workCover']     = $item->workCover;
                    $d['days']          = $item->days;
                    $d['hours']         = $item->hours;
                    $d['hoursLabel']    = ($item->hours) ? $item->hours : "--";
                    $d['startHourLabel']= ($item->startHour!='0000-00-00 00:00:00') ? date('H:i:00', strtotime($item->startHour)) : "--:--:--";
                    $d['endHourLabel']  = ($item->endHour!='0000-00-00 00:00:00') ? date('H:i:00', strtotime($item->endHour)) : "--:--:--";
                    
                    switch ($item->workCover) {
                        case "1" : $d['workCoverLabel'] = "Whole Working Day"; break;
                        case "2" : $d['workCoverLabel'] = "1st Half Working Day"; break;
                        case "3" : $d['workCoverLabel'] = "2nd Half Working Day"; break;
                        case "4" : $d['workCoverLabel'] = "Hours - Less Half Day"; break;
                    }
                    
                    $_SESSION['leave_dates'][] = $d;
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
        
        $table_fields = array('series', 'leaveNo', 'leaveTypeID', 'empID', 'employmentID', 'creditID','location','commutation', 'remarks', 'reason');
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                $this->db->where('leaveID', $this->record->pk);
                $this->db->delete('leave_dates');
                
                // save dates
                $total = 0;
                if (!empty($_SESSION['leave_dates'])) {
                    $batch = array();
                    foreach($_SESSION['leave_dates'] as $item){
                        $info = array();
                        $info['leaveID']     = $this->record->pk;
                        $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                        $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                        $info['workCover']   = $item['workCover'];
                        $info['days']        = $item['days'];
                        $info['hours']       = $item['hours'];
                        $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                        $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                        $batch[] = $info;
                        
                        $total += ($item['days'] + ($item['hours'] / 8));
                    }
                    $this->db->insert_batch('leave_dates', $batch);
                }
                
                $this->db->set('totalDays', $total);
                $this->db->where('leaveID', $this->record->pk);
                $this->db->update('leaves');
                
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
        
        
        // set fields
        $this->record->fields = array();
        // set table
        $this->record->table  = $this->table;
        // set where
        $this->record->where[$this->pfield] = $id;
        // execute retrieve
        $this->record->retrieve();
        
        // check roles
        if ($this->roles['delete'] && !$this->_in_used($id)) {
            // echo $this->db->last_query();
            
            if (! empty($this->record->field)) {
                $this->record->pfield = $this->pfield;
                $this->record->pk = $id;
                
                // record logs
                $logfield = $this->logfield;
                
                    if ($this->record->delete()) {
                        $this->db->where('leaveID', $id);
                        $this->db->delete('leave_dates');
                        
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
        
        // for retrieve with joining tables -------------------------------------------------
        // set table
        $this->record->table = $this->table;
        // set fields for the current table
        $this->record->setFields();
        // extend fields - join tables
        $this->record->fields[] = 'leave_types.leaveType';
        $this->record->fields[] = 'employees.empNo';
        $this->record->fields[] = 'employees.lname';
        $this->record->fields[] = 'employees.fname';
        $this->record->fields[] = 'employees.mname';
        $this->record->fields[] = 'employees.suffix';
        $this->record->fields[] = 'employees.imageExtension';
        $this->record->fields[] = 'employments.employmentNo';
        $this->record->fields[] = 'companies.companyName';
        $this->record->fields[] = 'companies.companyAbbr';
        $this->record->fields[] = 'branches.branchName';
        $this->record->fields[] = 'branches.branchAbbr';
        $this->record->fields[] = 'departments.deptName';
        $this->record->fields[] = 'divisions.divisionName';
        $this->record->fields[] = 'divisions.divisionAbbr';
        $this->record->fields[] = 'employee_types.employeeType';
        $this->record->fields[] = 'job_positions.positionCode';
        $this->record->fields[] = 'job_titles.jobTitle';
        
        // set joins
        $this->record->joins[]  = array('leave_types',$this->table.'.leaveTypeID=leave_types.leaveTypeID','left');
        $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
        $this->record->joins[]  = array('employments',$this->table.'.employmentID=employments.employmentID','left');
        $this->record->joins[]  = array('companies','employments.companyID=companies.companyID','left');
        $this->record->joins[]  = array('branches','employments.branchID=branches.branchID','left');
        $this->record->joins[]  = array('departments','employments.deptID=departments.deptID','left');
        $this->record->joins[]  = array('divisions','employments.divisionID=divisions.divisionID','left');
        $this->record->joins[]  = array('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
        $this->record->joins[]  = array('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        // set where
        $this->record->where[$this->table.'.'.$this->pfield] = $id;
        
        // execute retrieve
        $this->record->retrieve();
        // ----------------------------------------------------------------------------------
        
        // $this->roles['view'] = 1;
        if ($this->roles['view']) {
            $data['rec'] = $this->record->field;
//             var_dump($data['rec']);
            
            $this->session->set_userdata('current_empID', $this->record->field->empID);
            
            $this->db->where('leaveID', $id);
            $this->db->order_by('startDate','asc');
            $data['leave_dates'] = $this->db->get('leave_dates');   
            
            // record logs
            $pfield = $this->pfield;
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logs = "Record - " . $this->record->field->name;
                $this->log_model->table_logs($data['current_module']['module_label'], $this->table, $this->pfield, $this->record->field->$pfield, 'View', $logs);
            }
            
            $data['inUsed'] = $this->_in_used($id);
            
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
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'series', 'field'=>$this->table.'.series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'leaveNo', 'field'=>$this->table.'.leaveNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveTypeID', 'field'=>$this->table.'.leaveTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'employmentNo', 'field'=>'employments.employmentNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>$this->table.'.totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','leaveNo'=>'asc');
        
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
                
                $this->session->set_userdata($controller.'_dateFiled', $dateFiled);
                
                $this->session->set_userdata($controller.'_sortby', $sortby);
                $this->session->set_userdata($controller.'_sortorder', $sortorder);
                $this->session->set_userdata($controller.'_limit', $limit);
                
                // assign data variables for views
                foreach($condition_fields as $key) {
                    $data[$key['variable']] = $$key['variable'];
                }
                
                $data['dateFiled'] = $dateFiled;
                
                // select
                $this->db->select($this->table.'.*');
                $this->db->select('leave_types.leaveType');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.employmentNo');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('leave_types', $this->table.'.leaveTypeID=leave_types.leaveTypeID', 'left');
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments',$this->table.'.employmentID=employments.employmentID','left');
                $this->db->join('companies','employments.companyID=companies.companyID','left');
                $this->db->join('branches','employments.branchID=branches.branchID','left');
                
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
                
                // get
                $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
                
                // set pagination
                $config['base_url'] = $this->controller_page.'/show';
                $config['per_page'] = $limit;
                $this->pagination->initialize($config);
                
                
                // select
                $this->db->select($this->table.'.*');
                $this->db->select('leave_types.leaveType');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                $this->db->select('employments.employmentNo');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('leave_types', $this->table.'.leaveTypeID=leave_types.leaveTypeID', 'left');
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments',$this->table.'.employmentID=employments.employmentID','left');
                $this->db->join('companies','employments.companyID=companies.companyID','left');
                $this->db->join('branches','employments.branchID=branches.branchID','left');
                
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
//                 var_dump($data['records']);
                
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
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'series', 'field'=>$this->table.'.series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'leaveNo', 'field'=>$this->table.'.leaveNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveTypeID', 'field'=>$this->table.'.leaveTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>$this->table.'.totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','leaveNo'=>'asc');
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','leaveNo'=>'asc');
        
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
                $this->db->select($this->table.'.*');
                $this->db->select('leave_types.leaveType');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('leave_types', $this->table.'.leaveTypeID=leave_types.leaveTypeID', 'left');
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments',$this->table.'.employmentID=employments.employmentID','left');
                $this->db->join('companies','employments.companyID=companies.companyID','left');
                $this->db->join('branches','employments.branchID=branches.branchID','left');
                
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
        
        $data['title'] = "Leaves List";
        
        // load views
        $this->load->view('header_print', $data);
        $this->load->view($this->module_path . '/printlist');
        $this->load->view('footer_print');
    }
    
    public function exportlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $condition_fields = array(
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'series', 'field'=>$this->table.'.series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'leaveNo', 'field'=>$this->table.'.leaveNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'leaveTypeID', 'field'=>$this->table.'.leaveTypeID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'empNo', 'field'=>'employees.empNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'lname', 'field'=>'employees.lname', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>$this->table.'.totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>$this->table.'.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','leaveNo'=>'asc');
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','leaveNo'=>'asc');
        
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
                $this->db->select($this->table.'.*');
                $this->db->select('leave_types.leaveType');
                $this->db->select('employees.empNo');
                $this->db->select('employees.lname');
                $this->db->select('employees.fname');
                $this->db->select('employees.mname');
                $this->db->select('employees.suffix');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('leave_types', $this->table.'.leaveTypeID=leave_types.leaveTypeID', 'left');
                $this->db->join('employees', $this->table.'.empID=employees.empID', 'left');
                $this->db->join('employments',$this->table.'.employmentID=employments.employmentID','left');
                $this->db->join('companies','employments.companyID=companies.companyID','left');
                $this->db->join('branches','employments.branchID=branches.branchID','left');
                
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
                $records = $this->db->get()->result();
        
        $title = "Leaves List";
        $companyName = $this->config_model->getConfig('Company');
        $address = $this->config_model->getConfig('Address');
        
        // XML Blurb
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
            <Column ss:Index='4' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
            <Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
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
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'>" . strtoupper($title) . "</Data></Cell>";
        $data .= "</Row>";
        
        $data .= "<Row ss:StyleID='s24'>";
        $data .= "<Cell ss:MergeAcross='8'><Data ss:Type='String'></Data></Cell>";
        $data .= "</Row>";
        
        $fields[] = "  ";
        $fields[] = "SERIES";
        $fields[] = "DATE FILED";
        $fields[] = "LEAVE TYPE";
        $fields[] = "LEAVE NO.";
        $fields[] = "ID NUMBER";
        $fields[] = "EMPLOYEE";
        $fields[] = "DAYS";
        $fields[] = "STATUS";
        
        $data .= "<Row ss:StyleID='s24'>";
        // Field Name Data
        foreach ($fields as $fld) {
            $data .= "<Cell ss:StyleID='s23'><Data ss:Type='String'>$fld</Data></Cell>";
        }
        $data .= "</Row>";
        
        if (count($records)) {
            $ctr = 1;
            foreach ($records as $row) {
                $data .= "<Row>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $ctr . ".</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->series . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . date('m/d/Y', strtotime($row->dateFiled)) . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->leaveType . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->leaveNo . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->empNo . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->lname . ", " . $row->fname . " " . $row->mname . " " . $row->suffix . "</Data></Cell>";
                $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->totalDays . "</Data></Cell>";
                if ($row->status == 1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Pending</Data></Cell>";
                } else if ($row->status == 2) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Approved</Data></Cell>";
                } else if ($row->status == 0) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Cancelled</Data></Cell>";
                } else if ($row->status == -1) {
                    $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Disapproved</Data></Cell>";
                }
                $data .= "</Row>";
                
                $ctr ++;
            }
        }
        $data .= "</Table></Worksheet>";
        $data .= "</Workbook>";
        
        // Final XML Blurb
        $filename = "leave_list";
        
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename.xls;");
        header("Content-Type: application/ms-excel");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo $data;
    }
    
    public function print_record($id)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
    
        $data['title']      = "Personal Data Sheet";
        // **************************************************
    
        // check roles
        if ($this->roles['view']) {
            // **************************************************
            $id = $this->encrypter->decode($id);
        
            // for retrieve with joining tables -------------------------------------------------
            // set table
            $this->record->table = $this->table;
            // set fields for the current table
            $this->record->setFields();
            // extend fields - join tables
            $this->record->fields[] = 'leave_types.leaveType';
            $this->record->fields[] = 'employees.empNo';
            $this->record->fields[] = 'employees.lname';
            $this->record->fields[] = 'employees.fname';
            $this->record->fields[] = 'employees.mname';
            $this->record->fields[] = 'employees.suffix';
            $this->record->fields[] = 'employees.imageExtension';
            $this->record->fields[] = 'employments.employmentNo';
            $this->record->fields[] = 'companies.companyName';
            $this->record->fields[] = 'companies.companyAbbr';
            $this->record->fields[] = 'departments.deptName';
            $this->record->fields[] = 'branches.branchName';
            $this->record->fields[] = 'branches.branchAbbr';
            $this->record->fields[] = 'employee_types.employeeType';
            $this->record->fields[] = 'job_positions.positionCode';
            $this->record->fields[] = 'job_titles.jobTitle';
                
            // set joins
            $this->record->joins[]  = array('leave_types',$this->table.'.leaveTypeID=leave_types.leaveTypeID','left');
            $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
            $this->record->joins[]  = array('employments',$this->table.'.employmentID=employments.employmentID','left');
            $this->record->joins[]  = array('companies','employments.companyID=companies.companyID','left');
            $this->record->joins[]  = array('branches','employments.branchID=branches.branchID','left');
            $this->record->joins[]  = array('departments','employments.deptID=departments.deptID','left');
            $this->record->joins[]  = array('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
            $this->record->joins[]  = array('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
            $this->record->joins[]  = array('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
            // set where
            $this->record->where[$this->table.'.'.$this->pfield] = $id;
                
            // execute retrieve
            $this->record->retrieve();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            
            $this->db->where('leaveID', $id);
            $this->db->order_by('startDate','asc');
            $data['leave_dates'] = $this->db->get('leave_dates');
    
            $data['pdf_paging'] = TRUE;
            $data['title']      = "APPLICATION FOR LEAVE";
            $data['modulename'] = "APPLICATION FOR LEAVE";
    
            // load pdf class
            $this->load->library('mpdf');
            // load pdf class                                      left, right, top
            $this->mpdf->mpdf('en-GB',array(215.9,148.5),10,'Arial',15,15,5,5,0,20,'P');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;
    
            $html   = $this->load->view($this->module_path.'/print_record', $data, TRUE);
            $this->mpdf->WriteHTML($html);

            $footer = $this->load->view('print_record_footer', $data, TRUE);
            $this->mpdf->SetHTMLFooter($footer);
    
            $this->mpdf->Output("APPPLICATION_FOR_LEAVE.pdf","I");
        } else {
            $data["display"] = "block";
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header_print",$data);
            $this->load->view("message");
            $this->load->view("footer_print");
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // AJAX HANDLER FUNCTIONS
    public function check_duplicate()
    {
        // set table
        $this->record->table = $this->table;
        // set where
        $this->record->where['leaveNo'] = trim($this->input->post('leaveNo'));
        $this->record->where['series'] = trim($this->input->post('series'));
        // execute retrieve
        $this->record->retrieve();
        
        if (!empty($this->record->field))
            echo "1"; // duplicate
            else
                echo "0";
    }
    
    public function getLeaves()
    {
        $empID = trim($this->input->post('empID'));
        $employmentID = trim($this->input->post('employmentID'));
        
        if ($empID) {
            $this->db->where('empID', $empID);
        }
        if ($employmentID) {
            $this->db->where('employmentID', $employmentID);
        }
        $this->db->order_by('dateFiled','asc');
        $records = $this->db->get('leaves');
        echo $this->frameworkhelper->get_json_data($records, 'leaveID', 'leaveNo');
    }   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    private function _generateID($series)
    {
        $idSeries   = $this->config_model->getConfig('Leave Series');
        $idLength   = $this->config_model->getConfig('Order Series Length');
        
        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return substr($series, 2, 2).$id;
    }
    
    private function _incrementSeries()
    {
        $query = "UPDATE `config` SET `value` = `value`+ 1 WHERE `name` = 'Leave Series'";
        $this->db->query($query);
    }
    
    public function display_session_items($display_area='')
    {
        $sessionSet = $this->input->post('sessionSet');
        $records = isset($_SESSION[$sessionSet])? $_SESSION[$sessionSet]:array();
        
        // display session data
        $headers = array('Starting Date'=>'left','Ending Date'=>'left','Work Coverage'=>'left','Days'=>'right','Hours'=>'right','Start Hour'=>'center','End Hour'=>'center');
                $headers = array(
            array('column_header'=>'','column_field'=>'','width'=>'w-5','align'=>'center'),
            array('column_header'=>'Starting Date','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'Ending Date','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'Work Coverage','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'Days','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'Hours','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'Start Hour','column_field'=>'','width'=>'w-10','align'=>''),
            array('column_header'=>'End Hour','column_field'=>'','width'=>'w-10','align'=>''),
        );
        $display = array(
            array('align'=>'left','field'=>'startDate'),
            array('align'=>'left','field'=>'endDate'),
            array('align'=>'left','field'=>'workCoverLabel'),
            array('align'=>'left','field'=>'days'),
            array('align'=>'left','field'=>'hoursLabel'),
            array('align'=>'left','field'=>'startHourLabel'),
            array('align'=>'left','field'=>'endHourLabel'),
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

 
        if (!empty($_SESSION[$sessionID])) {
            foreach($_SESSION[$sessionID] as $id=>$item) {
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
    
    function confirm_form($id,$status)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        // ******************* end global settings *********************************
        $id = $this->encrypter->decode($id);
        
        if ($this->roles['approve']) {
            $data['id']     = $id;
            $data['status'] = $status;
            
            $this->db->where('leaveID', $id);
            $this->db->order_by('startDate','asc');
            $data['leave_dates'] = $this->db->get('leave_dates');
            
            $this->load->view($this->module_path.'/confirmform', $data);
        } else {
            // error
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("message",$data);
        }
    }
    
    public function display_session()
    {
        echo var_dump($_SESSION);
    }
    
    public function getJSON($table, $fields)
    {
        
        $fields = explode('_',$fields);
        // set table
        $this->record->table = $table;
        // set where
        if (!empty($fields)) {
            foreach($fields as $f) {
                $this->record->where[trim($f)] = trim($this->input->post(trim($f)));
            }
        }
        // execute retrieve
        $this->record->retrieve();
        $data = (array) $this->record->field;
        
        $res= json_encode($data);
        echo $res;
    }























    //status = -1
    public function disapprove_leave($id)
    {
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();
    
        // check roles
        if (($this->roles['approve'] && $this->record->field->status==1) || $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Leave Cancel')) {
            $this->record->fields = array();
    
            $this->record->fields['status'] = -1;
    
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;

            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Disapprove", $this->record->fields);

            if ($this->record->update()) {

                //change start
                $this->db->set('status', -1);
                $this->db->where('leaveID', $this->record->field->leaveID);
                $this->db->update('leave_dates');
                //change end


                // record logs
                if ($wasChange) {
                    $logfield = $this->logfield;
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Disapprove", $logs);
                }

                $encryptedID = $this->encrypter->encode($id);
                $this->view($encryptedID);

            }
        }

    }

    //status = 0
    public function cancel_leave($id)
    {
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();
    
        // check roles
        if (($this->roles['approve'] && $this->record->field->status==1) || $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Leave Cancel')) {
            $this->record->fields = array();
    
            $this->record->fields['status'] = 0;
    
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;

            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Cancel", $this->record->fields);

            if ($this->record->update()) {

                //change start
                $this->db->set('status', 0);
                $this->db->where('leaveID', $id);
                $this->db->update('leave_dates');

                // update credits
                $query = "UPDATE `leave_credits` SET `credit` = `credit` + ".$this->record->field->totalDays." WHERE `creditID` = ".$this->record->field->creditID;
                $this->db->query($query);

                    // post to ledger
                $this->db->set('empID', $this->record->field->empID);
                $this->db->set('leaveTypeID', $this->record->field->leaveTypeID);
                $this->db->set('date', date('Y-m-d H:i:s'));
                $this->db->set('debit', $this->record->field->totalDays);
                $this->db->set('credit', 0);
                $this->db->set('remarks', $this->record->field->leaveNo);
                $this->db->set('updatedBy', $this->session->userdata('current_user')->userID);
                $this->db->insert('leave_credit_ledger');
                //change end

                // record logs
                if ($wasChange) {
                    $logfield = $this->logfield;
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Cancel", $logs);
                }

                $encryptedID = $this->encrypter->encode($id);
                $this->view($encryptedID);

            }
        }
    }

    //status = 2
    public function approve_leave($id)
    {
        $id = $this->encrypter->decode($id);

        $this->record->table = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();
    
        // check roles
        if (($this->roles['approve'] && $this->record->field->status==1) || $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Leave Cancel')) {
            $this->record->fields = array();
    
            $this->record->fields['status'] = 2;
    
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = $id;

            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Approve", $this->record->fields);

            if ($this->record->update()) {

                //change start
                $this->db->where('leaveID', $id);
                $this->db->order_by('startDate','asc');
                $leave_dates = $this->db->get('leave_dates');

                $total = 0;
                if ($leave_dates->num_rows()) {
                    foreach ($leave_dates->result() as $row) {
                        $val =  trim($this->input->post('status_'.$row->leaveDateID));

                        $this->db->set('status', $val);
                        $this->db->where('leaveDateID', $row->leaveDateID);
                        $this->db->update('leave_dates');

                        if ($val == 2 || $val->$fld == 3) {
                            $total += floatval(($row->days + ($row->hours / 8)));
                        }
                    }
                }

                $this->db->set('totalDays', $total);
                $this->db->where('leaveID', $this->record->field->leaveID);
                $this->db->update('leaves');

                    // update credits
                $query = "UPDATE `leave_credits` SET `credit` = `credit` - ".$total." WHERE `creditID` = ".$this->record->field->creditID;
                $this->db->query($query);

                    // post to ledger
                $this->db->set('empID', $this->record->field->empID);
                $this->db->set('leaveTypeID', $this->record->field->leaveTypeID);
                $this->db->set('date', date('Y-m-d H:i:s'));
                $this->db->set('debit', 0);
                $this->db->set('credit', $total);
                $this->db->set('remarks', $this->record->field->leaveNo);
                $this->db->set('updatedBy', $this->session->userdata('current_user')->userID);
                $this->db->insert('leave_credit_ledger');
                //change end


                // record logs
                if ($wasChange) {
                    $logfield = $this->logfield;
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, "Approve", $logs);
                }

                $encryptedID = $this->encrypter->encode($id);
                $this->view($encryptedID);

            }
        }

    }

}




