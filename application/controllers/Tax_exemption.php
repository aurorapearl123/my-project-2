<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tax_exemption extends CI_Controller
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
        $this->data['current_module'] = $this->module = 'Withholding Tax Exemption';
        $this->module_label = 'Withholding Tax Exemption';
        $this->table        = 'tax_exemptions';
        $this->module_path  = 'modules/Pay/Tax_Exemption';
        $this->module_path_submenu  = 'modules/Pay/submenu';
        $this->pfield = 'taxID';
        $this->logfield = 'taxCode';
        $this->data['controller_page'] = $this->controller_page = site_url('tax_exemption');
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
            $data['required_fields'] = array('taxCode'=>'Code', 'exemption'=>'Name');

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
            
            $table_fields = array('taxCode', 'exemption', 'noDependents', 'personalExemption', 'additionalExemption', 'remarks');
            
            foreach($table_fields as $fld) {
                $this->record->fields[$fld] = trim($this->input->post($fld));
            }                                   
            
            if ($this->record->save()) {
                $this->record->fields = array();
                $this->record->where['taxCode']     = trim($this->input->post('taxCode'));              
                $this->record->retrieve();  
                
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

        if ($this->roles['edit']) {
            $data['required_fields'] = array('taxCode'=>'Code', 'exemption'=>'Name', 'status'=>'Status');   
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
        $table_fields = array('taxCode', 'exemption', 'noDependents', 'personalExemption', 'additionalExemption', 'remarks', 'status');

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
                // record logs
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


                if ($this->record->delete()) {

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
        // set table
        $this->record->table = $this->table;
        // set where        
        $this->record->where['taxCode'] = trim($this->input->post('taxCode'));
        // execute retrieve
        $this->record->retrieve();
        
        if (!empty($this->record->field))
            echo "1"; // duplicate
        else 
            echo "0";
    }




    //Ajax functions
    public function getTaxExemptions()
    {       
        $this->db->order_by('taxCode','asc');
        $records = $this->db->get('tax_exemptions');
        echo $this->frameworkhelper->get_json_data($records, 'taxID', 'taxCode');
    }   
    
    // public function display_session()
    // {               
    //     echo var_dump($_SESSION);
    // }







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
