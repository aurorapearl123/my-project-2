<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recognition extends CI_Controller
{
    //Default Variables
    var $common_menu;
    var $roles;
    var $data;
    var $table;
    var $pfield;
    var $logfield;
    var $module;
    var $module_label;
    var $module_path;
    var $controller_page;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('generic_model','record');
        // set variables
        $this->data['current_module'] = $this->module = 'Award and Recognition';
        $this->module_label = 'Awards and Recognitions';
        $this->table        = 'employee_recognitions';
        $this->module_path  = 'modules/Employee/Recognition';
        $this->module_path_submenu  = 'modules/Employee/submenu';
        $this->pfield = 'recognitionID';
        $this->logfield = 'recognition';
        $this->data['controller_page'] = $this->controller_page = site_url('recognition');
        // check if under maintenance
        if ($this->config_model->getConfig('Maintenance Mode')=='1') {
            header('location: '.site_url('maintenance_mode'));
        }
        // check if loggedin
    }

    private function submenu()
    {
        //submenu setup
    }
    
    private function check_roles()
    {
        $this->roles['create']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Add '.$this->module);
        $this->roles['view']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'View '.$this->module);
        $this->roles['edit']    = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Edit Existing '.$this->module);
        $this->roles['delete']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Delete Existing '.$this->module);
        $this->roles['approve'] = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Approve '.$this->module);
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
            $data['required_fields'] = array('empID'=>'Employee', 'recognition'=>'Recognition','organization'=>'Organization','yearGiven'=>'Year Given');
            $data['empID']  = $this->session->userdata('current_empID');
            $data['empNo']  = $this->session->userdata('current_empNo');
            
            if ($data['empID']) {
                $this->db->where('empID', $data['empID']);
                $query = $this->db->get('employees', 1)->row();
                
                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }            
            
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

        // check role
        if ($this->roles['create']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            $table_fields = array('empID', 'recognition', 'organization', 'yearGiven', 'monthGiven', 'dayGiven', 'remarks');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }

            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['empID']       = trim($this->input->post('empID'));
                $this->record->where['recognition'] = trim($this->input->post('recognition'));
                $this->record->where['organization']= trim($this->input->post('organization'));
                $this->record->where['yearGiven']   = trim($this->input->post('yearGiven'));
                $this->record->retrieve();          

                $this->session->set_userdata('current_empID', $this->record->field->empID);
                $this->session->set_userdata('current_empNo', $this->record->field->empNo);

                // record logs
                $logs = "Record - ".trim($this->input->post($this->logfield));
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'Insert', $logs);
                
                $logfield = $this->pfield;                

                // success msg
                $data['class']  = "success";
                $data['msg']    = $this->module." successfully saved.";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Unable to save
                $data['class']  = "danger";
                $data['msg']    = "Error in saving the ".strtolower($this->module)."!";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }
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
    
    public function edit($id)
    {
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        //Retrieve record object
        $this->record->table = $this->table;
        $this->record->setFields();
        $this->record->where[$this->table.'.'.$this->pfield] = $id;
        $this->record->retrieve();

        if ($this->roles['edit']) {
            $data['required_fields'] = array('empID'=>'Employee', 'recognition'=>'Recognition','organization'=>'Organization','yearGiven'=>'Year Given');   
            
            $data['rec'] = $this->record->field;
            
            if ($this->record->field->empID) {
                $this->db->where('empID', $this->record->field->empID);
                $query = $this->db->get('employees', 1)->row();

                $data['employee_name'] = $query->lname.', '.$query->fname.' '.$query->mname.' '.$query->suffix;
            }            

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
        $table_fields = array('empID', 'recognition', 'organization', 'yearGiven', 'monthGiven', 'dayGiven', 'remarks');

        if ($this->roles['edit']) {
            $this->record->table  = $this->table;
            $this->record->fields = array();
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                   
            
            $this->record->pfield   = $this->pfield;
            $this->record->pk       = trim($this->input->post($this->pfield));
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $this->record->fields);            
            if ($this->record->update()) {
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Update', $logs);
                }                

                // Successfully updated
                $data['class']  = "success";
                $data['msg']    = $this->module." successfully updated.";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            } else {
                // Error updating
                $data['class']  = "danger";
                $data['msg']    = "Error in updating the ".strtolower($this->module)."!";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }
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

    public function delete($id=0)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        //Once retrieved field becomes an object
        $this->record->fields = array();
        $this->record->table  = $this->table;
        $this->record->where[$this->pfield] = $id;
        $this->record->retrieve();

        if ($this->roles['delete'] && !$this->_in_used($id)) {
            if (!empty($this->record->field)) {
                $this->record->pfield   = $this->pfield;
                $this->record->pk       = $id;
                
                // record logs
                $logfield = $this->logfield;

                if ($this->record->delete()) {
                    // record logs
                    $logs = "Record - ".$this->record->field->$logfield;
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->pk, 'Delete', $logs);                    

                    //Success msg
                    $data['class']  = "success";
                    $data['msg']    = $this->module." successfully deleted.";
                    $data['urlredirect']    = $this->controller_page."/show";
                    $this->load->view('header', $data);
                    $this->load->view('message');
                    $this->load->view('footer');
                } else {
                    //Error Deleting
                    $data['class']  = "danger";
                    $data['msg']    = "Error in deleting the ".strtolower($this->module)."!";
                    $data['urlredirect']    = "";
                    $this->load->view('header', $data);
                    $this->load->view('message');
                    $this->load->view('footer');
                }

            } else {
                //Record not found
                $data['class']  = "danger";
                $data['msg']    = $this->module." record not found!";
                $data['urlredirect']    = "";
                $this->load->view('header', $data);
                $this->load->view('message');
                $this->load->view('footer');
            }
        } else {
            //No access this page
            $data['class']  = "danger";
            $data['msg']    = "Sorry, you don't have access to this page!";
            $data['urlredirect']    = "";
            $this->load->view('header', $data);
            $this->load->view('message');
            $this->load->view('footer');
        }
    }
    
    public function view($id)
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        $id = $this->encrypter->decode($id);
        // For retrieve with joining tables
        $this->record->table = $this->table;
        $this->record->setFields();
        $this->record->fields[] = 'employees.empNo';
        $this->record->fields[] = 'employees.lname';
        $this->record->fields[] = 'employees.fname';
        $this->record->fields[] = 'employees.mname';
        $this->record->fields[] = 'employees.suffix';
        $this->record->joins[]  = array('employees',$this->table.'.empID=employees.empID','left');
        $this->record->where[$this->table.'.'.$this->pfield] = $id;
        $this->record->retrieve();

        if ($this->roles['view']) {
            $data['rec'] = $this->record->field;
            // record logs
            if ($this->config_model->getConfig('Log all record views') == '1') {
                $logfield = $this->logfield;
                $logs = "Record - ".$this->record->field->$logfield;
                $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->record->field->$data['pfield'], 'View', $logs);
            }
            
            // check if record is used in other tables
            $data['inUsed'] = $this->_in_used($id);
            //load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/view');
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
    
    public function show()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        // Sorting Functions

        //Setup list
        $data['records'] = $this->db->get($this->table);
        //load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path.'/list');
        $this->load->view('footer');
    }
    
    public function printlist()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        //sorting

        //load views
        $this->load->view('header', $data);
        $this->load->view($this->module_path.'/printlist');
        $this->load->view('footer');
    }
    
	
    
    //Conditions and fields changes
    public function check_duplicate()
    {
        $this->record->table = $this->table;
        $this->record->where['empID']           = trim($this->input->post('empID'));
        $this->record->where['recognition']     = trim($this->input->post('recognition'));
        $this->record->where['organization']    = trim($this->input->post('organization'));
        $this->record->where['yearGiven']       = trim($this->input->post('yearGiven'));
        $this->record->retrieve();
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }

    public function get_recognitions()
    {
        $this->db->order_by('recognition','asc');
        $records = $this->db->get('recognitions');
        echo $this->frameworkhelper->get_json_data($records, 'recognitionID', 'recognition');
    }








    // Private functions
    private function _in_used($id=0)
    {
        $tables = array();
        
        if(!empty($tables)) {     
            foreach($tables as $table) {
                $this->db->where($this->pfield, $id);
                $result_count = $this->db->count_all_results($table);
                
                if($result_count > 0) {
                    return true;
                }
            }                               
        } 
        return false;
    }
}
