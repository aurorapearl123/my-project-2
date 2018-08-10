<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salary_wage extends CI_Controller
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
        $this->load->model('generic_model','records');
        $this->module       = 'Salary and Wages';
        $this->table        = 'employees';                                                 
        $this->pfield       = $this->data['pfield'] = 'empID';                                                 
        $this->logfield     = 'empNo';
        $this->module_path  = 'modules/salary_and_wages';           
        $this->data['controller_page']  = $this->controller_page = site_url('salary_wage');
        
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
    
        $this->data['modules']   = $this->modules;
        $this->data['current_main_module']   = $this->modules[$this->module]['main'];              // defines the current main module
        $this->data['current_module']   = $this->modules[$this->module]['sub']['Salaries and Wages'];      // defines the current sub module
        // check roles
        $this->check_roles();
        $this->data['roles']   = $this->roles;
    }

    private function check_roles()
    {
        // check roles
        $this->roles['manage']  = $this->userrole_model->has_access($this->session->userdata('current_userID'),'Manage '.$this->module);
    }
    
    public function index()
    {
        $this->view();
    }
    
    public function save()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $table_fields = array('course', 'organizer', 'venue', 'noHours', 'remarks');

        // check role
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Add '.$this->module)) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            if (trim($this->input->post('startDate')) != '') {
                $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
            }
            if (trim($this->input->post('endDate')) != '') {
                $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
            }
            $this->records->fields['empID']    = $this->encrypter->decode($this->input->post('empID'));
            
            if ($this->records->save()) {
                $this->records->fields = array();
                $pkey = $this->records->where[$this->pfield] = $this->db->insert_id();
                $this->records->retrieve();  
                
				// record logs
				$pfield = $this->pfield;
				$logs = "Record - ".trim($this->input->post($this->logfield));
				$this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->field->$pfield, 'Insert', $logs);
				
				$logfield = $this->pfield;
				// success msg
				$response->status  = 1;
                $response->message = $this->module." successfully saved.";
               
            } else {
                // error
                $response->message = "Error in saving the ".strtolower($this->module)."!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }
    
    public function update()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $table_fields = array('course', 'organizer', 'venue', 'noHours', 'remarks');

        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Edit Existing '.$this->module)) {
            $this->records->table = $this->table;
            $this->records->fields = array();
            
            foreach($table_fields as $fld) {
                $this->records->fields[$fld] = trim($this->input->post($fld));
            }
            
            if (trim($this->input->post('startDate')) != '') {
                $this->db->set('startDate', date('Y-m-d', strtotime(trim($this->input->post('startDate')))));
            }
            if (trim($this->input->post('endDate')) != '') {
                $this->db->set('endDate', date('Y-m-d', strtotime(trim($this->input->post('endDate')))));
            }
            
            $this->records->pfield   = $this->pfield;
            $this->records->pk       = $this->input->post($this->pfield);
            
            // field logs here
            $wasChange = $this->log_model->field_logs($this->module, $this->table, $this->pfield, $this->input->post($this->pfield), 'Update', $this->records->fields);
            
            if ($this->records->update()) {
                // record logs
                if ($wasChange) {
                    $logs = "Record - ".trim($this->input->post($this->logfield));
                    $this->log_model->table_logs($this->module, $this->table, $this->pfield, $this->records->pk, 'Update', $logs);
                }
                    
                // successful
                $response->status  = 1;
                $response->message = $this->module." successfully updated.";
            } else {
                // error
                $response->message = "Error in updating the ".$this->module."!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }

    public function delete($id=0)
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
        
        $id = trim($this->input->post('trainingID'));
        
        // check roles
        if ($this->userrole_model->has_access($this->session->userdata('current_userID'), 'Delete Existing '.$this->module)) {
            // set fields
            $this->records->fields = array();
            // set table
            $this->records->table = $this->table;
            // set where
            $this->records->where[$this->pfield] = $id;
            // execute retrieve
            $this->records->retrieve();
            
            if (!empty($this->records->field)) {
                $this->records->pfield   = $this->pfield;
                $this->records->pk       = $id;
                
                // record logs
                $logfield = $this->records->field->name;
                                
                if ($this->records->delete()) {
                    
                    // record logs
					$logs = "Record - ".$logfield;
					$this->log_model->table_logs($this->module, $this->table, $this->pfield, $id, 'Delete', $logs);
                    
                    // successful
					$response->status  = 1;
                    $response->message = $this->module." successfully deleted.";
                } else {
                    // error
                    $response->message = "Error in deleting the ".$this->module."!";
                }
            } else {
                // error
                $response->message = $this->module." record not found!";
            }
        } else {
            // error
            $response->message = "Sorry, you don't have access to this page!";
        }
        
        echo json_encode($response);
    }
    
    public function view()
    {
        // load submenu
        $this->submenu();
        $data = $this->data;
        
        if ($this->roles['manage']) {
            
            //load views
            $this->load->view('header', $data);
            $this->load->view($this->module_path.'/overview');
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
    
    public function show_employees()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        // check role
        $branchID    = trim($this->input->post('branchID'));
        $deptID      = trim($this->input->post('deptID'));
        $divisionID  = trim($this->input->post('divisionID'));
    
        $this->db->select('employments.empID');
        $this->db->select('employments.employmentID');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.fname');
        $this->db->select('employees.imageExtension');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        if ($branchID != '') {
            $this->db->where('employments.branchID', $branchID);
        }
        if ($deptID != '') {
            $this->db->where('employments.deptID', $deptID);
        }
        if ($divisionID != '') {
            $this->db->where('employments.divisionID', $divisionID);
        }
        $this->db->where('employments.status', 1);
        $this->db->order_by('employments.lname', 'asc');
        $this->db->order_by('employees.fname', 'asc');
        $this->db->order_by('employments.mname', 'asc');
        $records = $this->db->get('employments')->result();
        	
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function show_overview()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        // check role
        $employmentID = trim($this->input->post('employmentID'));
    
        $this->db->select('employments.*');
        $this->db->select('DATE_FORMAT(employments.dateAppointed, "%m/%d/%Y") as dateAppointed');
        $this->db->select('DATE_FORMAT(employments.dateTerminated, "%m/%d/%Y") as dateTerminated');
        $this->db->select('employees.fname');
        $this->db->select('employees.suffix');
        $this->db->select('departments.deptName');
        $this->db->select('departments.deptCode');
        $this->db->select('branches.branchName');
        $this->db->select('branches.branchCode');
        $this->db->select('divisions.divisionName');
        $this->db->select('divisions.divisionCode');
        $this->db->select('employee_types.employeeType');
        $this->db->select('job_positions.positionCode');
        $this->db->select('job_titles.jobTitle');
        $this->db->join('employees','employments.empID=employees.empID', 'left');
        $this->db->join('branches','employments.branchID=branches.branchID', 'left');
        $this->db->join('departments','employments.deptID=departments.deptID', 'left');
        $this->db->join('divisions','employments.divisionID=divisions.divisionID', 'left');
        $this->db->join('employee_types','employments.employeeTypeID=employee_types.employeeTypeID', 'left');
        $this->db->join('job_positions','employments.jobPositionID=job_positions.jobPositionID', 'left');
        $this->db->join('job_titles','job_positions.jobTitleID=job_titles.jobTitleID', 'left');
        $this->db->where('employments.employmentID', $employmentID);
        $record = $this->db->get('employments', 1)->row();
         
        $response->status  = 1;
        $response->record  = $record;
    
        echo json_encode($response);
    }
    
    public function show_incentives()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        // check role
        $employmentID = trim($this->input->post('employmentID'));
    
        $this->db->select('incentive_details.*');
        $this->db->select('incentive_types.name');
        $this->db->select('incentive_types.abbr');
        $this->db->select('DATE_FORMAT(incentive_details.effectivity, "%m/%d/%Y") as effectivity');
        $this->db->join('incentives', 'incentive_details.payID=incentives.payID', 'left');
        $this->db->join('incentive_types', 'incentives.incentiveTypeID=incentive_types.incentiveTypeID', 'left');
        $this->db->where('incentive_details.employmentID', $employmentID);
        $records = $this->db->get('incentive_details')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
    
    public function show_contributions()
    {
        $response = new stdClass();
        $response->status  = 0;
        $response->message = '';
         
        // check role
        $employmentID = trim($this->input->post('employmentID'));
    
        $this->db->select('contribution_details.*');
        $this->db->select('premiums.name');
        $this->db->select('premiums.abbr');
        $this->db->select('employments.lname');
        $this->db->select('employments.mname');
        $this->db->select('employees.empNo');
        $this->db->select('employees.fname');
        $this->db->select('DATE_FORMAT(contribution_details.effectivity, "%m/%d/%Y") as effectivity');
        $this->db->join('contributions', 'contribution_details.payID=contributions.payID', 'left');
        $this->db->join('premiums', 'contributions.premiumID=premiums.premiumID', 'left');
        $this->db->join('employments', 'contribution_details.employmentID=employments.employmentID', 'left');
        $this->db->join('employees', 'employments.empID=employees.empID', 'left');
        $this->db->where('contribution_details.employmentID', $employmentID);
        $records = $this->db->get('contribution_details')->result();
         
        $response->status  = 1;
        $response->records  = $records;
    
        echo json_encode($response);
    }
}
