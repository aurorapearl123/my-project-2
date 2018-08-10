<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Travel_order extends CI_Controller
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
        $this->data['controller_page'] = $this->controller_page = site_url('travel_order'); // defines contoller link
        $this->table = 'orders'; // defines the default table
        $this->pfield = $this->data['pfield'] = 'orderID'; // defines primary key
        $this->logfield = 'orderNo';
        $this->module_path = 'modules/' . strtolower(str_replace(" ", "_", $this->module)) . '/travel_order'; // defines module path
        
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
        $this->data['current_module'] = $this->modules[$this->module]['sub']['Travel Order']; // defines the current sub module
        
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
            $data['series']  = ($this->session->userdata('current_series')!="") ? $this->session->userdata('current_series') : date('Y');
            $data['type']    = $this->session->userdata('current_type');

            //Clear session items

            $this->frameworkhelper->clear_session_item('employees');
            $this->frameworkhelper->clear_session_item('order_dates');

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
            
            $table_fields = array('series','dateFiled', 'type', 'subject', 'destination','purpose','recipientType', 'remarks');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['orderNo']    = $genNo = trim($this->input->post('orderNo'));
            // $this->record->fields['orderNo']     = $genNo = $this->_generateID($this->record->fields['type'], $this->record->fields['series']);
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['orderNo']  = $genNo;
                $this->record->where['series']   = trim($this->input->post('series'));
                $id = $this->db->insert_id();
                $this->record->retrieve();

                $this->_incrementSeries($this->record->field->type);    
                
                if (!empty($_SESSION['order_dates'])) {
                    if ($this->record->field->recipientType==1) { // multiple companies
                        if ($this->input->post('chkAdd')) {
                            $batch = array();
                            foreach($this->input->post('chkAdd') as $companyID) {
                                $this->db->where('companyID', $companyID);
                                $this->db->where_in('employeeTypeID', $this->input->post('employeeTypeID'));
                                $this->db->where('status', 1);
                                $employments = $this->db->get('employments');
                                
                                if ($employments->num_rows()) {
                                    foreach ($employments->result() as $row) {
                                        $info = array();
                                        $info['orderID']        = $this->record->field->orderID;
                                        $info['empID']          = $row->empID;
                                        $info['employmentID']   = $row->employmentID;
                                        foreach($_SESSION['order_dates'] as $item){
                                            $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                                            $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                                            $info['workCover']   = $item['workCover'];
                                            $info['days']        = $item['days'];
                                            $info['hours']       = $item['hours'];
                                            $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                                            $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                                            $batch[] = $info;
                                        }
                                    }
                                }
                            }
                            if ($batch) {
                            $this->db->insert_batch('order_details', $batch);
                            }
                        }
                    } elseif ($this->record->field->recipientType==2) { // multiple branches
                        if ($this->input->post('chkAdd')) {
                            $batch = array();
                            foreach($this->input->post('chkAdd') as $branchID) {
                                $this->db->where('branchID', $branchID);
                                $this->db->where_in('employeeTypeID', $this->input->post('employeeTypeID'));
                                $this->db->where('status', 1);
                                $employments = $this->db->get('employments');
                                
                                if ($employments->num_rows()) {
                                    foreach ($employments->result() as $row) {
                                        $info = array();
                                        $info['orderID']        = $this->record->field->orderID;
                                        $info['empID']          = $row->empID;
                                        $info['employmentID']   = $row->employmentID;
                                        foreach($_SESSION['order_dates'] as $item){
                                            $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                                            $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                                            $info['workCover']   = $item['workCover'];
                                            $info['days']        = $item['days'];
                                            $info['hours']       = $item['hours'];
                                            $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                                            $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                                            $batch[] = $info;
                                        }
                                    }
                                }
                            }
                            if ($batch) {
                            $this->db->insert_batch('order_details', $batch);
                            }
                        }
                    } elseif ($this->record->field->recipientType==3) { // multiple divisions
                        if ($this->input->post('chkAdd')) {
                            $batch = array();
                            foreach($this->input->post('chkAdd') as $divisionID) {
                                $this->db->where('divisionID', $divisionID);
                                $this->db->where_in('employeeTypeID', $this->input->post('employeeTypeID'));
                                $this->db->where('status', 1);
                                $employments = $this->db->get('employments');
                                
                                if ($employments->num_rows()) {
                                    foreach ($employments->result() as $row) {
                                        $info = array();
                                        $info['orderID']        = $this->record->field->orderID;
                                        $info['empID']          = $row->empID;
                                        $info['employmentID']   = $row->employmentID;
                                        foreach($_SESSION['order_dates'] as $item){
                                            $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                                            $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                                            $info['workCover']   = $item['workCover'];
                                            $info['days']        = $item['days'];
                                            $info['hours']       = $item['hours'];
                                            $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                                            $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                                            $batch[] = $info;
                                        }
                                    }
                                }
                            }
                            if ($batch) {
                            $this->db->insert_batch('order_details', $batch);
                            }
                        }
                    } elseif ($this->record->field->recipientType==4 || $this->record->field->recipientType==5) { // branch or division employees
                        if ($this->input->post('chkAdd')) {
                            $batch = array();
                            foreach($this->input->post('chkAdd') as $employmentID) {
                                $info = array();
                                $info['orderID']        = $this->record->field->orderID;
                                $info['empID']          = trim($this->input->post('empID_'.$employmentID));
                                $info['employmentID']   = trim($this->input->post('employmentID_'.$employmentID));
                                foreach($_SESSION['order_dates'] as $item){
                                    $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                                    $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                                    $info['workCover']   = $item['workCover'];
                                    $info['days']        = $item['days'];
                                    $info['hours']       = $item['hours'];
                                    $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                                    $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                                    $batch[] = $info;
                                }
                            }
                            $this->db->insert_batch('order_details', $batch);
                        }
                    } elseif ($this->record->field->recipientType==6) { // individual employees
                        if (!empty($_SESSION['employees'])) {
                            $batch = array();
                            foreach($_SESSION['employees'] as $item){
                                $info = array();
                                $info['orderID']        = $this->record->field->orderID;
                                $info['empID']          = $item['empID'];
                                $info['employmentID']   = $item['employmentID'];
                                foreach($_SESSION['order_dates'] as $item){
                                    $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                                    $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                                    $info['workCover']   = $item['workCover'];
                                    $info['days']        = $item['days'];
                                    $info['hours']       = $item['hours'];
                                    $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                                    $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                                    $batch[] = $info;
                                }
                            }
                            if ($batch) {
                            $this->db->insert_batch('order_details', $batch);
                            }

                        }
                    }
                    
                    $total = 0;
                    foreach($_SESSION['order_dates'] as $item){
                        $total += ($item['days'] + ($item['hours'] / 8));
                    }
                    
                    $this->db->set('totalDays', $total);
                    $this->db->where('orderID', $this->record->field->orderID);
                    $this->db->update('orders');

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
            
            $this->frameworkhelper->clear_session_item('order_dates');

            // get details
            $this->db->where('orderID', $id);
            $this->db->group_by('startDate');
            $this->db->order_by('startDate','asc');
            $items = $this->db->get('order_details');
            
            if ($items->num_rows()) {
                foreach($items->result() as $item) {
                    $d = array();
                    $d['orderDateID']   = $item->orderDateID;
                    $d['orderID']       = $item->orderID;
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
                    
                    $_SESSION['order_dates'][] = $d;
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

        $table_fields = array('series','dateFiled','orderNo', 'type', 'subject', 'destination','purpose','remarks');
        
        // check roles
        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }
            
            $this->record->fields['dateFiled']  = ($this->input->post('dateFiled')!="") ? date('Y-m-d', strtotime(trim($this->input->post('dateFiled')))) : "0000-00-00";
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk = $id;
            
            // field logs here
            $wasChange = $this->log_model->field_logs($data['current_module']['module_label'], $this->table, $this->pfield, $id, 'Update', $this->record->fields);
            
            if ($this->record->update()) {
                $this->db->where('orderID', $this->record->pk);
                $this->db->delete('order_details');
                
                // save dates
                if (!empty($_SESSION['order_dates'])) {
                    $batch = array();
                    foreach($_SESSION['order_dates'] as $item){
                        $info = array();
                        $info['orderID']     = $this->record->pk;
                        $info['startDate']   = date('Y-m-d', strtotime($item['startDate']));
                        $info['endDate']     = date('Y-m-d', strtotime($item['endDate']));
                        $info['workCover']   = $item['workCover'];
                        $info['hours']       = $item['hours'];
                        $info['startHour']   = ($item['startHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['startDate'].' '.$item['startHourLabel'])) : '0000-00-00 00:00:00';
                        $info['endHour']     = ($item['endHourLabel']!='--:--:--') ? date('Y-m-d H:i:s', strtotime($item['endDate'].' '.$item['endHourLabel'])) : '0000-00-00 00:00:00';
                        $batch[] = $info;
                    }
                    $this->db->insert_batch('order_details', $batch);
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
                        $this->db->where('orderID', $id);
                        $this->db->delete('order_details');
                        
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
            
            $this->db->where('orderID', $id);
            $this->db->group_by('startDate');
            $this->db->order_by('startDate','asc');
            $data['order_details'] = $this->db->get('order_details');   
            
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
        $this->table = "order_details";
        
        $condition_fields = array(
            array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'series', 'field'=>'orders.series', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'orderNo', 'field'=>'orders.orderNo', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'type', 'field'=>'orders.type', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'subject', 'field'=>'orders.subject', 'default_value'=>'', 'operator'=>'like_both'),
            array('variable'=>'totalDays', 'field'=>'orders.totalDays', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>'orders.status', 'default_value'=>'', 'operator'=>'where'),
        );
        
        // sorting fields
        $sorting_fields = array('dateFiled'=>'desc','orderNo'=>'asc');
        
        $controller = $this->uri->segment(1);
        
        if ($this->uri->segment(3))
            $offset = $this->uri->segment(3);
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
                    $dateFiled  = trim($this->input->post('dateFiled'));
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
                $this->db->select($this->table.'.empID');
                $this->db->select('orders.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('orders', $this->table.'.orderID=orders.orderID','left');
                
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
                    $this->db->like('orders.dateFiled',date('Y-m-d',strtotime($dateFiled)));
                }
                
                // memo, oo, to
                $this->db->where_in('orders.type', $types);
                
                $this->db->group_by($this->table.'.orderID');
                
                // get
                $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();
                
                // set pagination
                $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
                $config['per_page'] = $limit;
                $config['uri_segment'] = 4;
                $this->pagination->initialize($config);
                
                
                // select
                $this->db->select($this->table.'.empID');
                $this->db->select('orders.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('orders', $this->table.'.orderID=orders.orderID','left');
                
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
                    $this->db->like('orders.dateFiled',date('Y-m-d',strtotime($dateFiled)));
                }
                
                // memo, oo, to
                $this->db->where_in('orders.type', $types);
                
                $this->db->group_by($this->table.'.orderID');    
                
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
                $this->table = "order_details";

                $condition_fields = array(
                    array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'series', 'field'=>'orders.series', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'orderNo', 'field'=>'orders.orderNo', 'default_value'=>'', 'operator'=>'like_both'),
                    array('variable'=>'type', 'field'=>'orders.type', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'subject', 'field'=>'orders.subject', 'default_value'=>'', 'operator'=>'like_both'),
                    array('variable'=>'totalDays', 'field'=>'orders.totalDays', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'status', 'field'=>'orders.status', 'default_value'=>'', 'operator'=>'where'),
                );

        // sorting fields
                $sorting_fields = array('dateFiled'=>'desc','orderNo'=>'asc');

                $types = array();
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Memorandum Order')) {
                    $types[] = 1;
                }
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Branch Order')) {
                    $types[] = 2;
                }
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Travel Order')) {
                    $types[] = 3;
                }

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
                $this->db->select($this->table.'.empID');
                $this->db->select('orders.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('orders', $this->table.'.orderID=orders.orderID','left');
                
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
                
                // memo, oo, to
                $this->db->where_in('orders.type', $types);
                
                $this->db->group_by($this->table.'.orderID');  
                
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
                
                $data['title'] = "Travel Order List";
                
                // load views
                $this->load->view('header_print', $data);
                $this->load->view($this->module_path . '/printlist');
                $this->load->view('footer_print');
            }

            function exportlist() {
        // load submenu
                $this->submenu();
                $data = $this->data;
        // sorting

        // variable:field:default_value:operator
        // note: dont include the special query field filter
                $this->table = "order_details";

                $condition_fields = array(
                    array('variable'=>'empID', 'field'=>$this->table.'.empID', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'series', 'field'=>'orders.series', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'orderNo', 'field'=>'orders.orderNo', 'default_value'=>'', 'operator'=>'like_both'),
                    array('variable'=>'type', 'field'=>'orders.type', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'subject', 'field'=>'orders.subject', 'default_value'=>'', 'operator'=>'like_both'),
                    array('variable'=>'totalDays', 'field'=>'orders.totalDays', 'default_value'=>'', 'operator'=>'where'),
                    array('variable'=>'status', 'field'=>'orders.status', 'default_value'=>'', 'operator'=>'where'),
                );

        // sorting fields
                $sorting_fields = array('dateFiled'=>'desc','orderNo'=>'asc');

                $types = array();
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Memorandum Order')) {
                    $types[] = 1;
                }
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Branch Order')) {
                    $types[] = 2;
                }
                if ($this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Travel Order')) {
                    $types[] = 3;
                }

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
                $this->db->select($this->table.'.empID');
                $this->db->select('orders.*');
                
                // from
                $this->db->from($this->table);
                
                // join
                $this->db->join('orders', $this->table.'.orderID=orders.orderID','left');
                
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
                
                // memo, oo, to
                $this->db->where_in('orders.type', $types);
                
                $this->db->group_by($this->table.'.orderID');  
                
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

                $title = "Travel Order List";
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
                <Column ss:Index='5' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
                <Column ss:Index='6' ss:AutoFitWidth=\"1\" ss:Width='150.00'/>
                <Column ss:Index='7' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
                <Column ss:Index='8' ss:AutoFitWidth=\"1\" ss:Width='80.00'/>
                ";

        // header
                $data .= "<Row ss:StyleID='s24'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
                $data .= "</Row>";

                $data .= "<Row ss:StyleID='s20'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . $companyName . "</Data></Cell>";
                $data .= "</Row>";
                $data .= "<Row ss:StyleID='s24A'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . $address . "</Data></Cell>";
                $data .= "</Row>";

                $data .= "<Row ss:StyleID='s24'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
                $data .= "</Row>";

                $data .= "<Row ss:StyleID='s24'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'>" . strtoupper ( $title ) . "</Data></Cell>";
                $data .= "</Row>";

                $data .= "<Row ss:StyleID='s24'>";
                $data .= "<Cell ss:MergeAcross='7'><Data ss:Type='String'></Data></Cell>";
                $data .= "</Row>";

                $fields [] = "  ";
                $fields [] = "SERIES";
                $fields [] = "DATE FILED";
                $fields [] = "ORDER TYPE";
                $fields [] = "ORDER NO.";
                $fields [] = "SUBJECT";
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
                        if ($row->type == 1) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Memorandum Order</Data></Cell>";
                        } else if ($row->type == 2) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Branch Order</Data></Cell>";
                        } else if ($row->type == 3) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Travel Order</Data></Cell>";
                        }
                        $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->orderNo . "</Data></Cell>";
                        $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->subject . "</Data></Cell>";
                        $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>" . $row->totalDays . "</Data></Cell>";

                        if ($row->status == 1) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Pending</Data></Cell>";
                        } else if ($row->status == 2) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Approved</Data></Cell>";
                        } else if ($row->status == 0) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Disapproved</Data></Cell>";
                        } else if ($row->status == -1) {
                            $data .= "<Cell ss:StyleID='s27'><Data ss:Type='String'>Cancelled</Data></Cell>";
                        }

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
        $this->record->pfield   = $this->pfield;
        $this->record->pk       = $id;
        
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




    public function setRecipients($recipientType=6, $employeeTypeID=0, $branchID=0, $divisionID=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        // **************************************************
        // // check roles
        if ($this->roles['create']) {   




            //View Start
            $data['recipientType']  = $recipientType;           
            $data['employeeTypeID'] = explode('_', $employeeTypeID);
            $data['branchID']       = $branchID;
            $data['divisionID']     = $divisionID;

            if ($data['recipientType']==1) {
                if ($this->session->userdata('assigned_companyID')) {
                    $this->db->where('companyID', $this->session->userdata('assigned_companyID'));
                }
                $this->db->where('status', 1);
                $this->db->order_by('companyName', 'asc');
                $data['companies'] = $this->db->get('companies');
                
            } else if ($data['recipientType']==2) {
                if ($this->session->userdata('assigned_companyID')) {
                    $this->db->where('companies.companyID', $this->session->userdata('assigned_companyID'));
                }               
                if ($this->session->userdata('assigned_branchID')) {
                    $this->db->where('branches.branchID', $this->session->userdata('assigned_branchID'));
                }
                $this->db->select('branches.*');
                $this->db->select('companies.companyAbbr');
                $this->db->select('companies.companyName');
                $this->db->from('branches');
                $this->db->join('companies','branches.companyID=companies.companyID','left');
                $this->db->where('branches.status', 1);
                $this->db->order_by('branches.branchName', 'asc');
                $data['branches'] = $this->db->get();
            } else if ($data['recipientType']==3) {
                if ($this->session->userdata('assigned_companyID')) {
                    $this->db->where('companies.companyID', $this->session->userdata('assigned_companyID'));
                }
                if ($this->session->userdata('assigned_branchID')) {
                    $this->db->where('branches.branchID', $this->session->userdata('assigned_branchID'));
                }
                if ($this->session->userdata('assigned_divisionID')) {
                    $this->db->where('divisions.divisionID', $this->session->userdata('assigned_divisionID'));
                }
                $this->db->select('divisions.*');
                $this->db->select('companies.companyAbbr');
                $this->db->select('companies.companyName');
                $this->db->select('branches.branchAbbr');
                $this->db->select('branches.branchName');
                $this->db->from('divisions');
                $this->db->join('companies','divisions.companyID=companies.companyID','left');
                $this->db->join('branches','divisions.branchID=branches.branchID','left');
                $this->db->where('divisions.status', 1);
                $this->db->order_by('divisions.divisionName', 'asc');
                $data['divisions'] = $this->db->get();

            } else if ($data['recipientType']==4) {
                if ($this->session->userdata('assigned_companyID')) {
                    $this->db->where('companies.companyID', $this->session->userdata('assigned_companyID'));
                }
                $this->db->where('status', 1);
                $this->db->order_by('companyName', 'asc');
                $data['companies'] = $this->db->get('companies');
                
                if ($data['branchID']) {
                    $this->db->select('employments.*');
                    $this->db->select('employees.empNo');
                    $this->db->select('employees.lname');
                    $this->db->select('employees.fname');
                    $this->db->select('employees.mname');
                    $this->db->select('employees.suffix');
                    $this->db->select('employee_types.employeeType');
                    $this->db->select('job_positions.positionCode');
                    $this->db->select('job_titles.jobTitle');
                    $this->db->from('employments');
                    $this->db->join('employees','employments.empID=employees.empID','left');
                    $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
                    $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
                    $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
                    $this->db->where('employments.branchID', $data['branchID']);
                    $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
                    $data['employees'] = $this->db->get();                  
                }
            } else if ($data['recipientType']==5) {
                if ($this->session->userdata('assigned_companyID')) {
                    $this->db->where('companies.companyID', $this->session->userdata('assigned_companyID'));
                }
                $this->db->where('status', 1);
                $this->db->order_by('companyName', 'asc');
                $data['companies'] = $this->db->get('companies');
                
                if ($data['divisionID']) {
                    $this->db->select('employments.*');
                    $this->db->select('employees.empNo');
                    $this->db->select('employees.lname');
                    $this->db->select('employees.fname');
                    $this->db->select('employees.mname');
                    $this->db->select('employees.suffix');
                    $this->db->select('employee_types.employeeType');
                    $this->db->select('job_positions.positionCode');
                    $this->db->select('job_titles.jobTitle');
                    $this->db->from('employments');
                    $this->db->join('employees','employments.empID=employees.empID','left');
                    $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID','left');
                    $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
                    $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
                    $this->db->where('employments.divisionID', $data['divisionID']);
                    $this->db->where_in('employments.employeeTypeID', $data['employeeTypeID']);
                    $data['employees'] = $this->db->get();
                }                
            }


            // load views
            echo $this->load->view($this->module_path.'/recipients', $data, true);  
            //View End






        } else {
            // error
            $data["display"] = "block";
            $data["class"]   = "danger";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            echo $this->load->view("message", $data, true);
        }
    }






    public function recipientlist()
    {

        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode(trim($this->input->post('id')));
        // **************************************************
        // variable:field:default_value:operator
        // note: dont include the special query field filter
        $this->table = "order_details";
        $condition_fields = array(
            array('variable'=>'orderID', 'field'=>$this->table.'.orderID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'companyID', 'field'=>'employments.companyID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'branchID', 'field'=>'employments.branchID', 'default_value'=>'', 'operator'=>'where'),
            array('variable'=>'status', 'field'=>'orders.status', 'default_value'=>'', 'operator'=>'where'),
        );


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

            $sortby     = trim($this->input->post('sortby'));
            $sortorder  = trim($this->input->post('sortorder'));

            break;
            case 2:
            foreach($condition_fields as $key) {
                $$key['variable'] = $this->session->userdata($controller.'_'.$key['variable']);
            }

            $sortby     = $this->session->userdata($controller.'_sortby');
            $sortorder  = $this->session->userdata($controller.'_sortorder');
            break;
            default:
            foreach($condition_fields as $key) {
                $$key['variable'] = $key['default_value'];
            }

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

        foreach($condition_fields as $key) {
            if ($key['variable']=='orderID') {
                $$key['variable'] = $id;
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
        $this->db->select($this->table.'.orderID');
        $this->db->select($this->table.'.employmentID');
        $this->db->select($this->table.'.empID');
        $this->db->select($this->table.'.status');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');
        //$this->db->select('job_titles.jobTitle');

        // from
        $this->db->from($this->table);

        // join
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID','left');
        $this->db->join('employees', $this->table.'.empID=employees.empID','left');
        $this->db->join('companies', 'employments.companyID=companies.companyID','left');
        $this->db->join('branches', 'employments.branchID=branches.branchID','left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID','left');
        $this->db->join('employee_types', 'employments.employeeTypeID=employee_types.employeeTypeID','left');
        //$this->db->join('job_titles', 'employments.jobTitleID=job_titles.jobTitleID','left');

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

        $this->db->group_by($this->table.'.employmentID');

        // get
        $data['ttl_rows'] = $config['total_rows'] = $this->db->count_all_results();

        // set pagination
        $config['base_url'] = $this->controller_page.'/show/'.$pageType.'/';
        $config['per_page'] = $limit;
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);


        // select
        $this->db->select($this->table.'.orderID');
        $this->db->select($this->table.'.employmentID');
        $this->db->select($this->table.'.empID');
        $this->db->select($this->table.'.status');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('companies.companyAbbr');
        $this->db->select('branches.branchAbbr');
        $this->db->select('divisions.divisionAbbr');
        $this->db->select('employee_types.employeeType');
        //$this->db->select('job_titles.jobTitle');

        // from
        $this->db->from($this->table);

        // join
        $this->db->join('employments', $this->table.'.employmentID=employments.employmentID','left');
        $this->db->join('employees', $this->table.'.empID=employees.empID','left');
        $this->db->join('companies', 'employments.companyID=companies.companyID','left');
        $this->db->join('branches', 'employments.branchID=branches.branchID','left');
        $this->db->join('divisions', 'employments.divisionID=divisions.divisionID','left');
        $this->db->join('employee_types', 'employments.employeeTypeID=employee_types.employeeTypeID','left');
        //$this->db->join('job_titles', 'employments.jobTitleID=job_titles.jobTitleID','left');

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

        $this->db->group_by($this->table.'.employmentID');

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
        
        // // select
        // $this->db->select('order_details.orderID');
        // $this->db->select('order_details.employmentID');
        // $this->db->select('order_details.empID');
        // $this->db->select('order_details.status');
        // $this->db->select('employments.lname');
        // $this->db->select('employments.mname');
        // $this->db->select('employees.fname');
        // $this->db->select('employees.suffix');
        // $this->db->select('companies.companyAbbr');
        // $this->db->select('branches.branchAbbr');
        // $this->db->select('divisions.divisionAbbr');
        // $this->db->select('employee_types.employeeType');
        // $this->db->select('job_titles.jobTitle');

        // // from
        // $this->db->from('order_details');

        // // join
        // $this->db->join('employments', 'order_details.employmentID=employments.employmentID','left');
        // $this->db->join('employees', 'order_details.empID=employees.empID','left');
        // $this->db->join('companies', 'employments.companyID=companies.companyID','left');
        // $this->db->join('branches', 'employments.branchID=branches.branchID','left');
        // $this->db->join('divisions', 'employments.divisionID=divisions.divisionID','left');
        // $this->db->join('employee_types', 'employments.employeeTypeID=employee_types.employeeTypeID','left');
        // $this->db->join('job_titles', 'employments.jobTitleID=job_titles.jobTitleID','left');

        // $this->db->group_by('order_details.employmentID');

        // get
        $data['orderID'] = $this->encrypter->encode($id);
        $data['records2'] = $this->db->get()->result();
        // echo $this->db->last_query();
        // var_dump($data['records2']);

        // // load views
        $this->load->view($this->module_path . '/recipientlist', $data);
    }

    public function delete_recipient($orderID=0, $employmentID=0, $pageType=0)
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        // **************************************************
        $orderID = $orderID;
        $employmentID = $employmentID;

        // check roles
        if ($this->roles['delete']) {
            // set fields
            $this->record->fields = array();
            // set table
            $this->record->table  = $this->table;
            // set where
            $this->record->where[$this->pfield] = $orderID;
            // execute retrieve
            $this->record->retrieve();

            if (!empty($this->record->field)) {
                $this->record->pfield   = $this->pfield;
                $this->record->pk       = $orderID;

                // record logs
                $logfield = $this->logfield;

                if ($this->record->field->status==1) {
                    $this->db->where('orderID', $orderID);
                    $this->db->where('employmentID', $employmentID);
                    $this->db->delete('order_details');

                    // record logs
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Remove Employee', $logs);
                } else {
                    $this->db->set('status', 0);
                    $this->db->where('orderID', $orderID);
                    $this->db->where('employmentID', $employmentID);
                    $this->db->update('order_details');

                    // record logs
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Cancel Employee', $logs);
                }
            }
        }
    }


    // For employee
    public function display_session_items($display_area='')
    {
        $sessionSet = $this->input->post('sessionSet');
        $records = isset($_SESSION[$sessionSet])? $_SESSION[$sessionSet]:array();
        if ($sessionSet == 'employees') {
            $headers = array(
                array('column_header'=>'','column_field'=>'','width'=>'w-5','align'=>'center'),
                array('column_header'=>'Employee','column_field'=>'','width'=>'','align'=>''),
                array('column_header'=>'Employment','column_field'=>'','width'=>'','align'=>''),
            );
            $display = array(
                array('align'=>'left','field'=>'employeename'),
                array('align'=>'left','field'=>'employment'),
            );
        // var_dump($records);
            echo $this->_tm_session_tabular_view($records,$headers,$display,$sessionSet,'950',$display_area);
        } else {

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


    }



    private function _tm_session_tabular_view($records, $headers, $display, $sessionID, $width="100%",$display_area='',$default_rows=9,$callback="")
    {
        $colspan = count($headers)+1;
        $view = '<table class="table">'."\n";

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
                <i style="font-size: 24px; color: #14699e!important;"class="la la-trash-o" alt="Delete" title="Delete Row" style="cursor: pointer;" onclick="delete_session_item(\''.$sessionID.'\',\''.$id.'\',\''.$display_area.'\',\''.$callback.'\');"></i>
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


    // For date coverage
    public function display_session_items1($display_area='')
    {
        $sessionSet = $this->input->post('sessionSet');
        $records = isset($_SESSION[$sessionSet])? $_SESSION[$sessionSet]:array();
        
        // display session data

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

    public function getOrders()
    {   
        $empID = trim($this->input->post('empID'));
        $employmentID = trim($this->input->post('employmentID'));
        
        if ($empID) {
            $this->db->where('empID', $empID);
        }
        if ($employmentID) {
            $this->db->where('employmentID', $employmentID);
        }
        $this->db->order_by('orderNo','asc');
        $records = $this->db->get('orders');
        echo $this->frameworkhelper->get_json_data($records, 'orderID', 'orderNo');
    }   

    private function _generateID($type,$series)
    {
        switch ($type) {
            case "1" : $code = "MO"; $label = "Memo"; break;
            case "2" : $code = "OO"; $label = "Office"; break;
            case "3" : $code = "TO"; $label = "Travel"; break;
        }
        
        $idSeries   = $this->config_model->getConfig($label.' Order Series');
        $idLength   = $this->config_model->getConfig('Order Series Length');

        $id  = str_pad($idSeries, $idLength, "0", STR_PAD_LEFT);
        return $code.substr($series, 2, 2).$id;
    }
    
    private function _incrementSeries($type)
    {
        switch ($type) {
            case "1" : $query = "UPDATE `config` SET `value` = `value`+ 1 WHERE `name` = 'Memo Order Series'"; $this->db->query($query); break;
            case "2" : $query = "UPDATE `config` SET `value` = `value`+ 1 WHERE `name` = 'Office Order Series'"; $this->db->query($query); break;
            case "3" : $query = "UPDATE `config` SET `value` = `value`+ 1 WHERE `name` = 'Travel Order Series'"; $this->db->query($query); break;
        }   
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
        if (($this->roles['approve'] && $this->record->field->status==1) || $this->userrole_model->has_access($this->session->userdata('current_user')->userID,'View Order Cancel')) {
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
            // $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $this->record->fields);

            if ($this->record->update()) {                              
                if ($status==2) {
                    $this->db->set('status', 2);
                } else {
                    $this->db->set('status', 0);
                }
                $this->db->where('orderID', $this->record->field->orderID);
                $this->db->update('order_details');
                
                
                // record logs
                // if ($wasChange) {
                //     $logfield = $this->logfield;
                //     $logs = "Record - ".$this->record->field->$logfield;
                //     $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, $operation, $logs);
                // }

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

    public function add_form($id)
    {
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
        if ($this->roles['create'] && $this->record->field->status!=0) {
            $data['required_fields'] = array('empID'=>'Employee','employmentID'=>'Employment');
            $data['orderID'] = $id;

            $this->frameworkhelper->clear_session_item('employees');
        // load views
            $this->load->view('header_popup', $data);
            $this->load->view('modules/leave_and_travel/travel_order/add_form');
            $this->load->view('footer_popup');
        } else {
        //error
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }


    public function save_recipient()
    {
        //************** general settings *******************
        // load submenu
        $this->submenu();
        $data = $this->data;

        // **************************************************

        // check roles
        if ($this->roles['create']) {
            $orderID = trim($this->input->post('orderID'));
            // get order detail
            $this->db->where('orderID', $orderID);
            $order = $this->db->get('orders', 1)->row();
            
            // get dates
            $this->db->where('orderID', $orderID);
            $this->db->group_by('employmentID');
            $detail = $this->db->get('order_details', 1)->row();

            $this->db->where('orderID', $orderID);
            $this->db->where('employmentID', $detail->employmentID);
            $dates = $this->db->get('order_details');           

            if ($dates->num_rows()) {
                if (!empty($_SESSION['employees'])) {
                    $batch = array();
                    foreach($_SESSION['employees'] as $employee){
                        $this->db->where('orderID', $orderID);
                        $this->db->where('employmentID', $employee['employmentID']);
                        $exist = $this->db->count_all_results('order_details');

                        if (!$exist) {
                            $info = array();
                            $info['orderID']        = $orderID;
                            $info['empID']          = $employee['empID'];
                            $info['employmentID']   = $employee['employmentID'];
                            foreach($dates->result() as $item){
                                $info['startDate']   = $item->startDate;
                                $info['endDate']     = $item->endDate;
                                $info['workCover']   = $item->workCover;
                                $info['days']        = $item->days;
                                $info['hours']       = $item->hours;
                                $info['startHour']   = $item->startHour;
                                $info['endHour']     = $item->endHour;
                                $info['status']      = $order->status;
                                $batch[] = $info;
                            }
                        }
                    }
                    if ($batch) {
                        $this->db->insert_batch('order_details', $batch);
                    }
                }
            }

            $this->frameworkhelper->clear_session_item('employees');

            // record logs
            $logs = "";
            $this->log_model->table_logs($this->module, $this->table, $this->pfield, $orderID, 'Add Recipient', $logs);

            $logfield = $this->pfield;
            // successful
            $data["display"] = "block";
            $data["class"]   = "notificationbox";
            $data["msg"]     = $this->module." successfully saved.";
            $data["urlredirect"] = $this->controller_page."/view/".$this->encrypter->encode($orderID);

            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        } else {
            // error
            // error
            $data["display"] = "block";
            $data["class"]   = "errorbox";
            $data["msg"]     = "Sorry, you don't have access to this page!";
            $data["urlredirect"] = "";
            $this->load->view("header",$data);
            $this->load->view("message");
            $this->load->view("footer");
        }
    }

    public function print_ticket($id)
    {

        //passed id is not encrypted
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
            // echo $this->db->last_query();
            // ----------------------------------------------------------------------------------
            $data['rec'] = $this->record->field;
            // var_dump($data['rec']);
            
            $this->db->where('orderID', $id);
            $this->db->group_by('startDate');
            $this->db->order_by('startDate','asc');
            $data['order_details'] = $this->db->get('order_details'); 
            // var_dump($data['order_details']);

            
            $data['pdf_paging'] = TRUE;
            $data['title']      = "APPLICATION FOR LEAVE";
            $data['modulename'] = "APPLICATION FOR LEAVE";
    
            // load pdf class
            $this->load->library('mpdf');
            // array(107.95,148.5), "Letter"
            // load pdf class                                      left, right, top, bottom
            $this->mpdf->mpdf('en-GB',array(215.9,148.5),10,'Arial',15,15,5,5,0,20,'P');
            $this->mpdf->setTitle($data['title']);
            $this->mpdf->SetDisplayMode('fullpage');
            $this->mpdf->shrink_tables_to_fit = 1;
            $this->mpdf->SetWatermarkImage(base_url().'images/logo/watermark.png');
            $this->mpdf->watermark_font = 'DejaVuSansCondensed';
            $this->mpdf->watermarkImageAlpha = 0.1;
            $this->mpdf->watermarkImgBehind = TRUE;
            $this->mpdf->showWatermarkImage = TRUE;
    
            $html   = $this->load->view($this->module_path.'/print_ticket', $data, TRUE);
            $this->mpdf->WriteHTML($html);

            $footer = $this->load->view('print_record_footer', $data, TRUE);
            $this->mpdf->SetHTMLFooter($footer);
    
            $this->mpdf->Output("TRIP_TICKET.pdf","I");
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
}
